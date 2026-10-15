<?php

namespace App\Controllers\Individual\Dropdown;

use App\Controllers\BaseController;
use App\Models\Individual\PlanDropdownModel;

class PlanDropdownController extends BaseController
{
    protected $format = 'json';

    public function getPlanDropdowns()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new PlanDropdownModel())->getPlanDropdowns([
                'Search' => $this->payloadValue($requestData, ['Search', 'search']),
                'Active' => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan dropdowns fetched successfully',
                    'res_massage' => 'Plan dropdowns fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
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
