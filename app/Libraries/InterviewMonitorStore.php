<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Throwable;

class InterviewMonitorStore
{
    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function recordEvent(array $payload): array
    {
        $sessionId = trim((string) ($payload['sessionId'] ?? ''));
        if ($sessionId === '') {
            throw new InvalidArgumentException('sessionId wajib diisi.');
        }

        $db = $this->db();
        if ($db === null) {
            return [];
        }

        $eventAt = $this->normalizeTimestamp($payload['occurredAt'] ?? null);
        $eventType = trim((string) ($payload['eventType'] ?? 'unknown'));

        $session = $db->table('peserta')->where('session_id', $sessionId)->get()->getRowArray();
        
        $data = [
            'id_user'          => $this->pickString($payload, 'idUser', (string) ($session['id_user'] ?? '')),
            'candidate_name'   => $this->pickString($payload, 'candidateName', (string) ($session['candidate_name'] ?? '')),
            'position_name'    => $this->pickString($payload, 'positionName', (string) ($session['position_name'] ?? '')),
            'hrd_name'         => $this->pickString($payload, 'hrdName', (string) ($session['hrd_name'] ?? '')),
            'session_code'     => $this->pickString($payload, 'sessionCode', (string) ($session['session_code'] ?? '')),
            'status'           => $this->pickString($payload, 'status', (string) ($session['status'] ?? 'draft')),
            'current_question' => max(0, (int) ($payload['current_question'] ?? ($session['current_question'] ?? 0))),
            'questions_total'  => max(0, (int) ($payload['questionsTotal'] ?? ($session['questions_total'] ?? 0))),
            'violations_count' => max(0, (int) ($payload['violations'] ?? ($session['violations_count'] ?? 0))),
            'tab_switches'     => max(0, (int) ($payload['tabSwitches'] ?? ($session['tab_switches'] ?? 0))),
            'is_blocked'       => (bool) ($payload['blockedCandidate'] ?? ($session['is_blocked'] ?? false)),
            'last_message'     => $this->pickString($payload, 'message', (string) ($session['last_message'] ?? '')),
            'answers'          => isset($payload['answers']) ? json_encode($payload['answers']) : ($session['answers'] ?? null),
            'updated_at'       => $eventAt,
        ];

        if ($eventType === 'session_started' && empty($session['started_at'])) {
            $data['started_at'] = $eventAt;
        }

        if (in_array($eventType, ['session_locked', 'session_completed', 'session_time_up'], true)) {
            $data['ended_at'] = $eventAt;
        }

        if ($eventType === 'session_locked') {
            $data['is_blocked'] = true;
            $data['status'] = 'locked';
        }

        if ($eventType === 'session_completed' && ($data['status'] === 'draft' || $data['status'] === 'active')) {
            $data['status'] = 'completed';
        }

        if ($eventType === 'session_time_up') {
            $data['status'] = 'time_up';
        }

        if ($session) {
            $db->table('peserta')->where('session_id', $sessionId)->update($data);
        } else {
            $data['session_id'] = $sessionId;
            $db->table('peserta')->insert($data);
        }

        // Save to jawaban_pegawai if completed or incrementally
        if (isset($payload['answers']) && is_array($payload['answers'])) {
            $this->saveDetailedAnswers($db, $payload, $data);
        }

        if ($eventType === 'violation_detected') {
            $db->table('pelanggaran')->insert([
                'session_id'  => $sessionId,
                'type'        => trim((string) ($payload['violationType'] ?? 'general')),
                'message'     => trim((string) ($payload['message'] ?? 'Pelanggaran terdeteksi.')),
                'occurred_at' => $eventAt,
            ]);
        }

        return $db->table('peserta')->where('session_id', $sessionId)->get()->getRowArray() ?? [];
    }

