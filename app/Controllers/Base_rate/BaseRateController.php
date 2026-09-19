<?php

namespace App\Controllers\Base_rate;

use App\Controllers\BaseController;
use App\Models\BaseRateModel;

class BaseRateController extends BaseController
{
    protected $format = 'json';

    public function getBaseRates()
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

            $model  = new BaseRateModel();
            $result = $model->getBaseRates([
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
                    'message'     => 'Base rates fetched successfully',
                    'res_massage' => 'Base rates fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['baseRates'] ?? [],
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

            $id      = $this->payloadValue($requestData, ['AgeBandId', 'ageBandId', 'Id', 'id']);
            $planId  = $this->payloadValue($requestData, ['PlanId', 'planId']);
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

            $result = (new BaseRateModel())->getById([
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
                    'message'     => 'Base rate fetched successfully',
                    'res_massage' => 'Base rate fetched successfully',
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

    public function saveBaseRate()
    {
        return $this->writeBaseRate(false);
    }

    public function updateBaseRate()
    {
        return $this->writeBaseRate(true);
    }

    private function writeBaseRate($isUpdate)
    {
        try {
            $payload = $this->baseRateWritePayload();
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $model  = new BaseRateModel();
            $result = $isUpdate
                ? $model->updateBaseRate($payload)
                : $model->saveBaseRate($payload);

            $message = $isUpdate
                ? 'Base rate updated successfully'
                : 'Base rate created successfully';

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

    private function baseRateWritePayload()
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
        $male     = $this->payloadValue($requestData, ['MalePremium', 'malePremium', 'Male', 'male']);
        $female   = $this->payloadValue($requestData, ['FemalePremium', 'femalePremium', 'Female', 'female']);

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
        if ($male === null || $male === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Male premium is required',
                    'res_massage' => 'Male premium is required',
                ]);
        }
        if ($female === null || $female === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Female premium is required',
                    'res_massage' => 'Female premium is required',
                ]);
        }

        return [
            'AgeBandId'     => (int) $id,
            'PlanId'        => ($planId === null || $planId === '') ? null : (int) $planId,
            'PlanCode'      => $planCode,
            'RegionCode'    => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
            'MalePremium'   => $male,
            'FemalePremium' => $female,
            'AsOfDate'      => $this->payloadValue($requestData, ['AsOfDate', 'asOfDate']),
        ];
    }

    public function getDropdowns()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new BaseRateModel())->getDropdowns([
                'RegionCode' => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Base rate dropdowns fetched successfully',
                    'res_massage' => 'Base rate dropdowns fetched successfully',
                    'data'        => [
                        'regions' => $result['regions'] ?? [],
                        'plans'   => $result['plans'] ?? [],
                    ],
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
