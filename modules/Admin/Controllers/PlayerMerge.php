<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\PlayerDedupModel;
use Modules\Player\Models\PlayerMergeModel;

/**
 * Admin UI for reviewing and merging the duplicate players that bulk auto-merge left
 * behind (invalid-checksum id_card dups + name/birthdate dups). The merge itself runs
 * through PlayerMergeModel (same engine as the bulk pass) so behaviour is identical.
 */
class PlayerMerge extends BaseController
{
    protected $dedupModel;

    public function __construct()
    {
        $this->dedupModel = new PlayerDedupModel();
    }

    /** List of duplicate groups awaiting review. */
    public function index()
    {
        return view('Modules\Admin\Views\PlayerMerge\index', [
            'groups' => $this->dedupModel->groups(),
        ]);
    }

    /** Side-by-side compare of one group's members. */
    public function compare()
    {
        $type = (string) $this->request->getGet('type');
        $key  = (string) $this->request->getGet('key');

        if (!in_array($type, ['id_card', 'name'], true) || $key === '') {
            return redirect()->to(base_url('admin/player-merge'))->with('error', 'พารามิเตอร์ไม่ถูกต้อง');
        }

        $members = $this->dedupModel->members($type, $key);
        if (count($members) < 2) {
            return redirect()->to(base_url('admin/player-merge'))
                ->with('success', 'กลุ่มนี้ไม่มีรายการซ้ำแล้ว');
        }

        return view('Modules\Admin\Views\PlayerMerge\compare', [
            'type'    => $type,
            'key'     => $key,
            'members' => $members,
        ]);
    }

    /** Merge selected drop_ids into keep_id. */
    public function doMerge()
    {
        $keepId  = (int) $this->request->getPost('keep_id');
        $dropIds = $this->request->getPost('drop_ids');
        $dropIds = is_array($dropIds) ? array_map('intval', $dropIds) : [];
        $dropIds = array_values(array_filter($dropIds, fn($d) => $d > 0 && $d !== $keepId));

        if ($keepId <= 0 || empty($dropIds)) {
            return redirect()->to(base_url('admin/player-merge'))
                ->with('error', 'กรุณาเลือกนักเตะที่จะเก็บไว้และอย่างน้อยหนึ่งรายการที่จะรวม');
        }

        $model   = new PlayerMergeModel();
        $adminId = (int) session()->get('member_id') ?: null;
        $merged  = 0;
        $errors  = [];

        foreach ($dropIds as $dropId) {
            try {
                $model->merge($keepId, $dropId, 'admin', $adminId);
                $merged++;
            } catch (\Throwable $e) {
                $errors[] = "id {$dropId}: " . $e->getMessage();
            }
        }

        $redirect = redirect()->to(base_url('admin/player-merge'));
        if ($merged > 0) {
            $redirect->with('success', "รวมนักเตะสำเร็จ {$merged} รายการเข้าสู่ id {$keepId}");
        }
        if ($errors) {
            $redirect->with('error', 'บางรายการรวมไม่สำเร็จ: ' . implode(' · ', $errors));
        }
        return $redirect;
    }
}
