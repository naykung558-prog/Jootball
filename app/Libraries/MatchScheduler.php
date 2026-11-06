<?php

namespace App\Libraries;

class MatchScheduler
{
    /**
     * สร้างแมตช์แบบพบกันหมด (Round Robin - รอบเดียว)
     *
     * สูตร: C(n,2) = n(n-1)/2
     * ตัวอย่าง: 4 ทีม = 6 แมตช์, 3 ทีม = 3 แมตช์, 5 ทีม = 10 แมตช์
     *
     * จำนวนคี่: Dummy อยู่ตำแหน่ง 0 + หมุนทวนเข็ม (ทีมแรกเล่นก่อน)
     * จำนวนคู่: Berger Table มาตรฐาน + สลับเหย้าเยือน
     *
     * @param array $teamIds - Array ของ ID ทีม
     * @return array - Array ของคู่แมตช์ [['team1' => id, 'team2' => id], ...]
     */
    public function generateRoundRobin(array $teamIds): array
    {
        $matches = [];
        $teamCount = count($teamIds);

        if ($teamCount < 2) {
            return [];
        }

        if ($teamCount % 2 !== 0) {
            // --- จำนวนคี่: Dummy อยู่ตำแหน่ง 0 (fixed) + หมุนทวนเข็ม ---
            // ทำให้ทีมแรก (index 0) ได้เล่นในแมตช์แรก แทนที่จะได้ bye
            // ตัวอย่าง 3 ทีม [A1,A2,A3]: สร้าง A1-A2, A2-A3, A3-A1
            $scheduleTeams = array_merge([null], $teamIds); // null (Bye) อยู่ตำแหน่ง 0
            $numTeams = count($scheduleTeams);
            $numRounds = $numTeams - 1;
            $halfSize = $numTeams / 2;

            for ($round = 0; $round < $numRounds; $round++) {
                for ($i = 0; $i < $halfSize; $i++) {
                    $t1 = $scheduleTeams[$i];
                    $t2 = $scheduleTeams[$numTeams - 1 - $i];

                    if ($t1 !== null && $t2 !== null) {
                        $matches[] = ['team1' => $t1, 'team2' => $t2];
                    }
                }

                // หมุนทวนเข็ม: ดึงตำแหน่ง 1 ออก แล้วต่อท้าย
                $removed = array_splice($scheduleTeams, 1, 1);
                $scheduleTeams[] = $removed[0];
            }
        } else {
            // --- จำนวนคู่: Berger Table มาตรฐาน + สลับเหย้า/เยือน ---
            $scheduleTeams = $teamIds;
            $numTeams = count($scheduleTeams);
            $numRounds = $numTeams - 1;
            $halfSize = $numTeams / 2;

            for ($round = 0; $round < $numRounds; $round++) {
                for ($i = 0; $i < $halfSize; $i++) {
                    $t1 = $scheduleTeams[$i];
                    $t2 = $scheduleTeams[$numTeams - 1 - $i];

                    if ($round % 2 === 0) {
                        $matches[] = ['team1' => $t1, 'team2' => $t2];
                    } else {
                        $matches[] = ['team1' => $t2, 'team2' => $t1];
                    }
                }

                // หมุนตามเข็ม: ดึงตัวสุดท้าย แทรกตำแหน่ง 1
                $last = array_pop($scheduleTeams);
                array_splice($scheduleTeams, 1, 0, [$last]);
            }
        }

        return $matches;
    }

    /**
     * สร้างแมตช์แบบพบกันหมด ไปและกลับ (Round Robin Double)
     * ใช้ algorithm Berger Table สร้างเลกแรก แล้วสร้างเลกสองโดยสลับเหย้าเยือน
     *
     * @param array $teamIds - Array ของ ID ทีม
     * @return array - Array ของคู่แมตช์ (เลกแรก ตามด้วย เลกสอง)
     */
    public function generateRoundRobinDouble(array $teamIds): array
    {
        // 1. สร้างเลกแรก
        $firstLeg = $this->generateRoundRobin($teamIds);

        // 2. สร้างเลกสอง (กลับบ้านทีมเยือนของเลกแรก)
        // โดยปกติจะเล่นจบเลกแรกก่อน แล้วค่อยเริ่มเลกสอง
        $secondLeg = [];
        foreach ($firstLeg as $match) {
            $secondLeg[] = [
                'team1' => $match['team2'],
                'team2' => $match['team1'],
            ];
        }

        // รวมกัน
        return array_merge($firstLeg, $secondLeg);
    }

