<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseRateModel extends Model
{
    protected $DBGroup = 'default';

    public function getBaseRates($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetBasePremiumRates
                @RegionCode = ?,
                @PlanId     = ?,
                @PlanCode   = ?,
                @Page       = ?,
                @PageSize   = ?,
                @AsOfDate   = ?
        ";

        $query = $db->query($sql, [
            $data['RegionCode'] ?? null,
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
            $data['AsOfDate'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch base rates');
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
            'summary'   => $summary,
            'baseRates' => $rows,
        ];
    }

    public function getDropdowns($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetBaseRateDropdowns
                @RegionCode = ?
        ";

        $query = $db->query($sql, [
            $data['RegionCode'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch dropdowns');
        }

        $regions = [];
        $plans   = [];

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classifyDropdown($row, $regions, $plans);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classifyDropdown($row, $regions, $plans);
                }
            }
        }

        return [
            'regions' => $regions,
            'plans'   => $plans,
        ];
    }

    public function getById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetBasePremiumRateById
                @AgeBandId  = ?,
                @PlanId     = ?,
                @PlanCode   = ?,
                @RegionCode = ?,
                @AsOfDate   = ?
        ";

        $query = $db->query($sql, [
            $data['AgeBandId'] ?? null,
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['RegionCode'] ?? null,
            $data['AsOfDate'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch base rate');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $row ?: [];
    }

    public function saveBaseRate($data)
    {
        return $this->writeBaseRate($data, 0);
    }

    public function updateBaseRate($data)
    {
        return $this->writeBaseRate($data, 1);
    }

    private function writeBaseRate($data, $isUpdate)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_updatedBasePremiumRate
                @AgeBandId     = ?,
                @PlanId        = ?,
                @PlanCode      = ?,
                @RegionCode    = ?,
                @MalePremium   = ?,
                @FemalePremium = ?,
                @AsOfDate      = ?,
                @IsUpdate      = ?
        ";

        $query = $db->query($sql, [
            $data['AgeBandId'] ?? null,
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['RegionCode'] ?? null,
            $data['MalePremium'] ?? null,
            $data['FemalePremium'] ?? null,
            $data['AsOfDate'] ?? null,
            $isUpdate,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save base rate');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $row ?: [];
    }

    private function classifyDropdown($row, &$regions, &$plans)
    {
        $type = $row['ResultSet'] ?? '';
        if ($type === 'Region') {
            $regions[] = $row;
            return;
        }
        if ($type === 'Plan') {
            $plans[] = $row;
        }
    }

    private function classify($row, &$summary, &$rows)
    {
        if (array_key_exists('FilteredCount', $row) && array_key_exists('TotalPages', $row)) {
            $summary = $row;
            return;
        }

        if (! empty($row['Id'])) {
            $rows[] = $row;
        }
    }
}
