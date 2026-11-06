<?php

namespace Modules\Admin\Models\Traits;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\Traits\TournamentScoringQueriesTrait;
use Modules\Admin\Models\Traits\TournamentMatchQueriesTrait;

trait TournamentSubmissionQueriesTrait
{
    /**
     * Submit player list for a tournament team
     * @param int $tour_team_id
     * @return bool
     */
    function submitPlayerListStatus($tour_team_id)
    {
        $builder = $this->db->table('tournament_team');
        $builder->set('player_list_status', 1);
        $builder->set('player_list_submitted_at', date('Y-m-d H:i:s'));
        $builder->where('tour_team_id', $tour_team_id);
        return $builder->update();
    }

    /**
     * Save submitted players for a tournament
     * @param int $tour_team_id
     * @param array $player_ids
     * @return bool
     */
    function saveSubmittedPlayers($tour_team_id, $player_ids)
    {
        //die(print_r($player_ids));
        if (empty($tour_team_id)) {
            return false;
        }

        // เริ่ม Transaction เพื่อความปลอดภัย (ถ้าพังจะ Rollback ข้อมูลเก่ากลับมา)
        $this->db->transStart();

        $builder = $this->db->table('tournament_submitted_players');

        // 1. ลบข้อมูลเดิมที่มีอยู่ของทีมทัวร์นาเมนต์นี้ออกก่อน
        $builder->where('tour_team_id', $tour_team_id)->delete();

        // 2. เตรียมข้อมูลและ Insert ทีละตัวตามคำขอ (Loop Insert)
        $player_ids = is_array($player_ids) ? array_unique(array_filter($player_ids)) : [];
        foreach ($player_ids as $player_id) {
            //die(print_r($player_id));
            if (!empty($player_id)) {
                $data = [
                    'tour_team_id' => $tour_team_id,
                    'player_id'    => $player_id,
                    'created_at'   => date('Y-m-d H:i:s')
                ];
                $this->db->table('tournament_submitted_players')->insert($data);
            }
        }

        // จบ Transaction
        $this->db->transComplete();

        // คืนค่าสถานะ (true ถ้าสำเร็จทั้งหมด, false ถ้ามีอะไรพังและ Rollback แล้ว)
        return $this->db->transStatus();
    }

    /**
     * Get submitted players for a tournament
     * @param int $tour_team_id
     * @return array
     */
    function getSubmittedPlayers($tour_team_id)
    {
        $builder = $this->db->table('tournament_submitted_players');
        $result = $builder->select('player_id')
            ->where('tour_team_id', $tour_team_id)
            ->get()
            ->getResultArray();

        return array_column($result, 'player_id');
    }

    /**
     * ดึงข้อมูลนักเตะที่ส่งแล้วพร้อมรายละเอียด (สำหรับ Academy view)
     */
    function getSubmittedPlayersWithDetails($tour_team_id)
    {
        $players = $this->db->table('tournament_submitted_players tsp')
            ->select('p.id, p.first_name, p.last_name, COALESCE(ape_spd.shirt_number, p.shirt_number) as shirt_number, p.image_path', false)
            ->join('player p', 'p.id = tsp.player_id', 'LEFT')
            ->join('tournament_team tt_spd', 'tt_spd.tour_team_id = tsp.tour_team_id', 'left', false)
            ->join('academy_player_enrollments ape_spd', 'ape_spd.player_id = p.id AND ape_spd.team_id = tt_spd.team_id AND ape_spd.is_active = 1', 'left', false)
            ->where('tsp.tour_team_id', $tour_team_id)
            ->orderBy('CAST(COALESCE(ape_spd.shirt_number, p.shirt_number) AS UNSIGNED)', 'ASC', false)
            ->get()
            ->getResultArray();

        // Censor last names for display
        helper('academy');
        foreach ($players as &$player) {
            if (!empty($player['last_name'])) {
                $player['last_name'] = mask_last_name($player['last_name']);
            }
        }

        return $players;
    }

