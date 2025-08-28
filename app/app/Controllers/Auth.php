<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            // Validar CSRF
            if (!$this->validate(['csrf_token' => 'required'])) {
                return redirect()->back()->withInput()->with('error', 'Token de seguridad inválido.');
            }
            
            // Tu lógica de autenticación aquí
        }
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

        // 2) Usuario
        $users = new \App\Models\UserModel();
        $user  = $users->findByEmail($email); // <- ya hace JOIN con cias y perfiles

        if (! $user || empty($user['user_habil'])) {
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas.');
        }

        // 3) Password
        if (! password_verify($pass, (string) $user['user_clave'])) {
            $users->logLoginAttempt((int) $user['user_id'], false);
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas.');
        } 
        

        // 7) Sesión base + branding
        session()->set([
            'user_id'        => (int) $user['user_id'],
            'user_name'      => $user['user_nombre'],
            'user_email'     => $user['user_email'],
            'user_perfil_id' => (int) ($user['user_perfil'] ?? 0),
            'perfil_nombre'  => $user['perfil_nombre']   ?? null,
            'perfil_tipo'    => $user['perfil_tipo']     ?? null,
            'perfil_nivel'   => (int) ($user['perfil_nivel'] ?? 0),
            'cia_id'         => $user['cia_id']          ?? null,
            'logged_in'      => true,

            // Branding usable en tus vistas
            'brand_title'    => $user['cia_nombre'],
            'brand_logo'     => env('app.ubicacion_logo_avatar').$user['user_avatar'],
            'nav_bg'         => $user['cia_brand_nav_bg'],
            'nav_text'       => $user['cia_brand_nav_text'],
            'sidebar_start'  => $user['cia_brand_side_start'],
            'sidebar_end'    => $user['cia_brand_side_end'],
            // Opcional: título de app para <title>
            'app_title'      => $user['cia_nombre'],
        ]);

        // 8) Auditoría y housekeeping
        $users->logLoginAttempt((int) $user['user_id'], true);
        $users->updateLastAccess((int) $user['user_id']);

        // 9) Recordarme (opcional)
        if ($remember) {
            // TODO: genera token, guárdalo en DB y en cookie segura (httpOnly/secure)
            // helper('cookie');
            // set_cookie('remember_token', $token, 60*60*24*30); // 30 días
        }

        // 10) Redirección
        $intended = session('intended'); // guardado por tu AuthGuard
        if (!empty($intended)) {
            session()->remove('intended');
            return redirect()->to($intended);
        }

        // Super Admin → CIAs
        $perfilNombre = strtolower(trim((string) ($user['perfil_nombre'] ?? '')));
        $isSuperAdmin = ($perfilNombre === 'super administrador') || ((int) ($user['user_perfil'] ?? 0) === 7);
        if ($isSuperAdmin) {
            return redirect()->to(base_url('cias'));
        }

        // Por defecto → dashboard
        return redirect()->to(base_url('dashboard'));
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
