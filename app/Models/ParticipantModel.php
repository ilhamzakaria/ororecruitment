<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table            = 'peserta';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_user',
        'candidate_name',
        'position_name',
        'hrd_name',
        'session_code',
        'session_id',
        'current_session',
        'status',
        'started_at',
        'ended_at',
        'durasi_total_detik',
        'is_blocked',
        'blocked_at',
        'unblocked_at',
        'unblocked_by',
        'answers',
        'current_question',
        'questions_total',
        'violations_count',
        'tab_switches',
        'last_message',
        'updated_at',
    ];

    public function getBySessionId(string $sessionId)
    {
        return $this->where('session_id', $sessionId)->first();
    }

    public function getBlocked()
    {
        return $this->where('is_blocked', true)->findAll();
    }
}
