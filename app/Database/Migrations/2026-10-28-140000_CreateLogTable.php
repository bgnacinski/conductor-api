<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'int',
                'constraint'     => 11,
                'auto_increment' => true,
                'null'           => false,
            ],
            'action_type' => [
                'type'       => 'enum',
                'constraint' => ['entry', 'exit'],
                'null'       => false,
            ],
            'client_fingerprint' => [
                'type'       => 'varchar',
                'constraint' => 100,
                'null'       => false,
            ],
            'gate_id' => [
                'type'       => 'varchar',
                'constraint' => 50,
                'null'       => false,
            ],
            'action_date' => [
                'type' => 'datetime',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => false,
            ],
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('gate_id', 'gates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('log');
    }

    public function down(): void
    {
        $this->forge->dropTable('log');
    }
}