    /**
     * ตรวจสอบนักเตะซ้ำข้ามทีมในทัวร์นาเมนต์เดียวกัน (ตรวจจาก id_card)
     * ไม่รวมนักเตะของทีมตัวเอง (เพื่อให้แก้ไขรายชื่อได้)
     * นักเตะที่ไม่มี id_card จะไม่ถูกตรวจ
     *
     * @param int $tourId - Tournament ID
     * @param int $tourTeamId - Current team's tour_team_id (exclude own submissions)
     * @param array $playerIds - Player IDs the team wants to submit
     * @param array $linkedTourIds - Additional tournament IDs to check across (cross-tournament)
     * @return array - Array of duplicate info: [{player_name, id_card, claimed_by_team}]
     */
    /**
     * Team ids that belong to the same parent/sub family as the team behind $tourTeamId.
     * A player shared between a parent team and its sub-teams is the SAME club, not a
     * cross-team duplicate — every roster/export path already treats parent+sub as one
     * pool, so the duplicate check must too.
     */
    /** cache: team_id => family team ids (parent + subs) */
    protected $dupCheckFamilyCache = [];

    public function getTeamFamilyIds($tourTeamId): array
    {
        $row = $this->db->table('tournament_team')->select('team_id')->where('tour_team_id', $tourTeamId)->get()->getRowArray();
        return $this->getTeamFamilyIdsByTeam((int) ($row['team_id'] ?? 0));
    }

    /** family (สโมสรเดียวกัน: parent + subs) จาก team_id ตรง ๆ */
    public function getTeamFamilyIdsByTeam(int $teamId): array
    {
        if ($teamId <= 0) return [];
        if (isset($this->dupCheckFamilyCache[$teamId])) return $this->dupCheckFamilyCache[$teamId];
        $ti   = $this->db->table('team')->select('id, parent_team_id')->where('id', $teamId)->get()->getRowArray();
        $root = (!empty($ti['parent_team_id'])) ? (int) $ti['parent_team_id'] : $teamId;
        $rows = $this->db->table('team')->select('id')
            ->groupStart()->where('id', $root)->orWhere('parent_team_id', $root)->groupEnd()
            ->get()->getResultArray();
        return $this->dupCheckFamilyCache[$teamId] = array_map('intval', array_column($rows, 'id'));
    }

    /**
     * claim ของทีมยัง "จริง" ไหม = นักเตะยังมี active enrollment ในทีมนั้น (หรือ family เดียวกัน)
     * ใช้แทน stale-guard เดิมที่เช็ค chart_selection JSON ซึ่งไม่ถูกล้างตอนลบนักเตะ → false positive
     * (v2: academy_player_enrollments เป็น source of truth ของ roster)
     */
    public function isPlayerActiveInTeamFamily(int $playerId, int $claimingTeamId): bool
    {
        if ($playerId <= 0 || $claimingTeamId <= 0) return false;
        $fam = $this->getTeamFamilyIdsByTeam($claimingTeamId);
        if (empty($fam)) $fam = [$claimingTeamId];
        return $this->db->table('academy_player_enrollments')
            ->where('player_id', $playerId)
            ->whereIn('team_id', $fam)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }

    /**
     * ชื่อ academy (สังกัด) ของทีมที่ claim — ใช้แสดงใน modal ให้ชัด
     * ทีมลูก (sub) ที่ดึงนักเตะจากทีมแม่จะไม่มี roster ของตัวเอง → โชว์ academy แม่ให้ผู้ใช้เข้าใจ
     */
    public function getTeamAcademyName(int $teamId): string
    {
        if ($teamId <= 0) return '';
        $fam = $this->getTeamFamilyIdsByTeam($teamId);
        if (empty($fam)) $fam = [$teamId];
        $row = $this->db->table('academy_teams at')
            ->select('a.name AS academy_name')
            ->join('academy a', 'a.id = at.academy_id', 'inner')
            ->whereIn('at.team_id', $fam)
            ->limit(1)
            ->get()->getRowArray();
        return $row['academy_name'] ?? '';
    }

