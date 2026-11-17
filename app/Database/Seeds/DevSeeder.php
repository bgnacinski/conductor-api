<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DevSeeder extends Seeder
{
    public function run()
    {
        // wipe all seeded tables so the seeder can be re-run
        // (MySQL-specific: disables FK checks during truncate)
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['log', 'bans', 'fare_cards', 'gates', 'clients'] as $table) {
            $this->db->table($table)->truncate();
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        // order respects foreign keys: clients -> fare_cards, gates -> log, clients -> bans
        $this->call('ClientSeeder');
        $this->call('FareCardSeeder');
        $this->call('GateSeeder');
        $this->call('LogSeeder');
        $this->call('BanSeeder');
    }
}
