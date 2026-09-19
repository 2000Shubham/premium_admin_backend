<?php

namespace App\Models;

use CodeIgniter\Model;

class MaternityModel extends Model
{
    protected $DBGroup = 'default';

    public function getMaternityRates($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetMaternityRates
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
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch maternity rates');
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
            'summary'        => $summary,
            'maternityRates' => $rows,
        ];
    }

    public function getById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetMaternityRateById
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
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch maternity rate');
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

    public function saveMaternityRate($data)
    {
        return $this->writeMaternityRate($data, 0);
    }

    public function updateMaternityRate($data)
    {
        return $this->writeMaternityRate($data, 1);
    }

    private function writeMaternityRate($data, $isUpdate)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_updatedMaternityRate
                @AgeBandId        = ?,
                @PlanId           = ?,
                @PlanCode         = ?,
                @RegionCode       = ?,
                @MaternityPremium = ?,
                @AsOfDate         = ?,
                @IsUpdate         = ?
        ";

        $query = $db->query($sql, [
            $data['AgeBandId'] ?? null,
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['RegionCode'] ?? null,
            $data['MaternityPremium'] ?? null,
            $data['AsOfDate'] ?? null,
            $isUpdate,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save maternity rate');
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
