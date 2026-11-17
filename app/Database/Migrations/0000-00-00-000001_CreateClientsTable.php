<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
                "null" => false,
            ],
            "name" => [
                "type" => "VARCHAR",
                "constraint" => 100,
                "null" => false,
            ],
            "surname" => [
                "type" => "VARCHAR",
                "constraint" => 100,
                "null" => false,
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => false,
            ],
            "updated_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "deleted_at" => [
                "type" => "DATETIME",
                "null" => true,
            ]
        ]);

        $this->forge->addKey("id", true);
        $this->forge->createTable("clients");
    }

    public function down()
    {
        $this->forge->dropTable("clients");
    }
}
