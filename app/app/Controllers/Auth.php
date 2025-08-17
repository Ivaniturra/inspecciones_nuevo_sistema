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
            'appTitle'   => 'InspectZu',
            'brandTitle' => 'InspectZu',
            'brandLogo'  => base_url('assets/img/logo.jpg'), // opcional
        ]);
    }

   public function attempt()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = strtolower(trim($this->request->getPost('email')));
        $pass     = (string)$this->request->getPost('password');
        $remember = (bool)$this->request->getPost('remember');

        $users = new \App\Models\UserModel();
        $user  = $users->findByEmail($email);

        if (! $user || empty($user['user_habil'])) {
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas.');
        }

        if (! password_verify($pass, $user['user_clave'])) {
            $users->logLoginAttempt((int)$user['user_id'], false);
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas.');
        }

        // OK
        session()->set([
            'user_id'        => (int)$user['user_id'],
            'user_name'      => $user['user_nombre'],
            'user_email'     => $user['user_email'],
            // útiles para checks posteriores:
            'user_perfil_id' => (int)$user['user_perfil'],
            'perfil_nombre'  => $user['perfil_nombre'] ?? null,
            'perfil_tipo'    => $user['perfil_tipo']   ?? null,
            'perfil_nivel'   => (int)($user['perfil_nivel'] ?? 0),
            'cia_id'         => $user['cia_id'] ?? null,
            'logged_in'      => true,
        ]);

        $users->logLoginAttempt((int)$user['user_id'], true);
        $users->updateLastAccess((int)$user['user_id']);

        // (Opcional) remember
        if ($remember) {
            // Implementa aquí tu token de "recordarme" si lo necesitas
        }

        // ===== Redirección por tipo/perfil =====
        $perfilNombre = strtolower(trim($user['perfil_nombre'] ?? ''));
        $perfilNivel  = (int)($user['perfil_nivel'] ?? 0);

        // Criterios de "Super Admin":Super Administrador
        $isSuperAdmin = ($perfilNombre === 'super administrador');

        if ($isSuperAdmin) {
            return redirect()->to(base_url('cias'));   // vista de compañías
        }

        // Otras variantes si quisieras:
        // if (($user['perfil_tipo'] ?? '') === 'interno') return redirect()->to(base_url('dashboard'));
        // if (($user['perfil_tipo'] ?? '') === 'compania') return redirect()->to(base_url('inspecciones'));

        // Por defecto:
       // return redirect()->to(base_url('dashboard'));  // ajusta si tu ruta inicial es otra
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Sesión cerrada correctamente.');
    }

    // Opcional: recuperación
    public function forgot()    { return view('auth/forgot'); }
    public function sendReset() { /* envío token */ }
}