    /**
     * @return array{summary: array<string, int>, sessions: list<array<string, mixed>>, recentViolations: list<array<string, mixed>>, updatedAt: string}
     */
    public function getDashboardData(): array
    {
        $db = $this->db();
        if ($db === null) {
            return $this->emptyDashboard();
        }

        try {
            $sessions = $db->table('peserta')
                ->orderBy('updated_at', 'DESC')
                ->get()
                ->getResultArray();

            $violations = $db->table('pelanggaran')
                ->select('pelanggaran.*, peserta.candidate_name, peserta.id_user, peserta.position_name, peserta.hrd_name, peserta.session_code')
                ->join('peserta', 'peserta.session_id = pelanggaran.session_id')
                ->orderBy('occurred_at', 'DESC')
                ->limit(30)
                ->get()
                ->getResultArray();

            $totalViolations = $db->table('peserta')->selectSum('violations_count')->get()->getRowArray()['violations_count'] ?? 0;
            $lockedSessions = $db->table('peserta')->where('status', 'locked')->countAllResults();
            $activeSessions = $db->table('peserta')->where('status', 'active')->countAllResults();
            $completedSessions = $db->table('peserta')->whereIn('status', ['completed', 'time_up'])->countAllResults();
            $blockedCandidates = $db->table('peserta')->where('is_blocked', true)->countAllResults();

            return [
                'summary' => [
                    'totalSessions'     => count($sessions),
                    'activeSessions'    => (int) $activeSessions,
                    'lockedSessions'    => (int) $lockedSessions,
                    'completedSessions' => (int) $completedSessions,
                    'totalViolations'   => (int) $totalViolations,
                    'blockedCandidates' => (int) $blockedCandidates,
                ],
                'sessions' => array_map(function($s) {
                    return [
                        'sessionId'        => $s['session_id'],
                        'idUser'           => $s['id_user'],
                        'candidateName'    => $s['candidate_name'],
                        'positionName'     => $s['position_name'],
                        'hrdName'          => $s['hrd_name'],
                        'sessionCode'      => $s['session_code'],
                        'status'           => $s['status'],
                        'violations'       => (int) $s['violations_count'],
                        'tabSwitches'      => (int) $s['tab_switches'],
                        'blockedCandidate' => (bool) $s['is_blocked'],
                        'lastMessage'      => $s['last_message'],
                        'answers'          => $s['answers'] ? json_decode($s['answers'], true) : [],
                        'updatedAt'        => $s['updated_at'],
                    ];
                }, $sessions),
                'recentViolations' => array_map(function($v) {
                    return [
                        'at'            => $v['occurred_at'],
                        'type'          => $v['type'],
                        'message'       => $v['message'],
                        'candidateName' => $v['candidate_name'],
                        'idUser'        => $v['id_user'],
                        'positionName'  => $v['position_name'],
                        'hrdName'       => $v['hrd_name'],
                        'sessionCode'   => $v['session_code'],
                    ];
                }, $violations),
                'updatedAt' => $this->nowIso(),
            ];
        } catch (Throwable) {
            return $this->emptyDashboard();
        }
    }

    private function db(): ?BaseConnection
    {
        try {
            $db = db_connect();
            $db->initialize();
            return $db;
        } catch (Throwable) {
            return null;
        }
    }

    private function emptyDashboard(): array
    {
        return [
            'summary' => [
                'totalSessions'     => 0,
                'activeSessions'    => 0,
                'lockedSessions'    => 0,
                'completedSessions' => 0,
                'totalViolations'   => 0,
                'blockedCandidates' => 0,
            ],
            'sessions'         => [],
            'recentViolations' => [],
            'updatedAt'        => $this->nowIso(),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function pickString(array $payload, string $key, string $fallback): string
    {
        $value = trim((string) ($payload[$key] ?? ''));
        return $value !== '' ? $value : $fallback;
    }

    private function normalizeTimestamp(mixed $value): string
    {
        $timestamp = is_string($value) ? trim($value) : '';
        if ($timestamp === '') {
            return $this->nowIso();
        }

        try {
            return (new DateTimeImmutable($timestamp))->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return $this->nowIso();
        }
    }

    private function nowIso(): string
    {
        return (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
    }

    /**
     * Save answers to jawaban_pegawai table
     */
    private function saveDetailedAnswers($db, $payload, $sessionData): void
    {
        $idPegawai = $sessionData['id_user'] ?? '';
        $namaPegawai = $sessionData['candidate_name'] ?? '';
        $answers = $payload['answers'];

        foreach ($answers as $idPertanyaan => $jawabanDipilih) {
            // Get correct answer from pertanyaan_tes
            $q = $db->table('pertanyaan_tes')->where('id_pertanyaan', $idPertanyaan)->get()->getRowArray();
            if (!$q) continue;

            $jawabanBenar = $q['jawaban_benar'];
            $statusJawaban = ($jawabanDipilih === $jawabanBenar) ? 'Benar' : 'Salah';
            $nilai = ($statusJawaban === 'Benar') ? 10 : 0; // Example scoring

            $dataJawaban = [
                'id_pertanyaan'    => $idPertanyaan,
                'id_pegawai'       => $idPegawai,
                'nama_pegawai'     => $namaPegawai,
                'jawaban_dipilih'  => $jawabanDipilih,
                'jawaban_benar'    => $jawabanBenar,
                'status_jawaban'   => $statusJawaban,
                'nilai'            => $nilai,
                'tanggal_menjawab' => $this->nowIso(),
            ];

            // Check if already answered
            $existing = $db->table('jawaban_pegawai')
                ->where('id_pertanyaan', $idPertanyaan)
                ->where('id_pegawai', $idPegawai)
                ->get()->getRowArray();

            if ($existing) {
                $db->table('jawaban_pegawai')
                    ->where('id_jawaban', $existing['id_jawaban'])
                    ->update($dataJawaban);
            } else {
                $db->table('jawaban_pegawai')->insert($dataJawaban);
            }
        }
    }
}
