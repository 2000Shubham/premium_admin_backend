<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class ProductConfigModel extends Model
{
    protected $DBGroup = 'default';

    public function getProductConfigs($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetProductConfigs
                @Search    = ?,
                @ProductId = ?,
                @Active    = ?,
                @Page      = ?,
                @PageSize  = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['ProductId'] ?? null,
            $data['Active'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch product configs');
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

    public function getProductConfigById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetProductConfigById
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch product config');
        }

        return $this->firstRow($query);
    }

    public function saveProductConfig($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SaveProductConfig
                @Id         = ?,
                @ProductId  = ?,
                @Name       = ?,
                @Code       = ?,
                @Value      = ?,
                @ConfigDesc = ?,
                @IsEditable = ?,
                @Active     = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['ProductId'] ?? 10,
            $data['Name'] ?? null,
            $data['Code'] ?? null,
            $data['Value'] ?? null,
            $data['ConfigDesc'] ?? null,
            $data['IsEditable'] ?? 1,
            $data['Active'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save product config');
        }

        return $this->firstRow($query);
    }

    public function updateProductConfig($data)
    {
        return $this->saveProductConfig($data);
    }

    public function deleteProductConfig($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeleteProductConfig
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete product config');
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
