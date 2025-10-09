<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InspeccionesModel;
use App\Models\BitacoraModel;
use App\Models\CiaModel;

class Inspecciones extends BaseController
{
    protected $inspeccionesModel;
    protected $bitacoraModel;
    protected $ciasModel;

    public function __construct()
    {
        $this->inspeccionesModel = new InspeccionesModel();
        $this->bitacoraModel = new BitacoraModel();
        $this->ciasModel = new CiaModel();
    }
 
   public function index()
    { 
        $inspecciones = $this->inspeccionesModel->select('
            inspecciones.*,
            cias.cia_nombre,
            users.user_nombre,
            comunas.comunas_nombre,
            estados.estado_nombre,
            estados.estado_color,
            ti.tipo_inspeccion_nombre,
            tc.tipo_carroceria_nombre
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left') // ← CAMBIO AQUÍ
        ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = inspecciones.tipo_inspeccion_id', 'left')
        ->join('tipo_carroceria tc', 'tc.tipo_carroceria_id = inspecciones.tipo_carroceria_id', 'left')
        ->orderBy('inspecciones.inspecciones_created_at', 'DESC')
        ->findAll();

        $data = [
            'title' => 'Inspecciones',
            'inspecciones' => $inspecciones
        ];

        return view('inspecciones/index', $data);
    }
    public function getCarroceriasByTipo($tipoInspeccionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Solicitud no permitida']);
        }

        $tipoCarroceriaModel = new \App\Models\TipoCarroceriaModel();
        $carrocerias = $tipoCarroceriaModel->getCarroceriasForSelect($tipoInspeccionId);
        
        return $this->response->setJSON([
            'success' => true,
            'carrocerias' => $carrocerias
        ]);
    }
 
    public function getTipoInspeccionInfo($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Solicitud no permitida']);
        }

        $tipoInspeccionModel = new \App\Models\TipoInspeccionModel();
        $tipo = $tipoInspeccionModel->find($id);
        
