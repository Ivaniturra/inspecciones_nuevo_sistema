<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ValoresComunasModel;
use App\Models\CiaModel;

class ValoresComunas extends BaseController
{
    protected $valoresComunasModel;
    protected $ciaModel;

    public function __construct()
    {
        $this->valoresComunasModel = new ValoresComunasModel();
        $this->ciaModel = new CiaModel();
        helper(['url', 'text', 'form']);
    }

    public function index()
    {
        $data = [
            'title'        => 'Gestión de Valores por Comuna',
            'valores'      => $this->valoresComunasModel->getValoresWithDetails(),
            'estadisticas' => $this->valoresComunasModel->getEstadisticas(),
            'filtros'      => [],
            'cias'         => $this->getCiasForSelect(),
        ];

        return view('valores_comunas/index', $data);
    }

    /** Formulario de creación */
    public function create()
    {
        $data = [
            'title'           => 'Nuevo Valor por Comuna',
            'validation'      => \Config\Services::validation(),
            'cias'            => $this->getCiasForSelect(),
            'regiones'        => $this->getRegionesForSelect(),
            'tipos_usuario'   => $this->getTiposUsuarioForSelect(),
            'tipos_vehiculo'  => $this->getTiposVehiculoForSelect(),
        ];

        return view('valores_comunas/create', $data);
    }

