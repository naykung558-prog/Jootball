<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\Traits\TournamentScoringQueriesTrait;
use Modules\Admin\Models\Traits\TournamentMatchQueriesTrait;
use Modules\Admin\Models\Traits\TournamentSubmissionQueriesTrait;
use Modules\Admin\Models\Traits\TournamentStatsQueriesTrait;

class TournamentModel extends Model
{
    use TournamentStatsQueriesTrait;
    use TournamentSubmissionQueriesTrait;
    use TournamentMatchQueriesTrait;
    use TournamentScoringQueriesTrait;
    protected $table      = "tournament";
    protected $primaryKey = "tour_id";
    protected $allowedFields = [];

    function get_Tournament($tour_id = '')
    {
        // Always use 'tournament' table explicitly (not $this->table which may have been changed by other methods)
        $tableName = 'tournament';

        if ($tour_id) {
            $builder = $this->db->table($tableName);
            $builder->select($tableName . ".*")
                ->where("tour_id", $tour_id);
            $result = $builder->get()->getRowArray();
            return $result;
        } else {
            $builder = $this->db->table($tableName);
            $builder->select("tournament.tour_id,tournament.name,tournament.name_en,tournament.logo,tournament.is_active,tournament.is_registration_open,tournament.end_date,tournament.generation,tournament.update_date");
            //$builder->join("tbl_player_position", "tbl_player_position.id = player.player_position_id", "LEFT");
            //$builder->where("is_active", 1);
            //$builder->where("team_id", session()->get("team.id"));
            if (!session()->get('is_super_admin') && !in_array('reporter',session()->get('user_roles'))) {
                $builder->where("create_by", session()->get('member_id'));
            }
            $builder->orderBy("tournament.is_active", "DESC");
            $builder->orderBy("tournament.tour_id", "DESC");

            return DataTable::of($builder)
                ->edit('name', function ($row, $meta) {
                    $link = base_url('tournament/index/' . $row->tour_id);
                    $sub  = !empty($row->name_en) ? '<div class="text-muted small mt-1">' . esc($row->name_en) . '</div>' : '';
                    return '<a href="' . esc($link, 'attr') . '" target="_blank">' . esc($row->name) . '</a>' . $sub;
                })
                ->edit('generation', function ($row, $meta) {
                    if (empty($row->generation)) {
                        return '<span class="text-muted small">-</span>';
                    }
                    return '<span class="gen-badge">' . esc($row->generation) . '</span>';
                })
                ->edit('logo', function ($row, $meta) {
                    $fallback = base_url('public/img/no-image.png');
                    $hasLogo  = !empty($row->logo);
                    $img_link = $hasLogo ? base_url(ltrim($row->logo, '/')) : $fallback;
                    $fbClass  = $hasLogo ? '' : ' is-fallback';
                    return '<div class="tour-logo-wrap">'
                        . '<img src="' . htmlspecialchars($img_link, ENT_QUOTES) . '" class="tour-logo-img' . $fbClass . '" alt="logo" '
                        . 'onerror="this.onerror=null;this.src=\'' . htmlspecialchars($fallback, ENT_QUOTES) . '\';this.classList.add(\'is-fallback\');">'
                        . '</div>';
                })
                ->edit('is_active', function ($row, $meta) {
                    $id = $row->tour_id;
                    $isOn = $row->is_active == 1;
                    $checked = $isOn ? 'checked' : '';
                    $label = $isOn ? lang('Common.status_active') : lang('Common.status_suspended');
                    $color = $isOn ? '#28a745' : '#dc3545';
                    if(!in_array('reporter',session()->get('user_roles'))){
                        return '<label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;white-space:nowrap;margin:0;">'
                            . '<input type="checkbox" ' . $checked . ' data-id="' . $id . '" data-field="is_active" '
                            . 'onchange="toggleField(this)" style="display:none;">'
                            . '<span class="jt-switch' . ($isOn ? ' on' : '') . '"><span class="jt-switch-knob"></span></span>'
                            . '<span class="toggle-label" style="color:' . $color . ';font-weight:500;font-size:.85rem;">' . $label . '</span>'
                            . '</label>';
                    }else {
                        return '<label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;white-space:nowrap;margin:0;">'
                            . '<input type="checkbox" ' . $checked . ' data-id="' . $id . '" data-field="is_active" '
                            . ' style="display:none;">'
                            . '<span class="jt-switch' . ($isOn ? ' on' : '') . '"><span class="jt-switch-knob"></span></span>'
                            . '<span class="toggle-label" style="color:' . $color . ';font-weight:500;font-size:.85rem;">' . $label . '</span>'
                            . '</label>';
                    }
                })
                ->edit('is_registration_open', function ($row, $meta) {
                    $id = $row->tour_id;
                    $isOn = $row->is_registration_open == 1;

                    // Auto-close if past end_date
                    $expired = false;
                    if (!empty($row->end_date) && $row->end_date !== '0000-00-00') {
                        $expired = date('Y-m-d') > $row->end_date;
                        if ($expired && $isOn) {
                            \Config\Database::connect()->table('tournament')
                                ->where('tour_id', $id)
                                ->update(['is_registration_open' => 0]);
                            $isOn = false;
                        }
                    }

                    $checked = $isOn ? 'checked' : '';
                    $label = $isOn ? lang('Common.registration_open') : lang('Common.registration_closed');
                    $color = $isOn ? '#0dcaf0' : '#6c757d';
                    $disabled = $expired ? 'disabled' : '';
                    $opacity = $expired ? 'opacity:.5;pointer-events:none;' : '';

                    $expiredNote = '';
                    if ($expired) {
                        $expiredNote = '<div style="font-size:.65rem;color:#dc3545;margin-top:2px;">'
                            . '<i class="ti ti-clock-off"></i> ' . lang('Common.registration_expired')
                            . '</div>';
                    }

                    return '<div style="text-align:center;">'
                        . '<label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;white-space:nowrap;margin:0;' . $opacity . '">'
                        . '<input type="checkbox" ' . $checked . ' ' . $disabled . ' data-id="' . $id . '" data-field="is_registration_open" '
                        . 'onchange="toggleField(this)" style="display:none;">'
                        . '<span class="jt-switch' . ($isOn ? ' on' : '') . '"><span class="jt-switch-knob"></span></span>'
                        . '<span class="toggle-label" style="color:' . $color . ';font-weight:500;font-size:.85rem;">' . $label . '</span>'
                        . '</label>'
                        . $expiredNote
                        . '</div>';
                })
                ->add('action', function ($row) {
                    $linkEdit = base_url('admin/tournament/tournament-manage/' . $row->tour_id);
                    $linkEditScore = base_url('admin/tournament/score-management/' . $row->tour_id);
                    $linkTeam = base_url('admin/tournament/tournament-team/' . $row->tour_id);
                    $linkMatch = base_url('admin/tournament/match-setup/' . $row->tour_id);
                    $linkClone = base_url('admin/tournament/clone/' . $row->tour_id);

                    // Dropdown Action Menu
                    $langManageData = lang('Common.manage_data');
                    $langEditData = lang('Common.edit_data');
                    $langEditDataScore = lang('Common.score_management');
                    $langSelectTeamPlayer = lang('Common.select_team_player');
                    $langManageMatch = lang('Common.manage_match_menu');
                    $langClone = lang('Common.clone_item');
                    $langRegReport = lang('Common.registration_report');
                    $langStsReport = lang('Common.registration_report_status');
                    $langDeleteItem = lang('Common.delete_item');
                    $linkExport = base_url('admin/tournament/export-registration/' . $row->tour_id);
                    $linkExportSts = base_url('admin/tournament/export-registration-status/' . $row->tour_id);
                    $linkExportPlayers = base_url('admin/tournament/export-players-csv/' . $row->tour_id);
                    $langExportPlayers = lang('Common.export_players_csv');

                     $actionConfig = "";
                    if(!in_array('reporter',session()->get('user_roles'))){
                        $actionConfig = "
                            <div class='dropdown'>
                                <button class='tour-action-btn' type='button' data-bs-toggle='dropdown' data-bs-strategy='fixed' data-bs-boundary='viewport' aria-expanded='false'>
                                    <i class='ti ti-settings'></i>
                                </button>
                                <ul class='dropdown-menu dropdown-menu-end tour-action-menu' style='z-index: 1000;'>
                                    <li>
                                        <h6 class='dropdown-header'>{$langManageData}</h6>
                                    </li>
                                    <li>
                                        <a class='dropdown-item' href='{$linkEdit}'>
                                            <i class='ti ti-edit me-2 text-warning'></i> {$langEditData}
                                        </a>
                                    </li>
                                    <li>
                                        <a class='dropdown-item' href='{$linkTeam}'>
                                            <i class='ti ti-users me-2 text-info'></i> {$langSelectTeamPlayer}
                                        </a>
                                    </li>
                                    <li>
                                        <a class='dropdown-item' href='{$linkMatch}'>
                                            <i class='ti ti-tournament me-2 text-warning'></i> {$langManageMatch}
                                        </a>
                                    </li>
                                    <li>
                                        <button class='dropdown-item' onclick='confirmClone(\"{$linkClone}\")'>
                                            <i class='ti ti-copy me-2 text-secondary'></i> {$langClone}
                                        </button>
                                    </li>
                                    <li>
                                        <a class='dropdown-item' href='{$linkExport}'>
                                            <i class='ti ti-report me-2 text-success'></i> {$langRegReport}
                                        </a>
                                    </li>
                                    <li>
                                        <a class='dropdown-item' href='{$linkExportSts}'>
                                            <i class='ti ti-report me-2 text-success'></i> {$langStsReport}
                                        </a>
                                    </li>
                                    <li>
                                        <a class='dropdown-item' href='{$linkExportPlayers}'>
                                            <i class='ti ti-file-spreadsheet me-2 text-success'></i> {$langExportPlayers}
                                        </a>
                                    </li>
                                    <li>
                                        <button class='dropdown-item' type='button' onclick='openPendingChartPlayers({$row->tour_id})'>
                                            <i class='ti ti-user-exclamation me-2 text-danger'></i> นักเตะที่ค้างในแผงรูป
                                        </button>
                                    </li>
                                    <li><hr class='dropdown-divider'></li>
                                    <li>
                                        <button class='dropdown-item text-danger' onclick='deleteTournament({$row->tour_id})'>
                                            <i class='ti ti-trash me-2'></i> {$langDeleteItem}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        ";
                    }else {
                        $actionConfig = "
                            <div class='dropdown'>
                                <button class='tour-action-btn' type='button' data-bs-toggle='dropdown' data-bs-strategy='fixed' data-bs-boundary='viewport' aria-expanded='false'>
                                    <i class='ti ti-settings'></i>
                                </button>
                                <ul class='dropdown-menu dropdown-menu-end tour-action-menu' style='z-index: 1000;'>
                                    <li>
                                        <a class='dropdown-item' href='{$linkEditScore}'>
                                            <i class='ti ti-edit me-2 text-warning'></i> {$langEditDataScore}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        ";
                    }
                    return $actionConfig;
                })
                ->setSearchableColumns(['name', 'name_en'])
                ->addNumbering()
                ->toJson(true);
            //

        }
    }

