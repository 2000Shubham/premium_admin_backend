<?php

namespace App\Controllers\Individual\Network;

use App\Controllers\BaseController;
use App\Models\Individual\NetworkMasterModel;

class NetworkMasterController extends BaseController
{
    protected $format = 'json';

    public function getNetworkMasters()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new NetworkMasterModel())->getNetworkMasters([
                'Search'   => $this->payloadValue($requestData, ['Search', 'search']),
                'Active'   => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'     => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize' => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Network masters fetched successfully',
                    'res_massage' => 'Network masters fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['networks'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getNetworkMasterById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id        = $this->payloadValue($requestData, ['Id', 'id']);
            $networkId = $this->payloadValue($requestData, ['NetworkId', 'networkId']);

            if (($id === null || $id === '') && ($networkId === null || $networkId === '')) {
                return $this->validationError('Id or NetworkId is required');
            }

            $result = (new NetworkMasterModel())->getNetworkMasterById([
                'Id'        => ($id === null || $id === '') ? null : (int) $id,
                'NetworkId' => ($networkId === null || $networkId === '') ? null : (int) $networkId,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Network fetched successfully',
                    'res_massage' => 'Network fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function saveNetworkMaster()
    {
        try {
            $payload = $this->networkPayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new NetworkMasterModel())->saveNetworkMaster($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Network created successfully',
                    'res_massage' => 'Network created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updateNetworkMaster()
    {
        try {
            $payload = $this->networkPayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new NetworkMasterModel())->updateNetworkMaster($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Network updated successfully',
                    'res_massage' => 'Network updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deleteNetworkMaster()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id        = $this->payloadValue($requestData, ['Id', 'id']);
            $networkId = $this->payloadValue($requestData, ['NetworkId', 'networkId']);

            if (($id === null || $id === '') && ($networkId === null || $networkId === '')) {
                return $this->validationError('Id or NetworkId is required');
            }

            $result = (new NetworkMasterModel())->deleteNetworkMaster([
                'Id'        => ($id === null || $id === '') ? null : (int) $id,
                'NetworkId' => ($networkId === null || $networkId === '') ? null : (int) $networkId,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Network deactivated successfully',
                    'res_massage' => 'Network deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function networkPayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $id   = $this->payloadValue($requestData, ['Id', 'id']);
        $code = $this->payloadValue($requestData, ['Code', 'code', 'NetworkCode', 'networkCode']);
        $name = $this->payloadValue($requestData, ['Name', 'name', 'NetworkName', 'networkName']);

        if ($isUpdate && ($id === null || (int) $id <= 0)) {
            return $this->validationError('Id is required');
        }
        if ($code === null || $code === '') {
            return $this->validationError('Network code is required');
        }
        if ($name === null || $name === '') {
            return $this->validationError('Network name is required');
        }

        $active = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));

        return [
            'Id'        => $isUpdate ? (int) $id : null,
            'Code'      => $code,
            'Name'      => $name,
            'CrsCode'   => $this->payloadValue($requestData, ['CrsCode', 'crsCode', 'CRS_NETWORK_CODE']),
            'CrsName'   => $this->payloadValue($requestData, ['CrsName', 'crsName', 'CRS_NETWORK_NAME']),
            'Active'    => $active === null ? 1 : $active,
            'NetworkId' => $this->payloadValue($requestData, ['NetworkId', 'networkId']),
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
