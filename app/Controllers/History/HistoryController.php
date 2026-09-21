<?php

namespace App\Controllers\History;

use App\Controllers\BaseController;
use App\Models\HistoryModel;

class HistoryController extends BaseController
{
    protected $format = 'json';

    public function getCalculationHistory()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new HistoryModel())->getCalculationHistory([
                'PlanCode' => $this->payloadValue($requestData, ['PlanCode', 'planCode']),
                'Gender'   => $this->payloadValue($requestData, ['Gender', 'gender']),
                'FromDate' => $this->payloadValue($requestData, ['FromDate', 'fromDate']),
                'ToDate'   => $this->payloadValue($requestData, ['ToDate', 'toDate']),
                'Page'     => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize' => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Calculation history fetched successfully',
                    'res_massage' => 'Calculation history fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['history'] ?? [],
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

    private function payloadValue(array $data, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== '' && $data[$key] !== null) {
                return $data[$key];
            }
        }

        return $default;
    }
}
