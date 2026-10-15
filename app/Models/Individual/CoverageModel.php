<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class CoverageModel extends Model
{
    protected $DBGroup = 'default';

    public function getCoverageAreas($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetCoverageAreas
                @Search    = ?,
                @CoverType = ?,
                @Active    = ?,
                @Page      = ?,
                @PageSize  = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['CoverType'] ?? null,
            $data['Active'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch coverage areas');
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
            'summary' => $summary,
            'areas'   => $rows,
        ];
    }

    public function getCoverageAreaById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetCoverageAreaById
                @Id             = ?,
                @CoverageAreaId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['CoverageAreaId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch coverage area');
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

    public function saveCoverageArea($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SaveCoverageArea
                @Id             = ?,
                @CoverageName   = ?,
                @CoverType      = ?,
                @CrsCode        = ?,
                @CrsName        = ?,
                @CoverageDesc   = ?,
                @FromDate       = ?,
                @ToDate         = ?,
                @Active         = ?,
                @CoverageAreaId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['CoverageName'] ?? null,
            $data['CoverType'] ?? null,
            $data['CrsCode'] ?? null,
            $data['CrsName'] ?? null,
            $data['CoverageDesc'] ?? null,
            $data['FromDate'] ?? null,
            $data['ToDate'] ?? null,
            $data['Active'] ?? 1,
            $data['CoverageAreaId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save coverage area');
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

    public function updateCoverageArea($data)
    {
        return $this->saveCoverageArea($data);
    }

    public function deleteCoverageArea($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeleteCoverageArea
                @Id             = ?,
                @CoverageAreaId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['CoverageAreaId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete coverage area');
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

    private function classify($row, &$summary, &$rows)
    {
        if (array_key_exists('FilteredCount', $row) && array_key_exists('TotalPages', $row)) {
            $summary = $row;
            return;
        }

        if (! empty($row['Id'])) {
            $rows[] = $this->mapRow($row);
        }
    }

    private function mapRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        foreach (['FromDate', 'ToDate'] as $key) {
            if (isset($row[$key]) && $row[$key] instanceof \DateTimeInterface) {
                $row[$key] = $row[$key]->format('Y-m-d');
            }
        }

        if (array_key_exists('Active', $row)) {
            $row['Active'] = ((int) $row['Active']) === 1;
        }

        return $row;
    }
}
