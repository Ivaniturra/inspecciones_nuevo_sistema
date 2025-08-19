<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        // Si la tabla NO existe ? creación completa
        if (!$this->db->tableExists('users')) {
            $this->forge->addField([
                'user_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'user_nombre' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => false,
                ],
                'user_email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => false,
                ],
                'user_telefono' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '20',
                    'null'       => true,
                ],
                'user_perfil' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'cia_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'comment'    => 'NULL para usuarios internos',
                ],
                'user_clave' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => false,
                ],
                'user_avatar' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'user_ultimo_acceso' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'user_intentos_login' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                    'null'       => false,
                ],
                'user_token_reset' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'user_habil' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'null'       => false,
                ],
                'user_debe_cambiar_clave' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'null'       => false,
                ],
                'user_metadata' => [
                    'type' => 'JSON',
                    'null' => true,
                ],
                'user_preferences' => [
                    'type' => 'JSON',
                    'null' => true,
                ],
                'user_security_settings' => [
                    'type' => 'JSON',
                    'null' => true,
                ],
                'user_login_history' => [
                    'type' => 'JSON',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'user_remember_selector' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '64',
                    'null'       => true,
                ],
                'user_remember_validator_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'user_remember_expires' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'user_token_reset_expires' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            // Claves e índices
            $this->forge->addKey('user_id', true);
            $this->forge->addUniqueKey('user_email');
            $this->forge->addKey('cia_id');
            $this->forge->addKey('user_perfil');
            $this->forge->addKey('user_habil');
            $this->forge->addKey('user_ultimo_acceso');

            // Crear la tabla
            $this->forge->createTable('users', true, [
                'ENGINE'  => 'InnoDB',
                'COMMENT' => 'Usuarios del sistema',
            ]);

            // FKs (solo si existen tablas referenciadas)
            if ($this->db->tableExists('cias')) {
                $this->forge->addForeignKey('cia_id', 'cias', 'cia_id', 'SET NULL', 'CASCADE');
            }
            if ($this->db->tableExists('perfiles')) {
                $this->forge->addForeignKey('user_perfil', 'perfiles', 'perfil_id', 'RESTRICT', 'CASCADE');
            }

            return; // FIN ruta "crear tabla"
        }

        // ---------------------------------------------------------------------
        // Si la tabla YA EXISTE ? añadir solo lo que falte (evita duplicados)
        // ---------------------------------------------------------------------

        // Definiciones de columnas que queremos garantizar
        $defs = [
            'user_nombre' => ['type'=>'VARCHAR','constraint'=>100,'null'=>false],
            'user_email'  => ['type'=>'VARCHAR','constraint'=>255,'null'=>false],
            'user_telefono' => ['type'=>'VARCHAR','constraint'=>20,'null'=>true],
            'user_perfil' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>false],
            'cia_id'      => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true,'comment'=>'NULL para usuarios internos'],
            'user_clave'  => ['type'=>'VARCHAR','constraint'=>255,'null'=>false],
            'user_avatar' => ['type'=>'VARCHAR','constraint'=>255,'null'=>true],
            'user_ultimo_acceso' => ['type'=>'DATETIME','null'=>true],
            'user_intentos_login' => ['type'=>'INT','constraint'=>11,'default'=>0,'null'=>false],
            'user_token_reset' => ['type'=>'VARCHAR','constraint'=>255,'null'=>true],
            'user_habil' => ['type'=>'TINYINT','constraint'=>1,'default'=>1,'null'=>false],
            'user_debe_cambiar_clave' => ['type'=>'TINYINT','constraint'=>1,'default'=>0,'null'=>false],
            'user_metadata' => ['type'=>'JSON','null'=>true],
            'user_preferences' => ['type'=>'JSON','null'=>true],
            'user_security_settings' => ['type'=>'JSON','null'=>true],
            'user_login_history' => ['type'=>'JSON','null'=>true],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
            'user_remember_selector' => ['type'=>'VARCHAR','constraint'=>64,'null'=>true],
            'user_remember_validator_hash' => ['type'=>'VARCHAR','constraint'=>255,'null'=>true],
            'user_remember_expires' => ['type'=>'DATETIME','null'=>true],
            'user_token_reset_expires' => ['type'=>'DATETIME','null'=>true],
        ];

        // Construir solo las columnas que falten
        $toAdd = [];
        foreach ($defs as $col => $def) {
            if (!$this->db->fieldExists($col, 'users')) {
                $toAdd[$col] = $def;
            }
        }
        if (!empty($toAdd)) {
            $this->forge->addColumn('users', $toAdd);
        }

        // Asegurar índice único en user_email (si falta)
        if (!$this->uniqueIndexExists('users', 'user_email')) {
            // Nota: nombre del índice será "user_email" (MySQL lo permite para UNIQUE)
            $this->db->query('ALTER TABLE `users` ADD UNIQUE KEY `user_email` (`user_email`)');
        }

        // (Opcional) podrías asegurar aquí otros índices con el mismo patrón.
        // FKs: omito en esta ruta para no chocar con esquemas preexistentes.
    }

    public function down()
    {
        // Eliminación segura
        if ($this->db->tableExists('users')) {
            $this->forge->dropTable('users', true);
        }
    }

    /**
     * Comprueba si existe un índice UNIQUE por nombre en la tabla.
     */
    private function uniqueIndexExists(string $table, string $indexName): bool
    {
        $table = $this->db->escapeString($table);
        $index = $this->db->escapeString($indexName);

        $query = $this->db->query(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = '{$index}' AND Non_unique = 0"
        );

        return ($query && $query->getNumRows() > 0);
    }
}