    /** Guardar nuevo valor */
    public function store()
    {
        $rules = [
            'comunas_id'           => 'required',
            'cia_id'               => 'required|integer',
            'tipo_usuario'         => 'required',
            'tipo_vehiculo_id'     => 'required|integer',
            'unidad_medida'        => 'required',
            'valor'                => 'required|decimal',
            'fecha_vigencia_desde' => 'required|valid_date',
            'fecha_vigencia_hasta' => 'permit_empty|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verificar si ya existe un valor activo para estos parámetros
        if ($this->valoresComunasModel->existeValorCompleto(
            $this->request->getPost('comunas_id'),
            $this->request->getPost('cia_id'),
            $this->request->getPost('tipo_usuario'),
            $this->request->getPost('tipo_vehiculo_id'),
            $this->request->getPost('unidad_medida')
        )) {
            return redirect()->back()->withInput()->with('error', 'Ya existe un valor activo para esta combinación específica');
        }

        $data = [
            'comunas_id'                     => (string)$this->request->getPost('comunas_id'),
            'cia_id'                         => (int)$this->request->getPost('cia_id'),
            'tipo_vehiculo_id'               => (int)$this->request->getPost('tipo_vehiculo_id'),
            'valores_tipo_usuario'           => (string)$this->request->getPost('tipo_usuario'),
            'valores_unidad_medida'          => (string)$this->request->getPost('unidad_medida'),
            'valores_valor'                  => (float)$this->request->getPost('valor'),
            'valores_moneda'                 => (string)($this->request->getPost('moneda') ?: $this->request->getPost('unidad_medida')),
            'valores_descripcion'            => (string)$this->request->getPost('descripcion'),
            'valores_fecha_vigencia_desde'   => (string)$this->request->getPost('fecha_vigencia_desde'),
            'valores_fecha_vigencia_hasta'   => $this->request->getPost('fecha_vigencia_hasta') ?: null,
            'valores_activo'                 => 1,
        ];

        if ($this->valoresComunasModel->save($data)) {
            return redirect()->to('/valores-comunas')->with('success', 'Valor creado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear el valor');
    }

    /** Ver detalle */
    public function show($id)
    {
        $valor = $this->valoresComunasModel->getValorWithFullDetails($id);

        if (! $valor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Valor no encontrado');
        }

        return view('valores_comunas/show', [
            'title' => 'Detalles del Valor',
            'valor' => $valor
        ]);
    }

    /** Formulario de edición */
    public function edit($id)
    {
        $id = (int) $id; // por si acaso

        $db = \Config\Database::connect();

        $builder = $db->table('valores_comunas AS vc');
        $builder->select('
            vc.*,
            c.comunas_nombre,
            p.provincias_id,
            p.provincias_nombre,
            r.region_id,
            r.region_nombre
        ');
        $builder->join('comunas      AS c', 'c.comunas_id      = vc.comunas_id',   'left');
        $builder->join('provincias   AS p', 'p.provincias_id   = c.provincias_id', 'left');
        // OJO acá: usar p.regiones_id (no p.region_id)
        $builder->join('regiones     AS r', 'r.region_id       = p.regiones_id',   'left');

        $builder->where('vc.valores_id', $id);

        $valor = $builder->get()->getRowArray();

        if (!$valor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Valor no encontrado');
        }

        $data = [
            'title'          => 'Editar Valor por Comuna',
            'valor'          => $valor,
            'regiones'       => $this->getRegionesForSelect(),
            'provincias'     => $this->getProvinciasByRegionHelper($valor['region_id']),
            'comunas'        => $this->getComunasByProvinciaHelper($valor['provincias_id']),
            'cias'           => $this->getCiasForSelect(),
            'tipos_usuario'  => $this->getTiposUsuarioForSelect(),
            'tipos_vehiculo' => $this->getTiposVehiculoForSelect(),
            'validation'     => \Config\Services::validation(),
        ];

        return view('valores_comunas/edit', $data);
    }


    /** Actualizar */
    public function update($id)
    {
        $valor = $this->valoresComunasModel->find($id);
        if (! $valor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Valor no encontrado');
        }

        $rules = [
            'comunas_id'           => 'required',
            'cia_id'               => 'required|integer',
            'tipo_usuario'         => 'required',
            'tipo_vehiculo_id'     => 'required|integer',
            'valor'                => 'required|decimal',
            'fecha_vigencia_desde' => 'required|valid_date',
            'fecha_vigencia_hasta' => 'permit_empty|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verificar si ya existe otro valor activo para estos parámetros (excluyendo el actual)
        if ($this->valoresComunasModel->existeValorCompleto(
            $this->request->getPost('comunas_id'),
            $this->request->getPost('cia_id'),
            $this->request->getPost('tipo_usuario'),
            $this->request->getPost('tipo_vehiculo_id'),
            $this->request->getPost('unidad_medida'),
            $id // Excluir el registro actual
        )) {
            return redirect()->back()->withInput()->with('error', 'Ya existe otro valor activo para esta combinación');
        }

        $data = [
            'comunas_id'                     => (string)$this->request->getPost('comunas_id'),
            'cia_id'                         => (int)$this->request->getPost('cia_id'),
            'tipo_vehiculo_id'               => (int)$this->request->getPost('tipo_vehiculo_id'),
            'valores_tipo_usuario'           => (string)$this->request->getPost('tipo_usuario'),
            'valores_unidad_medida'          => (string)$this->request->getPost('unidad_medida'),
            'valores_valor'                  => (float)$this->request->getPost('valor'),
            'valores_moneda'                 => (string)($this->request->getPost('moneda') ?: 'CLP'),
            'valores_descripcion'            => (string)$this->request->getPost('descripcion'),
            'valores_fecha_vigencia_desde'   => (string)$this->request->getPost('fecha_vigencia_desde'),
            'valores_fecha_vigencia_hasta'   => $this->request->getPost('fecha_vigencia_hasta') ?: null,
        ];

        if ($this->valoresComunasModel->update($id, $data)) {
            return redirect()->to('/valores-comunas')->with('success', 'Valor actualizado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar el valor');
    }

    /** Eliminar */
    public function delete($id)
    {
        $valor = $this->valoresComunasModel->find($id);
        if (! $valor) {
            return redirect()->to('/valores-comunas')->with('error', 'Valor no encontrado');
        }

        if ($this->valoresComunasModel->delete($id)) {
            return redirect()->to('/valores-comunas')->with('success', 'Valor eliminado exitosamente');
        }

        return redirect()->to('/valores-comunas')->with('error', 'Error al eliminar el valor');
    }

    /** Toggle estado (AJAX) */
    public function toggleStatus($id)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/valores-comunas');
        }

        if ($this->valoresComunasModel->toggleStatus($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado correctamente']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar el estado']);
    }

    /** Obtener provincias por región (AJAX) */
    public function getProvinciasByRegion($regionId)
    {
        if (! $this->request->isAJAX() && ! is_numeric($regionId)) {
            return redirect()->to('/valores-comunas');
        }

        $rows = $this->getProvinciasByRegionHelper($regionId);
        return $this->response->setJSON($rows);
    }

    /** Obtener comunas por provincia (AJAX) */
    public function getComunasByProvincia($provinciaId)
    {
        if (! $this->request->isAJAX() && ! is_numeric($provinciaId)) {
            return redirect()->to('/valores-comunas');
        }

        $rows = $this->getComunasByProvinciaHelper($provinciaId);
        return $this->response->setJSON($rows);
    }

    /** Obtener comunas por región (método legacy para backward compatibility) */
    public function getComunasByRegion($regionId)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/valores-comunas');
        }

        $db = \Config\Database::connect();
        
        $comunas = $db->table('comunas c')
                     ->select('c.comunas_id, c.comunas_nombre, p.provincias_nombre')
                     ->join('provincias p', 'p.provincias_id = c.provincias_id', 'left')
                     ->join('regiones r', 'r.region_id = p.region_id', 'left')
                     ->where('r.region_id', $regionId)
                     ->orderBy('p.provincias_nombre', 'ASC')
                     ->orderBy('c.comunas_nombre', 'ASC')
                     ->get()
                     ->getResultArray();
        
        return $this->response->setJSON($comunas);
    }

    /** Filtrar valores */
    public function filter()
    {
        $ciaId = $this->request->getGet('cia_id');
        $comunaCodigo = $this->request->getGet('comunas_id');
        $tipoUsuario = $this->request->getGet('tipo_usuario');

        $valores = $this->valoresComunasModel->select('
                valores_comunas.*, 
                comunas.comunas_nombre, 
                regiones.region_nombre, 
                cias.cia_nombre,
                tv.tipo_vehiculo_nombre
            ')
            ->join('comunas', 'comunas.comunas_id = valores_comunas.comunas_id', 'left')
            ->join('provincias', 'provincias.provincias_id = comunas.provincias_id', 'left')
            ->join('regiones', 'regiones.region_id = provincias.regiones_id', 'left')
            ->join('cias', 'cias.cia_id = valores_comunas.cia_id', 'left')
            ->join('tipo_vehiculo tv', 'tv.tipo_vehiculo_id = valores_comunas.tipo_vehiculo_id', 'left');

        if ($ciaId) {
            $valores->where('valores_comunas.cia_id', $ciaId);
        }

        if ($comunaCodigo) {
            $valores->where('valores_comunas.comunas_id', $comunaCodigo);
        }

        if ($tipoUsuario) {
            $valores->where('valores_comunas.valores_tipo_usuario', $tipoUsuario);
        }

        $data = [
            'title'        => 'Valores Filtrados',
            'valores'      => $valores->where('valores_comunas.valores_activo', 1)->findAll(),
            'estadisticas' => $this->valoresComunasModel->getEstadisticas(),
            'cias'         => $this->getCiasForSelect(),
            'filtros'      => [
                'cia_id'       => $ciaId,
                'comunas_id'   => $comunaCodigo,
                'tipo_usuario' => $tipoUsuario,
            ]
        ];

        return view('valores_comunas/index', $data);
    }

    /* =================== HELPERS PRIVADOS =================== */

    private function getCiasForSelect(): array
    {
        $cias = $this->ciaModel->select('cia_id, cia_nombre')
                              ->where('cia_habil', 1)
                              ->orderBy('cia_nombre', 'ASC')
                              ->findAll();

        $result = [];
        foreach ($cias as $cia) {
            $result[$cia['cia_id']] = $cia['cia_nombre'];
        }

        return $result;
    }

    private function getRegionesForSelect(): array
    {
        $db = \Config\Database::connect();
        
        $regiones = $db->table('regiones')
                      ->select('region_id, region_nombre')
                      ->orderBy('region_id', 'ASC')
                      ->get()
                      ->getResultArray();

        $result = [];
        foreach ($regiones as $region) {
            $result[$region['region_id']] = $region['region_id'] . ' - ' . $region['region_nombre'];
        }

        return $result;
    }

    /** Helper para provincias */
    private function getProvinciasByRegionHelper($regionId): array
    {
        if (!$regionId) return [];

        $db = \Config\Database::connect();
        return $db->table('provincias')
            ->select('provincias_id, provincias_nombre')
            ->where('regiones_id', (int)$regionId)
            ->orderBy('provincias_nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** Helper para comunas */
    private function getComunasByProvinciaHelper($provinciaId): array
    {
        if (!$provinciaId) return [];

        $db = \Config\Database::connect();
        return $db->table('comunas')
            ->select('comunas_id, comunas_nombre')
            ->where('provincias_id', (int)$provinciaId)
            ->orderBy('comunas_nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** Helper para tipos de vehículo */
    private function getTiposVehiculoForSelect(): array
    {
        $db = \Config\Database::connect();
        
        $tipos = $db->table('tipo_vehiculo')
                   ->select('tipo_vehiculo_id, tipo_vehiculo_nombre')
                   ->where('tipo_vehiculo_activo', 1)
                   ->orderBy('tipo_vehiculo_nombre', 'ASC')
                   ->get()
                   ->getResultArray();

        $result = [];
        foreach ($tipos as $tipo) {
            $result[$tipo['tipo_vehiculo_id']] = $tipo['tipo_vehiculo_nombre'];
        }

        return $result;
    }

    private function getTiposUsuarioForSelect(): array
    {
        $tipos = $this->valoresComunasModel->getTiposUsuario();
        
        // Agregar tipos predefinidos si no existen
        $tiposDefault = ['general', 'inspector', 'compania', 'supervisor', 'administrador'];
        $tipos = array_unique(array_merge($tipos, $tiposDefault));
        sort($tipos);

        return array_combine($tipos, array_map('ucfirst', $tipos));
    }
}