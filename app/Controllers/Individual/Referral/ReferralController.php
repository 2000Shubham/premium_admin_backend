<?php

namespace App\Controllers\Individual\Referral;

use App\Controllers\BaseController;
use App\Models\Individual\ReferralModel;

class ReferralController extends BaseController
{
    protected $format = 'json';

    public function getReferralRules()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new ReferralModel())->getReferralRules([
                'Search'   => $this->payloadValue($requestData, ['Search', 'search']),
                'Active'   => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'     => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize' => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 10)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Referral rules fetched successfully',
                    'res_massage' => 'Referral rules fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['rules'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getReferralRuleById()
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

            $result = (new ReferralModel())->getReferralRuleById([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Referral rule fetched successfully',
                    'res_massage' => 'Referral rule fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function saveReferralRule()
    {
        try {
            $payload = $this->rulePayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new ReferralModel())->saveReferralRule($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Referral rule created successfully',
                    'res_massage' => 'Referral rule created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updateReferralRule()
    {
        try {
            $payload = $this->rulePayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new ReferralModel())->updateReferralRule($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Referral rule updated successfully',
                    'res_massage' => 'Referral rule updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deleteReferralRule()
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

            $result = (new ReferralModel())->deleteReferralRule([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Referral rule deactivated successfully',
                    'res_massage' => 'Referral rule deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function rulePayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $id       = $this->payloadValue($requestData, ['Id', 'id']);
        $name     = $this->payloadValue($requestData, ['RuleName', 'ruleName']);
        $visaMode = $this->payloadValue($requestData, ['VisaMode', 'visaMode']);
        $emirates = $this->payloadValue($requestData, ['VisaEmirates', 'visaEmirates']);
        $minAge   = $this->optionalInt($this->payloadValue($requestData, ['MinAge', 'minAge']));
        $maxAge   = $this->optionalInt($this->payloadValue($requestData, ['MaxAge', 'maxAge']));
        $gender   = $this->payloadValue($requestData, ['Gender', 'gender'], 'ALL');
        $mafClean = $this->optionalBit($this->payloadValue($requestData, ['MafClean', 'mafClean', 'MAF', 'maf']));
        $action   = $this->payloadValue($requestData, ['Action', 'action']);
        $order    = $this->optionalInt($this->payloadValue($requestData, ['DisplayOrder', 'displayOrder', 'Order', 'order']));
        $active   = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));

        if ($isUpdate && ($id === null || (int) $id <= 0)) {
            return $this->validationError('Id is required');
        }
        if ($name === null || $name === '') {
            return $this->validationError('Rule name is required');
        }
        if ($visaMode === null || $visaMode === '') {
            return $this->validationError('Visa mode is required');
        }
        if ($action === null || $action === '') {
            return $this->validationError('Action is required');
        }

        if (is_array($emirates)) {
            $emirates = implode(',', array_filter(array_map('strval', $emirates)));
        }

        return [
            'Id'           => $isUpdate ? (int) $id : null,
            'RuleName'     => $name,
            'VisaMode'     => $visaMode,
            'VisaEmirates' => ($emirates === null || $emirates === '') ? null : $emirates,
            'MinAge'       => $minAge,
            'MaxAge'       => $maxAge,
            'Gender'       => $gender ?: 'ALL',
            'MafClean'     => $mafClean === null ? 1 : $mafClean,
            'Action'       => $action,
            'DisplayOrder' => $order,
            'Active'       => $active === null ? 1 : $active,
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

    private function optionalInt($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
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