    /**
     * สร้างแมตช์แบบแพ้คัดออก (Knockout) - First Round Only
     *
     * @param array $teamIds - Array ของ ID ทีม
     * @return array - Array ของคู่แมตช์
     */
    public function generateKnockout(array $teamIds): array
    {
        $matches = [];
        $teamCount = count($teamIds);

        if ($teamCount < 1) {
            return [];
        }

        $teams = array_values($teamIds); // Reset indices

        // ถ้าจำนวนทีมเป็นคี่
        if ($teamCount % 2 !== 0) {
            // ไม่เพิ่ม Bye ใน matches list โดยตรง แต่ต้องจัดการให้ทีมนั้นผ่านไปเลย
            // ในที่นี้เราคืนค่าแค่คู่ที่มีแข่งจริง หรืออาจจะคืนคู่กับ -1 ก็ได้เพื่อให้รู้ว่า Bye
            // แต่ Logic เดิมดูเหมือนจะไม่ได้ Handle -1 ใน DB Insert
            // ดังนั้นจับคู่ธรรมดา ทีมสุดท้ายถ้าเหลือเศษก็ปล่อย (หรือให้เป็น Bye ตาม Logic เดิม)
             $teams[] = -1; // Bye
        }

        $teamCount = count($teams);

        for ($i = 0; $i < $teamCount; $i += 2) {
            if ($i + 1 < $teamCount) {
                // ถ้าจับคู่กับ Bye (-1) อาจจะไม่ต้อง insert match หรือ insert เป็น bye
                // แต่เพื่อความปลอดภัยตาม Logic เดิม ใส่ไปก่อน
                 $matches[] = [
                    'team1' => $teams[$i],
                    'team2' => $teams[$i + 1],
                ];
            }
        }

        return $matches;
    }

    /**
     * สร้างแมตช์แบบแบ่งกลุ่ม (Group Stage - All Matches)
     *
     * แบ่งทีมเข้ากลุ่ม จากนั้นสร้างแมตช์ในแต่ละกลุ่ม
     *
     * @param array $teamIds - Array ของ ID ทีมทั้งหมด
     * @param int $numGroups - จำนวนกลุ่มที่ต้องการ
     * @param bool $isDouble - true = ไปและกลับ, false = รอบเดียว
     * @return array - Array ของแมตช์ พร้อม group_id
     */
    public function generateGroupStage(array $teamIds, int $numGroups, bool $isDouble = false): array
    {
        if ($numGroups <= 0 || count($teamIds) < 1) {
            return [];
        }

        // สุ่มทีมก่อนแบ่งกลุ่ม
        shuffle($teamIds);

        // สร้างโครงสร้างกลุ่ม
        $groups = array_fill(0, $numGroups, []);
        $groupNames = range('A', 'Z');

        // แบ่งทีมเข้ากลุ่มแบบ round-robin (ทีม 0→A, ทีม 1→B, ทีม 2→A, ทีม 3→B, ...)
        foreach ($teamIds as $index => $teamId) {
            $groupIndex = $index % $numGroups;
            $groups[$groupIndex][] = $teamId;
        }

        // สร้างแมตช์สำหรับแต่ละกลุ่ม
        $allMatches = [];
        foreach ($groups as $groupIndex => $groupTeams) {
            // กลุ่มที่มี 0 หรือ 1 ทีม ไม่ต้องสร้างแมตช์
            if (count($groupTeams) < 2) {
                continue;
            }

            // เลือก Round Robin แบบไปกลับหรือรอบเดียว
            if ($isDouble) {
                $groupMatches = $this->generateRoundRobinDouble($groupTeams);
            } else {
                $groupMatches = $this->generateRoundRobin($groupTeams);
            }

            // เพิ่ม group_id ให้กับทุกแมตช์ในกลุ่ม
            foreach ($groupMatches as &$match) {
                $match['group_id'] = $groupNames[$groupIndex];
            }
            unset($match); // Unset reference

            $allMatches = array_merge($allMatches, $groupMatches);
        }

        return $allMatches;
    }
}
