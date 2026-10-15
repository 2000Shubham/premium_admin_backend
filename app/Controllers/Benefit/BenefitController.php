<?php

namespace App\Controllers\Benefit;

use App\Controllers\BaseController;
use App\Models\BenefitPremiumModel;

class BenefitController extends BaseController
{
    protected $format = 'json';

    public function getBenefitPremiums()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $planId = $this->payloadValue($requestData, ['PlanId', 'planId']);
            $result = (new BenefitPremiumModel())->getBenefitPremiums([
                'PlanId'      => ($planId === null || $planId === '') ? null : (int) $planId,
                'PlanCode'    => $this->payloadValue($requestData, ['PlanCode', 'planCode']),
                'BenefitType' => $this->payloadValue($requestData, ['BenefitType', 'benefitType', 'Type', 'type']),
                'IsActive'    => $this->optionalBit($this->payloadValue($requestData, ['IsActive', 'isActive'])),
                'Page'        => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'    => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
                'AsOfDate'    => $this->payloadValue($requestData, ['AsOfDate', 'asOfDate']),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Benefit premiums fetched successfully',
                    'res_massage' => 'Benefit premiums fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['benefitPremiums'] ?? [],
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

            $id          = $this->payloadValue($requestData, ['PlanBenefitPremiumId', 'planBenefitPremiumId', 'Id', 'id']);
            $planId      = $this->payloadValue($requestData, ['PlanId', 'planId']);
            $planCode    = $this->payloadValue($requestData, ['PlanCode', 'planCode']);
            $benefitType = $this->payloadValue($requestData, ['BenefitType', 'benefitType', 'Type', 'type']);
            $code        = $this->payloadValue($requestData, ['Code', 'code', 'OptionCode', 'optionCode']);

            if (
                ($id === null || $id === '')
                && (
                    (($planId === null || $planId === '') && ($planCode === null || $planCode === ''))
                    || $benefitType === null || $benefitType === ''
                    || $code === null || $code === ''
                )
            ) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanBenefitPremiumId or Plan, BenefitType and Code is required',
                        'res_massage' => 'PlanBenefitPremiumId or Plan, BenefitType and Code is required',
                    ]);
            }

            $result = (new BenefitPremiumModel())->getById([
                'PlanBenefitPremiumId' => ($id === null || $id === '') ? null : (int) $id,
                'PlanId'               => ($planId === null || $planId === '') ? null : (int) $planId,
                'PlanCode'             => $planCode,
                'BenefitType'          => $benefitType,
                'Code'                 => $code,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Benefit premium fetched successfully',
                    'res_massage' => 'Benefit premium fetched successfully',
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

    public function deleteBenefitPremium()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $id          = $this->payloadValue($requestData, ['PlanBenefitPremiumId', 'planBenefitPremiumId', 'Id', 'id']);
            $planId      = $this->payloadValue($requestData, ['PlanId', 'planId']);
            $planCode    = $this->payloadValue($requestData, ['PlanCode', 'planCode']);
            $benefitType = $this->payloadValue($requestData, ['BenefitType', 'benefitType', 'Type', 'type']);
            $code        = $this->payloadValue($requestData, ['Code', 'code', 'OptionCode', 'optionCode']);

            if (
                ($id === null || $id === '')
                && (
                    (($planId === null || $planId === '') && ($planCode === null || $planCode === ''))
                    || $benefitType === null || $benefitType === ''
                    || $code === null || $code === ''
                )
            ) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanBenefitPremiumId or Plan, BenefitType and Code is required',
                        'res_massage' => 'PlanBenefitPremiumId or Plan, BenefitType and Code is required',
                    ]);
            }

            $result = (new BenefitPremiumModel())->deleteBenefitPremium([
                'PlanBenefitPremiumId' => ($id === null || $id === '') ? null : (int) $id,
                'PlanId'               => ($planId === null || $planId === '') ? null : (int) $planId,
                'PlanCode'             => $planCode,
                'BenefitType'          => $benefitType,
                'Code'                 => $code,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Benefit marked inactive',
                    'res_massage' => 'Benefit marked inactive',
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

    public function saveBenefitPremium()
    {
        return $this->writeBenefitPremium(false);
    }

    public function updateBenefitPremium()
    {
        return $this->writeBenefitPremium(true);
    }

    private function writeBenefitPremium($isUpdate)
    {
        try {
            $payload = $this->benefitWritePayload($isUpdate);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $model  = new BenefitPremiumModel();
            $result = $isUpdate
                ? $model->updateBenefitPremium($payload)
                : $model->saveBenefitPremium($payload);

            $message = $isUpdate
                ? 'Optional benefit updated successfully'
                : 'Optional benefit created successfully';

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

    private function benefitWritePayload($isUpdate = false)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            $requestData = [];
        }

        $id          = $this->payloadValue($requestData, ['PlanBenefitPremiumId', 'planBenefitPremiumId', 'Id', 'id']);
        $planId      = $this->payloadValue($requestData, ['PlanId', 'planId']);
        $planCode    = $this->payloadValue($requestData, ['PlanCode', 'planCode']);
        $benefitType = $this->payloadValue($requestData, ['BenefitType', 'benefitType', 'Type', 'type']);
        $code        = $this->payloadValue($requestData, ['Code', 'code', 'OptionCode', 'optionCode']);
        $label       = $this->payloadValue($requestData, ['Label', 'label', 'OptionLabel', 'optionLabel']);
        $premium     = $this->payloadValue($requestData, ['Premium', 'premium']);
        $fromDate    = $this->payloadValue($requestData, ['FromDate', 'fromDate', 'EffectiveFrom', 'effectiveFrom']);
        $toDate      = $this->payloadValue($requestData, ['ToDate', 'toDate', 'EffectiveTo', 'effectiveTo']);
        $isActive    = $this->optionalBit($this->payloadValue($requestData, ['IsActive', 'isActive']));

        $rates = $requestData['Rates'] ?? $requestData['rates'] ?? $requestData['BenefitPremiums'] ?? $requestData['benefitPremiums'] ?? null;
        if (is_array($rates) && isset($rates[0]) && is_array($rates[0])) {
            $rate = $rates[0];
            if ($code === null || $code === '') {
                $code = $this->payloadValue($rate, ['Code', 'code', 'OptionCode', 'optionCode']);
            }
            if ($label === null || $label === '') {
                $label = $this->payloadValue($rate, ['Label', 'label', 'OptionLabel', 'optionLabel']);
            }
            if ($premium === null || $premium === '') {
                $premium = $this->payloadValue($rate, ['Premium', 'premium']);
            }
        }

        if ($isUpdate && ($id === null || $id === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'PlanBenefitPremiumId is required',
                    'res_massage' => 'PlanBenefitPremiumId is required',
                ]);
        }
        if (! $isUpdate && ($planId === null || $planId === '') && ($planCode === null || $planCode === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'PlanId or PlanCode is required',
                    'res_massage' => 'PlanId or PlanCode is required',
                ]);
        }
        if (! $isUpdate && ($benefitType === null || $benefitType === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Type is required',
                    'res_massage' => 'Type is required',
                ]);
        }
        if (! $isUpdate && ($code === null || $code === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Option code is required',
                    'res_massage' => 'Option code is required',
                ]);
        }
        if ($premium === null || $premium === '' || ! is_numeric($premium) || (float) $premium < 0) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Premium must be zero or greater',
                    'res_massage' => 'Premium must be zero or greater',
                ]);
        }

        return [
            'PlanBenefitPremiumId' => ($id === null || $id === '') ? null : (int) $id,
            'PlanId'               => ($planId === null || $planId === '') ? null : (int) $planId,
            'PlanCode'             => $planCode,
            'BenefitType'          => $benefitType,
            'Code'                 => $code,
            'Label'                => $label,
            'Premium'              => $premium,
            'FromDate'             => $fromDate,
            'ToDate'               => $toDate,
            'IsActive'             => $isActive === null ? 1 : $isActive,
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
