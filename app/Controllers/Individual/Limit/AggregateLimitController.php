<?php

namespace App\Controllers\Individual\Limit;

use App\Controllers\BaseController;
use App\Models\Individual\AggregateLimitModel;

class AggregateLimitController extends BaseController
{
    protected $format = 'json';

    public function getAggregateLimits()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new AggregateLimitModel())->getAggregateLimits([
                'Search'   => $this->payloadValue($requestData, ['Search', 'search']),
                'Active'   => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'     => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize' => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Aggregate limits fetched successfully',
                    'res_massage' => 'Aggregate limits fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['limits'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getAggregateLimitById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id      = $this->payloadValue($requestData, ['Id', 'id']);
            $limitId = $this->payloadValue($requestData, ['AggregateLimitId', 'aggregateLimitId']);

            if (($id === null || $id === '') && ($limitId === null || $limitId === '')) {
                return $this->validationError('Id or AggregateLimitId is required');
            }

            $result = (new AggregateLimitModel())->getAggregateLimitById([
                'Id'               => ($id === null || $id === '') ? null : (int) $id,
                'AggregateLimitId' => ($limitId === null || $limitId === '') ? null : (int) $limitId,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Aggregate limit fetched successfully',
                    'res_massage' => 'Aggregate limit fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function saveAggregateLimit()
    {
        try {
            $payload = $this->limitPayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new AggregateLimitModel())->saveAggregateLimit($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Aggregate limit created successfully',
                    'res_massage' => 'Aggregate limit created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updateAggregateLimit()
    {
        try {
            $payload = $this->limitPayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new AggregateLimitModel())->updateAggregateLimit($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Aggregate limit updated successfully',
                    'res_massage' => 'Aggregate limit updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deleteAggregateLimit()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id      = $this->payloadValue($requestData, ['Id', 'id']);
            $limitId = $this->payloadValue($requestData, ['AggregateLimitId', 'aggregateLimitId']);

            if (($id === null || $id === '') && ($limitId === null || $limitId === '')) {
                return $this->validationError('Id or AggregateLimitId is required');
            }

            $result = (new AggregateLimitModel())->deleteAggregateLimit([
                'Id'               => ($id === null || $id === '') ? null : (int) $id,
                'AggregateLimitId' => ($limitId === null || $limitId === '') ? null : (int) $limitId,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Aggregate limit deactivated successfully',
                    'res_massage' => 'Aggregate limit deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function limitPayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $id    = $this->payloadValue($requestData, ['Id', 'id']);
        $label = $this->payloadValue($requestData, ['Label', 'label', 'AggregateLimit', 'aggregateLimit']);
        $max   = $this->payloadValue($requestData, ['MaxLimit', 'maxLimit', 'Max', 'max']);

        if ($isUpdate && ($id === null || (int) $id <= 0)) {
            return $this->validationError('Id is required');
        }
        if ($label === null || $label === '') {
            return $this->validationError('Limit label is required');
        }
        if ($max === null || $max === '') {
            return $this->validationError('Max limit is required');
        }

        $active = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));
        $min    = $this->payloadValue($requestData, ['MinLimit', 'minLimit', 'Min', 'min'], 0);
        $order  = $this->payloadValue($requestData, ['DisplayOrder', 'displayOrder', 'Order', 'order'], 1);

        return [
            'Id'               => $isUpdate ? (int) $id : null,
            'Label'            => $label,
            'MinLimit'         => (float) $min,
            'MaxLimit'         => (float) $max,
            'DisplayOrder'     => (int) $order,
            'CrsCode'          => $this->payloadValue($requestData, ['CrsCode', 'crsCode', 'CRS_LIMIT_CODE']),
            'CrsName'          => $this->payloadValue($requestData, ['CrsName', 'crsName', 'CRS_LIMIT_NAME']),
            'Active'           => $active === null ? 1 : $active,
            'AggregateLimitId' => $this->payloadValue($requestData, ['AggregateLimitId', 'aggregateLimitId']),
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
