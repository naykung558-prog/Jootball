<?php

namespace Modules\User\Controllers;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use \Hermawan\DataTables\DataTable;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel;
    }

    public function index()
    {
        return view('Modules\User\Views\index.php');
    }

    public function users()
    {
       
    }

    public function manage($id = '')
    {
        
    }

    function saveUser()
    {
        
    }

    function deleteUser()
    {
       
    }
}
