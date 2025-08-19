<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('audit_logs')) {
            return;
        }

        $this->forge->addField([
            'audit_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
                'comment'    => 'Acci�n realizada: CREATE, UPDATE, DELETE, LOGIN, etc.'
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID del usuario que realiz� la acci�n'
            ],
            'target_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID del usuario objetivo (si aplica)'
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => false,
                'comment'    => 'Direcci�n IP del usuario (soporta IPv6)'
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'User Agent del navegador'
            ],
            'details' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Detalles adicionales de la acci�n en formato JSON'
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Fecha y hora de la acci�n'
            ],
        ]);

        // Clave primaria
        $this->forge->addKey('audit_id', true);
        
        // �ndices para consultas frecuentes
        $this->forge->addKey('user_id');
        $this->forge->addKey('target_user_id');
        $this->forge->addKey('action');
        $this->forge->addKey('created_at');
        $this->forge->addKey('ip_address');
        
        // �ndice compuesto para b�squedas por usuario y fecha
        $this->forge->addKey(['user_id', 'created_at']);
        
        // �ndice compuesto para b�squedas por acci�n y fecha
        $this->forge->addKey(['action', 'created_at']);
        
        // Crear la tabla
        $this->forge->createTable('audit_logs', true, [
            'ENGINE' => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci'
        ]);

        // Agregar foreign keys (solo si las tablas existen)
        if ($this->db->tableExists('users')) {
            $this->forge->addForeignKey('user_id', 'users', 'user_id', 'SET NULL', 'CASCADE');
            $this->forge->addForeignKey('target_user_id', 'users', 'user_id', 'SET NULL', 'CASCADE');
        }
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs', true);
    }
}