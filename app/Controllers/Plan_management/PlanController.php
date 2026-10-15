<?php

namespace App\Controllers\Plan_management;

use App\Controllers\BaseController;
use App\Models\PlanModel;

class PlanController extends BaseController
{
    protected $format = 'json';

    public function index()
    {
        return view('plan_management/index');
    }

    public function addPlans(){
        
        try {

        $requestData = $this->request->getJSON(true);

        if (!$requestData) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'res_code'=>400,
                    'status' => false,
                    'message' => 'Invalid JSON payload',
                    'res_massage' => 'Invalid JSON payload'
                ]);
        }
        
        if (empty($requestData['PlanCode'])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'res_code'=>400,
                    'status' => false,
                    'message' => 'PlanCode is required',
                    'res_massage' => 'PlanCode is required'
                ]);
        }
        
        if (empty($requestData['PlanName'])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'res_code'=>400,
                    'status' => false,
                    'message' => 'PlanName is required',
                    'res_massage' => 'PlanName is required'
                ]);
        }

        $model = new PlanModel();

        $result = $model->savePlan([
            'PlanCode'       => $requestData['PlanCode'],
            'PlanName'       => $requestData['PlanName'],
            'Description'    => $requestData['Description'] ?? null,
            'RegionCode'     => $requestData['RegionCode'] ?? 'DUBAI',
            'CopyFromPlanId' => $requestData['CopyFromPlanId'] ?? null,
            'EffectiveFrom'  => $requestData['EffectiveFrom'] ?? $requestData['effectiveFrom'] ?? null,
            'EffectiveTo'    => $requestData['EffectiveTo'] ?? $requestData['effectiveTo'] ?? null,
            'IsActive'       => $requestData['IsActive'] ?? 1
        ]);
   
        return $this->response->setStatusCode(201)
        ->setJSON([
            'res_code' => 201,
            'status' => true,
            'message' => 'Plan created successfully',
            'res_massage' => 'Plan created successfully',
            'data' => $result
        ]);

    } catch (\Exception $e) {

        return $this->response->setStatusCode(400)
        ->setJSON([
            'res_code' => 400,
            'status' => false,
            'message' => $this->procedureMessage($e),
            'res_massage' => $this->procedureMessage($e)
        ]);
    }
}

    public function updatePlan()
    {
        try {
            $requestData = $this->request->getJSON(true);

            if (! $requestData) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'Invalid JSON payload',
                        'res_massage' => 'Invalid JSON payload',
                    ]);
            }

            $planId = $this->payloadValue($requestData, ['PlanId', 'planId']);
            if ($planId === null || (int) $planId <= 0) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId is required',
                        'res_massage' => 'PlanId is required',
                    ]);
            }

            if (empty($requestData['PlanName']) && empty($requestData['planName'])) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanName is required',
                        'res_massage' => 'PlanName is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->updatePlan([
                'PlanId'        => (int) $planId,
                'PlanName'      => $this->payloadValue($requestData, ['PlanName', 'planName']),
                'Description'   => $this->payloadValue($requestData, ['Description', 'description']),
                'RegionCode'    => $this->payloadValue($requestData, ['RegionCode', 'regionCode'], 'DUBAI'),
                'EffectiveFrom' => $this->payloadValue($requestData, ['EffectiveFrom', 'effectiveFrom']),
                'EffectiveTo'   => $this->payloadValue($requestData, ['EffectiveTo', 'effectiveTo']),
                'IsActive'      => $this->payloadValue($requestData, ['IsActive', 'isActive'], 1),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan updated successfully',
                    'res_massage' => 'Plan updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function updatePremiumRates()
    {
        try {
            $requestData = $this->request->getJSON(true);

            if (! $requestData) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'Invalid JSON payload',
                        'res_massage' => 'Invalid JSON payload',
                    ]);
            }

            $planId = $this->payloadValue($requestData, ['PlanId', 'planId']);
            if ($planId === null || (int) $planId <= 0) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId is required',
                        'res_massage' => 'PlanId is required',
                    ]);
            }

            $baseRates = $this->payloadValue($requestData, ['BasePremiumRates', 'BaseRates', 'basePremiumRates', 'baseRates', 'Rates']);

            if ($baseRates === null || $baseRates === []) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'BasePremiumRates is required',
                        'res_massage' => 'BasePremiumRates is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->updatePremiumRates([
                'PlanId'           => (int) $planId,
                'RegionCode'       => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
                'EffectiveFrom'    => $this->payloadValue($requestData, ['EffectiveFrom', 'effectiveFrom']),
                'EffectiveTo'      => $this->payloadValue($requestData, ['EffectiveTo', 'effectiveTo']),
                'IsActive'         => $this->payloadValue($requestData, ['IsActive', 'isActive'], 1),
                'BasePremiumRates' => $baseRates,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Premium rates updated successfully',
                    'res_massage' => 'Premium rates updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function updateMaternityRates()
    {
        try {
            $requestData = $this->request->getJSON(true);

            if (! $requestData) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'Invalid JSON payload',
                        'res_massage' => 'Invalid JSON payload',
                    ]);
            }

            $planId = $this->payloadValue($requestData, ['PlanId', 'planId']);
            if ($planId === null || (int) $planId <= 0) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId is required',
                        'res_massage' => 'PlanId is required',
                    ]);
            }

            $rates = $this->payloadValue($requestData, ['MaternityRates', 'maternityRates', 'Rates']);

            if ($rates === null || $rates === []) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'MaternityRates is required',
                        'res_massage' => 'MaternityRates is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->updateMaternityRates([
                'PlanId'         => (int) $planId,
                'EffectiveFrom'  => $this->payloadValue($requestData, ['EffectiveFrom', 'effectiveFrom']),
                'EffectiveTo'    => $this->payloadValue($requestData, ['EffectiveTo', 'effectiveTo']),
                'IsActive'       => $this->payloadValue($requestData, ['IsActive', 'isActive'], 1),
                'MaternityRates' => $rates,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Maternity rates updated successfully',
                    'res_massage' => 'Maternity rates updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function updateBenefitPremiums()
    {
        try {
            $requestData = $this->request->getJSON(true);

            if (! $requestData) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'Invalid JSON payload',
                        'res_massage' => 'Invalid JSON payload',
                    ]);
            }

            $planId = $this->payloadValue($requestData, ['PlanId', 'planId']);
            if ($planId === null || (int) $planId <= 0) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId is required',
                        'res_massage' => 'PlanId is required',
                    ]);
            }

            $rows = $this->payloadValue($requestData, ['BenefitPremiums', 'benefitPremiums', 'Rates']);

            if ($rows === null || $rows === []) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'BenefitPremiums is required',
                        'res_massage' => 'BenefitPremiums is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->updateBenefitPremiums([
                'PlanId'          => (int) $planId,
                'BenefitType'     => $this->payloadValue($requestData, ['BenefitType', 'benefitType']),
                'EffectiveFrom'   => $this->payloadValue($requestData, ['EffectiveFrom', 'effectiveFrom']),
                'EffectiveTo'     => $this->payloadValue($requestData, ['EffectiveTo', 'effectiveTo']),
                'IsActive'        => $this->payloadValue($requestData, ['IsActive', 'isActive'], 1),
                'BenefitPremiums' => $rows,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Benefit premiums updated successfully',
                    'res_massage' => 'Benefit premiums updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function saveHtPlan()
    {
        try {
            $requestData = $this->request->getJSON(true);

            if (! $requestData) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'Invalid JSON payload',
                        'res_massage' => 'Invalid JSON payload',
                    ]);
            }

            if (empty($requestData['PlanCode']) && empty($requestData['planCode'])) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanCode is required',
                        'res_massage' => 'PlanCode is required',
                    ]);
            }

            if (empty($requestData['PlanName']) && empty($requestData['planName'])) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanName is required',
                        'res_massage' => 'PlanName is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->saveHtPlan([
                'PlanCode'         => $this->payloadValue($requestData, ['PlanCode', 'planCode']),
                'PlanName'         => $this->payloadValue($requestData, ['PlanName', 'planName']),
                'Description'      => $this->payloadValue($requestData, ['Description', 'description']),
                'RegionCode'       => $this->payloadValue($requestData, ['RegionCode', 'regionCode'], 'DUBAI'),
                'CopyFromPlanId'   => $this->payloadValue($requestData, ['CopyFromPlanId', 'copyFromPlanId']),
                'EffectiveFrom'    => $this->payloadValue($requestData, ['EffectiveFrom', 'effectiveFrom']),
                'EffectiveTo'      => $this->payloadValue($requestData, ['EffectiveTo', 'effectiveTo']),
                'IsActive'         => $this->payloadValue($requestData, ['IsActive', 'isActive'], 1),
                'MaternityRates'   => $this->payloadValue($requestData, ['MaternityRates', 'maternityRates']),
                'BenefitPremiums'  => $this->payloadValue($requestData, ['BenefitPremiums', 'benefitPremiums']),
                'BasePremiumRates' => $this->payloadValue($requestData, ['BasePremiumRates', 'BaseRates', 'basePremiumRates', 'baseRates']),
            ]);

            $hasStatusCode = array_key_exists('StatusCode', $result)
                && $result['StatusCode'] !== null
                && $result['StatusCode'] !== '';
            $statusCode    = $hasStatusCode ? (int) $result['StatusCode'] : null;
            $planId        = $result['PlanId'] ?? null;

            if ($hasStatusCode) {
                $success = in_array($statusCode, [0, 200, 201], true);
            } else {
                $success = ! empty($planId);
            }

            $statusMessage = trim((string) ($result['StatusMessage'] ?? ''));
            if ($statusMessage === '') {
                $statusMessage = $success ? 'Plan created successfully' : 'Unable to save plan';
            }

            return $this->response
                ->setStatusCode($success ? 201 : 400)
                ->setJSON([
                    'res_code'    => $success ? 201 : 400,
                    'status'      => $success,
                    'message'     => $statusMessage,
                    'res_massage' => $statusMessage,
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

public function addPremiumRate()
{
    try {
        $requestData = $this->request->getJSON(true);

        if (! $requestData) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'res_code' => 400,
                    'status'   => false,
                    'message'  => 'Invalid JSON payload',
                    'res_massage' => 'Invalid JSON payload',
                ]);
        }

        $rows = $this->premiumRateRows($requestData);
        if ($rows === []) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'PlanId and at least one rate are required',
                    'res_massage' => 'PlanId and at least one rate are required',
                ]);
        }

        $required = ['PlanId', 'AgeBandId', 'Gender', 'BasePremium', 'EffectiveFrom'];
        $model    = new PlanModel();

        foreach ($rows as $index => $row) {
            foreach ($required as $field) {
                if (! isset($row[$field]) || $row[$field] === '') {
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'res_code'    => 400,
                            'status'      => false,
                            'message'     => $field . ' is required on rate ' . ($index + 1),
                            'res_massage' => $field . ' is required on rate ' . ($index + 1),
                        ]);
                }
            }

            $model->savePremiumRate([
                'PlanId'        => $row['PlanId'],
                'AgeBandId'     => $row['AgeBandId'],
                'Gender'        => $row['Gender'],
                'BasePremium'   => $row['BasePremium'],
                'RegionCode'    => $row['RegionCode'] ?? 'DUBAI',
                'EffectiveFrom' => $row['EffectiveFrom'],
                'EffectiveTo'   => $row['EffectiveTo'] ?? null,
                'IsActive'      => $row['IsActive'] ?? 1,
            ]);
        }

        return $this->response->setStatusCode(201)
            ->setJSON([
                'res_code'    => 201,
                'status'      => true,
                'message'     => 'Premium rate saved successfully',
                'res_massage' => 'Premium rate saved successfully',
            ]);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(400)
            ->setJSON([
                'res_code' => 400,
                'status'   => false,
                'message'  => $this->procedureMessage($e),
                'res_massage' => $this->procedureMessage($e),
            ]);
    }
}

    private function premiumRateRows(array $requestData): array
    {
        if (isset($requestData[0]) && is_array($requestData[0])) {
            $items = $requestData;
            $defaults = [];
        } else {
            $items = $requestData['Rates']
                ?? $requestData['BasePremiumRates']
                ?? $requestData['basePremiumRates']
                ?? null;

            if ($items === null) {
                $items = [$requestData];
            }

            $defaults = [
                'PlanId'        => $requestData['PlanId'] ?? null,
                'RegionCode'    => $requestData['RegionCode'] ?? 'DUBAI',
                'EffectiveFrom' => $requestData['EffectiveFrom'] ?? null,
                'EffectiveTo'   => $requestData['EffectiveTo'] ?? null,
                'IsActive'      => $requestData['IsActive'] ?? 1,
            ];
        }

        $rows = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $item = array_merge($defaults, $item);

            if (isset($item['MalePremium']) || isset($item['FemalePremium'])) {
                if (isset($item['MalePremium']) && $item['MalePremium'] !== '') {
                    $rows[] = array_merge($item, [
                        'Gender'      => 'M',
                        'BasePremium' => $item['MalePremium'],
                    ]);
                }
                if (isset($item['FemalePremium']) && $item['FemalePremium'] !== '') {
                    $rows[] = array_merge($item, [
                        'Gender'      => 'F',
                        'BasePremium' => $item['FemalePremium'],
                    ]);
                }
                continue;
            }

            $rows[] = $item;
        }

        return $rows;
    }

