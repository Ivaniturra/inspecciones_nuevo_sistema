<?php
namespace App\Controllers;
class Corredor extends BaseController 
{
    public function __construct()
    {
        // Verificar autenticación
        if (!session('logged_in')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }

        // Verificar que sea corredor
        $perfilTipo = session('perfil_tipo');
        $perfilId = session('user_perfil_id');
         
    }

   <?php
namespace App\Controllers;

use App\Models\InspeccionModel;
use App\Models\CorredorModel;

class Corredor extends BaseController 
{
    protected $inspeccionModel;
    protected $corredorModel;

    public function __construct()
    {
        $this->inspeccionModel = new InspeccionModel();
        $this->corredorModel = new CorredorModel();
        
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
        $corredorId = session('corredor_id');
        
        // Obtener inspecciones del corredor
        $inspecciones = $this->inspeccionModel->getInspeccionesPorCorrector($corredorId);
        
        // Calcular estadísticas reales
        $stats = $this->calcularEstadisticas($corredorId);
        
        $data = [
            'title' => 'Dashboard Corredor',
            'corredor_id' => $corredorId,
            'corredor_nombre' => session('brand_title') ?? 'Corredor',
            'inspecciones' => $inspecciones,
            'stats' => $stats,
            
            // Branding personalizado
            'brand_title' => session('brand_title'),
            'brand_logo' => session('brand_logo'),
            'nav_bg' => session('nav_bg'),
        ];

        return view('pagina_corredor/index', $data);
    }

    private function calcularEstadisticas($corredorId)
    {
        // Obtener estadísticas reales de la base de datos
        $pendientes = $this->inspeccionModel->contarPorEstado($corredorId, 'pendiente');
        $completadas = $this->inspeccionModel->contarPorEstado($corredorId, 'completada');
        $enProceso = $this->inspeccionModel->contarPorEstado($corredorId, 'en_proceso');
        
        // Calcular comisiones del mes actual (esto depende de tu modelo de negocio)
        $comisionesMes = $this->inspeccionModel->calcularComisionesMes($corredorId, date('Y-m'));
        
        return [
            'solicitudes_pendientes' => $pendientes,
            'en_proceso' => $enProceso,
            'completadas_mes' => $completadas,
            'comisiones_mes' => $comisionesMes,
            'total_inspecciones' => $pendientes + $completadas + $enProceso
        ];
    }

    public function show($id)
    {
        $corredorId = session('corredor_id');
        
        // Verificar que la inspección pertenece al corredor
        $inspeccion = $this->inspeccionModel->getInspeccionPorId($id, $corredorId);
        
        if (!$inspeccion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inspección no encontrada');
        }

        $data = [
            'title' => 'Detalle Inspección #' . $id,
            'inspeccion' => $inspeccion,
            'brand_title' => session('brand_title'),
        ];

        return view('pagina_corredor/show', $data);
    }

    public function edit($id)
    {
        $corredorId = session('corredor_id');
        
        // Verificar que la inspección pertenece al corredor
        $inspeccion = $this->inspeccionModel->getInspeccionPorId($id, $corredorId);
        
        if (!$inspeccion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Inspección no encontrada');
        }

        // Solo permitir editar si está en estado pendiente o en_proceso
        if (!in_array($inspeccion['estado'], ['pendiente', 'en_proceso'])) {
            return redirect()->back()->with('error', 'No se puede editar una inspección ' . $inspeccion['estado']);
        }

        $data = [
            'title' => 'Editar Inspección #' . $id,
            'inspeccion' => $inspeccion,
            'brand_title' => session('brand_title'),
        ];

        return view('pagina_corredor/edit', $data);
    }

    public function update($id)
    {
        $corredorId = session('corredor_id');
        
        // Verificar que la inspección pertenece al corredor
        $inspeccion = $this->inspeccionModel->getInspeccionPorId($id, $corredorId);
        
        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        $data = $this->request->getPost();
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->inspeccionModel->update($id, $data)) {
            return redirect()->to(base_url('corredor'))->with('success', 'Inspección actualizada correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al actualizar la inspección')->withInput();
        }
    }

    public function delete($id)
    {
        $corredorId = session('corredor_id');
        
        // Verificar que la inspección pertenece al corredor
        $inspeccion = $this->inspeccionModel->getInspeccionPorId($id, $corredorId);
        
        if (!$inspeccion) {
            return redirect()->back()->with('error', 'Inspección no encontrada');
        }

        // Solo permitir eliminar si está pendiente
        if ($inspeccion['estado'] !== 'pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden eliminar inspecciones pendientes');
        }

        if ($this->inspeccionModel->delete($id)) {
            return redirect()->to(base_url('corredor'))->with('success', 'Inspección eliminada correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar la inspección');
        }
    }

    public function create()
    {
        $data = [
            'title' => 'Nueva Inspección',
            'brand_title' => session('brand_title'),
        ];

        return view('pagina_corredor/create', $data);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $data['corredor_id'] = session('corredor_id');
        $data['estado'] = 'pendiente';
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->inspeccionModel->save($data)) {
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
        $corredorId = session('corredor_id');

        $inspecciones = $this->inspeccionModel->getInspeccionesPorEstado($corredorId, $estado);

        return $this->response->setJSON([
            'success' => true,
            'data' => $inspecciones
        ]);
    }
}
} // ← ASEGÚRATE DE QUE ESTÉ ESTA LLAVE DE CIERRE