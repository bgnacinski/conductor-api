<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LogSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $logs = [
            [
                'id'                 => sha1(random_bytes(16)),
                'action_type'        => 'entry',
                'client_fingerprint' => sha1('client-001'),
                'gate_id'            => 'A_1_01',
                'action_date'        => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'created_at'         => $now,
                'deleted_at'         => null,
            ],
            [
                'id'                 => sha1(random_bytes(16)),
                'action_type'        => 'exit',
                'client_fingerprint' => sha1('client-001'),
                'gate_id'            => 'B_2_02',
                'action_date'        => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'created_at'         => $now,
                'deleted_at'         => null,
            ],
            [
                'id'                 => sha1(random_bytes(16)),
                'action_type'        => 'entry',
                'client_fingerprint' => sha1('client-002'),
                'gate_id'            => 'C_3_03',
                'action_date'        => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'created_at'         => $now,
                'deleted_at'         => null,
            ],
            [
                'id'                 => sha1(random_bytes(16)),
                'action_type'        => 'exit',
                'client_fingerprint' => sha1('client-002'),
                'gate_id'            => 'D_4_04',
                'action_date'        => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'created_at'         => $now,
                'deleted_at'         => null,
            ],
            [
                'id'                 => sha1(random_bytes(16)),
                'action_type'        => 'entry',
                'client_fingerprint' => sha1('client-003'),
                'gate_id'            => 'E_5_05',
                'action_date'        => $now,
                'created_at'         => $now,
                'deleted_at'         => null,
            ],
        ];

        $this->db->table('log')->insertBatch($logs);
    }
}
