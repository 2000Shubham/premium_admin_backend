<?php

namespace App\Models;

use CodeIgniter\Model;

class AgeBandModel extends Model
{
    protected $DBGroup = 'default';

    public function getAgeBands($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetAgeBands
                @Search   = ?,
                @Page     = ?,
                @PageSize = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch age bands');
        }

        return $this->getAgeBandsResult($query);
    }

    public function getAgeBandById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetAgeBandById
                @AgeBandId = ?,
                @Code      = ?
        ";

        $query = $db->query($sql, [
            $data['AgeBandId'] ?? null,
            $data['Code'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch age band');
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

    public function saveAgeBand($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SaveAgeBand
                @AgeBandId = ?,
                @Code      = ?,
                @Label     = ?,
                @FromAge   = ?,
                @ToAge     = ?
        ";

        $query = $db->query($sql, [
            $data['AgeBandId'] ?? null,
            $data['Code'],
            $data['Label'],
            $data['FromAge'],
            $data['ToAge'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save age band');
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

    public function updateAgeBand($data)
    {
        return $this->saveAgeBand($data);
    }

    private function getAgeBandsResult($query)
    {
        $summary = null;
        $rows    = [];

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classifyAgeBandListRow($row, $summary, $rows);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classifyAgeBandListRow($row, $summary, $rows);
                }
            }
        }

        return [
            'summary'  => $summary,
            'ageBands' => $rows,
        ];
    }

    private function classifyAgeBandListRow($row, &$summary, &$rows)
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
