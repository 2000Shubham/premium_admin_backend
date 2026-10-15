<?php

namespace App\Models;

use CodeIgniter\Model;

class AdjustmentFactorModel extends Model
{
    protected $DBGroup = 'default';

    public function getAdjustmentFactors($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetAdjustmentFactors
                @FactorType = ?,
                @IsActive   = ?,
                @Page       = ?,
                @PageSize   = ?
        ";

        $query = $db->query($sql, [
            $data['FactorType'] ?? null,
            $data['IsActive'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch adjustment factors');
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
            'summary'           => $summary,
            'adjustmentFactors' => $rows,
        ];
    }

    public function getById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetAdjustmentFactorById
                @AdjustmentFactorId = ?,
                @FactorType         = ?,
                @Code               = ?
        ";

        $query = $db->query($sql, [
            $data['AdjustmentFactorId'] ?? null,
            $data['FactorType'] ?? null,
            $data['Code'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch adjustment');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapAdjustmentRow($row ?: []);
    }

    public function deleteAdjustmentFactor($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeleteAdjustmentFactor
                @AdjustmentFactorId = ?,
                @FactorType         = ?,
                @Code               = ?
        ";

        $query = $db->query($sql, [
            $data['AdjustmentFactorId'] ?? null,
            $data['FactorType'] ?? null,
            $data['Code'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete adjustment');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapAdjustmentRow($row ?: []);
    }

    public function saveAdjustmentFactor($data)
    {
        return $this->writeAdjustmentFactor($data, 0);
    }

    public function updateAdjustmentFactor($data)
    {
        return $this->writeAdjustmentFactor($data, 1);
    }

    private function writeAdjustmentFactor($data, $isUpdate)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_saveAdjustmentFactor
                @AdjustmentFactorId = ?,
                @FactorType         = ?,
                @Code               = ?,
                @Label              = ?,
                @Factor             = ?,
                @FromDate           = ?,
                @ToDate             = ?,
                @IsActive           = ?,
                @IsUpdate           = ?
        ";

        $query = $db->query($sql, [
            $data['AdjustmentFactorId'] ?? null,
            $data['FactorType'] ?? null,
            $data['Code'] ?? null,
            $data['Label'] ?? null,
            $this->formatFactor($data['Factor'] ?? null),
            $data['FromDate'] ?? null,
            $data['ToDate'] ?? null,
            $data['IsActive'] ?? 1,
            (int) $isUpdate,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save adjustment');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapAdjustmentRow($row ?: []);
    }

    private function classify($row, &$summary, &$rows)
    {
        if (array_key_exists('FilteredCount', $row) && array_key_exists('TotalPages', $row)) {
            $summary = $row;
            return;
        }

        if (array_key_exists('Id', $row) || array_key_exists('FactorType', $row)) {
            $rows[] = $this->mapAdjustmentRow($row);
        }
    }

    private function mapAdjustmentRow($row)
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

    private function formatFactor($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return number_format((float) $value, 10, '.', '');
    }
}
