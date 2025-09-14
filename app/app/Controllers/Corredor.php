<?php
namespace App\Controllers;

use App\Models\InspeccionesModel;
use App\Models\EstadoModel;


class Corredor extends BaseController 
{
    protected $inspeccionesModel;
    protected $estadoModel; // ← NUEVO
    protected $db;


    public function __construct()
    {
        $this->inspeccionesModel = new InspeccionesModel();
        $this->estadoModel = new EstadoModel(); // ← NUEVO
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
        
        // ← NUEVO: Obtener estados con colores
        $estados = $this->estadoModel->getAllEstados();
        $estadosMap = [];
        foreach ($estados as $estado) {
            $estadosMap[$estado['estado_id']] = [
                'nombre' => $estado['estado_nombre'],
                'color' => $estado['estado_color'] ?? '#6c757d'
            ];
        }
        
        $data = [
            'title' => 'Dashboard Corredor',
            'corredor_id' => session('corredor_id'),
            'corredor_nombre' => session('user_name') ?? session('user_nombre') ?? 'Corredor',
            'inspecciones' => $inspecciones,
            'stats' => $stats,
            'estados' => $estadosMap, // ← NUEVO
            
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
    private function getTextColorForBackground($hexColor)
    {
        // Remover # si existe
        $hex = ltrim($hexColor, '#');
        
        // Convertir a RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Calcular luminancia
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#ffffff';
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
            comunas.comunas_nombre,
            estados.estado_nombre,
            estados.estado_color
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left') // ← NUEVO
        ->where('inspecciones.inspecciones_id', $id)
        ->first();

        // ← NUEVO: Obtener todos los estados para el flujo
        $estados = $this->estadoModel->getEstadosPorFlujo();

        $data = [
            'title' => 'Detalle Inspección #' . $id,
            'inspeccion' => $inspeccion,
            'estados' => $estados, // ← NUEVO
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

        // 1) Verificar pertenencia
        $inspeccion = $this->inspeccionesModel
            ->where('inspecciones_id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        $post = $this->request->getPost() ?? [];
        log_message('debug', 'POST update inspección: ' . json_encode($post));

        // 2) Normalizaciones ligeras
        $rut      = strtoupper(trim($post['inspecciones_rut'] ?? ''));
        $patente  = strtoupper(trim($post['patente'] ?? ''));

        // 3) Mapeo datos
        $data = [
            'inspecciones_asegurado'     => trim($post['asegurado'] ?? ''),
            'inspecciones_rut'           => $rut,
            'inspecciones_patente'       => $patente,
            'inspecciones_marca'         => trim($post['marca'] ?? ''),
            'inspecciones_modelo'        => trim($post['modelo'] ?? ''),
            'inspecciones_n_poliza'      => trim($post['n_poliza'] ?? ''),
            'inspecciones_direccion'     => trim($post['inspecciones_direccion'] ?? ''),
            'inspecciones_celular'       => trim($post['celular'] ?? ''),
            'inspecciones_telefono'      => trim($post['telefono'] ?? '') ?: null,
            'inspecciones_observaciones' => trim($post['inspecciones_observaciones'] ?? '') ?: null,
            'cia_id'                     => (int)($post['cia_id'] ?? 0),
            'comunas_id'                 => (int)($post['comunas_id'] ?? 0),
        ];

        log_message('debug', 'Datos mapeados update: ' . json_encode($data));

        // 4) Validación básica
        $errores = [];
        if ($data['inspecciones_asegurado'] === '') $errores[] = 'El nombre del asegurado es obligatorio';
        if ($data['inspecciones_rut'] === '')       $errores[] = 'El RUT es obligatorio';
        if ($data['inspecciones_patente'] === '')   $errores[] = 'La patente es obligatoria';
        if ($data['inspecciones_marca'] === '')     $errores[] = 'La marca es obligatoria';
        if ($data['inspecciones_modelo'] === '')    $errores[] = 'El modelo es obligatorio';
        if ($data['inspecciones_n_poliza'] === '')  $errores[] = 'El número de póliza es obligatorio';
        if ($data['inspecciones_direccion'] === '') $errores[] = 'La dirección es obligatoria';
        if ($data['inspecciones_celular'] === '')   $errores[] = 'El celular es obligatorio';
        if ($data['cia_id'] <= 0)                   $errores[] = 'Debe seleccionar una compañía de seguros';
        if ($data['comunas_id'] <= 0)               $errores[] = 'Debe seleccionar una comuna';

        if (!empty($errores)) {
            log_message('error', 'Validación update: ' . implode(' | ', $errores));
            return redirect()->back()->with('errors', $errores)->withInput();
        }

        // 5) Transacción + update directo por PK
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // (Opcional) seguridad extra: revalidar pertenencia en el WHERE
            $updated = $this->inspeccionesModel
                ->where('inspecciones_id', $id)
                ->where('user_id', $userId)
                ->set($data)
                ->update();

            // Alternativa simple (requiere $primaryKey correcto):
            // $updated = $this->inspeccionesModel->update($id, $data);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transacción fallida');
            }

            // Informativo: filas afectadas
            $affected = $db->affectedRows();
            log_message('info', "Update inspección ID {$id}, filas afectadas: {$affected}");

            $msg = $affected > 0
                ? 'Inspección actualizada correctamente'
                : 'Sin cambios (los datos estaban iguales)';

            return redirect()->to(base_url('corredor/show/' . $id))->with('success', $msg);

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Error update inspección: ' . $e->getMessage());
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