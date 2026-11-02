<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GateSeeder extends Seeder
{
    public function run(): void
    {
        $gates = [
            [
                'id'  => 'A_1_01',
                'auth_key' => bin2hex(random_bytes(32)),
            ],
            [
                'id'  => 'B_2_02',
                'auth_key' => bin2hex(random_bytes(32)),
            ],
            [
                'id'  => 'C_3_03',
                'auth_key' => bin2hex(random_bytes(32)),
            ],
            [
                'id'  => 'D_4_04',
                'auth_key' => bin2hex(random_bytes(32)),
            ],
            [
                'id'  => 'E_5_05',
                'auth_key' => bin2hex(random_bytes(32)),
            ],
        ];

        $this->db->table('gates')->insertBatch($gates);
    }
}
