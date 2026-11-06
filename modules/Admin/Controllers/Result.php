<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\ResultModel;
use Modules\Admin\Models\TournamentModel;

class Result extends BaseController
{
    public function __construct() {
        $this->ResultModel = new ResultModel();
        $this->TournamentModel = new TournamentModel();
    }

    public function index()
    {
        $data = array();
        $data['tour_id'] = 0;
        $data['matches'] = $this->ResultModel->getMatch($data['tour_id']);


        return view('Modules\Admin\Views\Result\index',$data);
    }

    public function group()
    {
        $data = array();
        $data['tour_id'] = 0;
        $data['matches'] = $this->ResultModel->getMatch($data['tour_id']);


        return view('Modules\Admin\Views\Result\group',$data);
    }

}
