<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class SystemConfigModel extends Model
{
    protected $DBGroup = 'default';

    public function getSystemConfigs($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetSystemConfigs
                @Search     = ?,
                @ConfigType = ?,
                @AppSource  = ?,
                @PartnerId  = ?,
                @Active     = ?,
                @Page       = ?,
                @PageSize   = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['ConfigType'] ?? null,
            $data['AppSource'] ?? null,
            $data['PartnerId'] ?? null,
            $data['Active'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch system configs');
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
            'configs' => $rows,
        ];
    }

    public function getSystemConfigById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetSystemConfigById
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch system config');
        }

        return $this->firstRow($query);
    }

    public function saveSystemConfig($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SaveSystemConfig
                @Id         = ?,
                @PartnerId  = ?,
                @Name       = ?,
                @Code       = ?,
                @ConfigType = ?,
                @Value      = ?,
                @AppSource  = ?,
                @IsEditable = ?,
                @Active     = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['PartnerId'] ?? 1,
            $data['Name'] ?? null,
            $data['Code'] ?? null,
            $data['ConfigType'] ?? 'STRING',
            $data['Value'] ?? null,
            $data['AppSource'] ?? 'CRM',
            $data['IsEditable'] ?? 1,
            $data['Active'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save system config');
        }

        return $this->firstRow($query);
    }

    public function updateSystemConfig($data)
    {
        return $this->saveSystemConfig($data);
    }

    public function deleteSystemConfig($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeleteSystemConfig
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete system config');
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

        if (array_key_exists('IsEditable', $row)) {
            $row['IsEditable'] = ((int) $row['IsEditable']) === 1;
        }

        return $row;
    }
}
