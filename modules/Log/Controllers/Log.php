<?php

namespace Modules\Log\Controllers;

use App\Controllers\BaseController;
use Modules\Log\Models\LogModel;
use \Hermawan\DataTables\DataTable;

class Log extends BaseController
{
    protected $logModel;

    public function __construct()
    {
        $this->logModel = new LogModel;
    }

    public function index()
    {
        return view('Modules\Log\Views\index.php');
    }

    public function logs()
    {
       
    }

    public function manage($id = '')
    {
        
    }

    function saveLog()
    {
        
    }

    function deleteLog()
    {
       
    }
}
