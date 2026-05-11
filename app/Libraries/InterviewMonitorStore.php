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
            'current_session'  => max(1, (int) ($payload['currentSession'] ?? ($session['current_session'] ?? 1))),
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

        if (in_array($eventType, ['session_completed', 'session_time_up'], true)) {
            $startedAt = $session['started_at'] ?? $data['started_at'] ?? null;
            if ($startedAt) {
                $start = strtotime($startedAt);
                $end = strtotime($eventAt);
                $data['durasi_total_detik'] = $end - $start;
            }
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

            // Also insert into log_pelanggaran for dashboard
            $db->table('log_pelanggaran')->insert([
                'id_pegawai'         => $data['id_user'],
                'nama_pegawai'       => $data['candidate_name'],
                'kode_pegawai'       => $data['session_code'],
                'jenis_pelanggaran'  => trim((string) ($payload['violationType'] ?? 'general')),
                'detail_pelanggaran' => trim((string) ($payload['message'] ?? 'Pelanggaran terdeteksi.')),
                'jumlah_pelanggaran' => $data['violations_count'],
                'nomor_sesi'         => $data['current_session'],
                'status_sesi'        => $data['status'],
                'tanggal_pelanggaran'=> $eventAt,
            ]);
        }

        // --- UPDATE status_sesi_peserta ---
        $idPegawai = $data['id_user'];
        $numSesi = $data['current_session'];
        
        $statusUpdate = [
            'waktu_sisa' => max(0, (int) ($payload['timeLeftSeconds'] ?? 0)),
        ];

        if ($eventType === 'session_started' || $eventType === 'fullscreen_entered') {
            $statusUpdate['status_sesi'] = 'berjalan';
            // Only set waktu_mulai if it's not already set in DB
            $currentStatus = $db->table('status_sesi_peserta')
                ->where('id_pegawai', $idPegawai)
                ->where('nomor_sesi', $numSesi)
                ->get()->getRowArray();
            
            if (!$currentStatus || empty($currentStatus['waktu_mulai'])) {
                $statusUpdate['waktu_mulai'] = $eventAt;
            }
        }

        if ($eventType === 'session_completed') {
            $statusUpdate['status_sesi'] = 'selesai';
            $statusUpdate['tanggal_selesai'] = $eventAt;
            $statusUpdate['waktu_sisa'] = 0;
        }

        if ($eventType === 'session_time_up') {
            $statusUpdate['status_sesi'] = 'selesai';
            $statusUpdate['tanggal_selesai'] = $eventAt;
            $statusUpdate['waktu_sisa'] = 0;
        }

        if ($idPegawai && $numSesi) {
            $exists = $db->table('status_sesi_peserta')
                ->where('id_pegawai', $idPegawai)
                ->where('nomor_sesi', $numSesi)
                ->get()->getRowArray();
            
            if ($exists) {
                $db->table('status_sesi_peserta')
                    ->where('id_status', $exists['id_status'])
                    ->update($statusUpdate);
            } else {
                $statusUpdate['id_pegawai'] = $idPegawai;
                $statusUpdate['nomor_sesi'] = $numSesi;
                
                // Get duration from pengaturan_sesi
                $sessionSetting = $db->table('pengaturan_sesi')->where('id_sesi', $numSesi)->get()->getRowArray();
                $statusUpdate['durasi_menit'] = (int) ($sessionSetting['durasi_menit'] ?? 10);
                
                $db->table('status_sesi_peserta')->insert($statusUpdate);
            }

            // --- UPDATE kontrol_sesi_pegawai ---
            $kontrolUpdate = [];
            if ($eventType === 'session_started' || $eventType === 'fullscreen_entered') $kontrolUpdate['status_sesi'] = 'berjalan';
            if (in_array($eventType, ['session_completed', 'session_time_up'])) $kontrolUpdate['status_sesi'] = 'selesai';

            if (!empty($kontrolUpdate)) {
                $db->table('kontrol_sesi_pegawai')
                    ->where('id_pegawai', $idPegawai)
                    ->where('nomor_sesi', $numSesi)
                    ->update($kontrolUpdate);
            }
        }

        // --- LOG ACTIVITY ---
        $activityMap = [
            'session_started' => 'Mulai Sesi',
            'answer_updated' => 'Memilih Jawaban',
            'next_question' => 'Klik Lanjut',
            'prev_question' => 'Klik Sebelumnya',
            'question_viewed' => 'Membuka Soal',
            'session_completed' => 'Submit Sesi',
            'session_time_up' => 'Waktu Sesi Habis',
            'violation_detected' => 'Melakukan Pelanggaran',
            'session_locked' => 'Sesi Terkunci',
            'fullscreen_entered' => 'Klik Fullscreen Sesi',
            'fullscreen_exited' => 'Keluar Fullscreen',
        ];

        if (isset($activityMap[$eventType])) {
            $this->logActivity($db, $data, $numSesi, $activityMap[$eventType], $payload['message'] ?? '');
        }

        return $db->table('peserta')->where('session_id', $sessionId)->get()->getRowArray() ?? [];
    }

    private function logActivity($db, $data, $numSesi, $aktivitas, $detail = '')
    {
        $now = new \DateTimeImmutable();
        $db->table('log_aktivitas_tes')->insert([
            'id_pegawai' => $data['id_user'],
            'nama_pegawai' => $data['candidate_name'],
            'nomor_sesi' => $numSesi,
            'aktivitas' => $aktivitas,
            'detail_aktivitas' => $detail,
            'tanggal_aktivitas' => $now->format('Y-m-d'),
            'jam_aktivitas' => (int) $now->format('H'),
            'menit_aktivitas' => (int) $now->format('i'),
            'waktu_lengkap' => $now->format('Y-m-d H:i:s'),
        ]);
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

            $violations = $db->table('log_pelanggaran')
                ->orderBy('tanggal_pelanggaran', 'DESC')
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
                        'startedAt'        => $s['started_at'],
                        'testDuration'     => (int) $s['test_duration'],
                        'updatedAt'        => $s['updated_at'],
                    ];
                }, $sessions),
                'recentViolations' => array_map(function($v) {
                    return [
                        'idLog'             => $v['id_log'],
                        'at'                => $v['tanggal_pelanggaran'],
                        'type'              => $v['jenis_pelanggaran'],
                        'detail'            => $v['detail_pelanggaran'] ?? '',
                        'count'             => (int) $v['jumlah_pelanggaran'],
                        'session'           => (int) ($v['nomor_sesi'] ?? 1),
                        'status'            => $v['status_sesi'],
                        'candidateName'     => $v['nama_pegawai'],
                        'idUser'            => $v['id_pegawai'],
                        'sessionCode'       => $v['kode_pegawai'],
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
     * Save answers to session-specific answer table
     */
    private function saveDetailedAnswers($db, $payload, $sessionData): void
    {
        $idPegawai = $sessionData['id_user'] ?? '';
        $namaPegawai = $sessionData['candidate_name'] ?? '';
        $currentSession = (int) ($payload['currentSession'] ?? ($sessionData['current_session'] ?? 1));
        $answers = $payload['answers'];

        $qTable = "pertanyaan_sesi_$currentSession";
        $aTable = "jawaban_sesi_$currentSession";

        foreach ($answers as $idPertanyaan => $jawabanDipilih) {
            // Get correct answer from session-specific question table
            $q = $db->table($qTable)->where('id_pertanyaan', $idPertanyaan)->get()->getRowArray();
            if (!$q) continue;

            $jawabanBenar = $q['jawaban_benar'];
            $statusJawaban = ($jawabanDipilih === $jawabanBenar) ? 'Benar' : 'Salah';
            $nilai = ($statusJawaban === 'Benar') ? 10 : 0; 

            $dataJawaban = [
                'id_pertanyaan'    => $idPertanyaan,
                'id_pegawai'       => $idPegawai,
                'nama_pegawai'     => $namaPegawai,
                'nomor_pertanyaan' => $q['urutan_pertanyaan'],
                'jawaban_pegawai'  => $jawabanDipilih,
                'jawaban_benar'    => $jawabanBenar,
                'status_jawaban'   => $statusJawaban,
                'nilai'            => $nilai,
                'tanggal_menjawab' => $this->nowIso(),
            ];

            // Check if already answered in this session
            $existing = $db->table($aTable)
                ->where('id_pertanyaan', $idPertanyaan)
                ->where('id_pegawai', $idPegawai)
                ->get()->getRowArray();

            if ($existing) {
                $db->table($aTable)
                    ->where('id_jawaban', $existing['id_jawaban'])
                    ->update($dataJawaban);
            } else {
                $db->table($aTable)->insert($dataJawaban);
            }
        }
    }
}
