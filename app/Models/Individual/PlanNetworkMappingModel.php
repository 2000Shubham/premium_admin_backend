<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class PlanNetworkMappingModel extends Model
{
    protected $DBGroup = 'default';

    public function getPlanNetworkMappings($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetPlanNetworkMappings
                @PlanId    = ?,
                @NetworkId = ?,
                @Active    = ?,
                @Page      = ?,
                @PageSize  = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'] ?? null,
            $data['NetworkId'] ?? null,
            $data['Active'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch plan-network mappings');
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
            'mappings' => $rows,
        ];
    }

    public function getPlanNetworkMappingById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetPlanNetworkMappingById
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch plan-network mapping');
        }

        return $this->firstRow($query);
    }

    public function savePlanNetworkMapping($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SavePlanNetworkMapping
                @Id        = ?,
                @PlanId    = ?,
                @NetworkId = ?,
                @Active    = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['PlanId'] ?? null,
            $data['NetworkId'] ?? null,
            $data['Active'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save plan-network mapping');
        }

        return $this->firstRow($query);
    }

    public function updatePlanNetworkMapping($data)
    {
        return $this->savePlanNetworkMapping($data);
    }

    public function deletePlanNetworkMapping($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeletePlanNetworkMapping
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete plan-network mapping');
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
