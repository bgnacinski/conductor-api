<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GateSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $gates = [
            [
                'id'         => 'A_1_01',
                'auth_key'   => bin2hex(random_bytes(32)),
                'created_at' => $now,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id'         => 'B_2_02',
                'auth_key'   => bin2hex(random_bytes(32)),
                'created_at' => $now,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id'         => 'C_3_03',
                'auth_key'   => bin2hex(random_bytes(32)),
                'created_at' => $now,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id'         => 'D_4_04',
                'auth_key'   => bin2hex(random_bytes(32)),
                'created_at' => $now,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id'         => 'E_5_05',
                'auth_key'   => bin2hex(random_bytes(32)),
                'created_at' => $now,
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ];

        $this->db->table('gates')->insertBatch($gates);
    }
}
