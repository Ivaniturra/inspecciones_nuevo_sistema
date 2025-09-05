<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // Si ya está logueado, redirige
        if (session('user_id')) {
            return redirect()->to(base_url('cias')); 
        }

        return view('auth/login', [
            'title'      => 'Iniciar sesión',
            'appTitle'   =>  env('app.title'),
            'brandTitle' =>  env('app.title'),
            'brandLogo'  =>  env('app.ubicacion_logo_pagina').env('imagen_nomb_logo'), 
        ]);
    } 
    public function attempt()
    {
        // 1) Validación básica
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $pass     = (string) $this->request->getPost('password');
        $remember = (bool)  $this->request->getPost('remember');

        // 2) Usuario con JOIN actualizado para corredores
        $users = new \App\Models\UserModel();
        $user  = $users->findByEmail($email); // ya hace JOIN con cias, corredores y perfiles

        if (! $user || empty($user['user_habil'])) {
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas.');
        }

        // 3) Password
        if (! password_verify($pass, (string) $user['user_clave'])) {
            $users->logLoginAttempt((int) $user['user_id'], false);
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas.');
        }

        // 4) Verificar si necesita cambiar contraseña
        if (!empty($user['user_debe_cambiar_clave'])) {
            // Guardar datos temporales para el cambio de contraseña
            session()->setTempdata('temp_user_id', $user['user_id'], 600); // 10 minutos
            return redirect()->to(base_url('auth/change-password'))
                ->with('info', 'Debes cambiar tu contraseña antes de continuar.');
        }

        // 5) Determinar branding según tipo de usuario
        $brandingData = $this->getBrandingForUser($user);

        // 6) Sesión base + branding
        session()->set([
            'user_id'        => (int) $user['user_id'],
            'user_name'      => $user['user_nombre'],
            'user_email'     => $user['user_email'],
            'user_perfil_id' => (int) ($user['user_perfil'] ?? 0),
            'perfil_nombre'  => $user['perfil_nombre']   ?? null,
            'perfil_tipo'    => $user['perfil_tipo']     ?? null,
            'perfil_nivel'   => (int) ($user['perfil_nivel'] ?? 0),
            'cia_id'         => $user['cia_id']          ?? null,
            'corredor_id'    => $user['corredor_id']     ?? null,
            'logged_in'      => true,

            // Branding dinámico según tipo de usuario
            'brand_title'    => $brandingData['title'],
            'brand_logo'     => $brandingData['logo'],
            'nav_bg'         => $brandingData['nav_bg'],
            'nav_text'       => $brandingData['nav_text'],
            'sidebar_start'  => $brandingData['sidebar_start'],
            'sidebar_end'    => $brandingData['sidebar_end'],
            'app_title'      => $brandingData['app_title'],
        ]);

        // 7) Auditoría y housekeeping
        $users->logLoginAttempt((int) $user['user_id'], true);
        $users->updateLastAccess((int) $user['user_id']);

        // 8) Recordarme (opcional)
        if ($remember) {
            // TODO: implementar remember token
        }

        // 9) Redirección según tipo de usuario
        return $this->redirectByUserType($user);
    }

    /**
     * Obtener branding específico según tipo de usuario
     */
    private function getBrandingForUser(array $user): array
    {
        $perfil_tipo = $user['perfil_tipo'] ?? 'interno';
        
        switch ($perfil_tipo) {
            case 'compania':
                // Branding de la compañía
                return [
                    'title'        => $user['cia_nombre'] ?? 'Sistema',
                    'logo'         => $this->getLogoPath($user['user_avatar']),
                    'nav_bg'       => $user['cia_brand_nav_bg'] ?? '#0D6EFD',
                    'nav_text'     => $user['cia_brand_nav_text'] ?? '#FFFFFF',
                    'sidebar_start'=> $user['cia_brand_side_start'] ?? '#667EEA',
                    'sidebar_end'  => $user['cia_brand_side_end'] ?? '#764BA2',
                    'app_title'    => $user['cia_nombre'] ?? env('app.title'),
                ];

            case 'corredor':
                // Branding del corredor
                return [
                    'title'        => $user['corredor_nombre'] ?? 'Corredor',
                    'logo'         => $this->getCorredorLogo($user),
                    'nav_bg'       => $user['corredor_brand_nav_bg'] ?? '#FF6B35',
                    'nav_text'     => $user['corredor_brand_nav_text'] ?? '#FFFFFF',
                    'sidebar_start'=> $user['corredor_brand_side_start'] ?? '#FF6B35',
                    'sidebar_end'  => $user['corredor_brand_side_end'] ?? '#F7931E',
                    'app_title'    => $user['corredor_nombre'] ?? env('app.title'),
                ];

            case 'inspector':
                // Branding para inspectores
                return [
                    'title'        => 'Inspector - ' . $user['user_nombre'],
                    'logo'         => $this->getLogoPath($user['user_avatar']),
                    'nav_bg'       => '#28A745',
                    'nav_text'     => '#FFFFFF',
                    'sidebar_start'=> '#28A745',
                    'sidebar_end'  => '#20C997',
                    'app_title'    => 'Sistema de Inspecciones',
                ];

            case 'interno':
            default:
                // Branding por defecto del sistema
                return [
                    'title'        => env('app.title', 'Sistema'),
                    'logo'         => env('app.ubicacion_logo_pagina') . env('imagen_nomb_logo'),
                    'nav_bg'       => '#0D6EFD',
                    'nav_text'     => '#FFFFFF',
                    'sidebar_start'=> '#667EEA',
                    'sidebar_end'  => '#764BA2',
                    'app_title'    => env('app.title', 'Sistema'),
                ];
        }
    }

    /**
     * Redirección según tipo de usuario
     */
    private function redirectByUserType(array $user): \CodeIgniter\HTTP\RedirectResponse
    {
        // Verificar redirección intencionada
        $intended = session('intended');
        if (!empty($intended)) {
            session()->remove('intended');
            return redirect()->to($intended);
        }

        $perfil_tipo = $user['perfil_tipo'] ?? 'interno';
        $perfil_nivel = (int) ($user['perfil_nivel'] ?? 0);
        
        // Super Admin → CIAs
        $perfilNombre = strtolower(trim((string) ($user['perfil_nombre'] ?? '')));
        $isSuperAdmin = ($perfilNombre === 'super administrador') || ($perfil_nivel >= 4);
        if ($isSuperAdmin) {
            return redirect()->to(base_url('cias'));
        }

        // Redirección según tipo de perfil
        switch ($perfil_tipo) {
            case 'corredor':
                // Corredores van a su dashboard específico
                return redirect()->to(base_url('pagina_corredor/corredor'));

            case 'compania':
                // Usuarios de compañía van a su dashboard
                return redirect()->to(base_url('dashboard/compania'));

            case 'inspector':
                // Inspectores van a sus inspecciones asignadas
                return redirect()->to(base_url('inspecciones/mis-asignaciones'));

            case 'interno':
            default:
                // Usuarios internos al dashboard general o CIAs
                return redirect()->to(base_url('dashboard'));
        }
    }

    /**
     * Helper para obtener ruta del logo del usuario
     */
    private function getLogoPath(?string $avatar): string
    {
        if (!empty($avatar)) {
            return env('app.ubicacion_logo_avatar') . $avatar;
        }
        return env('app.ubicacion_logo_pagina') . env('imagen_nomb_logo');
    }

    /**
     * Helper para obtener logo del corredor
     */
    private function getCorredorLogo(array $user): string
    {
        // Si el corredor tiene logo propio
        if (!empty($user['corredor_logo_path'])) {
            return $user['corredor_logo_path'];
        }
        
        // Si no, usar avatar del usuario o logo por defecto
        return $this->getLogoPath($user['user_avatar']);
    }
    public function logout()
    {
        // helper('cookie'); delete_cookie('remember_token');
        session()->destroy(); // limpia todo, incluido branding
        return redirect()->to(base_url('/')); // login
    }
    public function forgot()
    {
        // Si ya está logueado, mándalo al dashboard (o donde prefieras)
        if (session('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/forgot', [
            'title'      => 'Recuperar contraseña',
            'appTitle'   => 'InspectZu',
            'brandTitle' => 'InspectZu',
        ]);
    }

    public function sendReset()
    {
        $rules = ['email' => 'required|valid_email'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = strtolower(trim((string)$this->request->getPost('email')));
        $users = new \App\Models\UserModel();

        // Usar tu helper de modelo
        $token = $users->generateResetToken($email);

        // No revelamos si existe o no el email (seguridad)
        $msgOk = 'Si el email existe en el sistema, enviaremos un enlace para restablecer tu contraseña. Revisa tu bandeja y el spam.';

        if (!$token) {
            return redirect()->to(base_url('forgot'))->with('success', $msgOk);
        }

        // Construir link
        $link = base_url('reset/' . $token);

        // Enviar correo
        helper('email');
        $emailSvc = service('email');

        // Configura tu SMTP en .env o app/Config/Email.php
        $emailSvc->setTo($email);
        $emailSvc->setSubject('Restablecer contraseña');
        // Puedes crear una vista HTML de email; aquí simple texto/HTML:
        $body = view('emails/reset_password', ['link' => $link]);
        $emailSvc->setMessage($body);

        if (!$emailSvc->send()) {
            // En dev, por si no tienes SMTP, logeamos el link y mostramos aviso "dev"
            log_message('error', 'No se pudo enviar email de reset. Link: ' . $link);
            return redirect()->to(base_url('forgot'))
                ->with('success', $msgOk . ' (Modo dev: ' . esc($link) . ')');
        }

        return redirect()->to(base_url('forgot'))->with('success', $msgOk);
    }

    public function reset(string $token)
    {
        // Busca por token
        $users = new \App\Models\UserModel();
        $user  = $users->where('user_token_reset', $token)->first();

        if (!$user) {
            return redirect()->to(base_url('/'))->with('error', 'Enlace inválido o expirado.');
        }

        return view('auth/reset', [
            'title'    => 'Nueva contraseña',
            'token'    => $token,
            'appTitle' => 'InspectZu',
            'brandTitle' => 'InspectZu',
        ]);
    }

    public function processReset()
    {
        $rules = [
            'token'           => 'required',
            'password'        => [
                'label' => 'Contraseña',
                // usa tu regex fuerte si quieres
                'rules' => 'required|min_length[8]',
            ],
            'confirm_password'=> 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token    = (string)$this->request->getPost('token');
        $password = (string)$this->request->getPost('password');

        $users = new \App\Models\UserModel();
        $user  = $users->where('user_token_reset', $token)->first();

        if (!$user) {
            return redirect()->to(base_url('/'))->with('error', 'Enlace inválido o expirado.');
        }

        // Actualizar clave; tu modelo la hashea en callbacks (beforeUpdate)
        $ok = $users->update((int)$user['user_id'], [
            'user_clave'       => $password,
            'user_token_reset' => null,         // invalidar el token
            'user_debe_cambiar_clave' => 0,
        ]);

        if (!$ok) {
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar la contraseña.');
        }

        return redirect()->to(base_url('/'))->with('success', 'Contraseña actualizada. Ahora puedes iniciar sesión.');
    }
}
