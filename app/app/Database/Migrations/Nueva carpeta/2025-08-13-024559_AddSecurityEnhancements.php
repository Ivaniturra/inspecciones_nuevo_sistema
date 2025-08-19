<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Ubicaci�n: app/Database/Migrations/2024-12-30-120000_AddSecurityEnhancements.php
 * Ejecutar: php spark migrate
 */
class AddSecurityEnhancements extends Migration
{
    public function up()
    {
        // Agregar campos a la tabla users
        $this->forge->addColumn('users', [
            'user_debe_cambiar_clave' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'user_habil'
            ],
            'user_metadata' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'user_debe_cambiar_clave'
            ],
            'user_preferences' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'user_metadata'
            ],
            'user_security_settings' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'user_preferences'
            ],
            'user_login_history' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'user_security_settings'
            ]
        ]);

        // Crear tabla de auditor�a
        $this->forge->addField([
            'audit_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'target_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'details' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('audit_id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('action');
        $this->forge->addKey('created_at');
        $this->forge->createTable('audit_logs');

        // Agregar �ndices para mejor performance
        $this->db->query('ALTER TABLE `users` ADD INDEX `idx_debe_cambiar_clave` (`user_debe_cambiar_clave`)');
    }

    public function down()
    {
        // Eliminar columnas de users
        $this->forge->dropColumn('users', 'user_debe_cambiar_clave');
        $this->forge->dropColumn('users', 'user_metadata');
        $this->forge->dropColumn('users', 'user_preferences');
        $this->forge->dropColumn('users', 'user_security_settings');
        $this->forge->dropColumn('users', 'user_login_history');

        // Eliminar tabla de auditor�a
        $this->forge->dropTable('audit_logs');
    }
}