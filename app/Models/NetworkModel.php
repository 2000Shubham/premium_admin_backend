<?php

namespace App\Models;

use CodeIgniter\Model;

class NetworkModel extends Model
{
    protected $DBGroup = 'default';

    public function getNetworks($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetNetworks
                @IsActive   = ?,
                @RegionCode = ?,
                @Page       = ?,
                @PageSize   = ?
        ";

        $query = $db->query($sql, [
            $data['IsActive'] ?? null,
            $data['RegionCode'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch networks');
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

    public function getById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetNetworkById
                @NetworkId = ?,
                @Code      = ?
        ";

        $query = $db->query($sql, [
            $data['NetworkId'] ?? null,
            $data['Code'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch network');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapNetworkRow($row ?: []);
    }

    public function saveNetwork($data)
    {
        return $this->writeNetwork($data, 0);
    }

    public function updateNetwork($data)
    {
        return $this->writeNetwork($data, 1);
    }

    private function writeNetwork($data, $isUpdate)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_saveNetwork
                @NetworkId  = ?,
                @Code       = ?,
                @Name       = ?,
                @RegionCode = ?,
                @IsUpdate   = ?
        ";

        $query = $db->query($sql, [
            $data['NetworkId'] ?? null,
            $data['Code'] ?? null,
            $data['Name'] ?? null,
            $data['RegionCode'] ?? null,
            (int) $isUpdate,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save network');
        }

        $row = $query->getRowArray();
        if (! $row) {
            $stmt = $query->resultID ?? null;
            if ($stmt && sqlsrv_next_result($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
            }
        }

        return $this->mapNetworkRow($row ?: []);
    }

    private function classify($row, &$summary, &$rows)
    {
        if (array_key_exists('FilteredCount', $row) && array_key_exists('TotalPages', $row)) {
            $summary = $row;
            return;
        }

        if (! empty($row['Id'])) {
            $rows[] = $this->mapNetworkRow($row);
        }
    }

    private function mapNetworkRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        $region = $row['RegionCode'] ?? $row['regionCode'] ?? null;
        if ($region === '') {
            $region = null;
        }

        $row['RegionCode'] = $region;

        return $row;
    }
}
