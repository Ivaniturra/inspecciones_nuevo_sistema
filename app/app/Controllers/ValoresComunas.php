<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ValoresComunasModel;
use App\Models\CiaModel;
use App\Models\ComunaModel;
use App\Models\RegionModel;

class ValoresComunas extends BaseController
{
    protected $valoresComunasModel;
    protected $ciaModel;
    protected $comunaModel;
    protected $regionModel;

    public function __construct()
    {
        $this->valoresComunasModel = new ValoresComunasModel();
        $this->ciaModel = new CiaModel();
        // Nota: Necesitarás crear ComunaModel y RegionModel siguiendo el mismo patrón
        helper(['url', 'text', 'form']);
    }

    public function index()
    {
        $data = [
            'title'        => 'Gestión de Valores por Comuna',
            'valores'      => $this->valoresComunasModel->getValoresWithDetails(),
            'estadisticas' => $this->valoresComunasModel->getEstadisticas(),
            'filtros'      => [], // Variable para los filtros aplicados
        ];

        return view('valores_comunas/index', $data);
    }

    /** Formulario de creación */
    public function create()
    {
        $data = [
            'title'      => 'Nuevo Valor por Comuna',
            'validation' => \Config\Services::validation(),
            'cias'       => $this->getCiasForSelect(),
            'regiones'   => $this->getRegionesForSelect(),
            'tipos_usuario' => $this->getTiposUsuarioForSelect(),
        ];

        return view('valores_comunas/create', $data);
    }