public function addMaternityRate()
{
    try {
        $requestData = $this->request->getJSON(true);

        if (! $requestData) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'res_code' => 400,
                    'status'   => false,
                    'message'  => 'Invalid JSON payload',
                    'res_massage' => 'Invalid JSON payload',
                ]);
        }

        $rows = $this->maternityRateRows($requestData);
        if ($rows === []) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'PlanId and at least one maternity rate are required',
                    'res_massage' => 'PlanId and at least one maternity rate are required',
                ]);
        }

        $required = ['PlanId', 'AgeBandId', 'Premium', 'EffectiveFrom'];
        $model    = new PlanModel();

        foreach ($rows as $index => $row) {
            foreach ($required as $field) {
                if (! isset($row[$field]) || $row[$field] === '') {
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'res_code'    => 400,
                            'status'      => false,
                            'message'     => $field . ' is required on rate ' . ($index + 1),
                            'res_massage' => $field . ' is required on rate ' . ($index + 1),
                        ]);
                }
            }

            $model->saveMaternityRate([
                'PlanId'        => $row['PlanId'],
                'AgeBandId'     => $row['AgeBandId'],
                'Premium'       => $row['Premium'],
                'EffectiveFrom' => $row['EffectiveFrom'],
                'EffectiveTo'   => $row['EffectiveTo'] ?? null,
                'IsActive'      => $row['IsActive'] ?? 1,
            ]);
        }

        return $this->response->setStatusCode(201)
            ->setJSON([
                'res_code'    => 201,
                'status'      => true,
                'message'     => 'Maternity rate saved successfully',
                'res_massage' => 'Maternity rate saved successfully',
            ]);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(400)
            ->setJSON([
                'res_code' => 400,
                'status'   => false,
                'message'  => $this->procedureMessage($e),
                'res_massage' => $this->procedureMessage($e),
            ]);
    }
}

    private function maternityRateRows(array $requestData): array
    {
        if (isset($requestData[0]) && is_array($requestData[0])) {
            $items = $requestData;
            $defaults = [];
        } else {
            $items = $requestData['Rates']
                ?? $requestData['MaternityRates']
                ?? $requestData['maternityRates']
                ?? null;

            if ($items === null) {
                $items = [$requestData];
            }

            $defaults = [
                'PlanId'        => $requestData['PlanId'] ?? null,
                'EffectiveFrom' => $requestData['EffectiveFrom'] ?? null,
                'EffectiveTo'   => $requestData['EffectiveTo'] ?? null,
                'IsActive'      => $requestData['IsActive'] ?? 1,
            ];
        }

        $rows = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $rows[] = array_merge($defaults, $item);
        }

        return $rows;
    }

    public function addBenefitPremium()
    {
        try {
            $requestData = $this->request->getJSON(true);

            if (! $requestData) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'Invalid JSON payload',
                        'res_massage' => 'Invalid JSON payload',
                    ]);
            }

            $rows   = $this->benefitPremiumRows($requestData);
            $planId = $requestData['PlanId'] ?? $requestData['planId'] ?? ($rows[0]['PlanId'] ?? null);

            if ($planId === null || $planId === '' || $rows === []) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId and at least one benefit premium are required',
                        'res_massage' => 'PlanId and at least one benefit premium are required',
                    ]);
            }

            foreach ($rows as $index => $row) {
                $benefitType = $row['BenefitType'] ?? null;
                if ($benefitType === null || $benefitType === '') {
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'res_code'    => 400,
                            'status'      => false,
                            'message'     => 'BenefitType is required on rate ' . ($index + 1),
                            'res_massage' => 'BenefitType is required on rate ' . ($index + 1),
                        ]);
                }

                foreach (['OptionCode', 'Premium'] as $field) {
                    if (! isset($row[$field]) || $row[$field] === '') {
                        return $this->response
                            ->setStatusCode(400)
                            ->setJSON([
                                'res_code'    => 400,
                                'status'      => false,
                                'message'     => $field . ' is required on rate ' . ($index + 1),
                                'res_massage' => $field . ' is required on rate ' . ($index + 1),
                            ]);
                    }
                }
            }

            $model = new PlanModel();
            $model->saveBenefitPremiums([
                'PlanId'          => $planId,
                'BenefitPremiums' => $rows,
                'BenefitType'     => $requestData['BenefitType'] ?? $requestData['benefitType'] ?? null,
                'EffectiveFrom'   => $requestData['EffectiveFrom'] ?? $requestData['effectiveFrom'] ?? null,
                'EffectiveTo'     => $requestData['EffectiveTo'] ?? $requestData['effectiveTo'] ?? null,
                'IsActive'        => $requestData['IsActive'] ?? $requestData['isActive'] ?? 1,
            ]);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Benefit premium saved successfully',
                    'res_massage' => 'Benefit premium saved successfully',
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    private function benefitPremiumRows(array $requestData): array
    {
        if (isset($requestData[0]) && is_array($requestData[0])) {
            $items = $requestData;
            $defaults = [];
        } else {
            $items = $requestData['Rates']
                ?? $requestData['Benefits']
                ?? $requestData['BenefitPremiums']
                ?? $requestData['benefitPremiums']
                ?? null;

            if ($items === null) {
                $items = [$requestData];
            }

            $defaults = [
                'PlanId'        => $requestData['PlanId'] ?? $requestData['planId'] ?? null,
                'BenefitType'   => $requestData['BenefitType'] ?? $requestData['benefitType'] ?? null,
                'EffectiveFrom' => $requestData['EffectiveFrom'] ?? $requestData['effectiveFrom'] ?? null,
                'EffectiveTo'   => $requestData['EffectiveTo'] ?? $requestData['effectiveTo'] ?? null,
                'IsActive'      => $requestData['IsActive'] ?? $requestData['isActive'] ?? 1,
            ];
        }

        $rows = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $item = array_merge($defaults, $item);

            if (isset($item['Label']) && (! isset($item['OptionLabel']) || $item['OptionLabel'] === '')) {
                $item['OptionLabel'] = $item['Label'];
            }

            if (isset($item['Status']) && $item['Status'] !== '' && ! array_key_exists('IsActive', $item)) {
                $item['IsActive'] = (strcasecmp((string) $item['Status'], 'Active') === 0) ? 1 : 0;
            }

            $rows[] = [
                'PlanId'        => $item['PlanId'] ?? $item['planId'] ?? null,
                'BenefitType'   => $item['BenefitType'] ?? $item['benefitType'] ?? null,
                'OptionCode'    => $item['OptionCode'] ?? $item['optionCode'] ?? null,
                'OptionLabel'   => $item['OptionLabel'] ?? $item['optionLabel'] ?? $item['Label'] ?? null,
                'Premium'       => $item['Premium'] ?? $item['premium'] ?? null,
                'EffectiveFrom' => $item['EffectiveFrom'] ?? $item['effectiveFrom'] ?? null,
                'EffectiveTo'   => $item['EffectiveTo'] ?? $item['effectiveTo'] ?? null,
                'IsActive'      => $item['IsActive'] ?? $item['isActive'] ?? 1,
            ];
        }

        return $rows;
    }

    public function getPremiumRates()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getGet() ?: $this->request->getPost();
            }

            $planId   = $requestData['PlanId'] ?? null;
            $planCode = $requestData['PlanCode'] ?? null;

            if ((empty($planId) || $planId === '') && (empty($planCode) || $planCode === '')) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId or PlanCode is required',
                        'res_massage' => 'PlanId or PlanCode is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->getPremiumRates([
                'PlanId'     => $planId,
                'PlanCode'   => $planCode,
                'RegionCode' => $requestData['RegionCode'] ?? 'DUBAI',
                'AsOfDate'   => $requestData['AsOfDate'] ?? null,
                'ActiveOnly' => $requestData['ActiveOnly'] ?? 1,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Premium rates fetched successfully',
                    'res_massage' => 'Premium rates fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function getMaternityRates()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getGet() ?: $this->request->getPost();
            }

            $planId   = $requestData['PlanId'] ?? $requestData['planId'] ?? null;
            $planCode = $requestData['PlanCode'] ?? $requestData['planCode'] ?? null;

            if ((empty($planId) || $planId === '') && (empty($planCode) || $planCode === '')) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId or PlanCode is required',
                        'res_massage' => 'PlanId or PlanCode is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->getMaternityRates([
                'PlanId'     => $planId,
                'PlanCode'   => $planCode,
                'AsOfDate'   => $requestData['AsOfDate'] ?? $requestData['asOfDate'] ?? null,
                'ActiveOnly' => $requestData['ActiveOnly'] ?? $requestData['activeOnly'] ?? 1,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Maternity rates fetched successfully',
                    'res_massage' => 'Maternity rates fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function getBenefitPremiums()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getGet() ?: $this->request->getPost();
            }

            $planId      = $requestData['PlanId'] ?? $requestData['planId'] ?? null;
            $planCode    = $requestData['PlanCode'] ?? $requestData['planCode'] ?? null;
            $benefitType = $requestData['BenefitType'] ?? $requestData['benefitType'] ?? null;

            if ((empty($planId) || $planId === '') && (empty($planCode) || $planCode === '')) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId or PlanCode is required',
                        'res_massage' => 'PlanId or PlanCode is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->getBenefitPremiums([
                'PlanId'      => $planId,
                'PlanCode'    => $planCode,
                'BenefitType' => $benefitType,
                'AsOfDate'    => $requestData['AsOfDate'] ?? $requestData['asOfDate'] ?? null,
                'ActiveOnly'  => $requestData['ActiveOnly'] ?? $requestData['activeOnly'] ?? 1,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Benefit premiums fetched successfully',
                    'res_massage' => 'Benefit premiums fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function getPlanReview()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getGet() ?: $this->request->getPost();
            }

            $planId   = $requestData['PlanId'] ?? $requestData['planId'] ?? null;
            $planCode = $requestData['PlanCode'] ?? $requestData['planCode'] ?? null;
            $activate = $requestData['Activate'] ?? $requestData['activate'] ?? 0;
            $activate = ($activate === true || $activate === 1 || $activate === '1') ? 1 : 0;

            if ((empty($planId) || $planId === '') && (empty($planCode) || $planCode === '')) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId or PlanCode is required',
                        'res_massage' => 'PlanId or PlanCode is required',
                    ]);
            }

            $model  = new PlanModel();
            $rows   = $model->getPlanReview([
                'PlanId'   => $planId,
                'PlanCode' => $planCode,
                'Activate' => $activate,
            ]);

            $okMessage = $activate
                ? 'Plan activated successfully'
                : 'Plan review fetched successfully';

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => $okMessage,
                    'res_massage' => $okMessage,
                    'data'        => $rows[0] ?? null,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function getPlans()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getGet() ?: $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $search = $this->payloadValue($requestData, ['Search', 'search', 'q']);
            $region = $this->payloadValue($requestData, ['RegionCode', 'regionCode', 'Region', 'region']);
            $status = $this->payloadValue($requestData, ['IsActive', 'isActive', 'Status', 'status']);

            if ($status === '' || $status === null) {
                $isActive = null;
            } else {
                $isActive = ($status === true || $status === 1 || $status === '1') ? 1 : 0;
            }

            $page     = (int) ($this->payloadValue($requestData, ['Page', 'page'], 1));
            $pageSize = (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5));

            $model  = new PlanModel();
            $result = $model->getPlans([
                'Search'     => $search,
                'RegionCode' => $region,
                'IsActive'   => $isActive,
                'Page'       => $page,
                'PageSize'   => $pageSize,
            ]);

            $summary = $result['summary'] ?? [];

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plans fetched successfully',
                    'res_massage' => 'Plans fetched successfully',
                    'summary'     => $summary,
                    'data'        => $result['plans'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function getPlanById()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getGet() ?: $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $planId   = $this->payloadValue($requestData, ['PlanId', 'planId']);
            $planCode = $this->payloadValue($requestData, ['PlanCode', 'planCode']);

            if ((empty($planId) || $planId === '') && (empty($planCode) || $planCode === '')) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId or PlanCode is required',
                        'res_massage' => 'PlanId or PlanCode is required',
                    ]);
            }

            $model  = new PlanModel();
            $result = $model->getPlanById([
                'PlanId'     => $planId,
                'PlanCode'   => $planCode,
                'RegionCode' => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
                'AsOfDate'   => $this->payloadValue($requestData, ['AsOfDate', 'asOfDate']),
                'ActiveOnly' => $this->payloadValue($requestData, ['ActiveOnly', 'activeOnly'], 1),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan fetched successfully',
                    'res_massage' => 'Plan fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
    }

    public function deletePlan()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $planId   = $this->payloadValue($requestData, ['PlanId', 'planId']);
            $planCode = $this->payloadValue($requestData, ['PlanCode', 'planCode']);

            if (($planId === null || $planId === '') && ($planCode === null || $planCode === '')) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'PlanId or PlanCode is required',
                        'res_massage' => 'PlanId or PlanCode is required',
                    ]);
            }

            $result = (new PlanModel())->deletePlan([
                'PlanId'   => ($planId === null || $planId === '') ? null : (int) $planId,
                'PlanCode' => $planCode,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan marked inactive',
                    'res_massage' => 'Plan marked inactive',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => $this->procedureMessage($e),
                    'res_massage' => $this->procedureMessage($e),
                ]);
        }
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

    private function procedureMessage(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (preg_match('/\[SQL Server\](.*)$/s', $message, $matches)) {
            return trim($matches[1]);
        }

        return $message;
    }
}
    


