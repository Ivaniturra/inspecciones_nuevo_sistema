<?php
// app/Controllers/Api/ComunasController.php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ComunasModel;

class ComunasController extends BaseController
{
    protected $comunasModel;

    public function __construct()
    {
        $this->comunasModel = new ComunasModel();
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        $page = (int)$this->request->getGet('page', 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        if (strlen($query) < 2) {
            return $this->response->setJSON([
                'items' => [],
                'has_more' => false
            ]);
        }

        // Buscar comunas
        $comunas = $this->comunasModel
            ->select('comunas.comunas_id, comunas.comunas_nombre, provincias.provincias_nombre')
            ->join('provincias', 'provincias.provincias_id = comunas.provincias_id', 'left')
            ->like('comunas.comunas_nombre', $query)
            ->orderBy('comunas.comunas_nombre', 'ASC')
            ->limit($limit, $offset)
            ->findAll();

        // Verificar si hay más resultados
        $total = $this->comunasModel
            ->like('comunas_nombre', $query)
            ->countAllResults();

        $has_more = ($offset + $limit) < $total;

        return $this->response->setJSON([
            'items' => $comunas,
            'has_more' => $has_more,
            'total' => $total
        ]);
    }
}