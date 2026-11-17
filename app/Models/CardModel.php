<?php

namespace App\Models;

use App\Entities\Card;
use CodeIgniter\Model;

class CardModel extends Model
{
    protected $table            = 'fare_cards';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Card::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["client_id", "fingerprint"];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        "client_id" => "required|integer|is_not_unique[clients.id]",
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getBanned($newer_than): array{
        $db = \Config\Database::connect();
        $builder = $db->table('fare_cards as fc')
            ->select('*')->join("bans as b", "b.client_id = fc.client_id")
            ->where("fc.deleted_at IS NULL");

        if($newer_than and strtotime($newer_than)){
            $builder->where("b.created_at >", $newer_than)->where("b.deleted_at IS NULL OR b.deleted_at > ", $newer_than);
        }

        return $builder->get()->getCustomResultObject(Card::class);
    }
}
