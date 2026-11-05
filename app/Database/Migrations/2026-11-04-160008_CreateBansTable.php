<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBansTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'int',
                'constraint'     => 11,
                'auto_increment' => true,
                'unique'         => true,
                'null'           => false,
            ],
            'client_fingerprint' => [
                'type'       => 'varchar',
                'constraint' => 100,
                'null'       => false,
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
        $this->forge->createTable('bans');
    }

    public function down(): void
    {
        $this->forge->dropTable('bans');
    }
}
