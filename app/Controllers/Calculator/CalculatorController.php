<?php

namespace App\Controllers\Calculator;

use App\Controllers\BaseController;
use App\Models\CalculatorModel;

class CalculatorController extends BaseController
{
    protected $format = 'json';

    public function calculatePremium()
    {
        try {
            $payload = $this->calculatePayload();
            if ($payload instanceof \CodeIgniter\HTTP\ResponseInterface) {
                return $payload;
            }

            $result = (new CalculatorModel())->calculatePremium($payload);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Premium calculated successfully',
                    'res_massage' => 'Premium calculated successfully',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getGenders()
    {
        return $this->dropdownResponse('genders', 'Genders fetched successfully');
    }

    public function getRegions()
    {
        return $this->dropdownResponse('regions', 'Regions fetched successfully');
    }

    public function getPlans()
    {
        return $this->dropdownResponse('plans', 'Plans fetched successfully');
    }

    public function getNetworks()
    {
        return $this->dropdownResponse('networks', 'Networks fetched successfully');
    }

    public function getAnnualLimits()
    {
        return $this->dropdownResponse('annualLimits', 'Annual limits fetched successfully');
    }

    public function getDiagnosticOptions()
    {
        return $this->dropdownResponse('adjustments', 'Diagnostic options fetched successfully', 'DIAGNOSTIC');
    }

    public function getIpCopayOptions()
    {
        return $this->dropdownResponse('adjustments', 'IP co-pay options fetched successfully', 'IP_COPAY');
    }

    public function getConsultationOptions()
    {
        return $this->dropdownResponse('adjustments', 'Consultation options fetched successfully', 'CONSULTATION');
    }

    public function getPharmacyLimitOptions()
    {
        return $this->dropdownResponse('adjustments', 'Pharmacy limit options fetched successfully', 'PHARMACY_LIMIT');
    }

    public function getPharmacyCopayOptions()
    {
        return $this->dropdownResponse('adjustments', 'Pharmacy co-pay options fetched successfully', 'PHARMACY_COPAY');
    }

    public function getTpaOptions()
    {
        return $this->dropdownResponse('adjustments', 'TPA options fetched successfully', 'TPA');
    }

    public function getDentalOptions()
    {
        return $this->dropdownResponse('benefits', 'Dental options fetched successfully', 'DENTAL');
    }

    public function getOpticalOptions()
    {
        return $this->dropdownResponse('benefits', 'Optical options fetched successfully', 'OPTICAL');
    }

    private function dropdownResponse($key, $message, $type = null)
    {
        try {
            $filters = $this->dropdownFilters();
            $grouped = (new CalculatorModel())->getDropdowns($filters);
            $rows    = $grouped[$key] ?? [];

            if ($type !== null) {
                $type = strtoupper($type);
                $rows = array_values(array_filter($rows, static function ($row) use ($type, $key) {
                    $rowType = $key === 'benefits'
                        ? ($row['BenefitType'] ?? $row['benefitType'] ?? '')
                        : ($row['FactorType'] ?? $row['factorType'] ?? '');

                    return strtoupper((string) $rowType) === $type;
                }));
            }

            if (in_array($key, ['networks', 'annualLimits'], true)) {
                $region = strtoupper(trim((string) ($filters['RegionCode'] ?? '')));
                if ($region !== '') {
                    $rows = array_values(array_filter($rows, static function ($row) use ($region) {
                        $rowRegion = strtoupper(trim((string) ($row['RegionCode'] ?? $row['regionCode'] ?? 'DUBAI')));
                        if ($rowRegion === '') {
                            $rowRegion = 'DUBAI';
                        }

                        return $rowRegion === $region;
                    }));
                }
            }

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => $message,
                    'res_massage' => $message,
                    'data'        => $rows,
                ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    private function dropdownFilters()
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            $requestData = [];
        }

        $planId = $this->payloadValue($requestData, ['PlanId', 'planId']);

        return [
            'RegionCode'  => $this->payloadValue($requestData, ['RegionCode', 'regionCode']),
            'NetworkCode' => $this->payloadValue($requestData, ['NetworkCode', 'networkCode', 'Network', 'network']),
            'PlanId'      => ($planId === null || $planId === '') ? null : (int) $planId,
            'PlanCode'    => $this->payloadValue($requestData, ['PlanCode', 'planCode']),
            'IsActive'    => $this->optionalBit($this->payloadValue($requestData, ['IsActive', 'isActive'])) ?? 1,
            'AsOfDate'    => $this->payloadValue($requestData, ['AsOfDate', 'asOfDate']),
        ];
    }

    private function errorResponse(\Exception $e)
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

    private function calculatePayload()
    {
        $requestData = $this->request->getJSON(true);
        if (! $requestData) {
            $requestData = $this->request->getPost();
        }
        if (! is_array($requestData)) {
            $requestData = [];
        }

        $age        = $this->payloadValue($requestData, ['Age', 'age']);
        $gender     = $this->payloadValue($requestData, ['Gender', 'gender']);
        $planId     = $this->payloadValue($requestData, ['PlanId', 'planId']);
        $planCode   = $this->payloadValue($requestData, ['PlanCode', 'planCode']);
        $regionCode = $this->payloadValue($requestData, ['RegionCode', 'regionCode'], 'DUBAI');
        $network    = $this->payloadValue($requestData, ['NetworkCode', 'networkCode', 'Network', 'network']);
        $annual     = $this->payloadValue($requestData, ['AnnualLimit', 'annualLimit']);

        if ($age === null || $age === '' || ! is_numeric($age) || (int) $age < 0) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Age must be zero or greater',
                    'res_massage' => 'Age must be zero or greater',
                ]);
        }
        if ($gender === null || $gender === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Gender is required',
                    'res_massage' => 'Gender is required',
                ]);
        }
        if (($planId === null || $planId === '') && ($planCode === null || $planCode === '')) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'PlanId or PlanCode is required',
                    'res_massage' => 'PlanId or PlanCode is required',
                ]);
        }
        if ($network === null || $network === '') {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Network is required',
                    'res_massage' => 'Network is required',
                ]);
        }
        if ($annual === null || $annual === '' || ! is_numeric($annual) || (int) $annual <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'res_code'    => 400,
                    'status'      => false,
                    'message'     => 'Annual limit is required',
                    'res_massage' => 'Annual limit is required',
                ]);
        }

        return [
            'Age'                     => (int) $age,
            'Gender'                  => $gender,
            'PlanId'                  => ($planId === null || $planId === '') ? null : (int) $planId,
            'PlanCode'                => $planCode,
            'RegionCode'              => $regionCode,
            'NetworkCode'             => $network,
            'AnnualLimit'             => (int) $annual,
            'DiagnosticOptionCode'    => $this->payloadValue($requestData, ['DiagnosticOptionCode', 'diagnosticOptionCode', 'Diagnostic', 'diagnostic']),
            'IPCopayOptionCode'       => $this->payloadValue($requestData, ['IPCopayOptionCode', 'ipCopayOptionCode', 'IpCopay', 'ipCopay']),
            'ConsultationOptionCode'  => $this->payloadValue($requestData, ['ConsultationOptionCode', 'consultationOptionCode', 'Consultation', 'consultation']),
            'PharmacyLimitOptionCode' => $this->payloadValue($requestData, ['PharmacyLimitOptionCode', 'pharmacyLimitOptionCode', 'PharmacyLimit', 'pharmacyLimit']),
            'PharmacyCopayOptionCode' => $this->payloadValue($requestData, ['PharmacyCopayOptionCode', 'pharmacyCopayOptionCode', 'PharmacyCopay', 'pharmacyCopay']),
            'TPAOptionCode'           => $this->payloadValue($requestData, ['TPAOptionCode', 'tpaOptionCode', 'Tpa', 'tpa']),
            'IncludeMaternity'        => $this->optionalBit($this->payloadValue($requestData, ['IncludeMaternity', 'includeMaternity'])) ?? 0,
            'IncludeDental'           => $this->optionalBit($this->payloadValue($requestData, ['IncludeDental', 'includeDental'])) ?? 0,
            'IncludeOptical'          => $this->optionalBit($this->payloadValue($requestData, ['IncludeOptical', 'includeOptical'])) ?? 0,
            'DentalOptionCode'        => $this->payloadValue($requestData, ['DentalOptionCode', 'dentalOptionCode', 'Dental', 'dental']),
            'OpticalOptionCode'       => $this->payloadValue($requestData, ['OpticalOptionCode', 'opticalOptionCode', 'Optical', 'optical']),
            'AsOfDate'                => $this->payloadValue($requestData, ['AsOfDate', 'asOfDate']),
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
