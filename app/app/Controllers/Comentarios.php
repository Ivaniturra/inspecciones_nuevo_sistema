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