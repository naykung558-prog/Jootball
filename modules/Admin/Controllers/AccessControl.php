<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;

class AccessControl extends BaseController
{
    public function accessDenied()
    {
        return view('Modules\Admin\Views\access_denied');
    }
}