    function get_TournamentList($is_active=1)
    {
        $builder = $this->db->table($this->table);
        $builder->select("tournament.tour_id,tournament.name,tournament.name_en,tournament.generation,tournament.detail,tournament.logo,tournament.create_by,tournament.create_date,tournament.update_by,tournament.update_date,tournament.is_active,tournament.is_deleted,tournament.start_date,tournament.end_date,tournament.tournament_start_date,tournament.tournament_end_date");
        if ($is_active !== null) {
            $builder->where("is_active", $is_active);
        }
        $builder->where("is_deleted", 0);
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function saveTournament($input)
    {
        $builder = $this->db->table($this->table);

        if (!empty($input['name'])) {
            $builder->set('name', $input['name']);
        }
        if (!empty($input['name_en'])) {
            $builder->set('name_en', $input['name_en']);
        }
        if (!empty($input['generation'])) {
            $builder->set('generation', $input['generation']);
        }
        if (!empty($input['detail'])) {
            $builder->set('detail', $input['detail']);
        }
        if (!empty($input['logo'])) {
            $builder->set('logo', $input['logo']);
        }
        if (!empty($input['start_date'])) {
            $formatDate = str_replace('/', '-', $input['start_date']);
            $builder->set('start_date', date('Y-m-d', strtotime($formatDate)));
        }
        if (!empty($input['end_date'])) {
            $formatDate = str_replace('/', '-', $input['end_date']);
            $builder->set('end_date', date('Y-m-d', strtotime($formatDate)));
        }
        if (!empty($input['tournament_start_date'])) {
            $formatDate = str_replace('/', '-', $input['tournament_start_date']);
            $builder->set('tournament_start_date', date('Y-m-d', strtotime($formatDate)));
        }
        if (!empty($input['tournament_end_date'])) {
            $formatDate = str_replace('/', '-', $input['tournament_end_date']);
            $builder->set('tournament_end_date', date('Y-m-d', strtotime($formatDate)));
        }
        if (!empty($input['pdf_path'])) {
            $builder->set('pdf_path', $input['pdf_path']);
        }
        if (!empty($input['bank_id'])) {
            $builder->set('bank_id', $input['bank_id']);
        }
        if (isset($input['is_active'])) {
            // If checkbox is checked, it sends '1', if not present (unchecked), handle default
            $builder->set('is_active', $input['is_active']);
        } else {
            // If updating and is_active is NOT in input (e.g. unchecked checkbox often sends nothing, or if form implies partial update),
            // but usually unchecked checkbox doesn't send key.
            // IMPORTANT: In CodeIgniter view, we might need a hidden input or handle "if keys exist".
            // Assuming this is a full form submission, if 'is_active' is missing, it means it's 0 (Inactive)
            // However, to be safe and avoid overwriting partial updates, we check context.
            // Given tour_manage is a full edit form:
            if (isset($input['tour_id'])) { // Only set to 0 if we are updating (and thus it was potentially unchecked)
                // But wait, $input comes from $this->request->getPost().
                // If unchecked, it's not there.
                $builder->set('is_active', 0);
            } else {
                // Creating new, formatted default is often 1, but if unchecked in create form..
                $builder->set('is_active', 0);
            }
        }

        if (isset($input['is_registration_open'])) {
            $builder->set('is_registration_open', $input['is_registration_open']);
        } else {
            if (isset($input['tour_id'])) {
                $builder->set('is_registration_open', 0);
            } else {
                $builder->set('is_registration_open', 0);
            }
        }

        if (!empty($input['player_count'])) {
            $builder->set('player_count', $input['player_count']);
        }
        if (!empty($input['account_name'])) {
            $builder->set('account_name', $input['account_name']);
        }
        if (!empty($input['account_no'])) {
            $builder->set('account_no', $input['account_no']);
        }
        if (!empty($input['promptpay'])) {
            $builder->set('promptpay', $input['promptpay']);
        }
        if (!empty($input['qrcode'])) {
            $builder->set('qrcode', $input['qrcode']);
        }

        // enable_payment toggle
        if (isset($input['enable_payment'])) {
            $builder->set('enable_payment', $input['enable_payment']);
        } else {
            $builder->set('enable_payment', 0);
        }

        // enable_document toggle
        if (isset($input['enable_document'])) {
            $builder->set('enable_document', $input['enable_document']);
        } else {
            $builder->set('enable_document', 0);
        }

        // prevent_duplicate_player toggle
        if (isset($input['prevent_duplicate_player'])) {
            $builder->set('prevent_duplicate_player', $input['prevent_duplicate_player']);
        } else {
            $builder->set('prevent_duplicate_player', 0);
        }

        // require_source_players toggle (whitelist นักเตะจากทัวร์นาเมนต์ต้นทาง)
        if (isset($input['require_source_players'])) {
            $builder->set('require_source_players', $input['require_source_players']);
        } else {
            $builder->set('require_source_players', 0);
        }

        // document_description
        if (isset($input['document_description'])) {
            $builder->set('document_description', $input['document_description']);
        }

        if (!empty($input['tour_id'])) {
            $builder->set('update_by', session()->get('member_id'));
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('tour_id', $input['tour_id']);
            $builder->update();
            $tourId = $input['tour_id'];
        } else {
            //$builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->set('create_by', session()->get('member_id'));
            //$builder->set('update_date', date('Y-m-d H:i:s'));
            // Default active for new tournaments if not specified?
            // Above logic handles it: if not in input, sets to 0. Maybe default 1 for new?
            // Let's stick to form value.
            $builder->insert();
            $tourId = $this->db->insertID();
        }

        // บันทึก linked tournaments สำหรับตรวจสอบนักเตะซ้ำข้ามรายการ
        $linkedTourIds = $input['linked_tournaments'] ?? [];
        $this->saveLinkedTournaments($tourId, $linkedTourIds);

        // บันทึกทัวร์นาเมนต์ต้นทางสำหรับ whitelist นักเตะ
        $sourceTourIds = $input['source_tournaments'] ?? [];
        $this->saveSourceTournaments($tourId, $sourceTourIds);
    }

    function getBanks()
    {
        $builder = $this->db->table('tbl_bank');
        $builder->where('is_active', 1);
        $builder->orderBy('id', 'ASC');
        return $builder->get()->getResultArray();
    }

    function getOneTournament($tour_id = '')
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . '.*');
        if ($tour_id) {
            $builder->where('tour_id', $tour_id);
            $result = $builder->get()->getRowArray();
        } else {
            $result = $builder->get()->getResultArray();
        }

