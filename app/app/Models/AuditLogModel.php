<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Ubicación: app/Models/AuditLogModel.php
 * Modelo para registrar todas las acciones de auditoría
 */
class AuditLogModel extends Model
{
    protected $table      = 'audit_logs';
    protected $primaryKey = 'audit_id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'action',
        'user_id',
        'target_user_id',
        'ip_address',
        'user_agent',
        'details',
        'created_at'
    ];

    // Timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Callbacks
    protected $beforeInsert = ['setDefaults'];
    protected $afterFind    = ['parseDetails'];

    /**
     * Establecer valores por defecto
     */
    protected function setDefaults(array $data): array
    {
        if (!isset($data['data']['created_at'])) {
            $data['data']['created_at'] = date('Y-m-d H:i:s');
        }

        if (!isset($data['data']['ip_address'])) {
            $data['data']['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        if (!isset($data['data']['user_agent'])) {
            $data['data']['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        }

        return $data;
    }

    /**
     * Parsear campo details JSON
     */
    protected function parseDetails(array $data): array
    {
        if (!isset($data['data'])) return $data;

        // Para múltiples registros
        if (isset($data['data'][0]) && is_array($data['data'][0])) {
            foreach ($data['data'] as &$row) {
                if (isset($row['details']) && is_string($row['details'])) {
                    $row['details'] = json_decode($row['details'], true) ?? [];
                }
            }
        }
        // Para un solo registro
        else {
            if (isset($data['data']['details']) && is_string($data['data']['details'])) {
                $data['data']['details'] = json_decode($data['data']['details'], true) ?? [];
            }
        }

        return $data;
    }

    /**
     * Obtener logs por usuario
     */
    public function getByUser(int $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Obtener logs por acción
     */
    public function getByAction(string $action, int $limit = 50): array
    {
        return $this->where('action', $action)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Obtener logs de un usuario objetivo
     */
    public function getByTargetUser(int $targetUserId, int $limit = 50): array
    {
        return $this->where('target_user_id', $targetUserId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Obtener logs por rango de fechas
     */
    public function getByDateRange(string $startDate, string $endDate): array
    {
        return $this->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Obtener estadísticas de auditoría
     */
    public function getStats(): array
    {
        $stats = [];

        // Total de logs
        $stats['total'] = $this->countAll();

        // Logs de hoy
        $stats['today'] = $this->where('DATE(created_at)', date('Y-m-d'))
                                ->countAllResults();

        // Acciones más comunes
        $stats['top_actions'] = $this->select('action, COUNT(*) as count')
                                     ->groupBy('action')
                                     ->orderBy('count', 'DESC')
                                     ->limit(5)
                                     ->findAll();

        // Usuarios más activos
        $stats['top_users'] = $this->select('user_id, COUNT(*) as count')
                                   ->where('user_id IS NOT NULL')
                                   ->groupBy('user_id')
                                   ->orderBy('count', 'DESC')
                                   ->limit(5)
                                   ->findAll();

        return $stats;
    }

    /**
     * Limpiar logs antiguos
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        
        return $this->where('created_at <', $cutoffDate)->delete();
    }
}