<?php

namespace App\Libraries;

use App\Models\ParticipantModel;
use App\Models\SessionQuestionModel;
use App\Models\SessionAnswerModel;
use App\Models\SessionStatusModel;
use App\Models\SessionSettingModel;
use App\Models\ViolationModel;
use App\Models\ViolationLogModel;
use DateTimeImmutable;
use InvalidArgumentException;
use Throwable;

class InterviewMonitorStore
{
    protected ParticipantModel $participantModel;
    protected SessionQuestionModel $questionModel;
    protected SessionAnswerModel $answerModel;
    protected SessionStatusModel $statusModel;
    protected SessionSettingModel $settingModel;
    protected ViolationModel $violationModel;
    protected ViolationLogModel $violationLogModel;

    public function __construct()
    {
        $this->participantModel = new ParticipantModel();
        $this->questionModel = new SessionQuestionModel();
        $this->answerModel = new SessionAnswerModel();
        $this->statusModel = new SessionStatusModel();
        $this->settingModel = new SessionSettingModel();
        $this->violationModel = new ViolationModel();
        $this->violationLogModel = new ViolationLogModel();
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function recordEvent(array $payload): array
    {
        $sessionId = trim((string) ($payload['sessionId'] ?? ''));
        if ($sessionId === '') {
            throw new InvalidArgumentException('sessionId wajib diisi.');
        }

        $eventAt = $this->normalizeTimestamp($payload['occurredAt'] ?? null);
        $eventType = trim((string) ($payload['eventType'] ?? 'unknown'));

        $session = $this->participantModel->getBySessionId($sessionId);
        
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
            $this->participantModel->update($session['id'], $data);
        } else {
            $data['session_id'] = $sessionId;
            $this->participantModel->insert($data);
        }

        if (isset($payload['answers']) && is_array($payload['answers'])) {
            $this->saveDetailedAnswers($payload, $data);
        }

        if ($eventType === 'violation_detected') {
            $this->violationModel->insert([
                'session_id'  => $sessionId,
                'type'        => trim((string) ($payload['violationType'] ?? 'general')),
                'message'     => trim((string) ($payload['message'] ?? 'Pelanggaran terdeteksi.')),
                'occurred_at' => $eventAt,
            ]);

            $this->violationLogModel->insert([
                'id_pegawai'         => $data['id_user'],
                'nama_pegawai'       => $data['candidate_name'],
                'kode_pegawai'       => $data['session_code'],
                'jenis_pelanggaran'  => trim((string) ($payload['violationType'] ?? 'general')),
                'jumlah_pelanggaran' => $data['violations_count'],
                'status_sesi'        => $data['status'],
                'tanggal_pelanggaran'=> $eventAt,
            ]);
        }

        $idPegawai = $data['id_user'];
        $numSesi = $data['current_session'];
        
        $statusUpdate = [
            'waktu_sisa' => max(0, (int) ($payload['timeLeftSeconds'] ?? 0)),
        ];

        if ($eventType === 'session_started') {
            $statusUpdate['status_sesi'] = 'berjalan';
            $statusUpdate['waktu_mulai'] = $eventAt;
        }

        if ($eventType === 'session_completed' || $eventType === 'session_time_up') {
            $statusUpdate['status_sesi'] = 'selesai';
            $statusUpdate['tanggal_selesai'] = $eventAt;
            $statusUpdate['waktu_sisa'] = 0;
        }

        if ($idPegawai && $numSesi) {
            $exists = $this->statusModel->getStatus($idPegawai, $numSesi);
            if ($exists) {
                $this->statusModel->update($exists['id_status'], $statusUpdate);
            } else {
                $statusUpdate['id_pegawai'] = $idPegawai;
                $statusUpdate['nomor_sesi'] = $numSesi;
                $sessionSetting = $this->settingModel->find($numSesi);
                $statusUpdate['durasi_menit'] = (int) ($sessionSetting['durasi_menit'] ?? 10);
                $this->statusModel->insert($statusUpdate);
            }
        }

        return $this->participantModel->getBySessionId($sessionId) ?? [];
    }

    /**
     * @return array{summary: array<string, int>, sessions: list<array<string, mixed>>, recentViolations: list<array<string, mixed>>, updatedAt: string}
     */
    public function getDashboardData(): array
    {
        try {
            $sessions = $this->participantModel->orderBy('updated_at', 'DESC')->findAll();
            $violations = $this->violationLogModel->orderBy('tanggal_pelanggaran', 'DESC')->findAll();

            $totalViolations = $this->participantModel->selectSum('violations_count')->first()['violations_count'] ?? 0;
            $lockedSessions = $this->participantModel->where('status', 'locked')->countAllResults();
            $activeSessions = $this->participantModel->where('status', 'active')->countAllResults();
            $completedSessions = $this->participantModel->whereIn('status', ['completed', 'time_up'])->countAllResults();
            $blockedCandidates = $this->participantModel->where('is_blocked', true)->countAllResults();

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
                        'testDuration'     => (int) $s['durasi_total_detik'],
                        'updatedAt'        => $s['updated_at'],
                    ];
                }, $sessions),
                'recentViolations' => array_map(function($v) {
                    return [
                        'idLog'             => $v['id_log'],
                        'at'                => $v['tanggal_pelanggaran'],
                        'type'              => $v['jenis_pelanggaran'],
                        'count'             => (int) $v['jumlah_pelanggaran'],
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

    private function saveDetailedAnswers(array $payload, $sessionData): void
    {
        $idPegawai = $sessionData['id_user'] ?? '';
        $namaPegawai = $sessionData['candidate_name'] ?? '';
        $currentSession = (int) ($payload['currentSession'] ?? ($sessionData['current_session'] ?? 1));
        $answers = $payload['answers'];

        $this->questionModel->setSession($currentSession);
        $this->answerModel->setSession($currentSession);

        foreach ($answers as $idPertanyaan => $jawabanDipilih) {
            $q = $this->questionModel->find($idPertanyaan);
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

            $existing = $this->answerModel->where('id_pertanyaan', $idPertanyaan)
                ->where('id_pegawai', $idPegawai)
                ->first();

            if ($existing) {
                $this->answerModel->update($existing['id_jawaban'], $dataJawaban);
            } else {
                $this->answerModel->insert($dataJawaban);
            }
        }
    }
}
