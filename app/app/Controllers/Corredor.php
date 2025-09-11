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
        print_r(session);
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
            cias.cia_email,
            cias.cia_telefono,
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

        $data = $this->request->getPost();
        
        // Validar datos básicos usando los nombres correctos de campos
        if (empty($data['inspecciones_asegurado']) || empty($data['inspecciones_rut']) || empty($data['inspecciones_patente'])) {
            return redirect()->back()->with('error', 'Faltan campos obligatorios')->withInput();
        }

        if ($this->inspeccionesModel->update($id, $data)) {
            return redirect()->to(base_url('corredor'))->with('success', 'Inspección actualizada correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al actualizar la inspección')->withInput();
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
        $data = $this->request->getPost();
        $data['user_id'] = session('user_id');
        $data['inspecciones_estado'] = 'pendiente'; // Estado inicial según tu BD
        $data['inspecciones_fecha_creacion'] = date('Y-m-d H:i:s');

        // Validar datos básicos usando los nombres correctos de campos
        if (empty($data['inspecciones_asegurado']) || empty($data['inspecciones_rut']) || empty($data['inspecciones_patente'])) {
            return redirect()->back()->with('error', 'Faltan campos obligatorios')->withInput();
        }

        // Usar el método que crea con bitácora si existe, si no usar save normal
        if (method_exists($this->inspeccionesModel, 'crearInspeccionConBitacora')) {
            $inspeccionId = $this->inspeccionesModel->crearInspeccionConBitacora($data);
        } else {
            $inspeccionId = $this->inspeccionesModel->save($data);
        }

        if ($inspeccionId) {
            return redirect()->to(base_url('corredor'))->with('success', 'Inspección creada correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al crear la inspección')->withInput();
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