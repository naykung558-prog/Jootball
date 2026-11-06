<?php

namespace Modules\Log\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class LogModel extends Model
{
    protected $table      = "logs_login";
    protected $primaryKey = "id";
    protected $allowedFields = [];

    /*
    function verifyLog($input)
    {
        $builder = $this->db->table($this->table);
        $log = $builder->select('*')->where('logname', $input['logname'])->where('password', $input['password'])->get()->getRowArray();
        return $log;
    }

    function check_uid_line($uid_line = '')
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->where("uid_line", $uid_line);
        $result = $builder->get()->getRowArray();
        return $result;
    }
    */

    function saveLoginLog($data)
    { // สำหรับเชื่อมไลน์ ตอนสมัคร
        $builder = $this->db->table($this->table);

        $builder->set('post_data', $data['post_data']);
        $builder->set('response_data', $data['response_data']);
        $builder->set('type', $data['type']);
        $builder->set('user_id', $data['user_id']);
        $builder->set('ip_address', $data['ip_address']);

        $builder->set('url', current_url());
        $builder->set('method', 'POST');
        $builder->set('user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
        $builder->set('created_at', date('Y-m-d H:i:s'));
        $builder->insert();
    }
    /*

    function editLog($input)
    {
        $builder = $this->db->table($this->table);
        if (!empty($input['logname'])) {
            $builder->set('logname', $input['logname']);
        }
        if (!empty($input['password'])) {
            $builder->set('password', $input['password']);
        }

        if (!empty($input['id'])) {
            $builder->where('id', $input['id']);
            $builder->update();
        } else {
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->insert();
        }
    }

    function getOneLog($id = '', $uid_line = '')
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        if ($id) {
            $builder->where('id', $id);
        }
        if ($uid_line) {
            $builder->where('uid_line', $uid_line);
        }
        $result = $builder->get()->getRowArray();
        return $result;
    }
    */
}
