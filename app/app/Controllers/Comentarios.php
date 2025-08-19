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

        $ciaId = $this->request->getGet('cia_id');
        $q     = trim((string)$this->request->getGet('q'));

        $builder = $this->comentarios->orderBy('comentario_id', 'DESC');

        if ($ciaId) {
            $builder->where('cia_id', (int)$ciaId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('comentario_nombre', $q)
                ->orLike('comentario_id_cia_interno', $q)
            ->groupEnd();
        }

        $rows  = $builder->paginate($perPage);
        $pager = $this->comentarios->pager;

        return view('comentarios/index', [
            'rows'    => $rows,
            'pager'   => $pager,
            'cias'    => $this->getCiasList(),
            'filtros' => ['cia_id'=>$ciaId,'q'=>$q,'per_page'=>$perPage],
        ]);
    }

    public function create()
    {
        return view('comentarios/create', ['cias' => $this->getCiasList()]);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $data['comentario_devuelve']     = isset($data['comentario_devuelve']) ? 1 : 0;
        $data['comentario_elimina']      = isset($data['comentario_elimina']) ? 1 : 0;
        $data['comentario_envia_correo'] = isset($data['comentario_envia_correo']) ? 1 : 0;

        if (! $this->comentarios->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->comentarios->errors());
        }
        return redirect()->to(base_url('comentarios'))->with('success', 'Comentario creado correctamente.');
    } 
    public function show($id = null)
    {
        $id  = (int)$id;
        $row = $this->comentarios->find($id);

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
        $row = $this->comentarios->find($id);
        if (! $row) return redirect()->to(base_url('comentarios'))->with('error','Comentario no encontrado.');
        return view('comentarios/edit', ['comentario'=>$row,'cias'=>$this->getCiasList()]);
    }

    public function update($id = null)
    {
        $id   = (int)$id;
        $data = $this->request->getPost() ?: $this->request->getRawInput();

        $data['comentario_devuelve']     = isset($data['comentario_devuelve']) ? 1 : 0;
        $data['comentario_elimina']      = isset($data['comentario_elimina']) ? 1 : 0;
        $data['comentario_envia_correo'] = isset($data['comentario_envia_correo']) ? 1 : 0;

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

    private function getCiasList(): array
    {
        $CompanyModel = model('CiaModel'); // ajusta si se llama distinto
        $rows = $CompanyModel->asArray()->select('cia_id, cia_nombre')->findAll();
        return array_column($rows, 'cia_nombre', 'cia_id');
    }
}