    /**
     * ทีม "ปัจจุบัน" ที่นักเตะ active อยู่จริงภายใน family ของทีมที่ claim
     * (นักเตะอาจถูก submit โดยทีมลูก/ทีมเดิม แต่ตอนนี้ย้ายไปอีกทีมในสโมสรเดียวกัน)
     * → ใช้แสดงใน modal ให้แอดมินไล่หาตัวถูก · primary ก่อน แล้ว active ใด ๆ
     */
    public function getPlayerCurrentTeamInFamily(int $playerId, int $claimingTeamId): array
    {
        if ($playerId <= 0 || $claimingTeamId <= 0) return [];
        $fam = $this->getTeamFamilyIdsByTeam($claimingTeamId);
        if (empty($fam)) $fam = [$claimingTeamId];
        $row = $this->db->table('academy_player_enrollments e')
            ->select('e.team_id, t.name AS team_name')
            ->join('team t', 't.id = e.team_id', 'left')
            ->where('e.player_id', $playerId)
            ->whereIn('e.team_id', $fam)
            ->where('e.is_active', 1)
            ->orderBy('e.is_primary', 'DESC')
            ->orderBy('e.enrolled_at', 'DESC')
            ->limit(1)
            ->get()->getRowArray();
        return $row ?: [];
    }

