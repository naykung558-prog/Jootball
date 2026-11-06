<?php

namespace App\Libraries\Notification;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Throwable;

class MatchNotificationWorker
{
    public const EVENT_KICKOFF = 'match_kickoff';
    public const EVENT_LINEUP = 'match_lineup';
    public const EVENT_GOAL = 'match_goal';
    public const EVENT_CARD = 'match_card';
    public const EVENT_SUBSTITUTION = 'match_substitution';
    public const EVENT_FINAL = 'match_final_score';
    public const EVENT_STANDINGS = 'competition_standings';

    private BaseConnection $db;
    private FcmPublisher $publisher;

    public function __construct(?BaseConnection $db = null, ?FcmPublisher $publisher = null)
    {
        $this->db = $db ?: Database::connect();
        $this->publisher = $publisher ?: new FcmPublisher();
    }

    public function run(string $mode = 'all', int $limit = 0): array
    {
        $mode = strtolower(trim($mode)) ?: 'all';
        $summary = [
            'mode' => $mode,
            'kickoff' => ['checked' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0],
            'lineup' => ['checked' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0],
            'goal' => ['checked' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0],
            'card' => ['checked' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0],
            'substitution' => ['checked' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0],
            'final' => ['checked' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0],
            'standings' => ['checked' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0],
        ];

        if ($mode === 'all' || $mode === 'kickoff') {
            $summary['kickoff'] = $this->dispatchKickoffNotifications($limit);
        }

        if ($mode === 'all' || $mode === 'lineup') {
            $summary['lineup'] = $this->dispatchLineupNotifications($limit);
        }

        if ($mode === 'all' || $mode === 'goal') {
            $summary['goal'] = $this->dispatchGoalNotifications($limit);
        }

        if ($mode === 'all' || $mode === 'card') {
            $summary['card'] = $this->dispatchCardNotifications($limit);
        }

        if ($mode === 'all' || $mode === 'substitution') {
            $summary['substitution'] = $this->dispatchSubstitutionNotifications($limit);
        }

        if ($mode === 'all' || $mode === 'final') {
            $summary['final'] = $this->dispatchFinalScoreNotifications($limit);
        }

        if ($mode === 'all' || $mode === 'standings') {
            $summary['standings'] = $this->dispatchStandingsNotifications($limit);
        }

        return $summary;
    }

