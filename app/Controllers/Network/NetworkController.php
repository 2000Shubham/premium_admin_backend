<?php

namespace App\Controllers\Network;

use App\Controllers\BaseController;
use App\Models\NetworkModel;

class NetworkController extends BaseController
{
    protected $format = 'json';

    public function getNetworks()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new NetworkModel())->getNetworks([
                'IsActive'   => $this->optionalBit($this->payloadValue($requestData, ['IsActive', 'isActive'])),
                'RegionCode' => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
                'Page'       => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'   => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Networks fetched successfully',
                    'res_massage' => 'Networks fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['networks'] ?? [],
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

            $id   = $this->payloadValue($requestData, ['NetworkId', 'networkId', 'Id', 'id']);
            $code = $this->payloadValue($requestData, ['Code', 'code', 'NetworkCode', 'networkCode']);

            if (($id === null || $id === '') && ($code === null || $code === '')) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'NetworkId or Code is required',
                        'res_massage' => 'NetworkId or Code is required',
                    ]);
            }

            $result = (new NetworkModel())->getById([
                'NetworkId' => ($id === null || $id === '') ? null : (int) $id,
                'Code'      => $code,
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

    public function deleteNetwork()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $id   = $this->payloadValue($requestData, ['NetworkId', 'networkId', 'Id', 'id']);
            $code = $this->payloadValue($requestData, ['Code', 'code', 'NetworkCode', 'networkCode']);

            if (($id === null || $id === '') && ($code === null || $code === '')) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'NetworkId or Code is required',
                        'res_massage' => 'NetworkId or Code is required',
                    ]);
            }

            $result = (new NetworkModel())->deleteNetwork([
                'NetworkId' => ($id === null || $id === '') ? null : (int) $id,
                'Code'      => $code,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Network deleted successfully',
                    'res_massage' => 'Network deleted successfully',
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

    public function saveNetwork()
    {
        return $this->writeNetwork(false);
    }

    public function updateNetwork()
    {
        return $this->writeNetwork(true);
    }

    private function writeNetwork($isUpdate)
    {
        try {
            $payload = $this->networkWritePayload($isUpdate);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $model  = new NetworkModel();
            $result = $isUpdate
                ? $model->updateNetwork($payload)
                : $model->saveNetwork($payload);

            $message = $isUpdate
                ? 'Network updated successfully'
                : 'Network created successfully';

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

    private function networkWritePayload($isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            $requestData = [];
        }

        $id     = $this->payloadValue($requestData, ['NetworkId', 'networkId', 'Id', 'id']);
        $code   = $this->payloadValue($requestData, ['Code', 'code', 'NetworkCode', 'networkCode']);
        $name   = $this->payloadValue($requestData, ['Name', 'name', 'NetworkName', 'networkName']);
        $region = $this->payloadValue($requestData, ['RegionCode', 'regionCode']);

        if (! $isUpdate && ($code === null || $code === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Code is required',
                    'res_massage' => 'Code is required',
                ]);
        }
        if ($isUpdate && ($id === null || $id === '') && ($code === null || $code === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'NetworkId or Code is required',
                    'res_massage' => 'NetworkId or Code is required',
                ]);
        }
        if ($name === null || $name === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Network name is required',
                    'res_massage' => 'Network name is required',
                ]);
        }

        return [
            'NetworkId'  => ($id === null || $id === '') ? null : (int) $id,
            'Code'       => $code,
            'Name'       => $name,
            'RegionCode' => $region,
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
