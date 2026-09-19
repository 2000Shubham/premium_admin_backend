<?php

namespace App\Controllers\Annual_limit;

use App\Controllers\BaseController;
use App\Models\AnnualLimitModel;

class AnnualLimitController extends BaseController
{
    protected $format = 'json';

    public function getAnnualLimitFactors()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new AnnualLimitModel())->getAnnualLimitFactors([
                'NetworkCode' => $this->payloadValue($requestData, ['NetworkCode', 'networkCode']),
                'RegionCode'  => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
                'IsActive'    => $this->optionalBit($this->payloadValue($requestData, ['IsActive', 'isActive'])),
                'Page'        => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'    => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Annual limit factors fetched successfully',
                    'res_massage' => 'Annual limit factors fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['annualLimitFactors'] ?? [],
                    'networks'    => $result['networks'] ?? [],
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

            $id    = $this->payloadValue($requestData, ['AnnualLimitFactorId', 'annualLimitFactorId', 'Id', 'id']);
            $limit = $this->payloadValue($requestData, ['AnnualLimit', 'annualLimit', 'Limit', 'limit']);

            if (($id === null || $id === '') && ($limit === null || $limit === '')) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'AnnualLimitFactorId or AnnualLimit is required',
                        'res_massage' => 'AnnualLimitFactorId or AnnualLimit is required',
                    ]);
            }

            $result = (new AnnualLimitModel())->getById([
                'AnnualLimitFactorId' => ($id === null || $id === '') ? null : (int) $id,
                'AnnualLimit'         => ($limit === null || $limit === '') ? null : (int) $limit,
                'RegionCode'          => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Annual limit factor fetched successfully',
                    'res_massage' => 'Annual limit factor fetched successfully',
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

    public function saveAnnualLimitFactor()
    {
        return $this->writeAnnualLimitFactor(false);
    }

    public function updateAnnualLimitFactor()
    {
        return $this->writeAnnualLimitFactor(true);
    }

    private function writeAnnualLimitFactor($isUpdate)
    {
        try {
            $payload = $this->annualLimitWritePayload($isUpdate);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $model  = new AnnualLimitModel();
            $result = $isUpdate
                ? $model->updateAnnualLimitFactor($payload)
                : $model->saveAnnualLimitFactor($payload);

            $message = $isUpdate
                ? 'Annual limit factor updated successfully'
                : 'Annual limit factor created successfully';

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

    private function annualLimitWritePayload($isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            $requestData = [];
        }

        $limit   = $this->payloadValue($requestData, ['AnnualLimit', 'annualLimit', 'Limit', 'limit']);
        $factors = $this->normalizeFactors(
            $this->payloadValue($requestData, ['Factors', 'factors'])
        );

        if ($limit === null || $limit === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'AnnualLimit is required',
                    'res_massage' => 'AnnualLimit is required',
                ]);
        }
        if ($factors === null) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Factors JSON is required',
                    'res_massage' => 'Factors JSON is required',
                ]);
        }

        return [
            'AnnualLimit'   => (int) $limit,
            'Factors'       => $factors,
            'EffectiveFrom' => $this->payloadValue($requestData, ['EffectiveFrom', 'effectiveFrom']),
        ];
    }

    private function normalizeFactors($factors)
    {
        if ($factors === null || $factors === '') {
            return null;
        }

        if (is_string($factors)) {
            return json_decode($factors, true) === null ? null : $factors;
        }

        if (! is_array($factors) || $factors === []) {
            return null;
        }

        $rows = [];
        if (array_is_list($factors)) {
            foreach ($factors as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $rows[] = [
                    'NetworkCode'      => $item['NetworkCode'] ?? $item['networkCode'] ?? $item['Code'] ?? $item['code'] ?? null,
                    'NetworkId'        => $item['NetworkId'] ?? $item['networkId'] ?? null,
                    'AdjustmentFactor' => $item['AdjustmentFactor'] ?? $item['adjustmentFactor'] ?? $item['Factor'] ?? $item['factor'] ?? null,
                ];
            }
        } else {
            foreach ($factors as $code => $factor) {
                if (is_array($factor)) {
                    $rows[] = [
                        'NetworkCode'      => $factor['NetworkCode'] ?? $factor['networkCode'] ?? $code,
                        'NetworkId'        => $factor['NetworkId'] ?? $factor['networkId'] ?? null,
                        'AdjustmentFactor' => $factor['AdjustmentFactor'] ?? $factor['adjustmentFactor'] ?? $factor['Factor'] ?? $factor['factor'] ?? null,
                    ];
                    continue;
                }
                $rows[] = [
                    'NetworkCode'      => $code,
                    'AdjustmentFactor' => $factor,
                ];
            }
        }

        if ($rows === []) {
            return null;
        }

        $json = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json === false ? null : $json;
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