    private function dispatchKickoffNotifications(int $limit = 0): array
    {
        $matches = $this->pendingKickoffMatches($limit);
        $stats = ['checked' => count($matches), 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($matches as $match) {
            $devices = $this->kickoffRecipients($match);
            $devices = $this->unsentDevices($devices, self::EVENT_KICKOFF, 'match', (int) $match['match_id']);
            if (empty($devices)) {
                $stats['skipped']++;
                continue;
            }

            foreach ($devices as $device) {
                $message = $this->buildKickoffMessage($match, $device);
                $payload = $this->buildKickoffPayload($match, $device);
                $result = $this->sendTracked($device, self::EVENT_KICKOFF, 'match', (int) $match['match_id'], $message['title'], $message['body'], $payload);

                if ($result['ok']) {
                    $stats['sent']++;
                } else {
                    $stats['failed']++;
                }
            }
        }

        return $stats;
    }

    private function dispatchLineupNotifications(int $limit = 0): array
    {
        $matches = $this->pendingLineupMatches($limit);
        $stats = ['checked' => count($matches), 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($matches as $match) {
            $devices = $this->lineupRecipients($match);
            $devices = $this->unsentDevices($devices, self::EVENT_LINEUP, 'match', (int) $match['match_id']);
            if (empty($devices)) {
                $stats['skipped']++;
                continue;
            }

            foreach ($devices as $device) {
                $message = $this->buildLineupMessage($match, $device);
                $payload = $this->buildLineupPayload($match, $device);
                $result = $this->sendTracked($device, self::EVENT_LINEUP, 'match', (int) $match['match_id'], $message['title'], $message['body'], $payload);

                if ($result['ok']) {
                    $stats['sent']++;
                } else {
                    $stats['failed']++;
                }
            }
        }

        return $stats;
    }

    private function dispatchGoalNotifications(int $limit = 0): array
    {
        $events = $this->pendingGoalEvents($limit);
        $stats = ['checked' => count($events), 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($events as $event) {
            $devices = $this->goalRecipients($event);
            $devices = $this->unsentDevices($devices, self::EVENT_GOAL, 'match_event', (int) $event['match_event_id']);
            if (empty($devices)) {
                $stats['skipped']++;
                continue;
            }

            foreach ($devices as $device) {
                $message = $this->buildGoalMessage($event, $device);
                $payload = $this->buildGoalPayload($event, $device);
                $result = $this->sendTracked($device, self::EVENT_GOAL, 'match_event', (int) $event['match_event_id'], $message['title'], $message['body'], $payload);

                if ($result['ok']) {
                    $stats['sent']++;
                } else {
                    $stats['failed']++;
                }
            }
        }

        return $stats;
    }

    private function dispatchCardNotifications(int $limit = 0): array
    {
        $events = $this->pendingCardEvents($limit);
        $stats = ['checked' => count($events), 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($events as $event) {
            $cardType = $this->cardType($event);
            if ($cardType === null) {
                $stats['skipped']++;
                continue;
            }

            $devices = $this->cardRecipients($event, $cardType);
            $devices = $this->unsentDevices($devices, self::EVENT_CARD, 'match_event', (int) $event['match_event_id']);
            if (empty($devices)) {
                $stats['skipped']++;
                continue;
            }

            foreach ($devices as $device) {
                $message = $this->buildCardMessage($event, $device, $cardType);
                $payload = $this->buildCardPayload($event, $device, $cardType);
                $result = $this->sendTracked($device, self::EVENT_CARD, 'match_event', (int) $event['match_event_id'], $message['title'], $message['body'], $payload);

                if ($result['ok']) {
                    $stats['sent']++;
                } else {
                    $stats['failed']++;
                }
            }
        }

        return $stats;
    }

    private function dispatchSubstitutionNotifications(int $limit = 0): array
    {
        $events = $this->pendingSubstitutionEvents($limit);
        $stats = ['checked' => count($events), 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($events as $event) {
            if (!$this->isSubstitutionEvent($event)) {
                $stats['skipped']++;
                continue;
            }

            $devices = $this->substitutionRecipients($event);
            $devices = $this->unsentDevices($devices, self::EVENT_SUBSTITUTION, 'match_event', (int) $event['match_event_id']);
            if (empty($devices)) {
                $stats['skipped']++;
                continue;
            }

            foreach ($devices as $device) {
                $message = $this->buildSubstitutionMessage($event, $device);
                $payload = $this->buildSubstitutionPayload($event, $device);
                $result = $this->sendTracked($device, self::EVENT_SUBSTITUTION, 'match_event', (int) $event['match_event_id'], $message['title'], $message['body'], $payload);

                if ($result['ok']) {
                    $stats['sent']++;
                } else {
                    $stats['failed']++;
                }
            }
        }

        return $stats;
    }

    private function dispatchFinalScoreNotifications(int $limit = 0): array
    {
        $matches = $this->pendingFinalMatches($limit);
        $stats = ['checked' => count($matches), 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($matches as $match) {
            $devices = $this->finalRecipients($match);
            $devices = $this->unsentDevices($devices, self::EVENT_FINAL, 'match', (int) $match['match_id']);
            if (empty($devices)) {
                $stats['skipped']++;
                continue;
            }

            foreach ($devices as $device) {
                $message = $this->buildFinalMessage($match, $device);
                $payload = $this->buildFinalPayload($match, $device);
                $result = $this->sendTracked($device, self::EVENT_FINAL, 'match', (int) $match['match_id'], $message['title'], $message['body'], $payload);

                if ($result['ok']) {
                    $stats['sent']++;
                } else {
                    $stats['failed']++;
                }
            }
        }

        return $stats;
    }

    private function dispatchStandingsNotifications(int $limit = 0): array
    {
        $matches = $this->pendingStandingsMatches($limit);
        $stats = ['checked' => count($matches), 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        foreach ($matches as $match) {
            $devices = $this->standingsRecipients($match);
            $devices = $this->unsentDevices($devices, self::EVENT_STANDINGS, 'match', (int) $match['match_id']);
            if (empty($devices)) {
                $stats['skipped']++;
                continue;
            }

            foreach ($devices as $device) {
                $message = $this->buildStandingsMessage($match, $device);
                $payload = $this->buildStandingsPayload($match, $device);
                $result = $this->sendTracked($device, self::EVENT_STANDINGS, 'match', (int) $match['match_id'], $message['title'], $message['body'], $payload);

                if ($result['ok']) {
                    $stats['sent']++;
                } else {
                    $stats['failed']++;
                }
            }
        }

        return $stats;
    }

    private function pendingKickoffMatches(int $limit = 0): array
    {
        $windowMinutes = max(1, (int) env('NOTIFICATION_KICKOFF_WINDOW_MINUTES', 60));
        $sql = "
            SELECT
                m.match_id,
                m.tour_id,
                m.team1_id,
                m.team2_id,
                m.date,
                m.time,
                m.match_status,
                t1.name AS team1_name,
                t1.name_en AS team1_name_en,
                t2.name AS team2_name,
                t2.name_en AS team2_name_en,
                tour.name AS tour_name,
                tour.name_en AS tour_name_en
            FROM tournament_match_result m
            LEFT JOIN team t1 ON t1.id = m.team1_id
            LEFT JOIN team t2 ON t2.id = m.team2_id
            LEFT JOIN tournament tour ON tour.tour_id = m.tour_id
            WHERE m.match_status = 0
              AND m.team1_id > 0
              AND m.team2_id > 0
              AND m.date IS NOT NULL
              AND m.date <> ''
              AND STR_TO_DATE(CONCAT(m.date, ' ', COALESCE(NULLIF(m.time, ''), '00:00:00')), '%Y-%m-%d %H:%i:%s')
                    BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL {$windowMinutes} MINUTE)
              AND NOT EXISTS (
                  SELECT 1
                  FROM notification_dispatch_logs l
                  WHERE l.event_key = ?
                    AND l.reference_type = 'match'
                    AND l.reference_id = m.match_id
                    AND l.status = 'success'
              )
            ORDER BY m.date ASC, m.time ASC, m.match_id ASC
        ";

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        return $this->db->query($sql, [self::EVENT_KICKOFF])->getResultArray();
    }

    private function pendingLineupMatches(int $limit = 0): array
    {
        if (!$this->db->tableExists('player_to_match')) {
            return [];
        }

        $sql = "
            SELECT
                m.match_id,
                m.tour_id,
                m.team1_id,
                m.team2_id,
                m.date,
                m.time,
                m.match_status,
                t1.name AS team1_name,
                t1.name_en AS team1_name_en,
                t2.name AS team2_name,
                t2.name_en AS team2_name_en,
                tour.name AS tour_name,
                tour.name_en AS tour_name_en
            FROM tournament_match_result m
            LEFT JOIN team t1 ON t1.id = m.team1_id
            LEFT JOIN team t2 ON t2.id = m.team2_id
            LEFT JOIN tournament tour ON tour.tour_id = m.tour_id
            WHERE m.match_status IN (0, 1)
              AND m.team1_id > 0
              AND m.team2_id > 0
              AND EXISTS (
                  SELECT 1
                  FROM player_to_match ptm
                  WHERE ptm.match_id = m.match_id
              )
              AND NOT EXISTS (
                  SELECT 1
                  FROM notification_dispatch_logs l
                  WHERE l.event_key = ?
                    AND l.reference_type = 'match'
                    AND l.reference_id = m.match_id
                    AND l.status = 'success'
              )
            ORDER BY m.date ASC, m.time ASC, m.match_id ASC
        ";

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        return $this->db->query($sql, [self::EVENT_LINEUP])->getResultArray();
    }

    private function pendingGoalEvents(int $limit = 0): array
    {
        $lookbackDays = $this->notificationLookbackDays();
        $sql = "
            SELECT
                e.match_event_id,
                e.match_id,
                e.tour_id,
                e.team_id,
                e.event_id,
                e.minute,
                e.player_1_id,
                e.player_2_id,
                m.team1_id,
                m.team2_id,
                m.team1_score,
                m.team2_score,
                m.date,
                m.time,
                m.match_status,
                t1.name AS team1_name,
                t1.name_en AS team1_name_en,
                t2.name AS team2_name,
                t2.name_en AS team2_name_en,
                tour.name AS tour_name,
                tour.name_en AS tour_name_en,
                p1.first_name AS player1_first_name,
                p1.last_name AS player1_last_name,
                p2.first_name AS player2_first_name,
                p2.last_name AS player2_last_name
            FROM tournament_match_event e
            INNER JOIN tournament_match_result m ON m.match_id = e.match_id
            LEFT JOIN team t1 ON t1.id = m.team1_id
            LEFT JOIN team t2 ON t2.id = m.team2_id
            LEFT JOIN tournament tour ON tour.tour_id = m.tour_id
            LEFT JOIN player p1 ON p1.id = e.player_1_id
            LEFT JOIN player p2 ON p2.id = e.player_2_id
            WHERE e.event_id IN (1, 5)
              AND m.team1_id > 0
              AND m.team2_id > 0
              AND COALESCE(m.update_date, NULLIF(m.date, '')) >= DATE_SUB(NOW(), INTERVAL {$lookbackDays} DAY)
            ORDER BY COALESCE(m.update_date, m.date) DESC, e.match_id DESC, CAST(e.minute AS UNSIGNED) ASC, e.match_event_id ASC
        ";

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        return $this->db->query($sql)->getResultArray();
    }

    private function pendingCardEvents(int $limit = 0): array
    {
        return $this->pendingClassifiedMatchEvents(self::EVENT_CARD, $limit);
    }

    private function pendingSubstitutionEvents(int $limit = 0): array
    {
        return $this->pendingClassifiedMatchEvents(self::EVENT_SUBSTITUTION, $limit);
    }

    private function pendingClassifiedMatchEvents(string $eventKey, int $limit = 0): array
    {
        $lookbackDays = $this->notificationLookbackDays();
        $sql = "
            SELECT
                e.match_event_id,
                e.match_id,
                e.tour_id,
                e.team_id,
                e.event_id,
                e.minute,
                e.player_1_id,
                e.player_2_id,
                ev.name AS event_name,
                ev.name_en AS event_name_en,
                m.team1_id,
                m.team2_id,
                m.team1_score,
                m.team2_score,
                m.date,
                m.time,
                m.match_status,
                t1.name AS team1_name,
                t1.name_en AS team1_name_en,
                t2.name AS team2_name,
                t2.name_en AS team2_name_en,
                tour.name AS tour_name,
                tour.name_en AS tour_name_en,
                p1.first_name AS player1_first_name,
                p1.last_name AS player1_last_name,
                p2.first_name AS player2_first_name,
                p2.last_name AS player2_last_name
            FROM tournament_match_event e
            INNER JOIN tournament_match_result m ON m.match_id = e.match_id
            LEFT JOIN tbl_event ev ON ev.id = e.event_id
            LEFT JOIN team t1 ON t1.id = m.team1_id
            LEFT JOIN team t2 ON t2.id = m.team2_id
            LEFT JOIN tournament tour ON tour.tour_id = m.tour_id
            LEFT JOIN player p1 ON p1.id = e.player_1_id
            LEFT JOIN player p2 ON p2.id = e.player_2_id
            WHERE e.event_id IN (2, 3, 4)
              AND m.team1_id > 0
              AND m.team2_id > 0
              AND COALESCE(m.update_date, NULLIF(m.date, '')) >= DATE_SUB(NOW(), INTERVAL {$lookbackDays} DAY)
            ORDER BY COALESCE(m.update_date, m.date) DESC, e.match_id DESC, CAST(e.minute AS UNSIGNED) ASC, e.match_event_id ASC
        ";

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        return $this->db->query($sql)->getResultArray();
    }

    private function pendingFinalMatches(int $limit = 0): array
    {
        $lookbackDays = $this->notificationLookbackDays();
        $sql = "
            SELECT
                m.match_id,
                m.tour_id,
                m.team1_id,
                m.team2_id,
                m.team1_score,
                m.team2_score,
                m.team1_penalty,
                m.team2_penalty,
                m.winner_team_id,
                m.is_penalty_used,
                m.match_status,
                m.date,
                m.time,
                t1.name AS team1_name,
                t1.name_en AS team1_name_en,
                t2.name AS team2_name,
                t2.name_en AS team2_name_en,
                tour.name AS tour_name,
                tour.name_en AS tour_name_en
            FROM tournament_match_result m
            LEFT JOIN team t1 ON t1.id = m.team1_id
            LEFT JOIN team t2 ON t2.id = m.team2_id
            LEFT JOIN tournament tour ON tour.tour_id = m.tour_id
            WHERE m.match_status = 3
              AND m.team1_id > 0
              AND m.team2_id > 0
              AND m.team1_score IS NOT NULL
              AND m.team2_score IS NOT NULL
              AND COALESCE(m.update_date, NULLIF(m.date, '')) >= DATE_SUB(NOW(), INTERVAL {$lookbackDays} DAY)
            ORDER BY COALESCE(m.update_date, m.date) DESC, m.match_id DESC
        ";

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        return $this->db->query($sql)->getResultArray();
    }

    private function pendingStandingsMatches(int $limit = 0): array
    {
        $lookbackDays = $this->notificationLookbackDays();
        $sql = "
            SELECT
                m.match_id,
                m.tour_id,
                m.team1_id,
                m.team2_id,
                m.team1_score,
                m.team2_score,
                m.match_status,
                m.date,
                m.time,
                t1.name AS team1_name,
                t1.name_en AS team1_name_en,
                t2.name AS team2_name,
                t2.name_en AS team2_name_en,
                tour.name AS tour_name,
                tour.name_en AS tour_name_en
            FROM tournament_match_result m
            LEFT JOIN team t1 ON t1.id = m.team1_id
            LEFT JOIN team t2 ON t2.id = m.team2_id
            LEFT JOIN tournament tour ON tour.tour_id = m.tour_id
            WHERE m.match_status = 3
              AND m.tour_id > 0
              AND m.team1_id > 0
              AND m.team2_id > 0
              AND COALESCE(m.update_date, NULLIF(m.date, '')) >= DATE_SUB(NOW(), INTERVAL {$lookbackDays} DAY)
            ORDER BY COALESCE(m.update_date, m.date) DESC, m.match_id DESC
        ";

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        return $this->db->query($sql)->getResultArray();
    }

    private function goalRecipients(array $event): array
    {
        $devices = [];
        $teamId = (int) $event['team_id'];
        if ((int) $event['event_id'] === 5) {
            $teamId = $this->otherTeamId($event);
        }

        if ($teamId > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('team', $teamId, 'team_goals', 1)
            );
        }

        if ((int) $event['tour_id'] > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('competition', (int) $event['tour_id'], 'competition_matches', 0)
            );
        }

        if ((int) $event['player_1_id'] > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('player', (int) $event['player_1_id'], 'player_goals', 1)
            );
        }

        return array_values($devices);
    }

    private function kickoffRecipients(array $match): array
    {
        $devices = [];

        foreach ([(int) $match['team1_id'], (int) $match['team2_id']] as $teamId) {
            if ($teamId > 0) {
                $devices = $this->mergeDevices(
                    $devices,
                    $this->devicesForFavorite('team', $teamId, 'team_kickoff', 1)
                );
            }
        }

        if ((int) $match['tour_id'] > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('competition', (int) $match['tour_id'], 'competition_matches', 0)
            );
        }

        return array_values($devices);
    }

    private function lineupRecipients(array $match): array
    {
        $devices = [];

        foreach ([(int) $match['team1_id'], (int) $match['team2_id']] as $teamId) {
            if ($teamId > 0) {
                $devices = $this->mergeDevices(
                    $devices,
                    $this->devicesForFavorite('team', $teamId, 'team_lineups', 1)
                );
            }
        }

        $playerIds = $this->lineupPlayerIds((int) $match['match_id']);
        if (!empty($playerIds)) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavoriteIds('player', $playerIds, 'player_lineups', 1)
            );
        }

        return array_values($devices);
    }