        return $result;
    }

    function get_Tournament_Team($tour_id = '', $only_approved = true)
    {
        $result = [];
        $this->table = "tournament_team";
        $builder = $this->db->table($this->table);
        $builder->select("tournament_team.tour_team_id, tournament_team.tour_id, tournament_team.team_id as tt_team_id, tournament_team.register_status, tournament_team.player_list_status, tournament_team.payment_slip, team.id as id, team.name, team.name_en, team.logo, team.organize_name, team.contact_phone");
        $builder->join("team", "{$this->table}.team_id = team.id", "LEFT");
        if ($only_approved) {
            $builder->where('register_status', 1);
        }
        $builder->where('tour_id', $tour_id);
        $result = $builder->get()->getResultArray();
        //die(print_r($result));
        return $result;
    }

    function get_Tournament_TeamTable($tour_id = '')
    {
        $this->table = "tournament_team";
        $builder = $this->db->table($this->table);
        // รูปชุด: ใช้ฟิลด์ใหม่ใน tournament_team (เดิม COALESCE กับ team_player_chart_selection.jersey_* ซึ่งถูกยกเลิก)
        $builder->select("{$this->table}.tour_team_id,{$this->table}.tour_id,{$this->table}.team_id, {$this->table}.register_status, {$this->table}.payment_slip, {$this->table}.registration_document, {$this->table}.home_set_img, {$this->table}.away_set_img, {$this->table}.home_goal_set_img, team.name, team.name_en, team.logo");
        $builder->join("team", "{$this->table}.team_id = team.id", "LEFT");
        //$builder->where("is_active", 1);
        $builder->where("{$this->table}.tour_id", $tour_id);

        return DataTable::of($builder)
            ->edit('logo', function ($row, $meta) {
                return '<img class="" style="width:50px" src="' . base_url($row->logo) . '">';
            })
            ->edit('name', function ($row, $meta) {
                // ภาษาอังกฤษให้ใช้ team.name_en ถ้ามี ไม่มีค่อยถอยไปใช้ชื่อไทย
                $isEn = service('request')->getLocale() === 'en';
                $display = ($isEn && !empty($row->name_en)) ? $row->name_en : $row->name;
                $link = base_url() . '/detail/team/' . $row->team_id;
                return '<a href="' . $link . '" target="_blank">' . $display . '</a>';
            })
            ->add('payment_status', function ($row) {
                // จัดเป็นคอลัมน์เดียวเรียงลงมา: ป้ายสถานะ → ปุ่มสลิป → ปุ่มเอกสาร
                // (เดิมใช้ <br> กับ div mt-2 ปนกัน ทำให้ระยะห่างไม่เท่ากันและไม่อยู่กึ่งกลาง)
                $html = "<div class='d-inline-flex flex-column align-items-center gap-2'>";

                $statusMap = [
                    3 => ['text-bg-warning', lang('Common.waiting_payment')],
                    0 => ['text-bg-info',    lang('Common.payment_verifying')],
                    1 => ['text-bg-success', lang('Common.payment_paid')],
                ];
                if (isset($statusMap[(int) $row->register_status])) {
                    [$badgeClass, $badgeText] = $statusMap[(int) $row->register_status];
                    $html .= "<span class='badge {$badgeClass} rounded-pill px-3 py-2'>" . esc($badgeText) . "</span>";
                }

                if (!empty($row->payment_slip)) {
                    $rawSlips = $row->payment_slip;
                    $slips = [];

                    // Robust decoding: Try direct, then stripped-slashes
                    $decoded = json_decode($rawSlips, true);
                    if (!is_array($decoded)) {
                        $decoded = json_decode(stripslashes($rawSlips), true);
                    }

                    if (is_array($decoded)) {
                        $slips = $decoded;
                    } else {
                        // Fallback: Regex extraction of anything that looks like a slip path
                        $searchIn = stripslashes($rawSlips);
                        preg_match_all('/public\/uploads\/slips\/[^\s,\]"\'\\\]+/', $searchIn, $matches);
                        if (!empty($matches[0])) {
                            $slips = $matches[0];
                        } else {
                            $slips = [$rawSlips];
                        }
                    }

                    $gallaryId = "gallery-" . $row->tour_team_id;
                    $validSlips = [];
                    foreach ($slips as $s) {
                        // Decode first to handle %22 (% -> quote) and html entities (&quot; -> quote)
                        $s = urldecode($s);
                        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5);

                        // Aggressive cleaning: Remove ALL quotes (single/double), brackets, backslashes, literal %22, and remaining entities
                        $clean = str_replace(['"', "'", '[', ']', '\\', '%22', '&quot;'], '', stripslashes($s));
                        // Trim whitespace AND quotes explicitly
                        $clean = trim($clean, " \t\n\r\0\x0B\"'");

                        // Smart Path Resolution
                        $candidates = [];
                        if (!empty($clean)) $candidates[] = $clean;
                        if (strpos($clean, 'public/') === 0) {
                            $candidates[] = substr($clean, 7);
                        }

                        $foundPath = null;
                        foreach ($candidates as $candidate) {
                            $fullPath = FCPATH . $candidate;
                            $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);

                            if (file_exists($fullPath)) {
                                $foundPath = $candidate;
                                break;
                            }
                        }

                        if ($foundPath) {
                            $validSlips[] = $foundPath;
                        } else {
                            if (!empty($clean)) $validSlips[] = $clean;
                        }
                    }

                    $validSlips = array_values(array_unique($validSlips));

                    if (!empty($validSlips)) {
                        $firstSlip = base_url($validSlips[0]);
                        $slipLabel = esc(lang('Common.view_payment_slip'));
                        // มีหลายสลิปให้บอกจำนวนไว้ ไม่งั้นผู้ใช้ไม่รู้ว่ากดแล้วเลื่อนดูต่อได้
                        $count     = count($validSlips) > 1 ? ' (' . count($validSlips) . ')' : '';
                        $html .= "<div title='" . htmlspecialchars(json_encode($slips), ENT_QUOTES) . "'>";
                        $html .= "<a href='{$firstSlip}' data-fancybox='{$gallaryId}' class='btn-view-slip'><i class='ti ti-photo'></i> {$slipLabel}{$count}</a>";

                        for ($i = 1; $i < count($validSlips); $i++) {
                            $url = base_url($validSlips[$i]);
                            $html .= "<a href='{$url}' data-fancybox='{$gallaryId}' class='d-none'></a>";
                        }
                        $html .= "</div>";
                    } else {
                        $html .= "<span class='text-muted small'>" . esc(lang('Common.no_payment_slip')) . "</span>";
                    }
                } else {
                    $html .= "<span class='text-muted small'>" . esc(lang('Common.no_payment_slip')) . "</span>";
                }

                // แสดงเอกสารรับสมัคร (ถ้ามี)
                if (!empty($row->registration_document)) {
                    $docUrl = base_url($row->registration_document);
                    $ext = strtolower(pathinfo($row->registration_document, PATHINFO_EXTENSION));
                    $docGalleryId = "doc-" . $row->tour_team_id;
                    $docLabel = esc(lang('Common.view_registration_document'));
                    // PDF เปิดแท็บใหม่ ส่วนรูปเปิดใน fancybox
                    $attr = $ext === 'pdf'
                        ? "target='_blank'"
                        : "data-fancybox='{$docGalleryId}'";
                    $html .= "<a href='{$docUrl}' {$attr} class='btn-view-slip'><i class='ti ti-file-text'></i> {$docLabel}</a>";
                }

                $html .= "</div>";

                return $html;
            })
            ->add('jerseys', function ($row) {
                $html = '<div class="d-flex gap-1 justify-content-center flex-wrap">';
                $jerseyTypes = [
                    'home_set_img' => ['label' => 'เหย้า', 'color' => '#16478C'],
                    'away_set_img' => ['label' => 'เยือน', 'color' => '#e74c3c'],
                    'home_goal_set_img' => ['label' => 'GK', 'color' => '#f39c12'],
                ];
                $hasAny = false;
                $galleryId = 'jersey-' . $row->tour_team_id;
                foreach ($jerseyTypes as $field => $info) {
                    if (!empty($row->$field)) {
                        $hasAny = true;
                        $imgUrl = base_url($row->$field);
                        $html .= "<a href='{$imgUrl}' data-fancybox='{$galleryId}' data-caption='{$info['label']}' title='{$info['label']}'>";
                        $html .= "<img src='{$imgUrl}' style='width:40px; height:40px; object-fit:cover; border-radius:6px; border:2px solid {$info['color']};' alt='{$info['label']}'>";
                        $html .= "</a>";
                    }
                }
                if (!$hasAny) {
                    $html .= '<span class="text-muted small">-</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->add('register_badge', function ($row) {
                $text = '';
                if ($row->register_status == 0) {
                    $text = '<span class="badge text-bg-warning rounded-pill">รออนุมัติ</span>';
                } else if ($row->register_status == 1) {
                    $text = '<span class="badge text-bg-success rounded-pill">อนุมัติ</span>';
                } else if ($row->register_status == 2) {
                    $text = '<span class="badge text-bg-danger rounded-pill">ไม่อนุมัติ</span>';
                } else if ($row->register_status == 3) {
                    $text = '<span class="badge text-bg-warning rounded-pill">รอชำระเงิน</span>';
                }
                return $text;
            })
            ->add('register_tools', function ($row) {
                $tools = '';
                $changeFunction = "onchange='changeRegisterStatus($row->tour_team_id, this.value)'";

                $options = [
                    3 => lang('Common.waiting_payment'),
                    0 => lang('Common.waiting_approval'),
                    1 => lang('Common.approved'),
                    2 => lang('Common.not_approved')
                ];

                $tools = "<select name='register_status' class='form-select bg-white' $changeFunction>";
                foreach ($options as $val => $text) {
                    // Check if status is valid (in case DB has something else)
                    $selected = ((int)$row->register_status === $val) ? 'selected' : '';
                    $tools .= "<option value='$val' $selected>$text</option>";
                }
                $tools .= "</select>";

                return $tools;
            })
            ->add('action', function ($row) {
                $manageIcon = '';
                $langDeleteItem = lang('Common.delete_item');
                $manageIcon .= "<button onclick='deleteTournamentTeam(" . $row->tour_team_id . ")' class='btn btn-danger btn-sm mx-1'><i class='ti ti-trash text-white' style='font-size: 16px;'></i> {$langDeleteItem}</button>";
                return $manageIcon;
            })
            ->setSearchableColumns(['team.name', 'team.name_en'])
            ->addNumbering()
            ->toJson(true);
        //
    }

    function delete_tournament_team($id)
    {
        $builder = $this->db->table('tournament_team');
        $builder->where('tour_team_id', $id)->delete();
        return "success";
    }

    function change_register_status($id, $status)
    {
        $builder = $this->db->table('tournament_team');
        $builder->set('register_status', $status);
        $builder->where('tour_team_id', $id)->update();
        return "success";
    }

    function getOnceTournament_Team($tour_team_id = '')
    {
        $this->table = "tournament_team";
        $builder = $this->db->table($this->table);
        $builder->select($this->table . '.*');
        $result = [];
        if ($tour_team_id) {
            $builder->where('tour_team_id', $tour_team_id);
        }
        $result = $builder->get()->getRowArray();
        return $result;
    }

    function getTournamentTeamSelected($tour_id)
    {
        $builder = $this->db->table('tournament_team');

        if (!empty($tour_id)) {
            $builder->where('tour_id', $tour_id);
        }
        $result = $builder->select('team_id')->get()->getResultArray();
        return $result;
    }

    function get_Ranking($tour_id = '')
    {
        $this->table = "tournament_team";
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*, team.name, team.logo");
        $builder->join("team", "{$this->table}.team_id = team.id", "LEFT");
        $result = [];
        if ($tour_id) {
            $builder->where('tour_id', $tour_id);
            $rs = $builder->get()->getResultArray();
            //die(print_r($rs));
            //die(print_r($rs));
            foreach ($rs as $key => $value) {
                $tmp_result = array(
                    'name' => $value['name'],
                    'logo' => $value['logo'],
                    'match' => 0,
                    'W' => 0,
                    'D' => 0,
                    'L' => 0,
                    'can' => 0,
                    'waste' => 0,
                    'diff' => 0,
                    'points' => 0,
                    'last_timeline' =>
                    '
                        <img src="//www.gstatic.com/onebox/sports/standings/last_5_win.svg">
                        <img src="//www.gstatic.com/onebox/sports/standings/last_5_loss.svg">
                        <img src="//www.gstatic.com/onebox/sports/standings/last_5_win.svg">
                        <img src="//www.gstatic.com/onebox/sports/standings/last_5_loss.svg">
                        <img src="//www.gstatic.com/onebox/sports/standings/last_5_recent_loss.svg">
                    '
                );

                $table = "tournament_match_result";
                $builder = $this->db->table($table);
                $builder->select($table . ".*");
                $builder->where('team1_id', $value['team_id']);
                $builder->orWhere('team2_id', $value['team_id']);
                $rs1 = $builder->get()->getResultArray();
                foreach ($rs1 as $key1 => $value0) {
                    $tmp_result['match']++;
                    if ($value0['team1_id'] == $value['team_id']) {
                        if ($value0['result'] == 1) {
                            $tmp_result['W']++;
                        } else if ($value0['result'] == 0) {
                            $tmp_result['D']++;
                        } else {
                            $tmp_result['L']++;
                        }
                        $tmp_result['can'] += $value0['team1_score'];
                        $tmp_result['waste'] += $value0['team2_score'];
                    }
                    if ($value0['team2_id'] == $value['team_id']) {
                        if ($value0['result'] == 2) {
                            $tmp_result['W']++;
                        } else if ($value0['result'] == 0) {
                            $tmp_result['D']++;
                        } else {
                            $tmp_result['L']++;
                        }
                        $tmp_result['can'] += $value0['team2_score'];
                        $tmp_result['waste'] += $value0['team1_score'];
                    }
                    $tmp_result['diff'] = $tmp_result['can'] - $tmp_result['waste'];
                }
                $result[] = $tmp_result;
            }
        }


        //รวมแต้มอีกรอบ
        foreach ($result as $key => $data) {
            $result[$key]['points'] = ($data['W'] * 3) + $data['D'];
        }
        //

        return $result;
    }

    function get_Tournament_Match_Result($tour_id = '', $match_date = '', $round_name = '')
    {
        $this->table = "tournament_match_result";
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*,
        t1.name as home_team_name, t1.logo as home_team_logo, t2.name as away_team_name, t2.logo as away_team_logo");
        $builder->join("team as t1", "t1.id = $this->table.team1_id", "left");
        $builder->join("team as t2", "t2.id = $this->table.team2_id", "left");

        $result = [];
        if ($tour_id) {
            $builder->where('tour_id', $tour_id);
        }

        if ($match_date) {
            $builder->where('STR_TO_DATE(tournament_match_result.date,"%Y-%m-%d")', $match_date);

            // แสดงแต่แมทช์ที่มีคู่แข่งแล้ว
            $builder->where("tournament_match_result.team1_id != ", 0);
            $builder->where("tournament_match_result.team2_id != ", 0);
        }

        if ($round_name) {
            $builder->where("tournament_match_result.match_round", $round_name);
        }

        $result = $builder->get()->getResultArray();

        // ตั้งชื่อจำลองสำหรับทีมที่ใช้ mock team ID (0)
        foreach ($result as &$match) {
            if ($match['team1_id'] == 0 && empty($match['home_team_name'])) {
                $match['home_team_name'] = 'ทีมจำลอง 1';
            }
            if ($match['team2_id'] == 0 && empty($match['away_team_name'])) {
                $match['away_team_name'] = 'ทีมจำลอง 2';
            }
        }

        return $result;
    }

    function clearMatchTournament($tour_id)
    {
        $builder = $this->db->table("tournament_match_result");
        $builder->where('tour_id', $tour_id)->delete();
    }

    function deleteMatchRound($tour_id, $round_name)
    {
        $builder = $this->db->table("tournament_match_result");
        $builder->where('tour_id', $tour_id)
            ->where('match_round', $round_name)
            ->delete();
        return true;
    }

    function deleteMatch($tour_id, $match_id)
    {
        $builder = $this->db->table("tournament_match_result");
        $builder->where('tour_id', $tour_id)
            ->where('match_id', $match_id)
            ->delete();
        return true;
    }

    function getOnceTournament_Match_Result($match_id = '')
    {
        $this->table = "tournament_match_result";
        $builder = $this->db->table($this->table);
        $builder->select($this->table . '.*,
        t1.name as home_team_name, t1.logo as home_team_logo, t2.name as away_team_name, t2.logo as away_team_logo');
        $builder->join("team as t1", "t1.id = $this->table.team1_id", "left");
        $builder->join("team as t2", "t2.id = $this->table.team2_id", "left");
        $result = [];
        if ($match_id) {
            $builder->where('match_id', $match_id);
        }
        $result = $builder->get()->getRowArray();

        // ตั้งชื่อจำลองสำหรับทีมที่ใช้ mock team ID (0)
        if ($result) {
            if ($result['team1_id'] == 0 && empty($result['home_team_name'])) {
                $result['home_team_name'] = 'ทีมจำลอง 1';
            }
            if ($result['team2_id'] == 0 && empty($result['away_team_name'])) {
                $result['away_team_name'] = 'ทีมจำลอง 2';
            }
        }

        return $result;
    }

    function get_Note_Type($type_id = '')
    {
        $this->table = "note_type";
        $builder = $this->db->table($this->table);
        $builder->select($this->table . '.*');
        $result = $builder->get()->getRowArray();

        if ($type_id != '') {
            $builder->where('type_id', $type_id);
            $result = $builder->get()->getRowArray();
        } else {
            $result = $builder->get()->getResultArray();
        }
        return $result;
    }

    function save_tournament_teams($input)
    {
        $tournament_id = $input['tour_id'];
        // Guard against empty payload and duplicate team ids in the same request.
        $teams = array_values(array_unique(array_filter($input['teams'] ?? [], function ($t) {
            return $t !== '' && $t !== null;
        })));
        foreach ($teams as $team) {
            // Fresh builder each iteration so leftover WHERE/SET state can never leak
            // between teams (a skipped update() used to leak WHERE and cause duplicates).
            $builder = $this->db->table("tournament_team");
            $regisData = $builder->select('*')->where('tour_id', $tournament_id)->where('team_id', $team)->get()->getRowArray();
            if (empty($regisData)) {
                $builder->set('tour_id', $tournament_id);
                $builder->set('team_id', $team);

                if (isset($input['register_status'])) {
                    $builder->set('register_status', $input['register_status']);
                } else {
                    $builder->set('register_status', 1);
                }

                if (!empty($input['payment_slip'])) {
                    // Sanitize New Slip
                    $slip = urldecode($input['payment_slip']);
                    $slip = html_entity_decode($slip, ENT_QUOTES | ENT_HTML5);
                    $slip = str_replace(['"', "'", '[', ']', '\\', '%22', '&quot;'], '', stripslashes($slip));
                    $slip = trim($slip, " \t\n\r\0\x0B\"'");
                    $builder->set('payment_slip', json_encode([$slip]));
                }

                if (!empty($input['registration_document'])) {
                    $builder->set('registration_document', $input['registration_document']);
                }

                if (!empty(session()->get('member_id'))) {
                    $builder->set('create_by', session()->get('member_id'));
                } else {
                    $builder->set('create_by', 0);
                }
                $builder->set('create_date', date('Y-m-d H:i:s'));
                $builder->insert();
            } else {
                // Update existing record
                $builder->where('tour_id', $tournament_id)->where('team_id', $team);
                $shouldUpdate = false;

                if (!empty($input['payment_slip'])) {
                    $currentSlips = $regisData['payment_slip'];
                    $slipArray = [];
                    if (!empty($currentSlips)) {
                        // Try to decode existing data robustly
                        $decoded = json_decode($currentSlips, true);
                        if (!is_array($decoded)) {
                            $decoded = json_decode(stripslashes($currentSlips), true);
                        }

                        if (is_array($decoded)) {
                            $slipArray = $decoded;
                        } else {
                            // Extract paths if it's a mess
                            preg_match_all('/public\/uploads\/slips\/[^\s,\]"\' ]+/', stripslashes($currentSlips), $matches);
                            if (!empty($matches[0])) {
                                $slipArray = $matches[0];
                            } else {
                                $slipArray = [trim(stripslashes($currentSlips), '[]"\'\\ ')];
                            }
                        }
                    }

                    // Add new slip
                    $slipArray[] = $input['payment_slip'];

                    // Clean/Sanitize all entries
                    $slipArray = array_values(array_filter(array_unique(array_map(function ($s) {
                        $s = urldecode($s);
                        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5);
                        $s = str_replace(['"', "'", '[', ']', '\\', '%22', '&quot;'], '', stripslashes($s));
                        return trim($s, " \t\n\r\0\x0B\"'");
                    }, $slipArray))));

                    $builder->set('payment_slip', json_encode($slipArray));
                    $shouldUpdate = true;
                }

                if (!empty($input['registration_document'])) {
                    $builder->set('registration_document', $input['registration_document']);
                    $shouldUpdate = true;
                }

                if (isset($input['register_status'])) {
                    $builder->set('register_status', $input['register_status']);
                    $shouldUpdate = true;
                }

                if ($shouldUpdate) {
                    $builder->update();
                }
            }
        }
        return "success";
    }

    function getTeamRegistration($tour_id, $team_id)
    {
        $builder = $this->db->table("tournament_team");
        $result = $builder->select('register_status, payment_slip, registration_document, tour_team_id, player_list_status')
            ->where('tour_id', $tour_id)
            ->where('team_id', $team_id)
            ->get()
            ->getRowArray();
        return $result ?? [];
    }

    function getTeamJoinedTournaments($team_id)
    {
        $builder = $this->db->table("tournament_team");
        $result = $builder->select('tournament.tour_id, tournament.name, tournament.generation, tournament.player_count, tournament_team.tour_team_id, tournament_team.player_list_status, tournament_team.player_list_submitted_at')
            ->join('tournament', 'tournament.tour_id = tournament_team.tour_id', 'INNER')
            ->where('tournament_team.team_id', $team_id)
            ->where('tournament_team.register_status', 1)  // Only approved tournaments
            ->get()
            ->getResultArray();
        return $result ?? [];
    }

    /**
     * Get joined tournaments for multiple teams (parent + sub-teams)
     * @param array $team_ids Array of team IDs
     * @return array
     */
    function getTeamJoinedTournamentsMultiple($team_ids)
    {
        if (empty($team_ids)) return [];

        $builder = $this->db->table("tournament_team");
        $result = $builder->select('tournament.tour_id, tournament.name, tournament.generation, tournament.player_count, tournament.prevent_duplicate_player, tournament.start_date, tournament.end_date, tournament.tournament_start_date, tournament.tournament_end_date, tournament.is_active, tournament_team.tour_team_id, tournament_team.team_id, tournament_team.register_status, tournament_team.player_list_status, tournament_team.player_list_submitted_at, team.name as team_name')
            ->join('tournament', 'tournament.tour_id = tournament_team.tour_id', 'INNER')
            ->join('team', 'team.id = tournament_team.team_id', 'LEFT')
            ->whereIn('tournament_team.team_id', $team_ids)
            ->whereIn('tournament_team.register_status', [0, 1, 3])  // รออนุมัติ, อนุมัติแล้ว, รอชำระเงิน (ไม่รวมถูกปฏิเสธ=2)
            ->orderBy('CASE WHEN tournament_team.register_status = 1 THEN 0 ELSE 1 END', '', false)  // อนุมัติแล้ว (1) ขึ้นก่อน
            ->orderBy('tournament.tour_id', 'DESC')  // ทัวร์นาเมนต์ใหม่กว่าขึ้นก่อน
            ->get()
            ->getResultArray();
        return $result ?? [];
    }




    function manageTournamentMatch($input)
    {
        try {
            $this->db->transBegin();

            $builder = $this->db->table('tournament_match_result');
            if (!empty($input['tour_id'])) {
                $builder->set('tour_id', $input['tour_id']);
            }
            if (!empty($input['match_round'])) {
                $builder->set('match_round', $input['match_round']);
            }
            if (!empty($input['team1_id'])) {
                $builder->set('team1_id', $input['team1_id']);
            }
            if (!empty($input['team2_id'])) {
                $builder->set('team2_id', $input['team2_id']);
            }

            // Handle referee and stadium
            if (isset($input['referee'])) {
                $builder->set('referee', $input['referee']);
            }
            if (isset($input['stadium'])) {
                $builder->set('stadium', $input['stadium']);
            }

            // Handle date and calculate match_status based on it
            $matchDate = null;
            $hasDateField = array_key_exists('date', $input);
            if ($hasDateField && !empty($input['date'])) {
                $dateInput = trim($input['date']);
                // รองรับทั้ง dd/mm/yyyy และ yyyy-mm-dd
                if (strpos($dateInput, '/') !== false) {
                    // Format: dd/mm/yyyy
                    $parts = explode('/', $dateInput);
                    if (count($parts) === 3) {
                        $matchDate = $parts[2] . '-' . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                    }
                } else {
                    // Format: yyyy-mm-dd
                    $matchDate = date('Y-m-d', strtotime($dateInput));
                }
                $builder->set('date', $matchDate);
            }

            if (!empty($input['time'])) {
                $builder->set('time', $input['time']);
            }
            if (!empty($input['create_by'])) {
                $builder->set('create_by', $input['create_by']);
            }
            if (!empty($input['create_date'])) {
                $builder->set('create_date', $input['create_date']);
            }
            if (!empty($input['update_by'])) {
                $builder->set('update_by', $input['update_by']);
            }
            if (!empty($input['update_date'])) {
                $builder->set('update_date', $input['update_date']);
            }
            if (!empty($input['group_id'])) {
                $builder->set('group_id', $input['group_id']);
            }

            if (!empty($input['match_id'])) {
                // Update match_status only when date is explicitly being saved.
                // This keeps bulk-edit for stadium/time/referee from mutating other fields.
                if ($hasDateField && $matchDate !== null) {
                    $today = date('Y-m-d');
                    $matchDate = trim($matchDate);
                    if ($matchDate === $today) {
                        $builder->set('match_status', 1); // กำลังแข่งขัน
                    } else {
                        $builder->set('match_status', 0); // รอแข่งขัน
                    }
                }

                $builder->where('match_id', $input['match_id']);
                $builder->update();
            } else {
                // For new matches, default status is waiting (0)
                $builder->set('match_status', 0);
                $builder->insert();
            }

            $this->db->transCommit();

            $result = [
                'status' => 'success',
                'msg' => 'บันทึกข้อมูลสำเร็จ'
            ];
            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();
            return [
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Swap the home/away (left/right) sides of a saved match.
     *
     * Swaps team1<->team2 together with their slots, scores, penalties and the
     * position-relative result flag (1<->2, draw stays 0). winner_team_id and
     * match events reference real team ids, so they are left untouched.
     *
     * The slot columns are what makes this work for a match whose teams have not
     * been picked yet: both team ids are 0 there, so swapping the ids alone would
     * write nothing at all and the UI would keep showing the same two labels.
     *
     * teams_json is deliberately left alone. Its slot list is shared by every
     * fixture in the stage, so reordering two entries would shuffle the other
     * matches as a side effect — and combined with the row swap below the two
     * changes cancel each other out.
     */
    function swapMatchTeams($match_id)
    {
        $match_id = (int) $match_id;
        if ($match_id <= 0) {
            return ['status' => 'error', 'msg' => 'invalid match_id'];
        }

        $this->db->transStart();
        try {
            $match = $this->db->table('tournament_match_result')
                ->where('match_id', $match_id)->get()->getRowArray();

            if (empty($match)) {
                $this->db->transComplete();
                return ['status' => 'error', 'msg' => 'match not found'];
            }

            // Flip result: 1 (team1 win) <-> 2 (team2 win); 0/draw unchanged.
            $result = (int) ($match['result'] ?? 0);
            $newResult = $result === 1 ? 2 : ($result === 2 ? 1 : $result);

            $this->db->table('tournament_match_result')
                ->where('match_id', $match_id)
                ->update([
                    'team1_id'      => $match['team2_id'],
                    'team2_id'      => $match['team1_id'],
                    'team1_slot'    => $match['team2_slot'],
                    'team2_slot'    => $match['team1_slot'],
                    'team1_score'   => $match['team2_score'],
                    'team2_score'   => $match['team1_score'],
                    'team1_penalty' => $match['team2_penalty'],
                    'team2_penalty' => $match['team1_penalty'],
                    'result'        => $newResult,
                    'update_by'     => session()->get('member_id') ?: null,
                    'update_date'   => date('Y-m-d H:i:s'),
                ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => 'error', 'msg' => 'เกิดข้อผิดพลาดในการสลับทีม'];
            }
            return ['status' => 'success', 'msg' => 'สลับทีมเรียบร้อย'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['status' => 'error', 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    function getMatchEvent($match_id)
    {
        $builder = $this->db->table('tournament_match_event');
        $builder->select('tournament_match_event.*,
        pl1.first_name as pl1_first_name,
        pl1.last_name as pl1_last_name,
        pl2.first_name as pl2_first_name,
        pl2.last_name as pl2_last_name,
        tbl_event.icon');
        $builder->join('player pl1', 'pl1.id = tournament_match_event.player_1_id', 'left');
        $builder->join('player pl2', 'pl2.id = tournament_match_event.player_2_id', 'left');
        $builder->join('tbl_event', 'tbl_event.id = tournament_match_event.event_id', 'left');
        $builder->where('match_id', $match_id);
        $builder->orderBy("cast(minute as unsigned)", "DESC");
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function saveTournamentMatchEvent($tournament_id = '', $match_id = '', $events = array())
    {
        $builder = $this->db->table('tournament_match_event');
        if (!empty($events)) {
            // deleted old data
            $builder->where('tour_id', $tournament_id)->where('match_id', $match_id)->delete();

            //insert new
            foreach ($events as $row) {
                if (!empty($row['event_id'])) {
                    $builder->set('event_id', $row['event_id']);
                }
                if (!empty($row['team_id'])) {
                    $builder->set('team_id', $row['team_id']);
                }
                if (!empty($row['player_1_id'])) {
                    $builder->set('player_1_id', $row['player_1_id']);
                }
                if (!empty($row['player_2_id'])) {
                    $builder->set('player_2_id', $row['player_2_id']);
                }
                if (!empty($row['minute'])) {
                    $builder->set('minute', $row['minute']);
                }
                $builder->set('tour_id', $tournament_id);
                $builder->set('match_id', $match_id);
                $builder->insert();
            }
        }
    }

    function saveMatchEvent($input)
    {
        $builder = $this->db->table('tournament_match_event');
        if (!empty($input['event_id'])) {
            $builder->set('event_id', $input['event_id']);
        }
        if (!empty($input['team_id'])) {
            $builder->set('team_id', $input['team_id']);
        }
        if (!empty($input['player_1_id'])) {
            $builder->set('player_1_id', $input['player_1_id']);
        }
        if (!empty($input['player_2_id'])) {
            $builder->set('player_2_id', $input['player_2_id']);
        }
        if (!empty($input['minute'])) {
            $builder->set('minute', $input['minute']);
        }
        $builder->set('tour_id', $input['tour_id']);
        $builder->set('match_id', $input['match_id']);
        $builder->insert();

        // update score
        $matchBuilder = $this->db->table("tournament_match_result");
        $match_data = $matchBuilder->select("*")->where("tournament_match_result.match_id", $input['match_id'])->get()->getRowArray();

        // find goal for team 1
        $goal_team1 = $builder->select("COUNT(match_event_id) as value")->where("event_id", 1)->where("match_id", $input['match_id'])->where("team_id", $match_data['team1_id'])->get()->getRowArray();
        $own_goal_team2 =  $builder->select("COUNT(match_event_id) as value")->where("event_id", 5)->where("match_id", $input['match_id'])->where("team_id", $match_data['team2_id'])->get()->getRowArray();
        $total_team1_goals = $goal_team1['value'] + $own_goal_team2['value'];
        // find goal for team 2
        $goal_team2 = $builder->select("COUNT(match_event_id) as value")->where("event_id", 1)->where("match_id", $input['match_id'])->where("team_id", $match_data['team2_id'])->get()->getRowArray();
        $own_goal_team1 =  $builder->select("COUNT(match_event_id) as value")->where("event_id", 5)->where("match_id", $input['match_id'])->where("team_id", $match_data['team1_id'])->get()->getRowArray();
        $total_team2_goals = $goal_team2['value'] + $own_goal_team1['value'];

        $matchBuilder->set("team1_score", $total_team1_goals);
        $matchBuilder->set("team2_score", $total_team2_goals);
        $matchBuilder->where("match_id", $input['match_id']);
        $matchBuilder->update();

        $result = [
            'team1_score' => $total_team1_goals,
            'team2_score' => $total_team2_goals
        ];
        return $result;
    }

    function deleteEvent($id, $match_id)
    {
        $builder = $this->db->table('tournament_match_event');
        $builder->where('match_event_id', $id)->delete();

        // update score
        $matchBuilder = $this->db->table("tournament_match_result");
        $match_data = $matchBuilder->select("*")->where("tournament_match_result.match_id", $match_id)->get()->getRowArray();

        // find goal for team 1
        $goal_team1 = $builder->select("COUNT(match_event_id) as value")->where("event_id", 1)->where("match_id", $match_id)->where("team_id", $match_data['team1_id'])->get()->getRowArray();
        $own_goal_team2 =  $builder->select("COUNT(match_event_id) as value")->where("event_id", 5)->where("match_id", $match_id)->where("team_id", $match_data['team2_id'])->get()->getRowArray();
        $total_team1_goals = $goal_team1['value'] + $own_goal_team2['value'];
        // find goal for team 2
        $goal_team2 = $builder->select("COUNT(match_event_id) as value")->where("event_id", 1)->where("match_id", $match_id)->where("team_id", $match_data['team2_id'])->get()->getRowArray();
        $own_goal_team1 =  $builder->select("COUNT(match_event_id) as value")->where("event_id", 5)->where("match_id", $match_id)->where("team_id", $match_data['team1_id'])->get()->getRowArray();
        $total_team2_goals = $goal_team2['value'] + $own_goal_team1['value'];

        $matchBuilder->set("team1_score", $total_team1_goals);
        $matchBuilder->set("team2_score", $total_team2_goals);
        $matchBuilder->where("match_id", $match_id);
        $matchBuilder->update();

        $result = [
            'team1_score' => $total_team1_goals,
            'team2_score' => $total_team2_goals
        ];
        return $result;
    }

    function getTeamOptions($tournament_id = '', $onlyNotEliminated = false)
    {
        $builder = $this->db->table('team');
        $builder->select('team.*');

        if ($tournament_id) {
            $builder->join("tournament_team", "tournament_team.team_id = team.id", "left");
            $builder->where("tournament_team.tour_id", $tournament_id);

            // ถ้าต้องการเฉพาะทีมที่ยังไม่ตกรอบ
            if ($onlyNotEliminated) {
                $builder->where("tournament_team.is_eliminated", 0);
            }
        }

        $result = $builder->get()->getResultArray();
        return $result;
    }

    function getEventTypes()
    {
        $builder = $this->db->table('tbl_event');
        $result = $builder->select('*')->get()->getResultArray();
        return $result;
    }


}
