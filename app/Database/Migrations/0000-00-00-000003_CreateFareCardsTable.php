<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFareCardsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                "auto_increment" => true,
                "unsigned" => true
            ],
            "client_id" => [
                "type" => "int",
                "constraint" => 11,
                "unsigned" => true,
                "null" => false
            ],
            "fingerprint" => [
                "type" => "varchar",
                "constraint" => 100,
                "unique" => true,
                "null" => false
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
        $this->forge->addForeignKey("client_id", "clients", "id");
        $this->forge->createTable("fare_cards");
    }

    public function down()
    {
        $this->forge->dropTable("fare_cards");
    }
}