    function checkDuplicatePlayers($tourId, $tourTeamId, $playerIds, $linkedTourIds = [])
    {
        if (empty($playerIds)) return [];

        // ดึง id_card ของนักเตะที่จะส่ง (ข้ามที่ id_card ว่าง)
        $submittingPlayers = $this->db->table('player')
            ->select('id, first_name, last_name, id_card')
            ->whereIn('id', $playerIds)
            ->where('id_card IS NOT NULL')
            ->where('id_card !=', '')
            ->get()
            ->getResultArray();

        if (empty($submittingPlayers)) return [];

        // id_card เก็บปนกัน (มีขีด/ไม่มีขีด/มีช่องว่าง) → normalize ตัด - และช่องว่าง ทั้งสองฝั่งตอนเทียบ
        $normId = static fn($v) => str_replace(['-', ' '], '', (string) $v);

        // สร้าง lookup: id_card (normalize) => player info + list เลข normalize สำหรับ query
        $idCardsNorm = [];
        $submittingLookup = [];
        foreach ($submittingPlayers as $sp) {
            $k = $normId($sp['id_card']);
            if ($k === '') continue;
            $submittingLookup[$k] = $sp;
            $idCardsNorm[$k] = true;
        }
        $idCardsNorm = array_keys($idCardsNorm);
        if (empty($idCardsNorm)) return [];

        // รวมทัวร์นาเมนต์ที่ต้องเช็ค: ตัวเอง + linked tournaments
        $checkTourIds = [$tourId];
        if (!empty($linkedTourIds)) {
            $checkTourIds = array_merge($checkTourIds, $linkedTourIds);
            $checkTourIds = array_unique($checkTourIds);
        }

        // ค้นหานักเตะที่ถูกส่งแล้วในทัวร์นาเมนต์ที่ต้องตรวจ โดยทีมอื่น
        $builder = $this->db->table('tournament_submitted_players tsp');
        $builder->select('tsp.player_id, tsp.created_at as submitted_at, p.first_name, p.last_name, p.id_card, t.name as team_name, tt.team_id as claiming_team_id, tour.name as tournament_name, tour.generation as tournament_generation');
        $builder->join('player p', 'p.id = tsp.player_id', 'INNER');
        $builder->join('tournament_team tt', 'tt.tour_team_id = tsp.tour_team_id', 'INNER');
        $builder->join('team t', 't.id = tt.team_id', 'LEFT');
        $builder->join('tournament tour', 'tour.tour_id = tt.tour_id', 'LEFT');
        $builder->whereIn('tt.tour_id', $checkTourIds);
        $builder->where('tsp.tour_team_id !=', $tourTeamId);
        // exclude the submitting team's own parent/sub family (same club ≠ duplicate)
        $familyIds = $this->getTeamFamilyIds($tourTeamId);
        if (!empty($familyIds)) {
            $builder->whereNotIn('tt.team_id', $familyIds);
        }
        // เทียบแบบ normalize (ตัด - และช่องว่างที่คอลัมน์) — escape ค่าเองให้ปลอดภัย
        $escapedIds = array_map(fn($v) => $this->db->escape($v), $idCardsNorm);
        $builder->where(
            "REPLACE(REPLACE(p.id_card, '-', ''), ' ', '') IN (" . implode(',', $escapedIds) . ")",
            null,
            false
        );
        $builder->where('p.id_card IS NOT NULL');
        $builder->where('p.id_card !=', '');

        $existingDuplicates = $builder->get()->getResultArray();

        // Stale guard (v2 enrollments-as-truth): นับเป็น duplicate เฉพาะเมื่อทีมที่ claim
        // ยังมีนักเตะคนนี้เป็น active enrollment จริง (ในทีมหรือ family เดียวกัน)
        // เดิมเช็ค chart_selection JSON ซึ่งไม่ถูกล้างตอนทีมลบนักเตะ → claim ค้าง block ทีมอื่นผิด
        $existingDuplicates = array_filter($existingDuplicates, function ($dup) {
            return $this->isPlayerActiveInTeamFamily(
                (int) ($dup['player_id'] ?? 0),
                (int) ($dup['claiming_team_id'] ?? 0)
            );
        });

        // จับคู่กลับกับนักเตะที่กำลังจะส่ง
        $duplicates = [];
        $processedIdCards = [];
        foreach ($existingDuplicates as $dup) {
            $dupKey = $normId($dup['id_card']);
            if (in_array($dupKey, $processedIdCards)) continue;
            $matchingSubmitter = $submittingLookup[$dupKey] ?? null;
            if ($matchingSubmitter) {
                $tourInfo = $dup['tournament_name'] ?? '';
                if (!empty($dup['tournament_generation'])) {
                    $tourInfo .= ' (' . $dup['tournament_generation'] . ')';
                }
                // แปลงวันเวลาที่ส่ง
                $submittedAt = '';
                if (!empty($dup['submitted_at'])) {
                    $submittedAt = date('d/m/Y H:i', strtotime($dup['submitted_at']));
                }
                // ทีมปัจจุบันที่นักเตะ active อยู่ (อาจย้ายจากทีมที่ submit เดิม) → ให้แอดมินไล่หาถูก
                $curTeam = $this->getPlayerCurrentTeamInFamily((int) ($dup['player_id'] ?? 0), (int) ($dup['claiming_team_id'] ?? 0));
                $currentTeamName = !empty($curTeam['team_name']) ? $curTeam['team_name'] : $dup['team_name'];
                $duplicates[] = [
                    'player_id'         => $matchingSubmitter['id'],
                    'player_name'       => $matchingSubmitter['first_name'] . ' ' . $matchingSubmitter['last_name'],
                    'id_card'           => $dup['id_card'],
                    'claimed_by_team'   => $currentTeamName,
                    'submitted_by_team' => $dup['team_name'],
                    'claimed_by_academy'=> $this->getTeamAcademyName((int) ($dup['claiming_team_id'] ?? 0)),
                    'tournament_name'   => $tourInfo,
                    'submitted_at'      => $submittedAt,
                ];
                $processedIdCards[] = $dupKey;
            }
        }

        return $duplicates;
    }

    /**
     * ดึงรายการทัวร์นาเมนต์ที่เชื่อมไว้สำหรับตรวจสอบนักเตะซ้ำข้ามรายการ
     */
    function getLinkedTournaments($tourId)
    {
        if (empty($tourId)) return [];
        return $this->db->table('tournament_duplicate_check_link')
            ->select('linked_tour_id')
            ->where('tour_id', $tourId)
            ->get()
            ->getResultArray();
    }

