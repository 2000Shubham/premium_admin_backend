<?php

namespace App\Models;

use CodeIgniter\Model;

class CalculatorModel extends Model
{
    protected $DBGroup = 'default';

    public function calculatePremium($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_CalculateMedicalPremium
                @Age                     = ?,
                @Gender                  = ?,
                @PlanId                  = ?,
                @PlanCode                = ?,
                @RegionCode              = ?,
                @NetworkCode             = ?,
                @AnnualLimit             = ?,
                @DiagnosticOptionCode    = ?,
                @IPCopayOptionCode       = ?,
                @ConsultationOptionCode  = ?,
                @PharmacyLimitOptionCode = ?,
                @PharmacyCopayOptionCode = ?,
                @TPAOptionCode           = ?,
                @IncludeMaternity        = ?,
                @IncludeDental           = ?,
                @IncludeOptical          = ?,
                @DentalOptionCode        = ?,
                @OpticalOptionCode       = ?,
                @AsOfDate                = ?
        ";

        $query = $db->query($sql, [
            $data['Age'] ?? null,
            $data['Gender'] ?? null,
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['RegionCode'] ?? null,
            $data['NetworkCode'] ?? null,
            $data['AnnualLimit'] ?? null,
            $data['DiagnosticOptionCode'] ?? null,
            $data['IPCopayOptionCode'] ?? null,
            $data['ConsultationOptionCode'] ?? null,
            $data['PharmacyLimitOptionCode'] ?? null,
            $data['PharmacyCopayOptionCode'] ?? null,
            $data['TPAOptionCode'] ?? null,
            $data['IncludeMaternity'] ?? 0,
            $data['IncludeDental'] ?? 0,
            $data['IncludeOptical'] ?? 0,
            $data['DentalOptionCode'] ?? null,
            $data['OpticalOptionCode'] ?? null,
            $data['AsOfDate'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to calculate premium');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapRow($row ?: []);
    }

    public function getDropdowns($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetCalculatorDropdowns
                @RegionCode  = ?,
                @NetworkCode = ?,
                @PlanId      = ?,
                @PlanCode    = ?,
                @IsActive    = ?,
                @AsOfDate    = ?
        ";

        $query = $db->query($sql, [
            $data['RegionCode'] ?? null,
            $data['NetworkCode'] ?? null,
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            array_key_exists('IsActive', $data) ? $data['IsActive'] : 1,
            $data['AsOfDate'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch calculator dropdowns');
        }

        $grouped = [
            'genders'      => [],
            'regions'      => [],
            'plans'        => [],
            'networks'     => [],
            'annualLimits' => [],
            'adjustments'  => [],
            'benefits'     => [],
        ];

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classifyDropdown($row, $grouped);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classifyDropdown($row, $grouped);
                }
            }
        }

        return $grouped;
    }

    private function classifyDropdown($row, array &$grouped)
    {
        $row = $this->mapRow(is_array($row) ? $row : []);
        $type = strtoupper((string) ($row['ResultSet'] ?? $row['resultSet'] ?? ''));

        if ($type === 'GENDER') {
            $grouped['genders'][] = $row;
            return;
        }
        if ($type === 'REGION') {
            $grouped['regions'][] = $row;
            return;
        }
        if ($type === 'PLAN') {
            $grouped['plans'][] = $row;
            return;
        }
        if ($type === 'NETWORK') {
            $grouped['networks'][] = $row;
            return;
        }
        if ($type === 'ANNUALLIMIT') {
            $grouped['annualLimits'][] = $row;
            return;
        }
        if ($type === 'ADJUSTMENT') {
            $grouped['adjustments'][] = $row;
            return;
        }
        if ($type === 'BENEFIT') {
            $grouped['benefits'][] = $row;
        }
    }

    private function mapRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        foreach (['RateDate', 'FromDate', 'ToDate', 'EffectiveFrom', 'EffectiveTo'] as $key) {
            if (isset($row[$key]) && $row[$key] instanceof \DateTimeInterface) {
                $row[$key] = $row[$key]->format('Y-m-d');
            }
        }

        return $row;
    }
}
