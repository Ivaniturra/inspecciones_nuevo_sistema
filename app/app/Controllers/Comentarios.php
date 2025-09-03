<?php

namespace App\Controllers;

use App\Models\ComentarioModel;

class Comentarios extends BaseController
{
    protected ComentarioModel $comentarios;

    public function __construct()
    {
        $this->comentarios = new ComentarioModel();
        helper(['form']);
    }

    public function index()
    {
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 10;

        $ciaId    = $this->request->getGet('cia_id');
        $perfilId = $this->request->getGet('perfil_id');
        $q        = trim((string)$this->request->getGet('q'));

        $builder = $this->comentarios
            ->select('comentarios.*, cias.cia_nombre, perfiles.perfil_nombre')
            ->join('cias', 'cias.cia_id = comentarios.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = comentarios.perfil_id', 'left')
            ->orderBy('comentarios.comentario_id', 'DESC');

        if ($ciaId) {
            $builder->where('comentarios.cia_id', (int)$ciaId);
        }
        if ($perfilId) {
            $builder->where('comentarios.perfil_id', (int)$perfilId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('comentarios.comentario_nombre', $q)
                ->orLike('comentarios.comentario_id_cia_interno', $q)
                ->orLike('cias.cia_nombre', $q)
                ->orLike('perfiles.perfil_nombre', $q)
            ->groupEnd();
        }

        $rows  = $builder->paginate($perPage);
        $pager = $this->comentarios->pager;

        return view('comentarios/index', [
            'rows'     => $rows,
            'pager'    => $pager,
            'cias'     => $this->getCiasList(),
            'perfiles' => $this->getPerfilesList(),
            'filtros'  => [
                'cia_id'    => $ciaId,
                'perfil_id' => $perfilId,
                'q'         => $q,
                'per_page'  => $perPage
            ],
        ]);
    }
    public function toggleStatus($id)
    {
        // Validar que sea petici?n AJAX
        if (!$this->request->isAJAX()) {
            return redirect()->to('/comentarios')->with('error', 'M?todo no permitido');
        }

        // Rate limit b?sico (opcional)
        // if (!$this->checkRateLimit('toggle_comentario', 20, 60)) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Demasiados intentos. Intenta m?s tarde.'
        //     ])->setStatusCode(429);
        // }

        $id = (int) $id;
        
        // Buscar comentario
        $comentario = $this->comentarioModel->find($id);
        
        if (!$comentario) {
            return $this->response
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON([
                    'success' => false,
                    'message' => 'Comentario no encontrado'
                ])->setStatusCode(404);
        }

        // Obtener estado actual (por defecto 1 si no existe el campo)
        $oldStatus = (int) ($comentario['comentario_habil'] ?? 1);
        $newStatus = $oldStatus === 1 ? 0 : 1;

        try {
            // Actualizar estado
            $updateData = ['comentario_habil' => $newStatus];
            
            if ($this->comentarioModel->update($id, $updateData)) {
                
                // Log opcional de auditor?a
                log_message('info', "Comentario {$id} cambi? estado: {$oldStatus} -> {$newStatus}");
                
                // Si tienes sistema de auditor?a:
                // $this->logAuditAction('comentario_status_changed', [
                //     'comentario_id' => $id,
                //     'old_status'    => $oldStatus,
                //     'new_status'    => $newStatus,
                //     'changed_by'    => $this->session->get('user_id') ?? 'system',
                //     'ip_address'    => $this->request->getIPAddress(),
                // ]);

                // ? RESPUESTA EXITOSA con nuevo token CSRF
                return $this->response
                    ->setHeader('X-CSRF-TOKEN', csrf_hash()) // Nuevo token
                    ->setJSON([
                        'success'     => true,
                        'message'     => 'Estado del comentario actualizado correctamente',
                        'new_status'  => $newStatus,
                        'status_text' => $newStatus ? 'Activo' : 'Inactivo',
                        'comentario_id' => $id
                    ]);
            }

            throw new \Exception('Error al actualizar en la base de datos');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en toggleStatus comentario: ' . $e->getMessage());
            
            return $this->response
                ->setHeader('X-CSRF-TOKEN', csrf_hash()) // Token incluso en error
                ->setJSON([
                    'success' => false,
                    'message' => 'Error interno al actualizar el estado del comentario'
                ])->setStatusCode(500);
        }
    }

    /**
     * Rate limit helper (opcional)
     */
    private function checkRateLimit(string $action, int $maxAttempts = 5, int $windowSeconds = 900): bool
    {
        $ip  = $this->request->getIPAddress();
        $key = 'rl_' . md5($action . '|' . $ip);
        return service('throttler')->check($key, $maxAttempts, $windowSeconds);
    }
    public function create()
    {
        return view('comentarios/create', [
            'cias'     => $this->getCiasList(),
            'perfiles' => $this->getPerfilesList()
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();
        
        // Validaciones
        $rules = [
            'comentario_nombre' => 'required|min_length[2]|max_length[2000]',
            'cia_id'           => 'required|integer',
            'perfil_id'        => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data['comentario_devuelve']     = isset($data['comentario_devuelve']) ? 1 : 0;
        $data['comentario_elimina']      = isset($data['comentario_elimina']) ? 1 : 0;
        $data['comentario_envia_correo'] = isset($data['comentario_envia_correo']) ? 1 : 0;

        // Si perfil_id viene vacío, guardarlo como NULL
        if (empty($data['perfil_id'])) {
            $data['perfil_id'] = null;
        }

        if (! $this->comentarios->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->comentarios->errors());
        }
        
        return redirect()->to(base_url('comentarios'))->with('success', 'Comentario creado correctamente.');
    } 

    public function show($id = null)
    {
        $id  = (int)$id;
        $row = $this->comentarios
            ->select('comentarios.*, cias.cia_nombre, perfiles.perfil_nombre')
            ->join('cias', 'cias.cia_id = comentarios.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = comentarios.perfil_id', 'left')
            ->find($id);

        if (! $row) {
            return redirect()->to(base_url('comentarios'))
                            ->with('error','Comentario no encontrado.');
        }  
        return redirect()->to(base_url('comentarios'))
                        ->with('success','Comentario Editado: '.$row['comentario_nombre']);
    } 

    public function edit($id = null)
    {
        $id  = (int)$id;
        $row = $this->comentarios
            ->select('comentarios.*, cias.cia_nombre, perfiles.perfil_nombre')
            ->join('cias', 'cias.cia_id = comentarios.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = comentarios.perfil_id', 'left')
            ->find($id);
            
        if (! $row) {
            return redirect()->to(base_url('comentarios'))->with('error','Comentario no encontrado.');
        }
        
        return view('comentarios/edit', [
            'comentario' => $row,
            'cias'       => $this->getCiasList(),
            'perfiles'   => $this->getPerfilesList()
        ]);
    }

    public function update($id = null)
    {
        $id   = (int)$id;
        $data = $this->request->getPost() ?: $this->request->getRawInput();

        // Validaciones
        $rules = [
            'comentario_nombre' => 'required|min_length[2]|max_length[2000]',
            'cia_id'           => 'required|integer',
            'perfil_id'        => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data['comentario_devuelve']     = isset($data['comentario_devuelve']) ? 1 : 0;
        $data['comentario_elimina']      = isset($data['comentario_elimina']) ? 1 : 0;
        $data['comentario_envia_correo'] = isset($data['comentario_envia_correo']) ? 1 : 0;

        // Si perfil_id viene vacío, guardarlo como NULL
        if (empty($data['perfil_id'])) {
            $data['perfil_id'] = null;
        }

        if (! $this->comentarios->find($id)) {
            return redirect()->to(base_url('comentarios'))->with('error','Comentario no encontrado.');
        }
        
        if (! $this->comentarios->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->comentarios->errors());
        }
        
        return redirect()->to(base_url('comentarios/show/'.$id))->with('success','Comentario actualizado correctamente.');
    }

    public function delete($id = null)
    {
        $id = (int)$id;
        if (! $this->comentarios->find($id)) {
            return redirect()->to(base_url('comentarios'))->with('error','Comentario no encontrado.');
        }
        if (! $this->comentarios->delete($id)) {
            return redirect()->back()->with('error','No se pudo eliminar el comentario.');
        }
        return redirect()->to(base_url('comentarios'))->with('success','Comentario eliminado.');
    }

    /**
     * Obtiene lista de compañías para los dropdowns
     */
    private function getCiasList(): array
    {
        $CompanyModel = model('CiaModel');
        $rows = $CompanyModel->asArray()
            ->select('cia_id, cia_nombre')
            ->where('cia_habil', 1)
            ->orderBy('cia_nombre')
            ->findAll();
        return array_column($rows, 'cia_nombre', 'cia_id');
    }

    /**
     * Obtiene lista de perfiles para los dropdowns
     */
    private function getPerfilesList(): array
    {
        $PerfilModel = model('PerfilModel'); // Asegúrate de que este modelo exista
        $rows = $PerfilModel->asArray()
            ->select('perfil_id, perfil_nombre, perfil_tipo')
            ->where('perfil_habil', 1)
            ->orderBy('perfil_tipo, perfil_nombre')
            ->findAll();
        
        $perfiles = [];
        foreach ($rows as $row) {
            $perfiles[$row['perfil_id']] = $row['perfil_nombre'] . ' (' . ucfirst($row['perfil_tipo']) . ')';
        }
        return $perfiles;
    }

    /**
     * API: Obtiene comentarios filtrados por perfil
     */
    public function getByPerfil($perfilId = null)
    {
        if (!$perfilId) {
            return $this->response->setJSON(['error' => 'Perfil ID requerido']);
        }

        $comentarios = $this->comentarios
            ->select('comentario_id, comentario_nombre, comentario_id_cia_interno, comentario_devuelve, comentario_elimina, comentario_envia_correo')
            ->where('perfil_id', (int)$perfilId)
            ->where('comentario_deleted_at IS NULL')
            ->orderBy('comentario_nombre')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $comentarios
        ]);
    }
}