    /** Guardar nuevo valor */
    public function store()
    {
        $rules = [
            'comunas_id'         => 'required',
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
        'comunas_id'          => (int)$this->request->getPost('comunas_id'),
        'cia_id'              => (int)$this->request->getPost('cia_id'),
        'tipo_usuario'        => (string)$this->request->getPost('tipo_usuario'),
        'tipo_vehiculo_id'    => (int)$this->request->getPost('tipo_vehiculo_id'),
        'valor'               => (float)$this->request->getPost('valor'),
        'unidad_medida'       => (string)$this->request->getPost('unidad_medida'),
        'moneda'              => (string)($this->request->getPost('moneda') ?: $this->request->getPost('unidad_medida')),
        'descripcion'         => (string)$this->request->getPost('descripcion'),
        'fecha_vigencia_desde'=> (string)$this->request->getPost('fecha_vigencia_desde'),
        'fecha_vigencia_hasta'=> $this->request->getPost('fecha_vigencia_hasta') ?: null,
        'activo'              => 1,
        ];

        if ($this->valoresComunasModel->save($data)) {
            return redirect()->to('/valores-comunas')->with('success', 'Valor creado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear el valor');
    }

    /** Ver detalle */
    public function show($id)
    {
        $valor = $this->valoresComunasModel->select('valores_comunas.*, comunas.comunas_nombre, regiones.region_nombre, cias.cia_nombre')
                                          ->join('comunas', 'comunas.comunas_id = valores_comunas.comunas_id', 'left')
                                          ->join('regiones', 'regiones.region_id = comunas.region_id', 'left')
                                          ->join('cias', 'cias.cia_id = valores_comunas.cia_id', 'left')
                                          ->find($id);

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
        $db = \Config\Database::connect();

        // Trae el valor + trilogía región/provincia/comuna (LEFT JOIN para no perder filas)
     $valor = $db->table('valores_comunas vc')
    ->select('
        vc.*,
        c.comunas_nombre,
        p.provincias_id,
        p.provincias_nombre,
        r.region_id,
        r.region_nombre
    ')
    ->join('comunas c',    'c.comunas_id    = vc.comunas_id',      'left')
    ->join('provincias p', 'p.provincias_id = c.provincias_id',    'left')
    ->join('regiones r',   'r.region_id     = p.regiones_id',      'left')
    ->where('vc.valores_id', $id)   // PK real de valores_comunas
    ->get()
    ->getRowArray(); 
        if (!$valor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Valor no encontrado');
        }

        // Listado completo de regiones (para el select)
        $regiones = $db->table('regiones')
            ->select('region_id, region_nombre')
            ->orderBy('region_id', 'ASC')
            ->get()->getResultArray();

        // Precarga dependientes SOLO si tenemos IDs
        $provincias = [];
        if (!empty($valor['region_id'])) {                // ¡OJO!: en regiones la PK es region_id
            $provincias = $db->table('provincias')
                ->select('provincias_id, provincias_nombre')
                ->where('regiones_id', $valor['region_id']) // en provincias la FK se llama regiones_id
                ->orderBy('provincias_nombre', 'ASC')
                ->get()->getResultArray();
        }

        $comunas = [];
        if (!empty($valor['provincias_id'])) {
            $comunas = $db->table('comunas')
                ->select('comunas_id, comunas_nombre')
                ->where('provincias_id', $valor['provincias_id'])
                ->orderBy('comunas_nombre', 'ASC')
                ->get()->getResultArray();
        }

        return view('valores_comunas/edit', [
            'title'      => 'Editar Valor por Comuna',
            'valor'      => $valor,
            'regiones'   => $regiones,
            'provincias' => $provincias,
            'comunas'    => $comunas,
            'validation' => \Config\Services::validation(),
            // si también pasas $cias desde aquí, mejor:
            // 'cias'    => $this->getCiasForSelect(),
        ]);
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
            'comunas_id'         => 'required',
            'cia_id'               => 'required|integer',
            'tipo_usuario'         => 'required',
            'valor'                => 'required|decimal',
            'fecha_vigencia_desde' => 'required|valid_date',
            'fecha_vigencia_hasta' => 'permit_empty|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verificar si ya existe otro valor activo para estos parámetros (excluyendo el actual)
        if ($this->valoresComunasModel->existeValor(
            $this->request->getPost('comunas_id'),
            $this->request->getPost('cia_id'),
            $this->request->getPost('tipo_usuario'),
            $id
        )) {
            return redirect()->back()->withInput()->with('error', 'Ya existe otro valor activo para esta combinación');
        }

        $data = [
            'comunas_id'         => $this->request->getPost('comunas_id'),
            'cia_id'               => (int) $this->request->getPost('cia_id'),
            'tipo_usuario'         => $this->request->getPost('tipo_usuario'),
            'valor'                => (float) $this->request->getPost('valor'),
            'moneda'               => $this->request->getPost('moneda') ?: 'CLP',
            'descripcion'          => $this->request->getPost('descripcion'),
            'fecha_vigencia_desde' => $this->request->getPost('fecha_vigencia_desde'),
            'fecha_vigencia_hasta' => $this->request->getPost('fecha_vigencia_hasta') ?: null,
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

    /** Obtener comunas por región (AJAX) */
    public function getComunasByRegion($regionId)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/valores-comunas');
        }

        $db = \Config\Database::connect();
        
        // Consulta directa a la tabla comunas por region_id
        $comunas = $db->table('comunas')
                     ->select('comunas_id, comunas_nombre')
                     ->where('region_id', $regionId)
                     ->orderBy('comunas_nombre', 'ASC')
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

        $valores = $this->valoresComunasModel->select('valores_comunas.*, comunas.comunas_nombre, regiones.region_nombre, cias.cia_nombre')
                                            ->join('comunas', 'comunas.comunas_id = valores_comunas.comunas_id', 'left')
                                            ->join('regiones', 'regiones.region_id = comunas.region_id', 'left')
                                            ->join('cias', 'cias.cia_id = valores_comunas.cia_id', 'left');

        if ($ciaId) {
            $valores->where('valores_comunas.cia_id', $ciaId);
        }

        if ($comunaCodigo) {
            $valores->where('valores_comunas.comunas_id', $comunaCodigo);
        }

        if ($tipoUsuario) {
            $valores->where('valores_comunas.tipo_usuario', $tipoUsuario);
        }

        $data = [
            'title'        => 'Valores Filtrados',
            'valores'      => $valores->where('valores_comunas.activo', 1)->findAll(),
            'estadisticas' => $this->valoresComunasModel->getEstadisticas(),
            'filtros'      => [
                'cia_id'        => $ciaId,
                'comunas_id' => $comunaCodigo,
                'tipo_usuario'  => $tipoUsuario,
            ]
        ];

        return view('valores_comunas/index', $data);
    }

    /* =================== Helpers privados =================== */

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
    public function getProvinciasByRegion($regionId)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/valores-comunas');
        }

        $db = \Config\Database::connect();
        $rows = $db->table('provincias')
            ->select('provincias_id, provincias_nombre')
            ->where('regiones_id', (int)$regionId)
            ->orderBy('provincias_nombre', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($rows);
    }

    public function getComunasByProvincia($provinciaId)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/valores-comunas');
        }

        $db = \Config\Database::connect();
        $rows = $db->table('comunas')
            ->select('comunas_id, comunas_nombre')
            ->where('provincias_id', (int)$provinciaId)
            ->orderBy('comunas_nombre', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($rows);
    }
    private function getRegionesForSelect(): array
    {
        $db = \Config\Database::connect();
        
        // Consulta directa a la tabla regiones
        $regiones = $db->table('regiones')
                      ->select('region_id, region_nombre, region_id')
                      ->orderBy('region_id', 'ASC')
                      ->get()
                      ->getResultArray();

        $result = [];
        foreach ($regiones as $region) {
            $result[$region['region_id']] = $region['region_id'] . ' - ' . $region['region_nombre'];
        }

        return $result;
    }

    private function getTiposUsuarioForSelect(): array
    {
        $tipos = $this->valoresComunasModel->getTiposUsuario();
        
        // Agregar tipos predefinidos si no existen
        $tiposDefault = ['general', 'inspector', 'supervisor', 'administrador'];
        $tipos = array_unique(array_merge($tipos, $tiposDefault));
        sort($tipos);

        return array_combine($tipos, array_map('ucfirst', $tipos));
    }
}