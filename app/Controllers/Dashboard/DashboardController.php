<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\DashboardModel;

class DashboardController extends BaseController
{
    protected $format = 'json';

    public function getDashboardSummary()
    {
        try {
            $row = (new DashboardModel())->getDashboardSummary();

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Dashboard summary fetched successfully',
                    'res_massage' => 'Dashboard summary fetched successfully',
                    'data'        => $row,
                ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (preg_match('/\[SQL Server\](.*)$/s', $message, $matches)) {
                $message = trim($matches[1]);
            }

            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $message,
                    'res_massage' => $message,
                ]);
        }
    }
}
