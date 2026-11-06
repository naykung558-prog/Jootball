<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\Mydate;

class Main_Model extends Model
{
    protected $table = '';           
    protected $primaryKey = 'id';
    protected $useSoftDeletes = false;
    protected $allowedFields = '';

    /**
     * ตั้งค่าชื่อตารางและ primary key ก่อนเรียกใช้งานคำสั่งพื้นฐาน
     */
    public function setTableAndKey(string $table, string $primaryKey = 'id')
    {
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function setSoftDelete(bool $useSoftDeletes)
    {
        $this->useSoftDeletes = $useSoftDeletes;
    }
    // ----- ฟังก์ชันสำหรับค้นหาข้อมูล -----

    /**
     * ค้นหาข้อมูลตาม id
     */
    public function find_ji(string $table, string $primaryKey, $id)
    {
        $this->setTableAndKey($table, $primaryKey);
        return $this->asArray()->find($id);
    }

    /**
     * ค้นหาทั้งหมด หรือระบุจำนวน
     */
    public function findAll_ji(string $table, int $limit = 0, int $offset = 0)
    {
        $this->setTableAndKey($table);
        return $this->asArray()->findAll($limit, $offset);
    }

    /**
     * ดึงเฉพาะคอลัมน์ที่ระบุ
     */
    public function findColumn_ji(string $table, string $primaryKey, string $column)
    {
        $this->setTableAndKey($table, $primaryKey);
        return $this->findColumn($column);
    }

    public function findWhere_ji(string $table, array $where = [], int $limit = 0, int $offset = 0)
    {
        $this->setTableAndKey($table);

        $builder = $this->db->table($table);

        if (!empty($where)) {
            $builder->where($where);
        }

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    // ตัวอย่าง conditions สำหรับ findWhere_ji_advance
    // $conditions = [
    //     'where' => [
    //         'status' => 'active',
    //     ],
    //     'orWhere' => [
    //         'role' => 'admin',
    //     ],
    //     'like' => [
    //         ['field' => 'username', 'value' => 'john', 'position' => 'both']
    //     ],
    //     'between' => [
    //         ['field' => 'created_date', 'start' => '2024-01-01', 'end' => '2024-12-31']
    //     ],
    //     'whereIn' => [
    //         'country' => ['Thailand', 'Japan']
    //     ]
    // ];
    public function findWhere_ji_advance(string $table, array $conditions = [], int $limit = 0, int $offset = 0, array $orderBy = [])
    {
        $this->setTableAndKey($table);
        $builder = $this->db->table($table);

        // --- WHERE เงื่อนไขพื้นฐาน ---
        if (!empty($conditions['where']) && is_array($conditions['where'])) {
            $builder->where($conditions['where']);
        }

        // --- OR WHERE ---
        if (!empty($conditions['orWhere']) && is_array($conditions['orWhere'])) {
            foreach ($conditions['orWhere'] as $field => $value) {
                $builder->orWhere($field, $value);
            }
        }

        // --- WHERE IN ---
        if (!empty($conditions['whereIn']) && is_array($conditions['whereIn'])) {
            foreach ($conditions['whereIn'] as $field => $values) {
                $builder->whereIn($field, $values);
            }
        }

        // --- LIKE ---
        if (!empty($conditions['like']) && is_array($conditions['like'])) {
            foreach ($conditions['like'] as $like) {
                if (isset($like['field'], $like['value'])) {
                    $position = $like['position'] ?? 'both'; // ค่าเริ่มต้น
                    $builder->like($like['field'], $like['value'], $position);
                }
            }
        }

        // --- BETWEEN ---
        if (!empty($conditions['between']) && is_array($conditions['between'])) {
            foreach ($conditions['between'] as $between) {
                if (isset($between['field'], $between['start'], $between['end'])) {
                    $builder->where("{$between['field']} >=", $between['start']);
                    $builder->where("{$between['field']} <=", $between['end']);
                }
            }
        }

        // --- ORDER BY ---
        foreach ($orderBy as $field => $direction) {
            $builder->orderBy($field, $direction);
        }

        // --- LIMIT / OFFSET ---
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * ดึงแถวแรกสุด
     */
    public function first_ji(string $table)
    {
        $this->setTableAndKey($table);
        return $this->asArray()->first();
    }

    /**
     * ค้นหาข้อมูลรวม soft delete
     */
    public function withDeleted_ji(string $table)
    {
        $this->setTableAndKey($table);
        return $this->asArray()->withDeleted()->findAll();
    }

    /**
     * ค้นหาเฉพาะ soft deleted
     */
    public function onlyDeleted_ji(string $table)
    {
        $this->setTableAndKey($table);
        return $this->asArray()->onlyDeleted()->findAll();
    }

    // ----- ฟังก์ชันบันทึกข้อมูล -----

    /**
     * แทรกข้อมูลใหม่
     */
    public function insert_ji(string $table, array $data)
    {
        $this->setTableAndKey($table);
        return $this->insert($data);
    }

    /**
     * อนุญาตให้ insert ข้อมูลว่างได้
     */
    public function allowEmptyInserts_ji(string $table)
    {
        $this->setTableAndKey($table);
        return $this->allowEmptyInserts();
    }

    /**
     * อัปเดตข้อมูลตาม id
     */
    public function update_ji(string $table, string $primaryKey, $id, array $data)
    {
        $this->setTableAndKey($table, $primaryKey);
        return $this->update($id, $data);
    }

    /**
     * save ข้อมูล (insert หรือ update อัตโนมัติ)
     */
    public function save_ji(string $table, string $primaryKey, array $data)
    {
        $this->setTableAndKey($table, $primaryKey);
        return $this->save($data);
    }

    // ----- ฟังก์ชันลบข้อมูล -----

    /**
     * ลบข้อมูลตาม id และฟิลด์ที่ระบุ
     * 
     * @param string $table ชื่อตาราง
     * @param string $field ชื่อฟิลด์ที่ใช้ในการค้นหา (ค่าเริ่มต้นเป็น primary key)
     * @param mixed $id ค่าที่ใช้ในการค้นหา
     * @param bool $setSoftDelete เปิดใช้งาน soft delete หรือไม่
     * @return bool ผลลัพธ์การลบข้อมูล
     */
    public function delete_ji(string $table, string $field, $id, bool $setSoftDelete = false): bool
    {
        $this->setTableAndKey($table, $field);
        $this->setSoftDelete($setSoftDelete);
        
        // สร้าง query builder
        $builder = $this->db->table($table);
        
        // ถ้าใช้ soft delete
        if ($setSoftDelete) {
            return $builder->update(['deleted_at' => date('Y-m-d H:i:s')], [$field => $id]);
        }
        
        // ลบข้อมูลตามฟิลด์และค่า id ที่ระบุ
        return $builder->delete([$field => $id]);
    }

    /**
     * ลบข้อมูล soft delete ทิ้งถาวร
     */
    public function purgeDeleted_ji(string $table, string $primaryKey)
    {
        $this->setTableAndKey($table, $primaryKey);
        return $this->purgeDeleted();
    }

    // ----- ฟังก์ชันบันทึกข้อมูล พร้อมแนบไฟล์ -----

    public function save_data(string $table, string $pk, array $input)
    {
        $session = session();
        $this->Mydate = new Mydate();
        $builder = $this->db->table($table);

        try {
            $this->db->transBegin();

            // เตรียมข้อมูลทั่วไป
            $data = [];
            if (!empty($input['DATA'])) {
                foreach ($input['DATA'] as $field => $value) {
                    if ($field !== $pk) {
                        $data[$field] = htmlentities($value, ENT_QUOTES, 'UTF-8');
                    }
                }
            }

            // แปลงวันที่
            if (!empty($input['DATA_DATE'])) {
                $add_year = $session->get('lang') === 'th' ? 543 : 0;
                foreach ($input['DATA_DATE'] as $field => $value) {
                    if (!empty($value)) {
                        $data[$field] = $this->Mydate->date_thai2eng($value, -$add_year);
                    }
                }
            }

            $current_time = date('Y-m-d H:i:s');
            $username = $session->get('username');

            // แยก insert หรือ update
            if (!empty($input[$pk])) {
                $builder->where($pk, $input[$pk])
                        ->set('updated_by', $username)
                        ->set('updated_date', $current_time)
                        ->set($data)
                        ->update();
                $pk_id = $input[$pk];
            } else {
                $data['created_by'] = $username;
                $data['created_date'] = $current_time;
                $builder->set($data)->insert();
                $pk_id = $this->db->insertID();
            }


            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                throw new \RuntimeException('Transaction failed');
            }

            $this->db->transCommit();
            return $pk_id;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error saving data: ' . $e->getMessage());
            throw new \RuntimeException('Unable to save data: ' . $e->getMessage());
        }
    }

    function save_attach(string $table, string $pk,int $pk_id,array $files, string $path )
    {    
        $session = session();
        $current_time = date('Y-m-d H:i:s');
        $username = $session->get('username');
        $uploadPath = 'public/uploads/'.$path.'/';
        $results = [];

        foreach ($files as $index => $file) {
            if ($file instanceof \CodeIgniter\HTTP\Files\UploadedFile) {

                // ตรวจสอบว่าไฟล์ถูกต้อง
                if ($file->isValid() && in_array($file->getClientMimeType(), ['application/vnd.ms-excel', 'image/jpg', 'image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                    // สร้างชื่อไฟล์ใหม่แบบสุ่มที่ไม่ซ้ำ
                    $newName = $this->generateUniqueFilename($file->getClientExtension());

                    // // Check if the upload directory exists, create it if it doesn't
                    // if (!is_dir($uploadPath)) {
                    //     if (!mkdir($uploadPath, 0777, true)) {
                    //         throw new Exception('Failed to create upload directory: ' . $uploadPath);
                    //     }
                    // }

                    // // Set directory permissions to 777 (rwxrwxrwx)
                    // if (!chmod($uploadPath, 0777)) {
                    //     throw new Exception('Failed to set permissions on directory: ' . $uploadPath);
                    // }
                    
                    // ย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง
                    $filePath = $uploadPath . $newName;

                    if ($file->move($uploadPath, $newName)) {
                        // คำนวณขนาดไฟล์เป็น MB
                        $sizeMb = $file->getSize() / (1024 * 1024);

                        // บันทึกข้อมูลลงฐานข้อมูล
                        
                        $data = [
                            $pk => $pk_id,
                            'attach_name' => $file->getName(),
                            'attach_file' => $filePath,
                            'attach_file_type' => $file->getClientMimeType(),
                            'attach_size' => round($sizeMb, 2)
                        ];
                        $data['created_by'] = $username;
                        $data['created_date'] = $current_time;

                        $builder = $this->db->table($table);
                        $builder->set($data)->insert();

                        $results[$index] = ['status' => 'success', 'message' => 'อัปโหลดไฟล์ ' . $file->getName() . ' เสร็จสิ้น', 'data' => $data];
                    } else {
                        $results[$index] = ['status' => 'error', 'message' => 'ไม่สามารถย้ายไฟล์ ' . $file->getName() . ' ได้: ' . $file->getErrorString()];
                    }
                } else {
                    $results[$index] = ['status' => 'error', 'message' => 'ไฟล์ ' . $file->getName() . ' ไม่ถูกต้องหรือไม่ได้รับอนุญาต'];
                }
            } else {
                $results[$index] = ['status' => 'error', 'message' => 'ไฟล์ไม่ใช่ UploadedFile object'];
            }
        }

        return $results;
       
    }

    function upload_attach($file, string $path )
    {

        $session = session();
        $current_time = date('Y-m-d H:i:s');
        $username = $session->get('username');
        $uploadPath = 'public/uploads/'.$path.'/'.date('Y').'/'.date('m').'/';
        $results = [];
        if ($file instanceof \CodeIgniter\HTTP\Files\UploadedFile) {
            if ($file->isValid() && in_array($file->getClientMimeType(), ['application/vnd.ms-excel', 'image/jpg', 'image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                // สร้างชื่อไฟล์ใหม่แบบสุ่มที่ไม่ซ้ำ
                $newName = $this->generateUniqueFilename($file->getClientExtension());

                // Check if the upload directory exists, create it if it doesn't
                if (!is_dir($uploadPath)) {
                    if (!mkdir($uploadPath, 0777, true)) {
                        throw new Exception('Failed to create upload directory: ' . $uploadPath);
                    }
                }

                // Set directory permissions to 777 (rwxrwxrwx)
                if (!chmod($uploadPath, 0777)) {
                    throw new Exception('Failed to set permissions on directory: ' . $uploadPath);
                }
                
                // ย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง
                $filePath = $uploadPath . $newName;

                if ($file->move($uploadPath, $newName)) {
                    // คำนวณขนาดไฟล์เป็น MB
                    $sizeMb = $file->getSize() / (1024 * 1024);
                    $data = [
                        $pk => $pk_id,
                        'attach_name' => $file->getName(),
                        'attach_file' => $filePath,
                        'attach_file_type' => $file->getClientMimeType(),
                        'attach_size' => round($sizeMb, 2)
                    ];


                    $results[$index] = ['status' => 'success', 'message' => 'อัปโหลดไฟล์ ' . $file->getName() . ' เสร็จสิ้น', 'data' => $data];
                } else {
                    $results[$index] = ['status' => 'error', 'message' => 'ไม่สามารถย้ายไฟล์ ' . $file->getName() . ' ได้: ' . $file->getErrorString()];
                }
            } else {
                $results[$index] = ['status' => 'error', 'message' => 'ไฟล์ ' . $file->getName() . ' ไม่ถูกต้องหรือไม่ได้รับอนุญาต'];
            }
        }

        return $results;

    }

    public function generateUniqueFilename($extension)
    {
        $uniqueId = bin2hex(random_bytes(16)); 
        $newName = $uniqueId . '.' . $extension;

        return $newName;
    }

    public function findJoin_ji(
        string $mainTable,
        array $joins = [],              // [['table' => 'other_table', 'condition' => 'main.id = other.fk_id', 'type' => 'left']]
        array $selectFields = [],       // ['main.id', 'other.name']
        array $where = [],              // ['status' => 'active']
        int $limit = 0,
        int $offset = 0,
        array $orderBy = []             // ['main.id' => 'DESC']
    ) {
        $this->setTableAndKey($mainTable);
        $builder = $this->db->table($mainTable);

        // SELECT
        if (!empty($selectFields)) {
            $builder->select(implode(',', $selectFields));
        } else {
            $builder->select("$mainTable.*");
        }

        // JOINs
        foreach ($joins as $join) {
            $type = strtolower($join['type'] ?? 'inner');
            switch ($type) {
                case 'left':
                    $builder->join($join['table'], $join['condition'], 'left');
                    break;
                case 'right':
                    $builder->join($join['table'], $join['condition'], 'right');
                    break;
                default:
                    $builder->join($join['table'], $join['condition']); // default inner
            }
        }

        // WHERE
        if (!empty($where)) {
            $builder->where($where);
        }

        // ORDER BY
        foreach ($orderBy as $field => $direction) {
            $builder->orderBy($field, $direction);
        }

        // LIMIT / OFFSET
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    public function count_ji(
        string $table,
        array $where = [],           // เงื่อนไข where
        string $groupBy = ''         // group by (เลือกฟิลด์เดียว)
    ) {
        $this->setTableAndKey($table);
        $builder = $this->db->table($table);

        // WHERE
        if (!empty($where)) {
            $builder->where($where);
        }

        // GROUP BY
        if (!empty($groupBy)) {
            $builder->select("$groupBy, COUNT(*) as cnt");
            $builder->groupBy($groupBy);
            return $builder->get()->getResultArray(); // คืนค่าเป็น array ของกลุ่ม
        }

        // ไม่มี group by => คืนค่า count ธรรมดา
        return $builder->countAllResults();
    }

}
