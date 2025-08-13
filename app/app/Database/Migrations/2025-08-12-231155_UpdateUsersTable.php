<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        // Eliminar la tabla actual si existe
        $this->forge->dropTable('users', true);
        
        // Crear la nueva tabla users
        $this->forge->addField([
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'user_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'user_telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'user_perfil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cia_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'NULL para usuarios internos'
            ],
            'user_clave' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'user_avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'user_ultimo_acceso' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'user_intentos_login' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 0,
            ],
            'user_token_reset' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'user_habil' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('user_id');
        $this->forge->createTable('users');
        
        // Insertar usuario administrador por defecto
        $this->insertDefaultAdmin();
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
    
    private function insertDefaultAdmin()
    {
        // Buscar el perfil de Super Administrador
        $db = \Config\Database::connect();
        $builder = $db->table('perfiles');
        $superAdmin = $builder->where('perfil_nombre', 'Super Administrador')->get()->getRowArray();
        
        if ($superAdmin) {
            $data = [
                'user_nombre' => 'Administrador del Sistema',
                'user_email' => 'admin@inspectzu.com',
                'user_telefono' => null,
                'user_perfil' => $superAdmin['perfil_id'],
                'cia_id' => null, // Usuario interno
                'user_clave' => password_hash('admin123', PASSWORD_DEFAULT),
                'user_avatar' => null,
                'user_ultimo_acceso' => null,
                'user_intentos_login' => 0,
                'user_token_reset' => null,
                'user_habil' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('users')->insert($data);
        }
    }
}