    /**
     * บันทึก linked tournaments สำหรับตรวจสอบนักเตะซ้ำข้ามรายการ (ลบเก่า + insert ใหม่)
     */
    function saveLinkedTournaments($tourId, $linkedTourIds)
    {
        if (empty($tourId)) return;

        $this->db->table('tournament_duplicate_check_link')
            ->where('tour_id', $tourId)
            ->delete();

        if (!empty($linkedTourIds)) {
            $batch = [];
            foreach ($linkedTourIds as $linkedId) {
                if ($linkedId != $tourId) { // ไม่ link กับตัวเอง
                    $batch[] = [
                        'tour_id' => $tourId,
                        'linked_tour_id' => $linkedId,
                    ];
                }
            }
            if (!empty($batch)) {
                $this->db->table('tournament_duplicate_check_link')->insertBatch($batch);
            }
        }
    }

    /**
     * ดึงรายการทัวร์นาเมนต์ต้นทางสำหรับ whitelist นักเตะ (require_source_players)
     */
    function getSourceTournaments($tourId)
    {
        if (empty($tourId)) return [];
        return $this->db->table('tournament_source_check_link')
            ->select('source_tour_id')
            ->where('tour_id', $tourId)
            ->get()
            ->getResultArray();
    }

    /**
     * บันทึกทัวร์นาเมนต์ต้นทางสำหรับ whitelist นักเตะ (ลบเก่า + insert ใหม่)
     */
    function saveSourceTournaments($tourId, $sourceTourIds)
    {
        if (empty($tourId)) return;

        // Transaction: delete + insert ต้อง atomic (กัน source หายหมดถ้า insert fail)
        $this->db->transStart();

        $this->db->table('tournament_source_check_link')
            ->where('tour_id', $tourId)
            ->delete();

        if (!empty($sourceTourIds)) {
            $batch = [];
            // dedupe กันชน UNIQUE KEY (tour_id, source_tour_id)
            foreach (array_unique($sourceTourIds) as $sourceId) {
                if ($sourceId != $tourId) { // ไม่ link กับตัวเอง
                    $batch[] = [
                        'tour_id' => $tourId,
                        'source_tour_id' => $sourceId,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }
            if (!empty($batch)) {
                $this->db->table('tournament_source_check_link')->insertBatch($batch);
            }
        }

        $this->db->transComplete();
    }

    /**
     * ข้อมูลนักเตะที่เคยถูกส่งรายชื่อในทัวร์นาเมนต์ต้นทาง (require_source_players)
     * แยกเป็น 2 ชุดตามทีมที่ส่ง:
     *  - 'own'   = เคยส่งโดยทีมเดียวกันเป๊ะ ($ownTeamId) → ชุดเดิม ได้รับยกเว้น
     *  - 'other' = เคยส่งโดยทีมอื่น → เข้าข่ายถูกบล็อค
     * นักเตะคนเดียวอาจอยู่ทั้งสองชุด (เคยส่งทั้งกับทีมเราและทีมอื่น) — ฝั่ง resolve ให้ own ชนะ
     * ถ้าส่ง $ownTourTeamId มา รายชื่อที่ทีมเคยส่งไว้แล้วในทัวร์ปลายทางจะถูกรวมเข้าชุด own ด้วย
     * (กรณีแก้ไขรายชื่อ — คนที่อยู่ในรายชื่อเดิมของทัวร์นี้ต้องไม่โดนบล็อคย้อนหลัง)
     *
     * @return array ['own' => ['player_ids' => int[], 'id_cards' => string[]], 'other' => [...]]
     *               (id_card ว่าง/NULL ถูกตัดทิ้ง)
     */
    function getSourceSubmissionSets(array $sourceTourIds, int $ownTeamId, $ownTourTeamId = null): array
    {
        $empty = ['own' => ['player_ids' => [], 'id_cards' => []], 'other' => ['player_ids' => [], 'id_cards' => []]];
        if (empty($sourceTourIds)) return $empty;

        $rows = $this->db->table('tournament_submitted_players tsp')
            ->select('DISTINCT tsp.player_id, p.id_card, tt.team_id', false)
            ->join('tournament_team tt', 'tt.tour_team_id = tsp.tour_team_id', 'INNER')
            ->join('player p', 'p.id = tsp.player_id', 'INNER')
            ->whereIn('tt.tour_id', $sourceTourIds)
            ->get()
            ->getResultArray();

        $sets = $empty;
        foreach ($rows as $r) {
            $bucket = ((int) $r['team_id'] === $ownTeamId) ? 'own' : 'other';
            $sets[$bucket]['player_ids'][] = (int) $r['player_id'];
            $idCard = trim((string) ($r['id_card'] ?? ''));
            if ($idCard !== '') {
                $sets[$bucket]['id_cards'][] = $idCard;
            }
        }

        // ชุดเดิมของทัวร์ปลายทาง: รายชื่อที่ tour_team นี้ส่งไว้แล้ว → ยกเว้นเสมอ
        if (!empty($ownTourTeamId)) {
            $rosterRows = $this->db->table('tournament_submitted_players tsp')
                ->select('DISTINCT tsp.player_id, p.id_card', false)
                ->join('player p', 'p.id = tsp.player_id', 'INNER')
                ->where('tsp.tour_team_id', $ownTourTeamId)
                ->get()
                ->getResultArray();
            foreach ($rosterRows as $r) {
                $sets['own']['player_ids'][] = (int) $r['player_id'];
                $idCard = trim((string) ($r['id_card'] ?? ''));
                if ($idCard !== '') {
                    $sets['own']['id_cards'][] = $idCard;
                }
            }
        }

        foreach ($sets as $key => $set) {
            $sets[$key]['player_ids'] = array_values(array_unique($set['player_ids']));
            $sets[$key]['id_cards']   = array_values(array_unique($set['id_cards']));
        }
        return $sets;
    }

    /**
     * คืน player_id ใน $candidateIds ที่ถูกบล็อค:
     * id ตรงกับ blocked player_ids หรือ id_card (ไม่ว่าง) ตรงกับ blocked id_cards
     */
    function resolveBlockedPlayerIds(array $candidateIds, array $blocked): array
    {
        $candidateIds = array_values(array_unique(array_map('intval', $candidateIds)));
        if (empty($candidateIds)) return [];

        $blockedIds   = array_map('intval', $blocked['player_ids'] ?? []);
        $blockedCards = $blocked['id_cards'] ?? [];

        $direct = array_values(array_intersect($candidateIds, $blockedIds));

        $byCard = [];
        if (!empty($blockedCards)) {
            $rows = $this->db->table('player')
                ->select('id')
                ->whereIn('id', $candidateIds)
                ->whereIn('id_card', $blockedCards)
                ->where('id_card IS NOT NULL', null, false)
                ->where('id_card !=', '')
                ->get()
                ->getResultArray();
            $byCard = array_map('intval', array_column($rows, 'id'));
        }

        return array_values(array_unique(array_merge($direct, $byCard)));
    }

    /**
     * ตัดสินนักเตะที่ส่งไม่ได้จาก 2 ชุดของ getSourceSubmissionSets:
     * เคยส่งกับทีมอื่น (other) และไม่เคยส่งกับทีมเรา (own) → ส่งไม่ได้
     * เคยส่งกับทีมเรา → ยกเว้นเสมอ (ชุดเดิม), ไม่เคยส่งเลย → ผ่าน
     */
    function resolveSourceIneligibleIds(array $candidateIds, array $sets): array
    {
        $ownMatched   = $this->resolveBlockedPlayerIds($candidateIds, $sets['own'] ?? []);
        $otherMatched = $this->resolveBlockedPlayerIds($candidateIds, $sets['other'] ?? []);
        return array_values(array_diff($otherMatched, $ownMatched));
    }

    /**
     * ประวัติการถูกส่งรายชื่อจริงในทัวร์ต้นทาง "โดยทีมอื่น" สำหรับแสดงบน UI
     * (แจ้งผู้ใช้ว่านักเตะที่โดนบล็อค เคยส่งรายการไหน โดยทีมไหน วันที่เท่าไหร่)
     * จับคู่ด้วย player_id ตรง หรือ id_card ตรง (ข้ามค่าว่าง)
     *
     * @return array map player_id => [{tournament, team, date}] (date เป็นวันที่ไทย อาจว่าง)
     */
    function getSourceSubmissionHistory(array $playerIds, array $sourceTourIds, int $excludeTeamId): array
    {
        if (empty($playerIds) || empty($sourceTourIds)) return [];
        $playerIds = array_values(array_unique(array_map('intval', $playerIds)));

        // id_card ของนักเตะที่ขอ → map บัตร -> player_id (สำหรับโยงประวัติของ record อื่นที่เป็นคนเดียวกัน)
        $cardRows = $this->db->table('player')
            ->select('id, id_card')
            ->whereIn('id', $playerIds)
            ->get()
            ->getResultArray();
        $cardToIds = [];
        foreach ($cardRows as $r) {
            $card = trim((string) ($r['id_card'] ?? ''));
            if ($card !== '') {
                $cardToIds[$card][] = (int) $r['id'];
            }
        }

        $builder = $this->db->table('tournament_submitted_players tsp')
            ->select('tsp.player_id, pp.id_card, t.name AS tour_name, t.generation, tm.name AS team_name, tsp.created_at')
            ->join('tournament_team tt', 'tt.tour_team_id = tsp.tour_team_id', 'INNER')
            ->join('tournament t', 't.tour_id = tt.tour_id', 'INNER')
            ->join('team tm', 'tm.id = tt.team_id', 'INNER')
            ->join('player pp', 'pp.id = tsp.player_id', 'INNER')
            ->whereIn('tt.tour_id', $sourceTourIds)
            ->where('tt.team_id !=', $excludeTeamId)
            ->groupStart()
            ->whereIn('tsp.player_id', $playerIds);
        if (!empty($cardToIds)) {
            $builder->orWhereIn('pp.id_card', array_keys($cardToIds));
        }
        $rows = $builder->groupEnd()->get()->getResultArray();

        $dateThai = new \App\Libraries\Date_thai();
        $history = [];
        foreach ($rows as $r) {
            $label = $r['tour_name'];
            if (!empty($r['generation'])) {
                $label .= ' (' . $r['generation'] . ')';
            }
            $dateStr = !empty($r['created_at']) ? $dateThai->date_eng2thai($r['created_at'], 543, 'S', 'L') : '';
            $entry = [
                'tournament' => $label,
                'team'       => $r['team_name'],
                'date'       => $dateStr,
            ];

            // โยงประวัติกลับไปยัง player_id ที่ขอ: ตรง id หรือคนเดียวกันผ่าน id_card
            $targets = [];
            $pid = (int) $r['player_id'];
            if (in_array($pid, $playerIds, true)) {
                $targets[] = $pid;
            }
            $card = trim((string) ($r['id_card'] ?? ''));
            if ($card !== '' && isset($cardToIds[$card])) {
                $targets = array_merge($targets, $cardToIds[$card]);
            }
            foreach (array_unique($targets) as $tid) {
                $key = $entry['tournament'] . '|' . $entry['team'] . '|' . $entry['date'];
                if (!isset($history[$tid]['_seen'][$key])) {
                    $history[$tid]['_seen'][$key] = true;
                    $history[$tid]['entries'][] = $entry;
                }
            }
        }
        $result = [];
        foreach ($history as $k => $h) {
            $result[$k] = $h['entries'] ?? [];
        }
        return $result;
    }

    /**
     * แปลง history entries เป็นข้อความบรรทัดเดียวต่อรายการ (ใช้ใน ineligible_list / log)
     */
    function formatSourceHistoryText(array $entries): string
    {
        $lines = [];
        foreach ($entries as $e) {
            $lines[] = $e['tournament'] . ' — ' . lang('Common.source_prev_by_team') . ' ' . $e['team']
                . ($e['date'] !== '' ? ' (' . $e['date'] . ')' : '');
        }
        return implode('; ', $lines);
    }

    /**
     * Source-player check (require_source_players): นักเตะใน $playerIds ที่ "เคย" ถูกส่งรายชื่อ
     * ในทัวร์นาเมนต์ต้นทางที่ config ไว้ โดยทีมอื่น จะไม่สามารถส่งรายชื่อได้ ยกเว้น
     *  - นักเตะชุดเดิมที่เคยส่งโดยทีมเดียวกัน (team_id ของ $tourTeamId) ในทัวร์ต้นทาง
     *  - นักเตะที่อยู่ในรายชื่อที่ทีมเคยส่งไว้แล้วในทัวร์นี้ (กรณีแก้ไขรายชื่อ)
     * จับคู่ด้วย player_id ตรง หรือ id_card ตรง (ข้าม id_card ว่าง — คนเดียวกันคนละสโมสรก็โดนบล็อค)
     *
     * @return array [] ถ้าผ่านทุกคน; ไม่ผ่านคืน [{player_id, player_name, nick_name, shirt_number, source_tournaments}]
     */
    function checkIneligibleSourcePlayers($tourTeamId, array $playerIds, array $sourceTourIds): array
    {
        if (empty($playerIds) || empty($sourceTourIds)) return [];

        $teamRow = $this->db->table('tournament_team')
            ->select('team_id')
            ->where('tour_team_id', $tourTeamId)
            ->get()
            ->getRowArray();
        $ownTeamId = (int) ($teamRow['team_id'] ?? 0);

        $sets = $this->getSourceSubmissionSets($sourceTourIds, $ownTeamId, $tourTeamId);
        $ineligibleIds = $this->resolveSourceIneligibleIds($playerIds, $sets);
        if (empty($ineligibleIds)) return [];

        // ชื่อทัวร์นาเมนต์ต้นทางสำหรับข้อความแจ้ง
        $sourceTours = $this->db->table('tournament')
            ->select('name, generation')
            ->whereIn('tour_id', $sourceTourIds)
            ->get()
            ->getResultArray();
        $sourceLabels = [];
        foreach ($sourceTours as $st) {
            $label = $st['name'];
            if (!empty($st['generation'])) {
                $label .= ' (' . $st['generation'] . ')';
            }
            $sourceLabels[] = $label;
        }
        $sourceNames = implode(', ', $sourceLabels);

        $players = $this->db->table('player')
            ->select('id, first_name, last_name, nick_name, shirt_number')
            ->whereIn('id', $ineligibleIds)
            ->get()
            ->getResultArray();

        // ประวัติจริง: เคยส่งรายการไหน โดยทีมไหน วันที่เท่าไหร่ (แสดงบน UI)
        $historyMap = $this->getSourceSubmissionHistory($ineligibleIds, $sourceTourIds, $ownTeamId);

        $ineligible = [];
        foreach ($players as $p) {
            $pid = (int) $p['id'];
            $history = $historyMap[$pid] ?? [];
            $ineligible[] = [
                'player_id'          => $pid,
                'player_name'        => trim($p['first_name'] . ' ' . $p['last_name']),
                'nick_name'          => $p['nick_name'],
                'shirt_number'       => $p['shirt_number'],
                'source_tournaments' => $sourceNames,
                'history'            => $history,
                'history_text'       => $this->formatSourceHistoryText($history),
            ];
        }
        return $ineligible;
    }

    function deleteTournament($id)
    {
        // deleted subtable
        $builder = $this->db->table('tournament_team');
        $builder->where('tour_id', $id)->delete();

        // deleted main
        $builder = $this->db->table($this->table);
        $builder->where('tour_id', $id)->delete();
        return "success";
    }
}
