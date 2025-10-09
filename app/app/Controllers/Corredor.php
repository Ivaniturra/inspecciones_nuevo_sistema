<?php
namespace App\Controllers;

use App\Models\InspeccionesModel;
use App\Models\EstadoModel;
use App\Models\TipoInspeccionModel;
use App\Models\TipoCarroceriaModel;
use App\Models\CiaModel;

class Corredor extends BaseController 
{
    protected $inspeccionesModel;
    protected $estadoModel;
    protected $tipoInspeccionModel;
    protected $tipoCarroceriaModel;
    protected $CiaModel;
    protected $db;

    public function __construct()
    {
        $this->inspeccionesModel = new InspeccionesModel();
        $this->estadoModel = new EstadoModel();
        $this->tipoInspeccionModel = new TipoInspeccionModel();
        $this->tipoCarroceriaModel = new TipoCarroceriaModel();
        $this->CiaModel = new CiaModel();
        $this->db = \Config\Database::connect();
        
        // Verificar autenticación
        if (!session('logged_in')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Obtener parámetros de búsqueda
        $search = $this->request->getGet('search') ?? '';
        $ciaId = $this->request->getGet('cia_id') ?? '';
        
        // Obtener inspecciones del usuario con JOIN a las tablas relacionadas
        $query = $this->inspeccionesModel->select('
            inspecciones.*,
            cias.cia_nombre,
            cias.cia_display_name,
            users.user_nombre,
            comunas.comunas_nombre,
            estados.estado_nombre,
            estados.estado_color,
            ti.tipo_inspeccion_nombre,
            tc.tipo_corroceria_nombre')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left') 
        ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = inspecciones.tipo_inspeccion_id', 'left')
        ->join('tipo_carroceria tc', 'tc.tipo_carroceria_id = inspecciones.tipo_carroceria_id', 'left')
        ->where('inspecciones.user_id', $userId);
        
        // Aplicar filtros de búsqueda
        if (!empty($search)) {
            $query->groupStart()
                  ->like('inspecciones.inspecciones_asegurado', $search)
                  ->orLike('inspecciones.inspecciones_patente', $search)
                  ->orLike('inspecciones.inspecciones_rut', $search)
                  ->orLike('inspecciones.inspecciones_email', $search)
                  ->orLike('inspecciones.inspecciones_n_poliza', $search)
                  ->groupEnd();
        }
        
        if (!empty($ciaId)) {
            $query->where('inspecciones.cia_id', $ciaId);
        }
        
        $inspecciones = $query->orderBy('inspecciones.inspecciones_created_at', 'DESC')
                             ->findAll();
        
        // Calcular estadísticas reales
        $stats = $this->calcularEstadisticas($userId);
        
        // Obtener estados con colores
        $estados = $this->estadoModel->getAllEstados();
        $estadosMap = [];
        foreach ($estados as $estado) {
            $estadosMap[$estado['estado_id']] = [
                'nombre' => $estado['estado_nombre'],
                'color' => $estado['estado_color'] ?? '#6c757d'
            ];
        }
        
        // Obtener compañías del corredor
        $cias = $this->getCiasDelUsuarioCorredor($userId);
        
        $data = [
            'title' => 'Dashboard Corredor',
            'corredor_id' => session('corredor_id'),
            'corredor_nombre' => session('user_name') ?? session('user_nombre') ?? 'Corredor',
            'inspecciones' => $inspecciones,
            'stats' => $stats,
            'estados' => $estadosMap,
            'cias' => $cias,
            'search' => $search,
            'ciaId' => $ciaId,
            'brand_title' => session('brand_title') ?? 'Mi Dashboard',
            'brand_logo' => session('brand_logo'),
            'nav_bg' => session('nav_bg'),
        ];

        return view('pagina_corredor/index', $data);
    }

    /**
     * Obtiene las compañías asociadas al usuario corredor
     */
    private function getCiasDelUsuarioCorredor($userId): array
    {
        // Opción 1: Si el usuario tiene un corredor_id asociado
        $corredorId = session('corredor_id');
        
        if (!empty($corredorId)) {
            // Obtener compañías del corredor específico
            $ciasDelCorredor = $this->db->table('corredor_cias cc')
                ->select('c.cia_id, c.cia_nombre, c.cia_display_name, c.cia_logo, c.cia_habil')
                ->join('cias c', 'c.cia_id = cc.cia_id', 'inner')
                ->where('cc.corredor_id', $corredorId)
                ->where('cc.corredor_cia_activo', 1)
                ->where('c.cia_habil', 1)
                ->orderBy('c.cia_nombre', 'ASC')
                ->get()
                ->getResultArray();
            
            if (!empty($ciasDelCorredor)) {
                return $ciasDelCorredor;
            }
        }
        
        // Opción 2: Si no hay corredor_id, obtener compañías de las inspecciones del usuario
        $ciasFromInspecciones = $this->db->table('inspecciones i')
            ->select('DISTINCT c.cia_id, c.cia_nombre, c.cia_display_name, c.cia_logo')
            ->join('cias c', 'c.cia_id = i.cia_id', 'inner')
            ->where('i.user_id', $userId)
            ->where('c.cia_habil', 1)
            ->orderBy('c.cia_nombre', 'ASC')
            ->get()
            ->getResultArray();
        
        if (!empty($ciasFromInspecciones)) {
            return $ciasFromInspecciones;
        }
        
        // Opción 3: Fallback - todas las compañías activas
        return $this->CiaModel->getActiveCias();
    }

    /**
     * Calcula estadísticas de inspecciones por estado
     */
    private function calcularEstadisticas($userId): array
    {
        // Usar estado_id en lugar de enum
        $pendientes = $this->inspeccionesModel->where('user_id', $userId)
                        ->where('estado_id', 1) // Solicitud
                        ->countAllResults();
        
        $enProceso = $this->inspeccionesModel->where('user_id', $userId)
                        ->whereIn('estado_id', [2, 3, 4]) // Coordinador, Control Calidad, En Inspector
                        ->countAllResults();
        
        $completadas = $this->inspeccionesModel->where('user_id', $userId)
                            ->where('estado_id', 5) // Terminada
                            ->countAllResults();
        
        $aceptadas = $this->inspeccionesModel->where('user_id', $userId)
                            ->where('estado_id', 6) // Aceptada
                            ->countAllResults();
        
        $rechazadas = $this->inspeccionesModel->where('user_id', $userId)
                            ->where('estado_id', 7) // Rechazada
                            ->countAllResults();
        
        return [
            'solicitudes_pendientes' => $pendientes,
            'en_proceso' => $enProceso,
            'completadas_mes' => $completadas,
            'aceptadas' => $aceptadas,
            'rechazadas' => $rechazadas,
            'total_inspecciones' => $pendientes + $enProceso + $completadas + $aceptadas + $rechazadas
        ];
    }

    public function create()
    {
        $userId = session('user_id');
        
        // Obtener datos para formulario
        $companias = $this->getCiasDelUsuarioCorredor($userId);
        $comunas = $this->db->table('comunas')
                            ->orderBy('comunas_nombre', 'ASC')
                            ->get()
                            ->getResultArray();
        
        // Usar el modelo de tipos de inspección
        $tiposInspeccion = $this->tipoInspeccionModel->getTiposForSelect();

        $data = [
            'title' => 'Nueva Inspección',
            'companias' => $companias,
            'comunas' => $comunas,
            'tipos_inspeccion' => $tiposInspeccion,
            'brand_title' => session('brand_title') ?? 'Nueva Inspección',
        ];

        return view('pagina_corredor/create', $data);
    }

    public function store()
    {
        $postData = $this->request->getPost();
        
        // Normalizar teléfono a formato WhatsApp
        $telefono = $this->normalizarTelefono($postData['inspecciones_celular'] ?? '');
        
        // Mapear campos según tu tabla real
        $data = [
            'inspecciones_asegurado' => trim($postData['inspecciones_asegurado'] ?? ''),
            'inspecciones_rut' => strtoupper(trim($postData['inspecciones_rut'] ?? '')),
            'inspecciones_email' => strtolower(trim($postData['inspecciones_email'] ?? '')),
            'inspecciones_patente' => strtoupper(trim($postData['inspecciones_patente'] ?? '')),
            'inspecciones_marca' => trim($postData['inspecciones_marca'] ?? ''),
            'inspecciones_modelo' => trim($postData['inspecciones_modelo'] ?? ''),
            'inspecciones_n_poliza' => trim($postData['inspecciones_n_poliza'] ?? ''),
            'inspecciones_direccion' => trim($postData['inspecciones_direccion'] ?? ''),
            'inspecciones_celular' => $telefono,
            'inspecciones_telefono' => $this->normalizarTelefono($postData['inspecciones_telefono'] ?? '') ?: null,
            'inspecciones_observaciones' => trim($postData['inspecciones_observaciones'] ?? '') ?: null,
            'cia_id' => (int)($postData['cia_id'] ?? 0),
            'comunas_id' => (int)($postData['comunas_id'] ?? 0),
            'tipo_inspeccion_id' => (int)($postData['tipo_inspeccion_id'] ?? 0),
            'tipo_carroceria_id' => (int)($postData['tipo_carroceria_id'] ?? 0),
            'user_id' => (int)session('user_id'),
            'estado_id' => 1, // Estado inicial: Solicitud
        ];
        
        // Validación
        $errores = $this->validarDatosInspeccion($data);
        
        if (!empty($errores)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Datos incompletos o inválidos',
                'errors' => $errores
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('inspecciones');
            
            $result = $builder->insert($data);
            
            if ($result) {
                $insertId = $db->insertID();
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Inspección creada exitosamente',
                    'id' => $insertId,
                    'whatsapp_url' => $this->generarWhatsAppURL($data)
                ]);
            } else {
                $error = $db->error();
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Error de BD: ' . $error['message']
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error en Corredor::store - ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Excepción: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $userId = session('user_id');
        
        // Obtener inspección con todos los datos relacionados
        $inspeccion = $this->inspeccionesModel->select('
            inspecciones.*,
            cias.cia_nombre,
            cias.cia_display_name,
            users.user_nombre,
            users.user_email,
            comunas.comunas_nombre,
            estados.estado_nombre,
            estados.estado_color,
            ti.tipo_inspeccion_nombre,
            ti.tipo_inspeccion_codigo,
            tc.tipo_carroceria_nombre
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left')
        ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = inspecciones.tipo_inspeccion_id', 'left')
        ->join('tipo_carroceria tc', 'tc.tipo_carroceria_id = inspecciones.tipo_carroceria_id', 'left')
        ->where('inspecciones.inspecciones_id', $id)
        ->where('inspecciones.user_id', $userId)
        ->first();

        if (!$inspeccion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inspección no encontrada');
        }

        // Obtener todos los estados para el flujo
        $estados = $this->estadoModel->getEstadosPorFlujo();

        $data = [
            'title' => 'Detalle Inspección #' . $id,
            'inspeccion' => $inspeccion,
            'estados' => $estados,
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

        // Obtener datos para formulario
        $companias = $this->getCiasDelUsuarioCorredor($userId);
        $comunas = $this->db->table('comunas')
                            ->orderBy('comunas_nombre', 'ASC')
                            ->get()
                            ->getResultArray();
        $tiposInspeccion = $this->tipoInspeccionModel->getTiposForSelect();
        
        // Obtener carrocerías del tipo actual
        $carrocerias = [];
        if (!empty($inspeccion['tipo_inspeccion_id'])) {
            $carrocerias = $this->tipoCarroceriaModel->getCarroceriasForSelect($inspeccion['tipo_inspeccion_id']);
        }

        $data = [
            'title' => 'Editar Inspección #' . $id,
            'inspeccion' => $inspeccion,
            'companias' => $companias,
            'comunas' => $comunas,
            'tipos_inspeccion' => $tiposInspeccion,
            'carrocerias' => $carrocerias,
            'brand_title' => session('brand_title') ?? 'Editar Inspección',
        ];

        return view('pagina_corredor/edit', $data);
    }

    public function update($id)
    {
        $userId = session('user_id');

        // Verificar pertenencia
        $inspeccion = $this->inspeccionesModel
            ->where('inspecciones_id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        $post = $this->request->getPost() ?? [];

        // Normalizaciones
        $data = [
            'inspecciones_asegurado' => trim($post['inspecciones_asegurado'] ?? ''),
            'inspecciones_rut' => strtoupper(trim($post['inspecciones_rut'] ?? '')),
            'inspecciones_email' => strtolower(trim($post['inspecciones_email'] ?? '')) ?: null,
            'inspecciones_patente' => strtoupper(trim($post['inspecciones_patente'] ?? '')),
            'inspecciones_marca' => trim($post['inspecciones_marca'] ?? ''),
            'inspecciones_modelo' => trim($post['inspecciones_modelo'] ?? ''),
            'inspecciones_n_poliza' => trim($post['inspecciones_n_poliza'] ?? ''),
            'inspecciones_direccion' => trim($post['inspecciones_direccion'] ?? ''),
            'inspecciones_celular' => $this->normalizarTelefono($post['inspecciones_celular'] ?? ''),
            'inspecciones_telefono' => $this->normalizarTelefono($post['inspecciones_telefono'] ?? '') ?: null,
            'inspecciones_observaciones' => trim($post['inspecciones_observaciones'] ?? '') ?: null,
            'cia_id' => (int)($post['cia_id'] ?? 0),
            'comunas_id' => (int)($post['comunas_id'] ?? 0),
            'tipo_inspeccion_id' => (int)($post['tipo_inspeccion_id'] ?? 0),
            'tipo_carroceria_id' => (int)($post['tipo_carroceria_id'] ?? 0),
        ];

        // Validación
        $errores = $this->validarDatosInspeccion($data);

        if (!empty($errores)) {
            return redirect()->back()->with('errors', $errores)->withInput();
        }

        // Update
        try {
            $this->inspeccionesModel
                ->where('inspecciones_id', $id)
                ->where('user_id', $userId)
                ->set($data)
                ->update();

            return redirect()->to(base_url('corredor/show/' . $id))
                ->with('success', 'Inspección actualizada correctamente');

        } catch (\Throwable $e) {
            log_message('error', 'Error en Corredor::update - ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function delete($id)
    {
        $userId = session('user_id');
        
        $inspeccion = $this->inspeccionesModel->where('inspecciones_id', $id)
                           ->where('user_id', $userId)
                           ->first();
        
        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        if ($this->inspeccionesModel->delete($id)) {
            return redirect()->to(base_url('corredor'))
                ->with('success', 'Inspección eliminada correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar la inspección');
        }
    }
 
    public function filterByStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $estado_id = $this->request->getGet('estado_id');
        $userId = session('user_id');

        $query = $this->inspeccionesModel
            ->select('
                inspecciones.*,
                cias.cia_nombre,
                cias.cia_display_name,
                estados.estado_nombre,
                estados.estado_color,
                ti.tipo_inspeccion_nombre,
                tc.tipo_carroceria_nombre
            ')
            ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
            ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left')
            ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = inspecciones.tipo_inspeccion_id', 'left')
            ->join('tipo_carroceria tc', 'tc.tipo_carroceria_id = inspecciones.tipo_carroceria_id', 'left')
            ->where('inspecciones.user_id', $userId);
        
        if ($estado_id !== 'all' && !empty($estado_id)) {
            $query->where('inspecciones.estado_id', $estado_id);
        }
        
        $inspecciones = $query->orderBy('inspecciones.inspecciones_created_at', 'DESC')
                             ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $inspecciones
        ]);
    }

    /* ===== MÉTODOS PRIVADOS ===== */ 
    
    /**
     * Normaliza números de teléfono al formato WhatsApp (+56...)
     */
    private function normalizarTelefono($telefono): string
    {
        if (empty($telefono)) return '';
        
        $solo_numeros = preg_replace('/[^0-9]/', '', $telefono);
        
        // Si ya tiene código de país
        if (substr($solo_numeros, 0, 2) === '56') {
            return '+' . $solo_numeros;
        }
        
        // Si empieza con 9 y tiene 9 dígitos (celular chileno)
        if (substr($solo_numeros, 0, 1) === '9' && strlen($solo_numeros) === 9) {
            return '+56' . $solo_numeros;
        }
        
        // Si tiene 8 dígitos, asumir que es celular sin el 9
        if (strlen($solo_numeros) === 8) {
            return '+569' . $solo_numeros;
        }
        
        // Por defecto, agregar código de país
        return '+56' . $solo_numeros;
    }

    /**
     * Valida los datos de una inspección
     */
    private function validarDatosInspeccion($data): array
    {
        $errores = [];
        
        if (empty($data['inspecciones_asegurado'])) 
            $errores[] = 'El nombre del asegurado es obligatorio';
            
        if (empty($data['inspecciones_rut'])) 
            $errores[] = 'El RUT es obligatorio';
        elseif (!$this->validarRUT($data['inspecciones_rut']))
            $errores[] = 'El RUT ingresado no es válido';
            
        if (!empty($data['inspecciones_email']) && !filter_var($data['inspecciones_email'], FILTER_VALIDATE_EMAIL))
            $errores[] = 'El email ingresado no es válido';
            
        if (empty($data['inspecciones_patente'])) 
            $errores[] = 'La patente es obligatoria';
            
        if (empty($data['inspecciones_marca'])) 
            $errores[] = 'La marca es obligatoria';
            
        if (empty($data['inspecciones_modelo'])) 
            $errores[] = 'El modelo es obligatorio';
            
        if ($data['tipo_inspeccion_id'] <= 0) 
            $errores[] = 'Debe seleccionar un tipo de inspección';
            
        if ($data['tipo_carroceria_id'] <= 0) 
            $errores[] = 'Debe seleccionar un tipo de carrocería';
            
        if (empty($data['inspecciones_n_poliza'])) 
            $errores[] = 'El número de póliza es obligatorio';
            
        if (empty($data['inspecciones_direccion'])) 
            $errores[] = 'La dirección es obligatoria';
            
        if (empty($data['inspecciones_celular'])) 
            $errores[] = 'El celular es obligatorio';
            
        if ($data['cia_id'] <= 0) 
            $errores[] = 'Debe seleccionar una compañía de seguros';
            
        if ($data['comunas_id'] <= 0) 
            $errores[] = 'Debe seleccionar una comuna';
        
        return $errores;
    }

    /**
     * Valida RUT chileno con dígito verificador
     */
    private function validarRUT($rut): bool
    {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (strlen($rut) < 8 || strlen($rut) > 9) return false;
        
        $dv = strtolower(substr($rut, -1));
        $numero = substr($rut, 0, -1);
        
        $suma = 0;
        $multiplicador = 2;
        
        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += $numero[$i] * $multiplicador;
            $multiplicador = ($multiplicador == 7) ? 2 : $multiplicador + 1;
        }
        
        $resto = $suma % 11;
        $dv_calculado = ($resto == 0) ? '0' : (($resto == 1) ? 'k' : (string)(11 - $resto));
        
        return $dv === $dv_calculado;
    }

    /**
     * Genera URL de WhatsApp con mensaje predefinido
     */
    private function generarWhatsAppURL($data): string
    {
        $numero = str_replace('+', '', $data['inspecciones_celular']);
        $asegurado = $data['inspecciones_asegurado'];
        $patente = $data['inspecciones_patente'];
        
        $mensaje = "Hola {$asegurado}, su solicitud de inspección para el vehículo patente {$patente} ha sido registrada exitosamente. Nos contactaremos pronto para coordinar la fecha de inspección.";
        
        return "https://wa.me/{$numero}?text=" . urlencode($mensaje);
    }
}