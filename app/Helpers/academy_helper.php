<?php

/**
 * Academy Helper
 * ฟังก์ชันช่วยตรวจสอบว่าทีมอยู่ใน Academy หรือไม่
 * และสร้าง URL ที่ถูกต้องสำหรับ redirect
 */

if (!function_exists('getTeamAcademyInfo')) {
    /**
     * ตรวจสอบว่าทีมอยู่ใน Academy หรือไม่
     * คืนค่า ['academy_id' => int, 'academy_name' => string] หรือ null
     *
     * @param int $team_id
     * @return array|null
     */
    function getTeamAcademyInfo($team_id)
    {
        if (empty($team_id)) {
            return null;
        }

        $db = db_connect();
        $result = $db->table('academy_teams')
            ->select('academy_teams.academy_id, academy.name as academy_name')
            ->join('academy', 'academy.id = academy_teams.academy_id')
            ->where('academy_teams.team_id', $team_id)
            ->where('academy.is_active', 1)
            ->get()
            ->getRowArray();

        return $result ?: null;
    }
}

if (!function_exists('getAcademyUrlForTeam')) {
    /**
     * สร้าง Academy URL สำหรับทีมที่อยู่ใน Academy
     * คืน URL เช่น academy/team-info/5/12 หรือ null ถ้าทีมไม่อยู่ใน Academy
     *
     * @param int $team_id
     * @param string $page  หน้าปลายทาง: 'info', 'player-list', 'player-chart', 'manage', 'coach-list'
     * @return string|null
     */
    function getAcademyUrlForTeam($team_id, $page = 'info')
    {
        $info = getTeamAcademyInfo($team_id);
        if (!$info) {
            return null;
        }

        $academyId = $info['academy_id'];

        $pageMap = [
            'info'         => "academy/team-info/{$academyId}/{$team_id}",
            'player-list'  => "academy/player-list/{$academyId}/{$team_id}",
            'player-chart' => "academy/player-chart/{$academyId}/{$team_id}",
            'manage'       => "academy/manage/{$academyId}",
            'coach-list'   => "academy/coaches/{$academyId}",
            'teams'        => "academy/teams/{$academyId}",
            'dashboard'    => "academy/info/{$academyId}",
        ];

        $path = $pageMap[$page] ?? "academy/info/{$academyId}";

        return base_url($path);
    }
}

if (!function_exists('isSuperAcademyUser')) {
    /**
     * ตรวจว่า user มี is_super_academy=1 หรือไม่
     * Per-request memoization ป้องกัน DB hit ซ้ำเมื่อถูกเรียกใน loop (เช่น getAllUserPermissions)
     *
     * @param int|null $user_id  ถ้าไม่ส่ง จะใช้ session('user_id')
     * @return bool
     */
    function isSuperAcademyUser($user_id = null): bool
    {
        $uid = $user_id ?: session()->get('user_id');
        if (empty($uid)) return false;

        static $cache = [];
        if (!array_key_exists($uid, $cache)) {
            $cache[$uid] = (bool) db_connect()->table('user')
                ->where('id', $uid)
                ->where('is_super_academy', 1)
                ->countAllResults();
        }
        return $cache[$uid];
    }
}

if (!function_exists('getUserAcademyId')) {
    /**
     * ดึง academy_id ของ user ปัจจุบัน (จาก academy_members)
     * คืน academy_id หรือ null
     *
     * @return int|null
     */
    function getUserAcademyId()
    {
        $userId = session()->get('user_id');
        if (empty($userId)) {
            return null;
        }

        // Super academy operator → ใช้ current_academy_id เท่านั้น + validate ว่า academy ยัง active
        if (session()->get('is_super_academy')) {
            $cur = (int)session()->get('current_academy_id');
            if ($cur <= 0) return null;
            $exists = db_connect()->table('academy')
                ->where('id', $cur)->where('is_active', 1)->countAllResults() > 0;
            if (!$exists) {
                session()->remove('current_academy_id');
                session()->remove('current_academy_name');
                return null;
            }
            return $cur;
        }

        $db = db_connect();
        $results = $db->table('academy_members')
            ->select('academy_id, role')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->whereIn('role', ['owner', 'admin', 'coach', 'viewer'])
            ->get()
            ->getResultArray();

        if (empty($results)) {
            return null;
        }

        // Priority: owner > admin > coach > viewer
        $rolePriority = ['owner' => 1, 'admin' => 2, 'coach' => 3, 'viewer' => 4];
        usort($results, function ($a, $b) use ($rolePriority) {
            $pa = $rolePriority[$a['role'] ?? ''] ?? 99;
            $pb = $rolePriority[$b['role'] ?? ''] ?? 99;
            return $pa - $pb;
        });

        return (int)$results[0]['academy_id'];
    }
}

if (!function_exists('getUserAcademyUrl')) {
    /**
     * คืน URL หน้าแรกของ Academy สำหรับ user ปัจจุบัน
     * ถ้า user ไม่มี Academy คืน null
     *
     * @return string|null
     */
    function getUserAcademyUrl()
    {
        $academyId = getUserAcademyId();
        if (!$academyId) {
            return null;
        }
        return base_url("academy/info/{$academyId}");
    }
}

if (!function_exists('getUserTeamAcademyUrl')) {
    /**
     * สำหรับ user ที่มีทีม — ตรวจสอบว่าทีมของ user อยู่ใน Academy หรือไม่
     * ถ้าอยู่ คืน Academy dashboard URL
     * ถ้าไม่อยู่ คืน null (ให้ใช้ Team module ตามปกติ)
     *
     * @return string|null
     */
    function getUserTeamAcademyUrl()
    {
        $teamId = session()->get('team.id');
        if (empty($teamId)) {
            // ไม่มีทีมใน session — ลองเช็คจาก academy_members
            return getUserAcademyUrl();
        }

        return getAcademyUrlForTeam($teamId, 'dashboard');
    }
}

if (!function_exists('mask_last_name')) {
    /**
     * Mask last name to show only the first character followed by a dot.
     * Example: "Doe" -> "D."
     *
     * @param string|null $lastName
     * @return string
     */
    function mask_last_name($lastName)
    {
        $lastName = trim($lastName);
        if (empty($lastName)) {
            return '';
        }

        // 1. ดึงตัวอักษรแรกสุดออกมา
        $firstChar = mb_substr($lastName, 0, 1, 'UTF-8');
        
        // 2. รายชื่อสระหน้าภาษาไทย
        $leadingVowels = ['เ', 'แ', 'โ', 'ใ', 'ไ'];

        // 3. ถ้าตัวแรกสุดเป็นสระหน้า ให้ขยับไปเอาตัวที่สองแทน (ซึ่งก็คือพยัญชนะต้น)
        if (in_array($firstChar, $leadingVowels)) {
            $masked = mb_substr($lastName, 1, 1, 'UTF-8');
        } else {
            // ถ้าเป็นพยัญชนะปกติ หรือภาษาอังกฤษ ก็เอาตัวแรกได้เลย
            $masked = $firstChar;
        }

        // 4. แปลงเป็นตัวใหญ่ (เผื่อเป็นอังกฤษ) แล้วใส่จุด
        return mb_strtoupper($masked, 'UTF-8') . '.';
    }

}
