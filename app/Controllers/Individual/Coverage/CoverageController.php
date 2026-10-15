<?php

namespace App\Controllers\Individual\Coverage;

use App\Controllers\BaseController;
use App\Models\Individual\CoverageModel;

class CoverageController extends BaseController
{
    protected $format = 'json';

    public function getCoverageAreas()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new CoverageModel())->getCoverageAreas([
                'Search'    => $this->payloadValue($requestData, ['Search', 'search']),
                'CoverType' => $this->payloadValue($requestData, ['CoverType', 'coverType']),
                'Active'    => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'      => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'  => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Coverage areas fetched successfully',
                    'res_massage' => 'Coverage areas fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['areas'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getCoverageAreaById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id             = $this->payloadValue($requestData, ['Id', 'id']);
            $coverageAreaId = $this->payloadValue($requestData, ['CoverageAreaId', 'coverageAreaId']);

            if (($id === null || $id === '') && ($coverageAreaId === null || $coverageAreaId === '')) {
                return $this->validationError('Id or CoverageAreaId is required');
            }

            $result = (new CoverageModel())->getCoverageAreaById([
                'Id'             => ($id === null || $id === '') ? null : (int) $id,
                'CoverageAreaId' => ($coverageAreaId === null || $coverageAreaId === '') ? null : (int) $coverageAreaId,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Coverage area fetched successfully',
                    'res_massage' => 'Coverage area fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function saveCoverageArea()
    {
        try {
            $payload = $this->coveragePayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new CoverageModel())->saveCoverageArea($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Coverage area created successfully',
                    'res_massage' => 'Coverage area created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updateCoverageArea()
    {
        try {
            $payload = $this->coveragePayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new CoverageModel())->updateCoverageArea($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Coverage area updated successfully',
                    'res_massage' => 'Coverage area updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deleteCoverageArea()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id             = $this->payloadValue($requestData, ['Id', 'id']);
            $coverageAreaId = $this->payloadValue($requestData, ['CoverageAreaId', 'coverageAreaId']);

            if (($id === null || $id === '') && ($coverageAreaId === null || $coverageAreaId === '')) {
                return $this->validationError('Id or CoverageAreaId is required');
            }

            $result = (new CoverageModel())->deleteCoverageArea([
                'Id'             => ($id === null || $id === '') ? null : (int) $id,
                'CoverageAreaId' => ($coverageAreaId === null || $coverageAreaId === '') ? null : (int) $coverageAreaId,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Coverage area deactivated successfully',
                    'res_massage' => 'Coverage area deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function coveragePayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $id   = $this->payloadValue($requestData, ['Id', 'id', 'CoverageAreaId', 'coverageAreaId']);
        $name = $this->payloadValue($requestData, ['CoverageName', 'coverageName', 'Name', 'name']);
        $type = $this->payloadValue($requestData, ['CoverType', 'coverType']);

        if ($isUpdate && ($id === null || (int) $id <= 0)) {
            return $this->validationError('Id is required');
        }
        if ($name === null || $name === '') {
            return $this->validationError('Coverage name is required');
        }
        if ($type === null || $type === '') {
            return $this->validationError('Cover type is required');
        }

        $active = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));

        return [
            'Id'             => $isUpdate ? (int) $id : null,
            'CoverageName'   => $name,
            'CoverType'      => $type,
            'CrsCode'        => $this->payloadValue($requestData, ['CrsCode', 'crsCode', 'CRS_COVERAGEAREA_CODE']),
            'CrsName'        => $this->payloadValue($requestData, ['CrsName', 'crsName', 'CRS_COVERAGEAREA_NAME']),
            'CoverageDesc'   => $this->payloadValue($requestData, ['CoverageDesc', 'coverageDesc', 'Desc', 'desc']),
            'FromDate'       => $this->payloadValue($requestData, ['FromDate', 'fromDate', 'from']),
            'ToDate'         => $this->payloadValue($requestData, ['ToDate', 'toDate', 'to']),
            'Active'         => $active === null ? 1 : $active,
            'CoverageAreaId' => $this->payloadValue($requestData, ['CoverageAreaId', 'coverageAreaId']),
        ];
    }

    private function validationError(string $message)
    {
        return $this->response->setStatusCode(400)
            ->setJSON([
                'res_code'    => 400,
                'status'      => false,
                'message'     => $message,
                'res_massage' => $message,
            ]);
    }

    private function failResponse(\Exception $e)
    {
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
