<?php

namespace App\Controllers\Individual\Contribution;

use App\Controllers\BaseController;
use App\Models\Individual\PlanContributionModel;

class PlanContributionController extends BaseController
{
    protected $format = 'json';

    public function getPlanContributions()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $result = (new PlanContributionModel())->getPlanContributions([
                'Search'         => $this->payloadValue($requestData, ['Search', 'search']),
                'PlanId'         => $this->optionalInt($this->payloadValue($requestData, ['PlanId', 'planId'])),
                'NetworkId'      => $this->optionalInt($this->payloadValue($requestData, ['NetworkId', 'networkId'])),
                'CoverageAreaId' => $this->optionalInt($this->payloadValue($requestData, ['CoverageAreaId', 'coverageAreaId', 'CoverageId', 'coverageId'])),
                'Gender'         => $this->payloadValue($requestData, ['Gender', 'gender']),
                'Active'         => $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive'])),
                'Page'           => (int) ($this->payloadValue($requestData, ['Page', 'page'], 1)),
                'PageSize'       => (int) ($this->payloadValue($requestData, ['PageSize', 'pageSize'], 5)),
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan contributions fetched successfully',
                    'res_massage' => 'Plan contributions fetched successfully',
                    'summary'     => $result['summary'] ?? [],
                    'data'        => $result['contributions'] ?? [],
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function getPlanContributionById()
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

            $result = (new PlanContributionModel())->getPlanContributionById([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan contribution fetched successfully',
                    'res_massage' => 'Plan contribution fetched successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function savePlanContribution()
    {
        try {
            $payload = $this->contributionPayload(false);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new PlanContributionModel())->savePlanContribution($payload);

            return $this->response->setStatusCode(201)
                ->setJSON([
                    'res_code'    => 201,
                    'status'      => true,
                    'message'     => 'Plan contribution created successfully',
                    'res_massage' => 'Plan contribution created successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function updatePlanContribution()
    {
        try {
            $payload = $this->contributionPayload(true);
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new PlanContributionModel())->updatePlanContribution($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan contribution updated successfully',
                    'res_massage' => 'Plan contribution updated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    public function deletePlanContribution()
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

            $result = (new PlanContributionModel())->deletePlanContribution([
                'Id' => (int) $id,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Plan contribution deactivated successfully',
                    'res_massage' => 'Plan contribution deactivated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->failResponse($e);
        }
    }

    private function contributionPayload(bool $isUpdate)
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            return $this->validationError('Invalid JSON payload');
        }

        $id             = $this->payloadValue($requestData, ['Id', 'id']);
        $planId         = $this->optionalInt($this->payloadValue($requestData, ['PlanId', 'planId']));
        $coverType      = $this->payloadValue($requestData, ['CoverType', 'coverType']);
        $coverageAreaId = $this->optionalInt($this->payloadValue($requestData, ['CoverageAreaId', 'coverageAreaId']));
        $networkId      = $this->optionalInt($this->payloadValue($requestData, ['NetworkId', 'networkId']));
        $gender         = $this->payloadValue($requestData, ['Gender', 'gender']);

        if ($isUpdate && ($id === null || (int) $id <= 0)) {
            return $this->validationError('Id is required');
        }
        if (! $planId) {
            return $this->validationError('Plan is required');
        }
        if ($coverType === null || $coverType === '') {
            return $this->validationError('Cover type is required');
        }
        if (! $coverageAreaId) {
            return $this->validationError('Coverage area is required');
        }
        if (! $networkId) {
            return $this->validationError('Network is required');
        }
        if ($gender === null || $gender === '') {
            return $this->validationError('Gender is required');
        }

        $active = $this->optionalBit($this->payloadValue($requestData, ['Active', 'active', 'IsActive', 'isActive']));

        return [
            'Id'                    => $isUpdate ? (int) $id : null,
            'PlanId'                => $planId,
            'CoverType'             => $coverType,
            'SubCoverType'          => $this->payloadValue($requestData, ['SubCoverType', 'subCoverType']),
            'CoverageAreaId'        => $coverageAreaId,
            'NetworkId'             => $networkId,
            'AggregateLimitId'      => $this->optionalInt($this->payloadValue($requestData, ['AggregateLimitId', 'aggregateLimitId'])),
            'CoverageType'          => $this->payloadValue($requestData, ['CoverageType', 'coverageType']),
            'PlanType'              => $this->payloadValue($requestData, ['PlanType', 'planType']),
            'MinAge'                => (int) ($this->payloadValue($requestData, ['MinAge', 'minAge'], 0)),
            'MaxAge'                => (int) ($this->payloadValue($requestData, ['MaxAge', 'maxAge'], 0)),
            'Gender'                => $gender,
            'MaritalStatus'         => $this->payloadValue($requestData, ['MaritalStatus', 'maritalStatus']),
            'Relationship'          => $this->payloadValue($requestData, ['Relationship', 'relationship', 'RelationShip']),
            'PolicyPeriod'          => (int) ($this->payloadValue($requestData, ['PolicyPeriod', 'policyPeriod'], 12)),
            'MinSI'                 => $this->optionalFloat($this->payloadValue($requestData, ['MinSI', 'minSI'], 0)),
            'MaxSI'                 => $this->optionalFloat($this->payloadValue($requestData, ['MaxSI', 'maxSI'], 0)),
            'MinSumAssured'         => $this->optionalFloat($this->payloadValue($requestData, ['MinSumAssured', 'minSumAssured'])),
            'MaxSumAssured'         => $this->optionalFloat($this->payloadValue($requestData, ['MaxSumAssured', 'maxSumAssured'])),
            'ContributionType'      => $this->payloadValue($requestData, ['ContributionType', 'contributionType'], 'FIXED'),
            'Rate'                  => $this->optionalFloat($this->payloadValue($requestData, ['Rate', 'rate'], 0)),
            'FixedContribution'     => $this->optionalFloat($this->payloadValue($requestData, ['FixedContribution', 'fixedContribution', 'Fixed', 'fixed'], 0)),
            'MinimumContribution'   => $this->optionalFloat($this->payloadValue($requestData, ['MinimumContribution', 'minimumContribution', 'MinContribution', 'minContribution'], 0)),
            'MaternityContribution' => $this->optionalFloat($this->payloadValue($requestData, ['MaternityContribution', 'maternityContribution', 'Maternity', 'maternity'], 0)),
            'NetFixedContribution'  => $this->optionalFloat($this->payloadValue($requestData, ['NetFixedContribution', 'netFixedContribution', 'NetFixed', 'netFixed'], 0)),
            'ContributionPeriod'    => $this->payloadValue($requestData, ['ContributionPeriod', 'contributionPeriod', 'Period', 'period'], 'ANNUAL'),
            'CurrencyCode'          => $this->payloadValue($requestData, ['CurrencyCode', 'currencyCode', 'Currency', 'currency'], 'AED'),
            'CoPayment'             => $this->payloadValue($requestData, ['CoPayment', 'coPayment', 'Copay', 'copay']),
            'Deductible'            => $this->optionalFloat($this->payloadValue($requestData, ['Deductible', 'deductible'], 0)),
            'Pharmacy'              => $this->payloadValue($requestData, ['Pharmacy', 'pharmacy']),
            'LoadingME'             => $this->optionalFloat($this->payloadValue($requestData, ['LoadingME', 'loadingME'], 0)),
            'LoadingSMO'            => $this->optionalFloat($this->payloadValue($requestData, ['LoadingSMO', 'loadingSMO'], 0)),
            'LoadingBasmah'         => $this->optionalFloat($this->payloadValue($requestData, ['LoadingBasmah', 'loadingBasmah'], 0)),
            'MinGroupCount'         => (int) ($this->payloadValue($requestData, ['MinGroupCount', 'minGroupCount', 'MinGroup', 'minGroup'], 1)),
            'MaxGroupCount'         => (int) ($this->payloadValue($requestData, ['MaxGroupCount', 'maxGroupCount', 'MaxGroup', 'maxGroup'], 1)),
            'FromDate'              => $this->payloadValue($requestData, ['FromDate', 'fromDate', 'From', 'from']),
            'ToDate'                => $this->payloadValue($requestData, ['ToDate', 'toDate', 'To', 'to']),
            'Active'                => $active === null ? 1 : $active,
            'PartnerId'             => $this->optionalInt($this->payloadValue($requestData, ['PartnerId', 'partnerId'])) ?? 1,
            'CedantId'              => $this->optionalInt($this->payloadValue($requestData, ['CedantId', 'cedantId'])),
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

    private function optionalFloat($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
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
