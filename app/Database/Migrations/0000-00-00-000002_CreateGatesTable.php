<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGatesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'       => 'varchar',
                'constraint' => 50,
                'null'       => false,
            ],
            'auth_key' => [
                'type'       => 'varchar',
                'constraint' => 256,
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'datetime',
                'null'       => false,
            ],
            'updated_at' => [
                'type'       => 'datetime',
                'null'       => true,
            ],
            'deleted_at' => [
                'type'       => 'datetime',
                'null'       => true,
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('gates');
    }

    public function down(): void
    {
        $this->forge->dropTable('gates');
    }
}
