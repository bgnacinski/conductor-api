<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $clients = [
            [
                'name'       => 'Jan',
                'surname'    => 'Kowalski',
                'created_at' => $now,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'name'       => 'Anna',
                'surname'    => 'Nowak',
                'created_at' => $now,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'name'       => 'Piotr',
                'surname'    => 'Wiśniewski',
                'created_at' => $now,
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ];

        $this->db->table('clients')->insertBatch($clients);
    }
}
