<?php

namespace App\Controllers\Maternity;

use App\Controllers\BaseController;
use App\Models\MaternityModel;

class MaternityController extends BaseController
{
    protected $format = 'json';

    public function getMaternityRates()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $planId   = $this->payloadValue($requestData, ['PlanId', 'planId']);
            $planCode = $this->payloadValue($requestData, ['PlanCode', 'planCode']);
            $region   = $this->payloadValue($requestData, ['RegionCode', 'regionCode']);

            $result = (new MaternityModel())->getMaternityRates([
                'RegionCode' => $region,
                'PlanId'     => ($planId === null || $planId === '') ? null : (int) $planId,
                'PlanCode'   => $planCode,
                'Page'       => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'   => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
                'AsOfDate'   => $this->payloadValue($requestData, ['AsOfDate', 'asOfDate']),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Maternity rates fetched successfully',
                    'res_massage' => 'Maternity rates fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['maternityRates'] ?? [],
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

    public function getById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $id       = $this->payloadValue($requestData, ['AgeBandId', 'ageBandId', 'Id', 'id']);
            $planId   = $this->payloadValue($requestData, ['PlanId', 'planId']);
            $planCode = $this->payloadValue($requestData, ['PlanCode', 'planCode']);

            if ($id === null || $id === '') {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'AgeBandId is required',
                        'res_massage' => 'AgeBandId is required',
                    ]);
            }
            if (($planId === null || $planId === '') && ($planCode === null || $planCode === '')) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId or PlanCode is required',
                        'res_massage' => 'PlanId or PlanCode is required',
                    ]);
            }

            $result = (new MaternityModel())->getById([
                'AgeBandId'  => (int) $id,
                'PlanId'     => ($planId === null || $planId === '') ? null : (int) $planId,
                'PlanCode'   => $planCode,
                'RegionCode' => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
                'AsOfDate'   => $this->payloadValue($requestData, ['AsOfDate', 'asOfDate']),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Maternity rate fetched successfully',
                    'res_massage' => 'Maternity rate fetched successfully',
                    'data'        => $result,
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

    public function saveMaternityRate()
    {
        return $this->writeMaternityRate(false);
    }

    public function updateMaternityRate()
    {
        return $this->writeMaternityRate(true);
    }

    private function writeMaternityRate($isUpdate)
    {
        try {
            $payload = $this->maternityWritePayload();
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $model  = new MaternityModel();
            $result = $isUpdate
                ? $model->updateMaternityRate($payload)
                : $model->saveMaternityRate($payload);

            $message = $isUpdate
                ? 'Maternity rate updated successfully'
                : 'Maternity rate created successfully';

            return $this->response->setStatusCode($isUpdate ? 200 : 201)
                ->setJSON([
                    'res_code'    => $isUpdate ? 200 : 201,
                    'status'      => true,
                    'message'     => $message,
                    'res_massage' => $message,
                    'data'        => $result,
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

    private function maternityWritePayload()
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            $requestData = [];
        }

        $id       = $this->payloadValue($requestData, ['AgeBandId', 'ageBandId', 'Id', 'id']);
        $planId   = $this->payloadValue($requestData, ['PlanId', 'planId']);
        $planCode = $this->payloadValue($requestData, ['PlanCode', 'planCode']);
        $premium  = $this->payloadValue($requestData, ['MaternityPremium', 'maternityPremium', 'Premium', 'premium']);

        if ($id === null || $id === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'AgeBandId is required',
                    'res_massage' => 'AgeBandId is required',
                ]);
        }
        if (($planId === null || $planId === '') && ($planCode === null || $planCode === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'PlanId or PlanCode is required',
                    'res_massage' => 'PlanId or PlanCode is required',
                ]);
        }
        if ($premium === null || $premium === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Maternity premium is required',
                    'res_massage' => 'Maternity premium is required',
                ]);
        }

        return [
            'AgeBandId'         => (int) $id,
            'PlanId'            => ($planId === null || $planId === '') ? null : (int) $planId,
            'PlanCode'          => $planCode,
            'RegionCode'        => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
            'MaternityPremium'  => $premium,
            'AsOfDate'          => $this->payloadValue($requestData, ['AsOfDate', 'asOfDate']),
        ];
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
