<?php

namespace App\Controllers\Individual\PlanNetwork;

use App\Controllers\BaseController;
use App\Models\Individual\PlanNetworkMappingModel;

class PlanNetworkMappingController extends BaseController
{
    protected $format = 'json';

    public function getPlanNetworkMappings()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new PlanNetworkMappingModel())->getPlanNetworkMappings([
                'PlanId'    => $this->optionalInt($this->payloadValue($requestData, ['PlanId', 'planId'])),
                'NetworkId' => $this->optionalInt($this->payloadValue($requestData, ['NetworkId', 'networkId'])),
                'Active'    => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'      => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'  => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan-network mappings fetched successfully',
                    'res_massage' => 'Plan-network mappings fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['mappings'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getPlanNetworkMappingById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id = $this->optionalInt($this->payloadValue($requestData, ['Id', 'id']));
            if ($id === null) {
                return $this->validationError('Id is required');
            }

            $result = (new PlanNetworkMappingModel())->getPlanNetworkMappingById(['Id' => $id]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan-network mapping fetched successfully',
                    'res_massage' => 'Plan-network mapping fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function savePlanNetworkMapping()
    {
        try {
            $payload = $this->mappingPayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new PlanNetworkMappingModel())->savePlanNetworkMapping($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Plan-network mapping created successfully',
                    'res_massage' => 'Plan-network mapping created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updatePlanNetworkMapping()
    {
        try {
            $payload = $this->mappingPayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new PlanNetworkMappingModel())->updatePlanNetworkMapping($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan-network mapping updated successfully',
                    'res_massage' => 'Plan-network mapping updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deletePlanNetworkMapping()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id = $this->optionalInt($this->payloadValue($requestData, ['Id', 'id']));
            if ($id === null) {
                return $this->validationError('Id is required');
            }

            $result = (new PlanNetworkMappingModel())->deletePlanNetworkMapping(['Id' => $id]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan-network mapping deactivated successfully',
                    'res_massage' => 'Plan-network mapping deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function mappingPayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $id        = $this->optionalInt($this->payloadValue($requestData, ['Id', 'id']));
        $planId    = $this->optionalInt($this->payloadValue($requestData, ['PlanId', 'planId']));
        $networkId = $this->optionalInt($this->payloadValue($requestData, ['NetworkId', 'networkId']));

        if ($isUpdate && $id === null) {
            return $this->validationError('Id is required');
        }
        if ($planId === null) {
            return $this->validationError('Plan is required');
        }
        if ($networkId === null) {
            return $this->validationError('Network is required');
        }

        $active = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));

        return [
            'Id'        => $isUpdate ? $id : null,
            'PlanId'    => $planId,
            'NetworkId' => $networkId,
            'Active'    => $active === null ? 1 : $active,
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

    private function optionalInt($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $int = (int) $value;

        return $int > 0 ? $int : null;
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
