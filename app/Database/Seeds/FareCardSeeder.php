<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FareCardSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $cards = [
            [
                'client_id'   => 1,
                'fingerprint' => "5a37c12f7429927c87ca8d89b5df9167bdf72e82094ded173bdc4bd4450c1c3c",
                'created_at'  => $now,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
            [
                'client_id'   => 2,
                'fingerprint' => hash("sha256", 'client-002'),
                'created_at'  => $now,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
            [
                'client_id'   => 3,
                'fingerprint' => hash("sha256", 'client-003'),
                'created_at'  => $now,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
        ];

        $this->db->table('fare_cards')->insertBatch($cards);
    }
}