    private function cardRecipients(array $event, string $cardType): array
    {
        $devices = [];
        $teamId = (int) ($event['team_id'] ?? 0);
        $playerId = (int) ($event['player_1_id'] ?? 0);

        if ($cardType === 'red' && $teamId > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('team', $teamId, 'team_red_cards', 1)
            );
        }

        if ((int) $event['tour_id'] > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('competition', (int) $event['tour_id'], 'competition_matches', 0)
            );
        }

        if ($playerId > 0) {
            $preference = $cardType === 'red' ? 'player_red_cards' : 'player_cards';
            $defaultValue = $cardType === 'red' ? 1 : 0;
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('player', $playerId, $preference, $defaultValue)
            );
        }

        return array_values($devices);
    }

    private function substitutionRecipients(array $event): array
    {
        $devices = [];
        $teamId = (int) ($event['team_id'] ?? 0);

        if ($teamId > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('team', $teamId, 'team_substitutions', 0)
            );
        }

        $playerIds = array_values(array_filter([
            (int) ($event['player_1_id'] ?? 0),
            (int) ($event['player_2_id'] ?? 0),
        ]));

        if (!empty($playerIds)) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavoriteIds('player', $playerIds, 'player_substitutions', 0)
            );
        }

        return array_values($devices);
    }

    private function finalRecipients(array $match): array
    {
        $devices = [];
        $team1Id = (int) $match['team1_id'];
        $team2Id = (int) $match['team2_id'];

        if ($team1Id > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('team', $team1Id, 'team_final_score', 1)
            );
        }

        if ($team2Id > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('team', $team2Id, 'team_final_score', 1)
            );
        }

        if ((int) $match['tour_id'] > 0) {
            $devices = $this->mergeDevices(
                $devices,
                $this->devicesForFavorite('competition', (int) $match['tour_id'], 'competition_final_scores', 1)
            );
        }

        return array_values($devices);
    }

    private function standingsRecipients(array $match): array
    {
        if ((int) $match['tour_id'] <= 0) {
            return [];
        }

        return array_values(
            $this->devicesForFavorite('competition', (int) $match['tour_id'], 'competition_standings', 0)
        );
    }

    private function devicesForFavorite(string $favoriteType, int $favoriteId, string $preferenceField, int $defaultValue = 1): array
    {
        $allowedPreferences = [
            'team_kickoff',
            'team_lineups',
            'team_goals',
            'team_red_cards',
            'team_substitutions',
            'team_final_score',
            'competition_matches',
            'competition_final_scores',
            'competition_standings',
            'player_lineups',
            'player_goals',
            'player_red_cards',
            'player_cards',
            'player_substitutions',
        ];
        if (!in_array($preferenceField, $allowedPreferences, true)) {
            return [];
        }

        $sql = "
            SELECT
                d.id,
                d.user_id,
                d.user_type,
                d.token,
                d.platform,
                d.timezone,
                d.locale
            FROM notification_devices d
            LEFT JOIN notification_preferences np
                ON np.user_id = d.user_id
               AND np.user_type = d.user_type
            WHERE d.enabled = 1
              AND d.token IS NOT NULL
              AND d.token <> ''
              AND EXISTS (
                  SELECT 1
                  FROM notification_favorites f
                  WHERE f.user_id = d.user_id
                    AND f.user_type = d.user_type
                    AND f.favorite_type = ?
                    AND f.favorite_id = ?
                    AND COALESCE(np.{$preferenceField}, {$defaultValue}) = 1
              )
        ";

        $rows = $this->db->query($sql, [$favoriteType, $favoriteId])->getResultArray();
        $devices = [];
        foreach ($rows as $row) {
            $devices[(int) $row['id']] = $row;
        }

        return $devices;
    }

    private function devicesForFavoriteIds(string $favoriteType, array $favoriteIds, string $preferenceField, int $defaultValue = 1): array
    {
        $favoriteIds = array_values(array_unique(array_filter(array_map('intval', $favoriteIds))));
        if (empty($favoriteIds)) {
            return [];
        }

        $allowedPreferences = [
            'team_kickoff',
            'team_lineups',
            'team_goals',
            'team_red_cards',
            'team_substitutions',
            'team_final_score',
            'competition_matches',
            'competition_final_scores',
            'competition_standings',
            'player_lineups',
            'player_goals',
            'player_red_cards',
            'player_cards',
            'player_substitutions',
        ];
        if (!in_array($preferenceField, $allowedPreferences, true)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($favoriteIds), '?'));
        $params = array_merge([$favoriteType], $favoriteIds);
        $sql = "
            SELECT
                d.id,
                d.user_id,
                d.user_type,
                d.token,
                d.platform,
                d.timezone,
                d.locale
            FROM notification_devices d
            LEFT JOIN notification_preferences np
                ON np.user_id = d.user_id
               AND np.user_type = d.user_type
            WHERE d.enabled = 1
              AND d.token IS NOT NULL
              AND d.token <> ''
              AND EXISTS (
                  SELECT 1
                  FROM notification_favorites f
                  WHERE f.user_id = d.user_id
                    AND f.user_type = d.user_type
                    AND f.favorite_type = ?
                    AND f.favorite_id IN ({$placeholders})
                    AND COALESCE(np.{$preferenceField}, {$defaultValue}) = 1
              )
        ";

        $rows = $this->db->query($sql, $params)->getResultArray();
        $devices = [];
        foreach ($rows as $row) {
            $devices[(int) $row['id']] = $row;
        }

        return $devices;
    }

    private function lineupPlayerIds(int $matchId): array
    {
        if (!$this->db->tableExists('player_to_match')) {
            return [];
        }

        $rows = $this->db->table('player_to_match')
            ->select('player_id')
            ->where('match_id', $matchId)
            ->get()
            ->getResultArray();

        return array_values(array_unique(array_filter(array_map(
            static fn ($row) => (int) ($row['player_id'] ?? 0),
            $rows
        ))));
    }

    private function buildKickoffMessage(array $match, array $device): array
    {
        $locale = strtolower(substr((string) ($device['locale'] ?? 'th'), 0, 2));
        $isEnglish = $locale === 'en';
        $team1 = $this->teamName($match, 1, $isEnglish);
        $team2 = $this->teamName($match, 2, $isEnglish);
        $time = trim((string) ($match['time'] ?? ''));

        return [
            'title' => $isEnglish ? 'Match starting soon' : 'การแข่งขันใกล้เริ่มแล้ว',
            'body' => trim(sprintf(
                $isEnglish ? '%s vs %s%s' : '%s พบ %s%s',
                $team1,
                $team2,
                $time !== '' ? ' - ' . substr($time, 0, 5) : ''
            )),
        ];
    }

    private function buildLineupMessage(array $match, array $device): array
    {
        $locale = strtolower(substr((string) ($device['locale'] ?? 'th'), 0, 2));
        $isEnglish = $locale === 'en';
        $team1 = $this->teamName($match, 1, $isEnglish);
        $team2 = $this->teamName($match, 2, $isEnglish);

        return [
            'title' => $isEnglish ? 'Lineups are available' : 'รายชื่อนักเตะมาแล้ว',
            'body' => $isEnglish
                ? sprintf('%s vs %s starting lineups are available', $team1, $team2)
                : sprintf('%s พบ %s ประกาศรายชื่อตัวจริงแล้ว', $team1, $team2),
        ];
    }

    private function buildGoalMessage(array $event, array $device): array
    {
        $locale = strtolower(substr((string) ($device['locale'] ?? 'th'), 0, 2));
        $isEnglish = $locale === 'en';
        $team1 = $this->teamName($event, 1, $isEnglish);
        $team2 = $this->teamName($event, 2, $isEnglish);
        $scorer = trim((string) (($event['player1_first_name'] ?? '') . ' ' . ($event['player1_last_name'] ?? '')));
        $minute = trim((string) ($event['minute'] ?? ''));
        $isOwnGoal = (int) $event['event_id'] === 5;
        $scoringTeam = $isOwnGoal
            ? $this->otherTeamName($event, $isEnglish)
            : $this->eventTeamName($event, $isEnglish);

        $title = $isOwnGoal
            ? ($isEnglish ? 'Own goal' : 'ทำเข้าประตูตัวเอง')
            : ($isEnglish ? trim('Goal! ' . $scoringTeam) : trim('ประตู! ' . $scoringTeam));
        $scoreLine = $this->scoreLine($event, $team1, $team2);
        $detail = $isOwnGoal
            ? ($isEnglish
                ? trim('Own goal by ' . $scorer . ($scoringTeam !== '' ? ', goal for ' . $scoringTeam : ''))
                : trim('ทำเข้าประตูตัวเองโดย ' . $scorer . ($scoringTeam !== '' ? ' ส่งผลให้ ' . $scoringTeam . ' ได้ประตู' : '')))
            : ($isEnglish ? 'Scored by ' . $scorer : 'ทำประตูโดย ' . $scorer);

        $body = trim($scoreLine . ' - ' . $detail . ($minute !== '' ? ' (' . $minute . '\')' : ''));

        return [
            'title' => $title,
            'body' => $body,
        ];
    }

    private function buildFinalMessage(array $match, array $device): array
    {
        $locale = strtolower(substr((string) ($device['locale'] ?? 'th'), 0, 2));
        $isEnglish = $locale === 'en';
        $team1 = $this->teamName($match, 1, $isEnglish);
        $team2 = $this->teamName($match, 2, $isEnglish);
        $competition = $this->competitionName($match, $isEnglish);
        $scoreLine = $this->scoreLine($match, $team1, $team2);
        $penaltyLine = $this->penaltyLine($match, $isEnglish);

        $title = $isEnglish ? 'Full time' : 'จบเกม';
        $body = $penaltyLine !== '' ? $scoreLine . ' ' . $penaltyLine : $scoreLine;

        if ($competition !== '') {
            $body = $competition . ' - ' . $body;
        }

        return [
            'title' => $title,
            'body' => $body,
        ];
    }

    private function buildCardMessage(array $event, array $device, string $cardType): array
    {
        $locale = strtolower(substr((string) ($device['locale'] ?? 'th'), 0, 2));
        $isEnglish = $locale === 'en';
        $team1 = $this->teamName($event, 1, $isEnglish);
        $team2 = $this->teamName($event, 2, $isEnglish);
        $player = $this->playerName($event, 1);
        $minute = trim((string) ($event['minute'] ?? ''));
        $isRed = $cardType === 'red';
        $team = $this->eventTeamName($event, $isEnglish);

        $title = $isEnglish
            ? trim(($isRed ? 'Red card' : 'Yellow card') . ($team !== '' ? ': ' . $team : ''))
            : trim(($isRed ? 'ใบแดง' : 'ใบเหลือง') . ($team !== '' ? ': ' . $team : ''));
        $body = sprintf('%s - %s', $this->scoreLine($event, $team1, $team2), $player);

        if ($minute !== '') {
            $body .= ' (' . $minute . '\')';
        }

        return [
            'title' => $title,
            'body' => trim($body),
        ];
    }

    private function buildSubstitutionMessage(array $event, array $device): array
    {
        $locale = strtolower(substr((string) ($device['locale'] ?? 'th'), 0, 2));
        $isEnglish = $locale === 'en';
        $team1 = $this->teamName($event, 1, $isEnglish);
        $team2 = $this->teamName($event, 2, $isEnglish);
        $playerOut = $this->playerName($event, 1);
        $playerIn = $this->playerName($event, 2);
        $minute = trim((string) ($event['minute'] ?? ''));
        $team = $this->eventTeamName($event, $isEnglish);

        $detail = $playerIn !== ''
            ? ($isEnglish ? "{$playerIn} on, {$playerOut} off" : "{$playerIn} ลง, {$playerOut} ออก")
            : $playerOut;
        $body = sprintf('%s - %s', $this->scoreLine($event, $team1, $team2), $detail);

        if ($minute !== '') {
            $body .= ' (' . $minute . '\')';
        }

        return [
            'title' => $isEnglish
                ? trim('Substitution' . ($team !== '' ? ': ' . $team : ''))
                : trim('เปลี่ยนตัว' . ($team !== '' ? ': ' . $team : '')),
            'body' => trim($body),
        ];
    }

    private function buildStandingsMessage(array $match, array $device): array
    {
        $locale = strtolower(substr((string) ($device['locale'] ?? 'th'), 0, 2));
        $isEnglish = $locale === 'en';
        $competition = $this->competitionName($match, $isEnglish);
        $team1 = $this->teamName($match, 1, $isEnglish);
        $team2 = $this->teamName($match, 2, $isEnglish);

        return [
            'title' => $isEnglish ? 'Standings updated' : 'ตารางคะแนนอัปเดต',
            'body' => $competition !== ''
                ? ($isEnglish
                    ? sprintf('%s standings updated after %s vs %s', $competition, $team1, $team2)
                    : sprintf('%s อัปเดตอันดับล่าสุดหลังจบเกม %s พบ %s', $competition, $team1, $team2))
                : ($isEnglish
                    ? sprintf('Standings updated after %s vs %s', $team1, $team2)
                    : sprintf('ตารางคะแนนอัปเดตหลังจบเกม %s พบ %s', $team1, $team2)),
        ];
    }

    private function buildKickoffPayload(array $match, array $device): array
    {
        return [
            'type' => 'match_kickoff',
            'matchId' => (string) $match['match_id'],
            'tourId' => (string) $match['tour_id'],
            'team1Id' => (string) $match['team1_id'],
            'team2Id' => (string) $match['team2_id'],
            'locale' => (string) ($device['locale'] ?? 'th'),
        ];
    }

    private function buildLineupPayload(array $match, array $device): array
    {
        return [
            'type' => 'match_lineup',
            'matchId' => (string) $match['match_id'],
            'tourId' => (string) $match['tour_id'],
            'team1Id' => (string) $match['team1_id'],
            'team2Id' => (string) $match['team2_id'],
            'locale' => (string) ($device['locale'] ?? 'th'),
        ];
    }

    private function buildGoalPayload(array $event, array $device): array
    {
        return [
            'type' => 'match_goal',
            'matchId' => (string) $event['match_id'],
            'eventId' => (string) $event['match_event_id'],
            'tourId' => (string) $event['tour_id'],
            'teamId' => (string) $event['team_id'],
            'playerId' => (string) ($event['player_1_id'] ?? ''),
            'locale' => (string) ($device['locale'] ?? 'th'),
        ];
    }

    private function buildFinalPayload(array $match, array $device): array
    {
        return [
            'type' => 'match_final_score',
            'matchId' => (string) $match['match_id'],
            'tourId' => (string) $match['tour_id'],
            'team1Id' => (string) $match['team1_id'],
            'team2Id' => (string) $match['team2_id'],
            'locale' => (string) ($device['locale'] ?? 'th'),
        ];
    }

    private function buildCardPayload(array $event, array $device, string $cardType): array
    {
        return [
            'type' => $cardType === 'red' ? 'match_red_card' : 'match_yellow_card',
            'matchId' => (string) $event['match_id'],
            'eventId' => (string) $event['match_event_id'],
            'tourId' => (string) $event['tour_id'],
            'teamId' => (string) $event['team_id'],
            'playerId' => (string) ($event['player_1_id'] ?? ''),
            'cardType' => $cardType,
            'locale' => (string) ($device['locale'] ?? 'th'),
        ];
    }

    private function buildSubstitutionPayload(array $event, array $device): array
    {
        return [
            'type' => 'match_substitution',
            'matchId' => (string) $event['match_id'],
            'eventId' => (string) $event['match_event_id'],
            'tourId' => (string) $event['tour_id'],
            'teamId' => (string) $event['team_id'],
            'playerId' => (string) ($event['player_1_id'] ?? ''),
            'playerInId' => (string) ($event['player_2_id'] ?? ''),
            'locale' => (string) ($device['locale'] ?? 'th'),
        ];
    }

    private function buildStandingsPayload(array $match, array $device): array
    {
        return [
            'type' => 'competition_standings',
            'matchId' => (string) $match['match_id'],
            'competitionId' => (string) $match['tour_id'],
            'tourId' => (string) $match['tour_id'],
            'competitionName' => $this->competitionName($match, strtolower(substr((string) ($device['locale'] ?? 'th'), 0, 2)) === 'en'),
            'locale' => (string) ($device['locale'] ?? 'th'),
        ];
    }

    private function sendTracked(array $device, string $eventKey, string $referenceType, int $referenceId, string $title, string $body, array $payload): array
    {
        $now = date('Y-m-d H:i:s');
        $log = $this->db->table('notification_dispatch_logs')
            ->where('event_key', $eventKey)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('device_id', (int) $device['id'])
            ->get()
            ->getRowArray();

        if ($log && ($log['status'] ?? null) === 'success') {
            return ['ok' => true, 'log_id' => (int) $log['id'], 'skipped' => true];
        }

        $logPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $baseData = [
            'event_key' => $eventKey,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'device_id' => (int) $device['id'],
            'user_id' => (int) $device['user_id'],
            'user_type' => (string) $device['user_type'],
            'locale' => (string) ($device['locale'] ?? 'th'),
            'title' => $title,
            'body' => $body,
            'payload_json' => $logPayload,
            'last_attempt_at' => $now,
            'updated_at' => $now,
        ];

        if ($log) {
            $baseData['attempt_count'] = ((int) $log['attempt_count']) + 1;
            $baseData['status'] = 'sending';
            $this->db->table('notification_dispatch_logs')
                ->where('id', (int) $log['id'])
                ->update($baseData);
            $logId = (int) $log['id'];
        } else {
            $baseData['attempt_count'] = 1;
            $baseData['status'] = 'sending';
            $baseData['created_at'] = $now;
            $this->db->table('notification_dispatch_logs')->insert($baseData);
            $logId = (int) $this->db->insertID();
        }

        try {
            $result = $this->publisher->sendToToken($device['token'], $title, $body, $payload);
            $this->db->table('notification_dispatch_logs')
                ->where('id', $logId)
                ->update([
                    'status' => 'success',
                    'response_json' => json_encode($result, JSON_UNESCAPED_UNICODE),
                    'sent_at' => $now,
                    'updated_at' => $now,
                    'last_error' => null,
                ]);

            return ['ok' => true, 'log_id' => $logId];
        } catch (Throwable $e) {
            if ($this->isInvalidFcmTokenError($e->getMessage())) {
                $this->db->table('notification_devices')
                    ->where('id', (int) $device['id'])
                    ->update([
                        'enabled' => 0,
                        'updated_at' => $now,
                    ]);
            }

            $this->db->table('notification_dispatch_logs')
                ->where('id', $logId)
                ->update([
                    'status' => 'failed',
                    'last_error' => $e->getMessage(),
                    'updated_at' => $now,
                ]);

            return ['ok' => false, 'log_id' => $logId, 'error' => $e->getMessage()];
        }
    }

    private function isInvalidFcmTokenError(string $message): bool
    {
        $message = strtolower($message);
        $markers = [
            'unregistered',
            'registration-token-not-registered',
            'requested entity was not found',
            'invalid registration token',
        ];

        foreach ($markers as $marker) {
            if (strpos($message, $marker) !== false) {
                return true;
            }
        }

        return false;
    }

    private function cardType(array $event): ?string
    {
        $name = $this->normalizedEventName($event);
        if (strpos($name, 'red') !== false || strpos($name, 'ใบแดง') !== false) {
            return 'red';
        }

        if (strpos($name, 'yellow') !== false || strpos($name, 'ใบเหลือง') !== false) {
            return 'yellow';
        }

        $eventId = (int) ($event['event_id'] ?? 0);
        if ($eventId === 4) {
            return 'red';
        }

        if ($eventId === 3) {
            return 'yellow';
        }

        return null;
    }

    private function isSubstitutionEvent(array $event): bool
    {
        $name = $this->normalizedEventName($event);
        if (
            strpos($name, 'substitution') !== false ||
            strpos($name, 'subsitation') !== false ||
            strpos($name, 'เปลี่ยน') !== false
        ) {
            return true;
        }

        return (int) ($event['event_id'] ?? 0) === 2;
    }

    private function normalizedEventName(array $event): string
    {
        return strtolower(trim((string) (
            $event['event_name_en']
            ?? $event['event_name']
            ?? ''
        )));
    }

    private function mergeDevices(array $current, array $incoming): array
    {
        foreach ($incoming as $deviceId => $device) {
            $current[(int) $deviceId] = $device;
        }

        return $current;
    }

    private function notificationLookbackDays(): int
    {
        return max(1, (int) env('NOTIFICATION_MATCH_LOOKBACK_DAYS', 7));
    }

    private function unsentDevices(array $devices, string $eventKey, string $referenceType, int $referenceId): array
    {
        if (empty($devices)) {
            return [];
        }

        $deviceIds = array_values(array_unique(array_filter(array_map(
            static fn ($device) => (int) ($device['id'] ?? 0),
            $devices
        ))));
        if (empty($deviceIds)) {
            return [];
        }

        $logs = $this->db->table('notification_dispatch_logs')
            ->select('device_id')
            ->where('event_key', $eventKey)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('status', 'success')
            ->whereIn('device_id', $deviceIds)
            ->get()
            ->getResultArray();

        $sentDeviceIds = [];
        foreach ($logs as $log) {
            $sentDeviceIds[(int) $log['device_id']] = true;
        }

        if (empty($sentDeviceIds)) {
            return array_values($devices);
        }

        return array_values(array_filter(
            $devices,
            static fn ($device) => empty($sentDeviceIds[(int) ($device['id'] ?? 0)])
        ));
    }

    private function otherTeamId(array $event): int
    {
        $teamId = (int) ($event['team_id'] ?? 0);
        $team1Id = (int) ($event['team1_id'] ?? 0);
        $team2Id = (int) ($event['team2_id'] ?? 0);

        if ($teamId > 0 && $team1Id > 0 && $team2Id > 0) {
            if ($teamId === $team1Id) {
                return $team2Id;
            }

            if ($teamId === $team2Id) {
                return $team1Id;
            }
        }

        return 0;
    }

    private function eventTeamName(array $row, bool $isEnglish): string
    {
        $teamId = (int) ($row['team_id'] ?? 0);
        if ($teamId > 0 && $teamId === (int) ($row['team1_id'] ?? 0)) {
            return $this->teamName($row, 1, $isEnglish);
        }

        if ($teamId > 0 && $teamId === (int) ($row['team2_id'] ?? 0)) {
            return $this->teamName($row, 2, $isEnglish);
        }

        return '';
    }

    private function otherTeamName(array $event, bool $isEnglish): string
    {
        $otherTeamId = $this->otherTeamId($event);
        if ($otherTeamId > 0 && $otherTeamId === (int) ($event['team1_id'] ?? 0)) {
            return $this->teamName($event, 1, $isEnglish);
        }

        if ($otherTeamId > 0 && $otherTeamId === (int) ($event['team2_id'] ?? 0)) {
            return $this->teamName($event, 2, $isEnglish);
        }

        return '';
    }

    private function scoreLine(array $row, string $team1, string $team2): string
    {
        $score1 = (string) ($row['team1_score'] ?? '0');
        $score2 = (string) ($row['team2_score'] ?? '0');

        return sprintf('%s %s-%s %s', $team1, $score1, $score2, $team2);
    }

    private function penaltyLine(array $row, bool $isEnglish): string
    {
        if ((int) ($row['is_penalty_used'] ?? 0) !== 1) {
            return '';
        }

        $penalty1 = trim((string) ($row['team1_penalty'] ?? ''));
        $penalty2 = trim((string) ($row['team2_penalty'] ?? ''));
        if ($penalty1 === '' || $penalty2 === '') {
            return '';
        }

        return $isEnglish
            ? sprintf('(penalties %s-%s)', $penalty1, $penalty2)
            : sprintf('(จุดโทษ %s-%s)', $penalty1, $penalty2);
    }

    private function playerName(array $row, int $playerIndex): string
    {
        $firstKey = $playerIndex === 1 ? 'player1_first_name' : 'player2_first_name';
        $lastKey = $playerIndex === 1 ? 'player1_last_name' : 'player2_last_name';

        return trim((string) (($row[$firstKey] ?? '') . ' ' . ($row[$lastKey] ?? '')));
    }

    private function teamName(array $row, int $teamIndex, bool $isEnglish): string
    {
        $englishKey = $teamIndex === 1 ? 'team1_name_en' : 'team2_name_en';
        $localKey = $teamIndex === 1 ? 'team1_name' : 'team2_name';
        $value = $isEnglish ? ($row[$englishKey] ?? '') : ($row[$localKey] ?? '');
        if ($value === '') {
            $value = $row[$localKey] ?? ($row[$englishKey] ?? '');
        }

        return trim((string) $value);
    }

    private function competitionName(array $row, bool $isEnglish): string
    {
        $value = $isEnglish ? ($row['tour_name_en'] ?? '') : ($row['tour_name'] ?? '');
        if ($value === '') {
            $value = $row['tour_name'] ?? ($row['tour_name_en'] ?? '');
        }

        return trim((string) $value);
    }
}
