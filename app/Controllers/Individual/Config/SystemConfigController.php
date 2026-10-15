<?php

namespace App\Controllers\Individual\Config;

use App\Controllers\BaseController;
use App\Models\Individual\SystemConfigModel;

class SystemConfigController extends BaseController
{
    protected $format = 'json';

    public function getSystemConfigs()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new SystemConfigModel())->getSystemConfigs([
                'Search'     => $this->payloadValue($requestData, ['Search', 'search']),
                'ConfigType' => $this->payloadValue($requestData, ['ConfigType', 'configType', 'Type', 'type']),
                'AppSource'  => $this->payloadValue($requestData, ['AppSource', 'appSource', 'Source', 'source']),
                'PartnerId'  => $this->optionalInt($this->payloadValue($requestData, ['PartnerId', 'partnerId'])),
                'Active'     => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'       => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'   => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'System configs fetched successfully',
                    'res_massage' => 'System configs fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['configs'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getSystemConfigById()
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

            $result = (new SystemConfigModel())->getSystemConfigById([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'System config fetched successfully',
                    'res_massage' => 'System config fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function saveSystemConfig()
    {
        try {
            $payload = $this->systemPayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new SystemConfigModel())->saveSystemConfig($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'System config created successfully',
                    'res_massage' => 'System config created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updateSystemConfig()
    {
        try {
            $payload = $this->systemPayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new SystemConfigModel())->updateSystemConfig($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'System config updated successfully',
                    'res_massage' => 'System config updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deleteSystemConfig()
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

            $result = (new SystemConfigModel())->deleteSystemConfig([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'System config deactivated successfully',
                    'res_massage' => 'System config deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function systemPayload(bool $isUpdate)
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

        $active    = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));
        $editable  = $this->optionalBit($this->payloadValue($requestData, ['IsEditable', 'isEditable', 'Editable', 'editable']));
        $partnerId = $this->optionalInt($this->payloadValue($requestData, ['PartnerId', 'partnerId'])) ?? 1;
        $type      = $this->payloadValue($requestData, ['ConfigType', 'configType', 'Type', 'type'], 'STRING');
        $source    = $this->payloadValue($requestData, ['AppSource', 'appSource', 'Source', 'source'], 'CRM');

        return [
            'Id'         => $isUpdate ? (int) $id : null,
            'PartnerId'  => $partnerId,
            'Name'       => $name,
            'Code'       => $code,
            'ConfigType' => $type,
            'Value'      => $value,
            'AppSource'  => $source,
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
