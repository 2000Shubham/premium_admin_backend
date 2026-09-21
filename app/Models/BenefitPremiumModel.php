<?php

namespace App\Models;

use CodeIgniter\Model;

class BenefitPremiumModel extends Model
{
    protected $table            = 'HT_PlanBenefitPremium';
    protected $primaryKey       = 'PlanBenefitPremiumId';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'PlanId', 'BenefitType', 'OptionCode', 'OptionLabel', 'Premium',
        'EffectiveFrom', 'EffectiveTo', 'IsActive',
    ];
    protected $useTimestamps = false;
    protected $DBGroup       = 'default';

    public function getBenefitPremiums($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetBenefitPremiums
                @PlanId      = ?,
                @PlanCode    = ?,
                @BenefitType = ?,
                @IsActive    = ?,
                @Page        = ?,
                @PageSize    = ?,
                @AsOfDate    = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['BenefitType'] ?? null,
            $data['IsActive'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
            $data['AsOfDate'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch benefit premiums');
        }

        $summary = null;
        $rows    = [];

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classify($row, $summary, $rows);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classify($row, $summary, $rows);
                }
            }
        }

        return [
            'summary'         => $summary,
            'benefitPremiums' => $rows,
        ];
    }

    public function getById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetBenefitPremiumById
                @PlanBenefitPremiumId = ?,
                @PlanId               = ?,
                @PlanCode             = ?,
                @BenefitType          = ?,
                @Code                 = ?
        ";

        $query = $db->query($sql, [
            $data['PlanBenefitPremiumId'] ?? null,
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['BenefitType'] ?? null,
            $data['Code'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch benefit premium');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapBenefitRow($row ?: []);
    }

    public function saveBenefitPremium($data)
    {
        return $this->writeBenefitPremium($data, 0);
    }

    public function updateBenefitPremium($data)
    {
        return $this->writeBenefitPremium($data, 1);
    }

    private function writeBenefitPremium($data, $isUpdate)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_saveBenefitPremium
                @PlanBenefitPremiumId = ?,
                @PlanId               = ?,
                @PlanCode             = ?,
                @BenefitType          = ?,
                @Code                 = ?,
                @Label                = ?,
                @Premium              = ?,
                @FromDate             = ?,
                @ToDate               = ?,
                @IsActive             = ?,
                @IsUpdate             = ?
        ";

        $query = $db->query($sql, [
            $data['PlanBenefitPremiumId'] ?? null,
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['BenefitType'] ?? null,
            $data['Code'] ?? null,
            $data['Label'] ?? null,
            $this->formatPremium($data['Premium'] ?? null),
            $data['FromDate'] ?? null,
            $data['ToDate'] ?? null,
            $data['IsActive'] ?? 1,
            (int) $isUpdate,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save benefit premium');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapBenefitRow($row ?: []);
    }

    private function classify($row, &$summary, &$rows)
    {
        if (array_key_exists('FilteredCount', $row) && array_key_exists('TotalPages', $row)) {
            $summary = $row;
            return;
        }

        if (array_key_exists('Id', $row) || array_key_exists('BenefitType', $row)) {
            $rows[] = $this->mapBenefitRow($row);
        }
    }

    private function mapBenefitRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        foreach (['FromDate', 'ToDate', 'EffectiveFrom', 'EffectiveTo'] as $key) {
            if (isset($row[$key]) && $row[$key] instanceof \DateTimeInterface) {
                $row[$key] = $row[$key]->format('Y-m-d');
            }
        }

        return $row;
    }

    private function formatPremium($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return number_format((float) $value, 6, '.', '');
    }
}
