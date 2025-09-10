<?php
// Agregar estos métodos al InspeccionesModel existente

/**
 * Obtener inspecciones de un usuario con detalles completos
 */
public function getInspeccionesByUserWithDetails($userId)
{
    return $this->select('
        inspecciones.*,
        cias.cia_nombre,
        users.user_nombre,
        comunas.comunas_nombre
    ')
    ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
    ->join('users', 'users.user_id = inspecciones.user_id', 'left')
    ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
    ->where('inspecciones.user_id', $userId)
    ->orderBy('inspecciones.created_at', 'DESC')
    ->findAll();
}

/**
 * Obtener una inspección específica con detalles
 */
public function getInspeccionWithDetailsById($id)
{
    return $this->select('
        inspecciones.*,
        cias.cia_nombre,
        cias.cia_email,
        cias.cia_telefono,
        users.user_nombre,
        users.user_email,
        comunas.comunas_nombre,
        regiones.regiones_nombre
    ')
    ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
    ->join('users', 'users.user_id = inspecciones.user_id', 'left')
    ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
    ->join('regiones', 'regiones.regiones_id = comunas.regiones_id', 'left')
    ->where('inspecciones.inspeccion_id', $id)
    ->first();
}

/**
 * Contar inspecciones por estado para un usuario
 */
public function contarPorEstadoUsuario($userId, $estado)
{
    return $this->where('user_id', $userId)
                ->where('estado', $estado)
                ->countAllResults();
}

/**
 * Obtener inspecciones recientes de un usuario
 */
public function getInspeccionesRecientesUsuario($userId, $limite = 5)
{
    return $this->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit($limite)
                ->findAll();
}

/**
 * Buscar inspecciones de un usuario
 */
public function buscarInspeccionesUsuario($userId, $termino)
{
    return $this->where('user_id', $userId)
                ->groupStart()
                    ->like('asegurado', $termino)
                    ->orLike('rut', $termino)
                    ->orLike('patente', $termino)
                    ->orLike('marca', $termino)
                    ->orLike('modelo', $termino)
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->findAll();
}

/**
 * Obtener estadísticas del usuario por mes
 */
public function getEstadisticasUsuarioMes($userId, $año = null, $mes = null)
{
    if (!$año) $año = date('Y');
    if (!$mes) $mes = date('m');
    
    return $this->select('estado, COUNT(*) as cantidad')
                ->where('user_id', $userId)
                ->where('YEAR(created_at)', $año)
                ->where('MONTH(created_at)', $mes)
                ->groupBy('estado')
                ->findAll();
}

/**
 * Validar RUT chileno
 */
public function validarRut($rut)
{
    // Limpiar el RUT
    $rut = preg_replace('/[^0-9kK]/', '', $rut);
    $rut = strtoupper($rut);
    
    if (strlen($rut) < 8 || strlen($rut) > 9) {
        return false;
    }
    
    $dv = substr($rut, -1);
    $numero = substr($rut, 0, -1);
    
    // Calcular dígito verificador
    $suma = 0;
    $multiplicador = 2;
    
    for ($i = strlen($numero) - 1; $i >= 0; $i--) {
        $suma += $numero[$i] * $multiplicador;
        $multiplicador = $multiplicador == 7 ? 2 : $multiplicador + 1;
    }
    
    $resto = $suma % 11;
    $dvCalculado = 11 - $resto;
    
    if ($dvCalculado == 11) {
        $dvCalculado = '0';
    } elseif ($dvCalculado == 10) {
        $dvCalculado = 'K';
    }
    
    return $dv == $dvCalculado;
}