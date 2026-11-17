<?php

namespace App\Models;

use App\Entities\LogEntry;
use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table            = 'log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = LogEntry::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'action_type', 'card_fingerprint', 'gate_id', 'action_date'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id' => 'required|max_length[100]|is_unique[log.id]',
        'action_type' => 'required|in_list[entry,exit]',
        'action_date' => 'required|valid_date',
        'card_fingerprint' => 'required|max_length[100]|is_not_unique[fare_cards.fingerprint]',
        'gate_id' => 'required|max_length[100]|is_not_unique[gates.id]',
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
}
