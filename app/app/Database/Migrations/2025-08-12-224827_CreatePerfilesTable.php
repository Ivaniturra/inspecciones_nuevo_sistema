<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerfilesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'perfil_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'perfil_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'perfil_tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'compania',
                'comment'    => 'compania o interno'
            ],
            'perfil_descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'perfil_permisos' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON string with permissions'
            ],
            'perfil_nivel' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1,
                'comment'    => '1=Básico, 2=Intermedio, 3=Avanzado, 4=Admin'
            ],
            'perfil_habil' => [
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
        
        $this->forge->addPrimaryKey('perfil_id');
        $this->forge->addKey('perfil_tipo');
        $this->forge->addKey('perfil_habil');
        $this->forge->addKey(['perfil_tipo', 'perfil_habil']);
        
        $this->forge->createTable('perfiles');
        
        // Insertar perfiles predeterminados
        $this->insertDefaultPerfiles();
    }

    public function down()
    {
        $this->forge->dropTable('perfiles');
    }
    
    private function insertDefaultPerfiles()
    {
        $data = [
            // Perfiles de Compañía
            [
                'perfil_nombre' => 'Visualizador',
                'perfil_tipo' => 'compania',
                'perfil_descripcion' => 'Solo puede ver reportes de su compañía',
                'perfil_permisos' => json_encode([
                    'ver_reportes' => true,
                    'ver_inspecciones' => true,
                    'generar_reportes' => false,
                    'crear_usuarios' => false
                ]),
                'perfil_nivel' => 1,
                'perfil_habil' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'perfil_nombre' => 'Supervisor',
                'perfil_tipo' => 'compania',
                'perfil_descripcion' => 'Puede ver información y generar reportes básicos',
                'perfil_permisos' => json_encode([
                    'ver_reportes' => true,
                    'ver_inspecciones' => true,
                    'generar_reportes' => true,
                    'crear_usuarios' => false
                ]),
                'perfil_nivel' => 2,
                'perfil_habil' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'perfil_nombre' => 'Administrador Compañía',
                'perfil_tipo' => 'compania',
                'perfil_descripcion' => 'Acceso completo a datos de su compañía',
                'perfil_permisos' => json_encode([
                    'ver_reportes' => true,
                    'ver_inspecciones' => true,
                    'generar_reportes' => true,
                    'crear_usuarios' => true,
                    'gestionar_usuarios' => true
                ]),
                'perfil_nivel' => 3,
                'perfil_habil' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            
            // Perfiles Internos
            [
                'perfil_nombre' => 'Inspector',
                'perfil_tipo' => 'interno',
                'perfil_descripcion' => 'Realiza inspecciones en campo',
                'perfil_permisos' => json_encode([
                    'crear_inspecciones' => true,
                    'editar_inspecciones' => true,
                    'ver_inspecciones' => true,
                    'subir_fotos' => true,
                    'generar_reportes' => false
                ]),
                'perfil_nivel' => 2,
                'perfil_habil' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'perfil_nombre' => 'Coordinador',
                'perfil_tipo' => 'interno',
                'perfil_descripcion' => 'Asigna y supervisa inspecciones',
                'perfil_permisos' => json_encode([
                    'crear_inspecciones' => true,
                    'editar_inspecciones' => true,
                    'ver_inspecciones' => true,
                    'asignar_inspecciones' => true,
                    'ver_reportes' => true,
                    'generar_reportes' => true
                ]),
                'perfil_nivel' => 3,
                'perfil_habil' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'perfil_nombre' => 'Control de Calidad',
                'perfil_tipo' => 'interno',
                'perfil_descripcion' => 'Revisa y aprueba inspecciones',
                'perfil_permisos' => json_encode([
                    'ver_inspecciones' => true,
                    'aprobar_inspecciones' => true,
                    'rechazar_inspecciones' => true,
                    'ver_reportes' => true,
                    'generar_reportes' => true,
                    'auditar_sistema' => true
                ]),
                'perfil_nivel' => 3,
                'perfil_habil' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'perfil_nombre' => 'Super Administrador',
                'perfil_tipo' => 'interno',
                'perfil_descripcion' => 'Acceso completo al sistema',
                'perfil_permisos' => json_encode([
                    'acceso_total' => true,
                    'gestionar_compañias' => true,
                    'gestionar_usuarios' => true,
                    'gestionar_perfiles' => true,
                    'configurar_sistema' => true
                ]),
                'perfil_nivel' => 4,
                'perfil_habil' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $this->db->table('perfiles')->insertBatch($data);
    }
}