        if (!$tipo) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Tipo de inspección no encontrado'
            ]);
        } 
        $codigo = $tipo['tipo_inspeccion_codigo'] ?? 'LIVIANO';
        $info = $infoAdicional[$codigo] ?? $infoAdicional['LIVIANO'];

        return $this->response->setJSON([
            'success' => true,
            'tipo' => $tipo, 
        ]);
    }
 
    public function getTiposInspeccion()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Solicitud no permitida']);
        }

        $tipoInspeccionModel = new \App\Models\TipoInspeccionModel();
        $tipos = $tipoInspeccionModel->getTiposForSelect();
        
        return $this->response->setJSON([
            'success' => true,
            'tipos_inspeccion' => $tipos
        ]);
    }
    /**
     * Mostrar formulario para crear nueva inspección
     */
    public function create()
    {
          $cias = $this->ciasModel->where('cia_habil', 1)->findAll();
    
        // ✅ Agregar comunas
        $comunasModel = new \App\Models\ComunasModel(); // Crear este modelo
        $comunas = $comunasModel->orderBy('comunas_nombre', 'ASC')->findAll();

        $data = [
            'title' => 'Nueva Inspección',
            'cias' => $cias,
            'comunas' => $comunas, // ← Agregar
            'validation' => null
        ];

        return view('inspecciones/create', $data);
    }  

    /**
     * Procesar creación de nueva inspección
     */
    public function store()
    {
        $rules = [
            'asegurado' => 'required|min_length[3]|max_length[100]',
            'rut' => 'required|min_length[8]|max_length[12]',
            'patente' => 'required|min_length[6]|max_length[8]',
            'marca' => 'required|min_length[2]|max_length[50]',
            'modelo' => 'required|min_length[2]|max_length[50]',
            'n_poliza' => 'required|min_length[3]|max_length[20]',
            'direccion' => 'required|min_length[5]|max_length[200]',
            'comuna' => 'required|min_length[3]|max_length[50]',
            'celular' => 'required|min_length[8]|max_length[15]',
            'telefono' => 'permit_empty|min_length[8]|max_length[15]',
            'cia_id' => 'required|is_natural_no_zero',
            'comunas_id' => 'required|is_natural_no_zero', 

        ];

        if (!$this->validate($rules)) {
            $cias = $this->ciasModel->where('cia_habil', 1)->findAll();
            
            $data = [
                'title' => 'Nueva Inspección',
                'cias' => $cias,
                'validation' => $this->validator
            ];

            return view('inspecciones/create', $data);
        }

        // Preparar datos para insertar
        $data = [
            'asegurado' => $this->request->getPost('asegurado'),
            'rut' => $this->formatRut($this->request->getPost('rut')),
            'patente' => strtoupper($this->request->getPost('patente')),
            'marca' => $this->request->getPost('marca'),
            'modelo' => $this->request->getPost('modelo'),
            'n_poliza' => $this->request->getPost('n_poliza'),
            'direccion' => $this->request->getPost('direccion'),
            'comuna' => $this->request->getPost('comuna'),
            'celular' => $this->request->getPost('celular'),
            'telefono' => $this->request->getPost('telefono'),
            'cia_id' => $this->request->getPost('cia_id'),
            'user_id' => session('user_id'),
            'estado' => 'pendiente',
            'comunas_id' => $this->request->getPost('comunas_id'), // ← Cambio aquí

        ];

        // Insertar en base de datos
        $inspeccion_id = $this->inspeccionesModel->insert($data);
        
        if ($inspeccion_id) {
            // Crear comentario inicial en la bitácora
            $this->bitacoraModel->agregarComentario([
                'inspeccion_id' => $inspeccion_id,
                'user_id' => session('user_id'),
                'comentario' => 'Inspección creada. Estado inicial: Pendiente',
                'tipo_comentario' => 'estado_cambio',
                'estado_nuevo' => 'pendiente',
                'es_privado' => 0
            ]);

            session()->setFlashdata('success', 'Inspección creada exitosamente');
            return redirect()->to(base_url('inspecciones/show/' . $inspeccion_id));
        } else {
            session()->setFlashdata('error', 'Error al crear la inspección');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Mostrar detalles de una inspección con bitácora
     */
    public function show($id)
    {
        // Obtener inspección con estado de la tabla
        $inspeccion = $this->inspeccionesModel->select('
            inspecciones.*,
            cias.cia_nombre,
            users.user_nombre,
            comunas.comunas_nombre,
            estados.estado_nombre,
            estados.estado_color,
            ti.tipo_inspeccion_nombre,
            tc.tipo_carroceria_nombre
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left') // ← CAMBIO AQUÍ
        ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = inspecciones.tipo_inspeccion_id', 'left')
        ->join('tipo_carroceria tc', 'tc.tipo_carroceria_id = inspecciones.tipo_carroceria_id', 'left')
        ->where('inspecciones.inspecciones_id', $id)
        ->first();

        if (!$inspeccion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inspección no encontrada');
        }

        // Obtener bitácora
        $es_inspector = in_array(session('user_perfil_id'), [3, 7]);
        $bitacora = $this->bitacoraModel->getBitacoraByInspeccion($id, $es_inspector);
        
        // Estadísticas de la bitácora
        $stats_bitacora = $this->bitacoraModel->getEstadisticasComentarios($id);
        
        // Obtener todos los estados disponibles para cambios
        $estadoModel = new \App\Models\EstadoModel();
        $estados_disponibles = $estadoModel->getAllEstados();

        $data = [
            'title' => 'Inspección #' . $id,
            'inspeccion' => $inspeccion,
            'bitacora' => $bitacora,
            'stats_bitacora' => $stats_bitacora,
            'estados_disponibles' => $estados_disponibles, // ← NUEVO
            'puede_comentar' => true,
            'puede_ver_privados' => $es_inspector
        ];

        return view('inspecciones/show', $data);
    }

    /**
     * Agregar comentario a la bitácora (AJAX)
     */
    public function agregarComentario()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Solicitud inválida']);
        }

        $rules = [
            'inspeccion_id' => 'required|is_natural_no_zero',
            'comentario' => 'required|min_length[3]|max_length[2000]',
            'tipo_comentario' => 'required|in_list[general,observacion,seguimiento]',
            'es_privado' => 'in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Datos inválidos',
                'messages' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'inspeccion_id' => $this->request->getPost('inspeccion_id'),
            'user_id' => session('user_id'),
            'comentario' => $this->request->getPost('comentario'),
            'tipo_comentario' => $this->request->getPost('tipo_comentario'),
            'es_privado' => (int)$this->request->getPost('es_privado', 0)
        ];

        $comentario_id = $this->bitacoraModel->agregarComentario($data);

        if ($comentario_id) {
            // Obtener el comentario recién creado con datos del usuario
            $comentario = $this->bitacoraModel
                ->select('inspeccion_bitacora.*, users.user_nombre, perfiles.perfil_nombre')
                ->join('users', 'users.user_id = inspeccion_bitacora.user_id', 'left')
                ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
                ->find($comentario_id);

            return $this->response->setJSON([
                'success' => true,
                'comentario' => $comentario,
                'message' => 'Comentario agregado exitosamente'
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Error al agregar el comentario'
            ]);
        }
    }
 
    public function cambiarEstado()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Solicitud inválida']);
        }

        $rules = [
            'inspeccion_id' => 'required|is_natural_no_zero',
            'nuevo_estado_id' => 'required|is_natural_no_zero', // ← CAMBIO: ahora es estado_id
            'comentario' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Datos inválidos',
                'messages' => $this->validator->getErrors()
            ]);
        }

        $inspeccion_id = $this->request->getPost('inspeccion_id');
        $nuevo_estado_id = $this->request->getPost('nuevo_estado_id'); // ← CAMBIO
        $comentario = $this->request->getPost('comentario');

        // Obtener estado actual
        $inspeccion = $this->inspeccionesModel->find($inspeccion_id);
        if (!$inspeccion) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Inspección no encontrada']);
        }

        $estado_anterior_id = $inspeccion['estado_id']; // ← CAMBIO

        // Obtener nombres de estados para el comentario
        $estadoModel = new \App\Models\EstadoModel();
        $estado_anterior = $estadoModel->find($estado_anterior_id);
        $estado_nuevo = $estadoModel->find($nuevo_estado_id);

        // Actualizar estado
        $this->inspeccionesModel->update($inspeccion_id, ['estado_id' => $nuevo_estado_id]); // ← CAMBIO

        // Registrar en bitácora
        $comentarioTexto = "Estado cambiado de '{$estado_anterior['estado_nombre']}' a '{$estado_nuevo['estado_nombre']}'";
        if ($comentario) {
            $comentarioTexto .= ". Observación: " . $comentario;
        }
        
        $this->bitacoraModel->insert([
            'inspecciones_id' => $inspeccion_id,
            'user_id' => session('user_id'),
            'bitacora_comentario' => $comentarioTexto,
            'bitacora_tipo_comentario' => 'estado_cambio',
            'bitacora_estado_anterior_id' => $estado_anterior_id, // ← NUEVO
            'bitacora_estado_nuevo_id' => $nuevo_estado_id, // ← NUEVO
            'bitacora_es_privado' => 0
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'nuevo_estado' => $estado_nuevo['estado_nombre'],
            'nuevo_estado_color' => $estado_nuevo['estado_color']
        ]);
    }
    public function eliminarComentario($bitacora_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Solicitud inválida']);
        }

        $es_admin = in_array(session('user_perfil_id'), [3, 7]);
        
        if ($this->bitacoraModel->eliminarComentario($bitacora_id, session('user_id'), $es_admin)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Comentario eliminado exitosamente'
            ]);
        } else {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'No tienes permisos para eliminar este comentario'
            ]);
        }
    }

    /**
     * Formatear RUT chileno
     */
    private function formatRut($rut)
    {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (strlen($rut) > 1) {
            $rut = substr($rut, 0, -1) . '-' . substr($rut, -1);
            $rut = number_format(substr($rut, 0, -2), 0, '', '.') . substr($rut, -2);
        }
        
        return $rut;
    }
}