<?php

namespace App\Controllers\Adjustment;

use App\Controllers\BaseController;
use App\Models\AdjustmentFactorModel;

class AdjustmentController extends BaseController
{
    protected $format = 'json';

    public function getAdjustmentFactors()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new AdjustmentFactorModel())->getAdjustmentFactors([
                'FactorType' => $this->payloadValue($requestData, ['FactorType', 'factorType', 'Type', 'type']),
                'IsActive'   => $this->optionalBit($this->payloadValue($requestData, ['IsActive', 'isActive'])),
                'Page'       => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'   => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Adjustment factors fetched successfully',
                    'res_massage' => 'Adjustment factors fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['adjustmentFactors'] ?? [],
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

            $id         = $this->payloadValue($requestData, ['AdjustmentFactorId', 'adjustmentFactorId', 'Id', 'id']);
            $factorType = $this->payloadValue($requestData, ['FactorType', 'factorType', 'Type', 'type']);
            $code       = $this->payloadValue($requestData, ['Code', 'code', 'OptionCode', 'optionCode']);

            if (($id === null || $id === '') && ($factorType === null || $factorType === '' || $code === null || $code === '')) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'AdjustmentFactorId or FactorType and Code is required',
                        'res_massage' => 'AdjustmentFactorId or FactorType and Code is required',
                    ]);
            }

            $result = (new AdjustmentFactorModel())->getById([
                'AdjustmentFactorId' => ($id === null || $id === '') ? null : (int) $id,
                'FactorType'         => $factorType,
                'Code'               => $code,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Adjustment fetched successfully',
                    'res_massage' => 'Adjustment fetched successfully',
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

    public function saveAdjustmentFactor()
    {
        return $this->writeAdjustmentFactor(false);
    }

    public function updateAdjustmentFactor()
    {
        return $this->writeAdjustmentFactor(true);
    }

    public function deleteAdjustmentFactor()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $id         = $this->payloadValue($requestData, ['AdjustmentFactorId', 'adjustmentFactorId', 'Id', 'id']);
            $factorType = $this->payloadValue($requestData, ['FactorType', 'factorType', 'Type', 'type']);
            $code       = $this->payloadValue($requestData, ['Code', 'code', 'OptionCode', 'optionCode']);

            if (($id === null || $id === '') && ($factorType === null || $factorType === '' || $code === null || $code === '')) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'AdjustmentFactorId or FactorType and Code is required',
                        'res_massage' => 'AdjustmentFactorId or FactorType and Code is required',
                    ]);
            }

            $result = (new AdjustmentFactorModel())->deleteAdjustmentFactor([
                'AdjustmentFactorId' => ($id === null || $id === '') ? null : (int) $id,
                'FactorType'         => $factorType,
                'Code'               => $code,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Adjustment marked inactive',
                    'res_massage' => 'Adjustment marked inactive',
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

    private function writeAdjustmentFactor($isUpdate)
    {
        try {
            $payload = $this->adjustmentWritePayload($isUpdate);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $model  = new AdjustmentFactorModel();
            $result = $isUpdate
                ? $model->updateAdjustmentFactor($payload)
                : $model->saveAdjustmentFactor($payload);

            $message = $isUpdate
                ? 'Adjustment updated successfully'
                : 'Adjustment created successfully';

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

    private function adjustmentWritePayload($isUpdate = false)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            $requestData = [];
        }

        $id         = $this->payloadValue($requestData, ['AdjustmentFactorId', 'adjustmentFactorId', 'Id', 'id']);
        $factorType = $this->payloadValue($requestData, ['FactorType', 'factorType', 'Type', 'type']);
        $code       = $this->payloadValue($requestData, ['Code', 'code', 'OptionCode', 'optionCode']);
        $label      = $this->payloadValue($requestData, ['Label', 'label', 'OptionLabel', 'optionLabel']);
        $factor     = $this->payloadValue($requestData, ['Factor', 'factor', 'AdjustmentFactor', 'adjustmentFactor']);
        $fromDate   = $this->payloadValue($requestData, ['FromDate', 'fromDate', 'EffectiveFrom', 'effectiveFrom']);
        $toDate     = $this->payloadValue($requestData, ['ToDate', 'toDate', 'EffectiveTo', 'effectiveTo']);
        $isActive   = $this->optionalBit($this->payloadValue($requestData, ['IsActive', 'isActive']));

        if ($isUpdate && ($id === null || $id === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'AdjustmentFactorId is required',
                    'res_massage' => 'AdjustmentFactorId is required',
                ]);
        }
        if (! $isUpdate && ($factorType === null || $factorType === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Factor type is required',
                    'res_massage' => 'Factor type is required',
                ]);
        }
        if (! $isUpdate && ($code === null || $code === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Option code is required',
                    'res_massage' => 'Option code is required',
                ]);
        }
        if ($label === null || $label === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Label is required',
                    'res_massage' => 'Label is required',
                ]);
        }
        if ($factor === null || $factor === '' || ! is_numeric($factor) || (float) $factor < 0) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Factor must be zero or greater',
                    'res_massage' => 'Factor must be zero or greater',
                ]);
        }
        if ((float) $factor >= 10000) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Factor must be a number from 0 to 9999.9999',
                    'res_massage' => 'Factor must be a number from 0 to 9999.9999',
                ]);
        }
        if ($fromDate === null || $fromDate === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'From date is required',
                    'res_massage' => 'From date is required',
                ]);
        }

        return [
            'AdjustmentFactorId' => ($id === null || $id === '') ? null : (int) $id,
            'FactorType'         => $factorType,
            'Code'               => $code,
            'Label'              => $label,
            'Factor'             => $factor,
            'FromDate'           => $fromDate,
            'ToDate'             => $toDate,
            'IsActive'           => $isActive === null ? 1 : $isActive,
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

    private function optionalBit($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value === true || $value === 1 || $value === '1') {
            return 1;
        }

        if ($value === false || $value === 0 || $value === '0') {
            return 0;
        }

        return null;
    }
}
