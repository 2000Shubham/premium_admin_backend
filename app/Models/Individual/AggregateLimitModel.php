<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class AggregateLimitModel extends Model
{
    protected $DBGroup = 'default';

    public function getAggregateLimits($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetAggregateLimits
                @Search   = ?,
                @Active   = ?,
                @Page     = ?,
                @PageSize = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['Active'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch aggregate limits');
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
            'limits'  => $rows,
        ];
    }

    public function getAggregateLimitById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetAggregateLimitById
                @Id               = ?,
                @AggregateLimitId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['AggregateLimitId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch aggregate limit');
        }

        return $this->firstRow($query);
    }

    public function saveAggregateLimit($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SaveAggregateLimit
                @Id               = ?,
                @Label            = ?,
                @MinLimit         = ?,
                @MaxLimit         = ?,
                @DisplayOrder     = ?,
                @CrsCode          = ?,
                @CrsName          = ?,
                @Active           = ?,
                @AggregateLimitId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['Label'] ?? null,
            $data['MinLimit'] ?? 0,
            $data['MaxLimit'] ?? null,
            $data['DisplayOrder'] ?? 1,
            $data['CrsCode'] ?? null,
            $data['CrsName'] ?? null,
            $data['Active'] ?? 1,
            $data['AggregateLimitId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save aggregate limit');
        }

        return $this->firstRow($query);
    }

    public function updateAggregateLimit($data)
    {
        return $this->saveAggregateLimit($data);
    }

    public function deleteAggregateLimit($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeleteAggregateLimit
                @Id               = ?,
                @AggregateLimitId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['AggregateLimitId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete aggregate limit');
        }

        return $this->firstRow($query);
    }

    private function firstRow($query)
    {
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

        if (array_key_exists('Active', $row)) {
            $row['Active'] = ((int) $row['Active']) === 1;
        }

        return $row;
    }
}
