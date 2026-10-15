<?php

namespace App\Controllers\Individual\Config;

use App\Controllers\BaseController;
use App\Models\Individual\ProductConfigModel;

class ProductConfigController extends BaseController
{
    protected $format = 'json';

    public function getProductConfigs()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new ProductConfigModel())->getProductConfigs([
                'Search'    => $this->payloadValue($requestData, ['Search', 'search']),
                'ProductId' => $this->optionalInt($this->payloadValue($requestData, ['ProductId', 'productId'])),
                'Active'    => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'      => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'  => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Product configs fetched successfully',
                    'res_massage' => 'Product configs fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['configs'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getProductConfigById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id = $this->payloadValue($requestData, ['Id', 'id']);
            if ($id === null || $id === '' || (int) $id <= 0) {
                return $this->validationError('Id is required');
            }

            $result = (new ProductConfigModel())->getProductConfigById([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Product config fetched successfully',
                    'res_massage' => 'Product config fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function saveProductConfig()
    {
        try {
            $payload = $this->productPayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new ProductConfigModel())->saveProductConfig($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Product config created successfully',
                    'res_massage' => 'Product config created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updateProductConfig()
    {
        try {
            $payload = $this->productPayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new ProductConfigModel())->updateProductConfig($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Product config updated successfully',
                    'res_massage' => 'Product config updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deleteProductConfig()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                return $this->validationError('Invalid JSON payload');
            }

            $id = $this->payloadValue($requestData, ['Id', 'id']);
            if ($id === null || $id === '' || (int) $id <= 0) {
                return $this->validationError('Id is required');
            }

            $result = (new ProductConfigModel())->deleteProductConfig([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Product config deactivated successfully',
                    'res_massage' => 'Product config deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function productPayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $id    = $this->payloadValue($requestData, ['Id', 'id']);
        $name  = $this->payloadValue($requestData, ['Name', 'name', 'ConfigName', 'configName']);
        $code  = $this->payloadValue($requestData, ['Code', 'code', 'ConfigCode', 'configCode']);
        $value = $this->payloadValue($requestData, ['Value', 'value', 'ConfigValue', 'configValue']);

        if ($isUpdate && ($id === null || (int) $id <= 0)) {
            return $this->validationError('Id is required');
        }
        if ($name === null || $name === '') {
            return $this->validationError('Config name is required');
        }
        if ($code === null || $code === '') {
            return $this->validationError('Config code is required');
        }
        if ($value === null || $value === '') {
            return $this->validationError('Config value is required');
        }

        $active   = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));
        $editable = $this->optionalBit($this->payloadValue($requestData, ['IsEditable', 'isEditable', 'Editable', 'editable']));
        $productId = $this->optionalInt($this->payloadValue($requestData, ['ProductId', 'productId'])) ?? 10;

        return [
            'Id'         => $isUpdate ? (int) $id : null,
            'ProductId'  => $productId,
            'Name'       => $name,
            'Code'       => $code,
            'Value'      => $value,
            'ConfigDesc' => $this->payloadValue($requestData, ['ConfigDesc', 'configDesc', 'Desc', 'desc']),
            'IsEditable' => $editable === null ? 1 : $editable,
            'Active'     => $active === null ? 1 : $active,
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

    private function optionalInt($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $number = (int) $value;

        return $number > 0 ? $number : null;
    }
}
