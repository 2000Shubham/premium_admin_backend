<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class NetworkMasterModel extends Model
{
    protected $DBGroup = 'default';

    public function getNetworkMasters($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetNetworkMasters
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
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch network masters');
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
            'summary'  => $summary,
            'networks' => $rows,
        ];
    }

    public function getNetworkMasterById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetNetworkMasterById
                @Id        = ?,
                @NetworkId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['NetworkId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch network');
        }

        return $this->firstRow($query);
    }

    public function saveNetworkMaster($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SaveNetworkMaster
                @Id        = ?,
                @Code      = ?,
                @Name      = ?,
                @CrsCode   = ?,
                @CrsName   = ?,
                @Active    = ?,
                @NetworkId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['Code'] ?? null,
            $data['Name'] ?? null,
            $data['CrsCode'] ?? null,
            $data['CrsName'] ?? null,
            $data['Active'] ?? 1,
            $data['NetworkId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save network');
        }

        return $this->firstRow($query);
    }

    public function updateNetworkMaster($data)
    {
        return $this->saveNetworkMaster($data);
    }

    public function deleteNetworkMaster($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeleteNetworkMaster
                @Id        = ?,
                @NetworkId = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['NetworkId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete network');
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
