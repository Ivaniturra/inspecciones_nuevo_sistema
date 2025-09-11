<?php
namespace App\Controllers;

use App\Models\InspeccionesModel;
use App\Models\CorredorModel;
use App\Models\CiasModel;
use App\Models\ComunasModel;

class Corredor extends BaseController 
{
    protected $inspeccionesModel;
    protected $corredorModel;
    protected $ciasModel;
    protected $comunasModel;

    public function __construct()
    {
        $this->inspeccionesModel = new InspeccionesModel();
        $this->corredorModel = new CorredorModel();
        $this->ciasModel = new CiasModel();
        $this->comunasModel = new ComunasModel();
        
        // Verificar autenticación
        if (!session('logged_in')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }

        // Verificar que sea corredor
        $perfilTipo = session('perfil_tipo');
        if ($perfilTipo !== 'corredor') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado - Solo para corredores');
        }
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Obtener inspecciones del usuario (corredor)
        $inspecciones = $this->inspeccionesModel->getInspeccionesByUserWithDetails($userId);
        
        // Calcular estadísticas reales
        $stats = $this->calcularEstadisticas($userId);
        
        $data = [
            'title' => 'Dashboard Corredor',
            'corredor_id' => session('corredor_id'),
            'corredor_nombre' => session('user_name') ?? 'Corredor',
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
        // Obtener estadísticas reales de la base de datos
        $pendientes = $this->inspeccionesModel->where('user_id', $userId)
                           ->where('estado', 'pendiente')
                           ->countAllResults();
        
        $enProceso = $this->inspeccionesModel->where('user_id', $userId)
                          ->where('estado', 'en_proceso')
                          ->countAllResults();
        
        $completadas = $this->inspeccionesModel->where('user_id', $userId)
                            ->where('estado', 'completada')
                            ->countAllResults();
        
        // Calcular comisiones del mes actual (ejemplo: $50.000 por completada)
        $completadasMes = $this->inspeccionesModel->where('user_id', $userId)
                               ->where('estado', 'completada')
                               ->where('MONTH(created_at)', date('m'))
                               ->where('YEAR(created_at)', date('Y'))
                               ->countAllResults();
        
        $comisionesMes = $completadasMes * 50000;
        
        return [
            'solicitudes_pendientes' => $pendientes,
            'en_proceso' => $enProceso,
            'completadas_mes' => $completadas,
            'comisiones_mes' => $comisionesMes,
            'total_inspecciones' => $pendientes + $enProceso + $completadas
        ];
    }

    public function show($id)
    {
        $userId = session('user_id');
        
        // Verificar que la inspección pertenece al usuario
        $inspeccion = $this->inspeccionesModel->where('inspeccion_id', $id)
                           ->where('user_id', $userId)
                           ->first();
        
        if (!$inspeccion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inspección no encontrada');
        }

        // Obtener detalles completos
        $inspeccion = $this->inspeccionesModel->getInspeccionWithDetailsById($id);

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
        $inspeccion = $this->inspeccionesModel->where('inspeccion_id', $id)
                           ->where('user_id', $userId)
                           ->first();
        
        if (!$inspeccion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inspección no encontrada');
        }

        // Solo permitir editar si está en estado pendiente o en_proceso
        if (!in_array($inspeccion['estado'], ['pendiente', 'en_proceso'])) {
            return redirect()->back()->with('error', 'No se puede editar una inspección ' . $inspeccion['estado']);
        }

        // Obtener datos para formulario
        $companias = $this->ciasModel->findAll();
        $comunas = $this->comunasModel->findAll();

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
        $inspeccion = $this->inspeccionesModel->where('inspeccion_id', $id)
                           ->where('user_id', $userId)
                           ->first();
        
        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        $data = $this->request->getPost();
        
        // Validar datos
        if (!$this->validate($this->inspeccionesModel->getValidationRules())) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
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
        $inspeccion = $this->inspeccionesModel->where('inspeccion_id', $id)
                           ->where('user_id', $userId)
                           ->first();
        
        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        // Solo permitir eliminar si está pendiente
        if ($inspeccion['estado'] !== 'pendiente') {
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
        // Obtener datos para formulario
        $companias = $this->ciasModel->findAll();
        $comunas = $this->comunasModel->findAll();

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
        $data['estado'] = 'pendiente';
        $data['fecha_creacion'] = date('Y-m-d H:i:s');

        // Validar datos
        if (!$this->validate($this->inspeccionesModel->getValidationRules())) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        // Usar el método que crea con bitácora
        $inspeccionId = $this->inspeccionesModel->crearInspeccionConBitacora($data);

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
            $query->where('estado', $estado);
        }
        
        $inspecciones = $query->orderBy('created_at', 'DESC')->findAll();

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