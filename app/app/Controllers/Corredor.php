<?php
namespace App\Controllers;

use App\Models\InspeccionesModel;

class Corredor extends BaseController 
{
    protected $inspeccionesModel;
    protected $db;

    public function __construct()
    {
        $this->inspeccionesModel = new InspeccionesModel();
        $this->db = \Config\Database::connect();
        
        // Verificar autenticación
        if (!session('logged_in')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Obtener inspecciones del usuario usando el método que existe en tu modelo
        $inspecciones = $this->inspeccionesModel->getInspeccionesWithDetails();
        
        // Filtrar solo las del usuario actual
        $inspecciones = array_filter($inspecciones, function($inspeccion) use ($userId) {
            return $inspeccion['user_id'] == $userId;
        });
        
        // Calcular estadísticas reales
        $stats = $this->calcularEstadisticas($userId);
        
        $data = [
            'title' => 'Dashboard Corredor',
            'corredor_id' => session('corredor_id'),
            'corredor_nombre' => session('user_name') ?? session('user_nombre') ?? 'Corredor',
            'inspecciones' => $inspecciones,
            'stats' => $stats,
            
            // Branding personalizado
            'brand_title' => session('brand_title') ?? 'Mi Dashboard',
            'brand_logo' => session('brand_logo'),
            'nav_bg' => session('nav_bg'),
        ];

        return view('pagina_corredor/index', $data);
    }

    private function calcularEstadisticas($userId)
    {
        // Obtener estadísticas usando los campos y estados correctos de tu BD
        $pendientes = $this->inspeccionesModel->where('user_id', $userId)
                           ->where('inspecciones_estado', 'pendiente')
                           ->countAllResults();
        
        $enProceso = $this->inspeccionesModel->where('user_id', $userId)
                          ->where('inspecciones_estado', 'en_proceso')
                          ->countAllResults();
        
        $completadas = $this->inspeccionesModel->where('user_id', $userId)
                            ->where('inspecciones_estado', 'completada')
                            ->countAllResults();
        
        $canceladas = $this->inspeccionesModel->where('user_id', $userId)
                            ->where('inspecciones_estado', 'cancelada')
                            ->countAllResults();
        
        // Calcular comisiones del mes actual (ejemplo: $50.000 por completada)
        $completadasMes = $this->inspeccionesModel->where('user_id', $userId)
                               ->where('inspecciones_estado', 'completada')
                               ->where('MONTH(inspecciones_created_at)', date('m'))
                               ->where('YEAR(inspecciones_created_at)', date('Y'))
                               ->countAllResults();
         
        
        return [
            'solicitudes_pendientes' => $pendientes,
            'en_proceso' => $enProceso,
            'completadas_mes' => $completadas,
            'canceladas' => $canceladas, 
            'total_inspecciones' => $pendientes + $enProceso + $completadas + $canceladas
        ];
    }

    public function show($id)
    {
        $userId = session('user_id');
        
        // Verificar que la inspección pertenece al usuario
        $inspeccion = $this->inspeccionesModel->where('inspecciones_id', $id)
                           ->where('user_id', $userId)
                           ->first();
        
        if (!$inspeccion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inspección no encontrada');
        }

        // Obtener detalles completos usando query builder
        $inspeccion = $this->inspeccionesModel->select('
            inspecciones.*,
            cias.cia_nombre,  
            users.user_nombre,
            users.user_email,
            comunas.comunas_nombre
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->where('inspecciones.inspecciones_id', $id)
        ->first();

        $data = [
            'title' => 'Detalle Inspección #' . $id,
            'inspeccion' => $inspeccion,
            'brand_title' => session('brand_title') ?? 'Detalle Inspección',
        ];

        return view('pagina_corredor/show', $data);
    }

    public function edit($id)
    {
        $userId = session('user_id');
        
        // Verificar que la inspección pertenece al usuario
        $inspeccion = $this->inspeccionesModel->where('inspecciones_id', $id)
                           ->where('user_id', $userId)
                           ->first();
        
        if (!$inspeccion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inspección no encontrada');
        }

        // Solo permitir editar si está en estado pendiente o en_proceso
        if (!in_array($inspeccion['inspecciones_estado'], ['pendiente', 'en_proceso'])) {
            return redirect()->back()->with('error', 'No se puede editar una inspección en estado: ' . $inspeccion['inspecciones_estado']);
        }

        // Obtener datos para formulario usando consultas directas
        $companias = $this->db->table('cias')->get()->getResultArray();
        $comunas = $this->db->table('comunas')->get()->getResultArray();

        $data = [
            'title' => 'Editar Inspección #' . $id,
            'inspeccion' => $inspeccion,
            'companias' => $companias,
            'comunas' => $comunas,
            'brand_title' => session('brand_title') ?? 'Editar Inspección',
        ];

        return view('pagina_corredor/edit', $data);
    }

    public function update($id)
    {
        $userId = session('user_id');
        
        // Verificar que la inspección pertenece al usuario
        $inspeccion = $this->inspeccionesModel->where('inspecciones_id', $id)
                        ->where('user_id', $userId)
                        ->first();
        
        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        $postData = $this->request->getPost();
        
        // Debug: ver qué datos llegan
        log_message('debug', 'Datos POST recibidos en update: ' . json_encode($postData));
        
        // Mapear campos del formulario a campos de la BD (igual que en store)
        $data = [
            'inspecciones_asegurado' => $postData['asegurado'] ?? '',
            'inspecciones_rut' => $postData['inspecciones_rut'] ?? '',
            'inspecciones_patente' => $postData['patente'] ?? '',
            'inspecciones_marca' => $postData['marca'] ?? '',
            'inspecciones_modelo' => $postData['modelo'] ?? '',
            'inspecciones_n_poliza' => $postData['n_poliza'] ?? '',
            'inspecciones_direccion' => $postData['inspecciones_direccion'] ?? '',
            'inspecciones_celular' => $postData['celular'] ?? '',
            'inspecciones_telefono' => $postData['telefono'] ?? null,
            'inspecciones_observaciones' => $postData['inspecciones_observaciones'] ?? null,
            'cia_id' => (int)($postData['cia_id'] ?? 0),
            'comunas_id' => (int)($postData['comunas_id'] ?? 0),
            // NO incluir user_id ni campos de fecha - no deben cambiar en update
        ];
        
        // Debug: ver datos mapeados
        log_message('debug', 'Datos mapeados para update: ' . json_encode($data));
        
        // Validación básica
        $errores = [];
        
        if (empty($data['inspecciones_asegurado'])) {
            $errores[] = 'El nombre del asegurado es obligatorio';
        }
        
        if (empty($data['inspecciones_rut'])) {
            $errores[] = 'El RUT es obligatorio';
        }
        
        if (empty($data['inspecciones_patente'])) {
            $errores[] = 'La patente es obligatoria';
        }
        
        if (empty($data['inspecciones_marca'])) {
            $errores[] = 'La marca es obligatoria';
        }
        
        if (empty($data['inspecciones_modelo'])) {
            $errores[] = 'El modelo es obligatorio';
        }
        
        if (empty($data['inspecciones_n_poliza'])) {
            $errores[] = 'El número de póliza es obligatorio';
        }
        
        if (empty($data['inspecciones_direccion'])) {
            $errores[] = 'La dirección es obligatoria';
        }
        
        if (empty($data['inspecciones_celular'])) {
            $errores[] = 'El celular es obligatorio';
        }
        
        if ($data['cia_id'] <= 0) {
            $errores[] = 'Debe seleccionar una compañía de seguros';
        }
        
        if ($data['comunas_id'] <= 0) {
            $errores[] = 'Debe seleccionar una comuna';
        }
        
        // Si hay errores, retornar
        if (!empty($errores)) {
            log_message('error', 'Errores de validación en update: ' . implode(', ', $errores));
            return redirect()->back()
                ->with('errors', $errores)
                ->withInput();
        }
        
        try {
            // Intentar actualizar usando el método correcto
            $result = $this->inspeccionesModel
                ->where('inspecciones_id', $id)
                ->where('user_id', $userId) // Seguridad adicional
                ->set($data)
                ->update();
            
            // Alternativa si el método anterior no funciona:
            // $result = $this->inspeccionesModel->update($id, $data);
            
            if ($result) {
                log_message('info', 'Inspección actualizada exitosamente: ID ' . $id);
                return redirect()->to(base_url('corredor/show/' . $id))
                    ->with('success', 'Inspección actualizada correctamente');
            } else {
                throw new \Exception('No se pudo actualizar en la base de datos');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar inspección: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar la inspección: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function delete($id)
    {
        $userId = session('user_id');
        
        // Verificar que la inspección pertenece al usuario
        $inspeccion = $this->inspeccionesModel->where('inspecciones_id', $id)
                           ->where('user_id', $userId)
                           ->first();
        
        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        // Solo permitir eliminar si está pendiente
        if ($inspeccion['inspecciones_estado'] !== 'pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden eliminar inspecciones pendientes');
        }

        if ($this->inspeccionesModel->delete($id)) {
            return redirect()->to(base_url('corredor'))->with('success', 'Inspección eliminada correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar la inspección');
        }
    }

    public function create()
    {
        // Obtener datos para formulario usando consultas directas
        $companias = $this->db->table('cias')->get()->getResultArray();
        $comunas = $this->db->table('comunas')->get()->getResultArray();

        $data = [
            'title' => 'Nueva Inspección',
            'companias' => $companias,
            'comunas' => $comunas,
            'brand_title' => session('brand_title') ?? 'Nueva Inspección',
        ];

        return view('pagina_corredor/create', $data);
    }

    public function store()
    {
        // Mostrar datos recibidos
        $postData = $this->request->getPost();
        
        // Mapear campos
        $data = [
            'inspecciones_asegurado' => $postData['asegurado'] ?? '',
            'inspecciones_rut' => $postData['inspecciones_rut'] ?? '',
            'inspecciones_patente' => $postData['patente'] ?? '',
            'inspecciones_marca' => $postData['marca'] ?? '',
            'inspecciones_modelo' => $postData['modelo'] ?? '',
            'inspecciones_n_poliza' => $postData['n_poliza'] ?? '',
            'inspecciones_direccion' => $postData['inspecciones_direccion'] ?? '',
            'inspecciones_celular' => $postData['celular'] ?? '',
            'inspecciones_telefono' => $postData['telefono'] ?? null,
            'cia_id' => (int)($postData['cia_id'] ?? 0),
            'comunas_id' => (int)($postData['comunas_id'] ?? 0),
            'user_id' => (int)session('user_id'),
            'inspecciones_estado' => 'pendiente'
            // NO incluir inspecciones_fecha_creacion - la tabla usa created_at automático
        ];
        
        // Validación rápida
        if (empty($data['inspecciones_asegurado']) || empty($data['inspecciones_rut'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Datos incompletos',
                'debug' => ['postData' => $postData, 'mappedData' => $data]
            ]);
        }
        
        try {
            // Usar Query Builder directo para ver el error exacto
            $db = \Config\Database::connect();
            $builder = $db->table('inspecciones');
            
            $result = $builder->insert($data);
            
            if ($result) {
                $insertId = $db->insertID();
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Inspección creada exitosamente',
                    'id' => $insertId
                ]);
            } else {
                // Obtener error específico
                $error = $db->error();
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Error de BD: ' . $error['message'],
                    'debug' => [
                        'error_code' => $error['code'],
                        'error_message' => $error['message'],
                        'last_query' => $db->getLastQuery()->getQuery(),
                        'data_sent' => $data
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Excepción: ' . $e->getMessage(),
                'debug' => [
                    'exception_trace' => $e->getTraceAsString(),
                    'data_sent' => $data
                ]
            ]);
        }
    }

    // Método para filtrar por AJAX
    public function filterByStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $estado = $this->request->getGet('estado');
        $userId = session('user_id');

        $query = $this->inspeccionesModel->where('user_id', $userId);
        
        if ($estado !== 'all') {
            $query->where('inspecciones_estado', $estado);
        }
        
        $inspecciones = $query->orderBy('inspecciones_created_at', 'DESC')->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $inspecciones
        ]);
    }

    // Método para obtener estadísticas por AJAX
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $userId = session('user_id');
        $stats = $this->calcularEstadisticas($userId);

        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats
        ]);
    }
}