<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ProdCloneService;

class ApiManager extends BaseController
{
    public function index()
    {
        $service = new ProdCloneService();
        $cloneEnvErrors = $service->checkEnvironment();
        $rootReal = realpath(ROOTPATH) ?: ROOTPATH;
        return view('Modules\Admin\Views\Api\index', [
            'cloneEnvErrors'   => $cloneEnvErrors,
            'cloneCurrentDb'   => env('database.default.database', ''),
            'cloneCurrentPath' => rtrim(str_replace('\\', '/', $rootReal), '/'),
            'cloneCurrentHost' => $_SERVER['HTTP_HOST'] ?? '',
        ]);
    }

    public function cloneFromProd()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'AJAX only']);
        }

        $confirm = trim((string) $this->request->getPost('confirm'));
        if ($confirm !== 'CLONE') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'error'   => 'confirmation text mismatch (must type "CLONE")',
            ]);
        }

        try {
            $service = new ProdCloneService();
            // Safety guards — throws if not safe to run
            $service->assertSafeEnvironment();
            $result  = $service->cloneAll();

            return $this->response->setJSON($result);
        } catch (\Throwable $e) {
            log_message('error', 'CloneFromProd exception: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
