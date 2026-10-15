<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnualLimitModel extends Model
{
    protected $DBGroup = 'default';

    public function getAnnualLimitFactors($data)
    {
        $db = \Config\Database::connect();
        $region = $this->normalizeRegion($data['RegionCode'] ?? null);

        $sql = "
            EXEC dbo.usp_HT_GetAnnualLimitFactors
                @NetworkCode = ?,
                @RegionCode  = ?,
                @IsActive    = ?,
                @Page        = ?,
                @PageSize    = ?
        ";

        $query = $db->query($sql, [
            $data['NetworkCode'] ?? null,
            $region,
            $data['IsActive'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch annual limit factors');
        }

        $summary  = null;
        $rows     = [];
        $networks = [];

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classify($row, $summary, $rows, $networks);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classify($row, $summary, $rows, $networks);
                }
            }
        }

        $rows     = $this->filterByRegion($rows, $region);
        $networks = $this->filterByRegion($networks, $region);
        if ($networks === []) {
            $networks = $this->networksFromFactors($rows);
        }

        return [
            'summary'            => $summary,
            'annualLimitFactors' => $rows,
            'networks'           => $networks,
        ];
    }

    public function getById($data)
    {
        $db = \Config\Database::connect();
        $region = $this->normalizeRegion($data['RegionCode'] ?? null);

        $sql = "
            EXEC dbo.usp_HT_GetAnnualLimitFactorById
                @AnnualLimitFactorId = ?,
                @AnnualLimit         = ?,
                @RegionCode          = ?
        ";

        $query = $db->query($sql, [
            $data['AnnualLimitFactorId'] ?? null,
            $data['AnnualLimit'] ?? null,
            $region,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch annual limit factor');
        }

        return $this->filterByRegion($this->collectIdRows($query), $region);
    }

    public function deleteAnnualLimitFactor($data)
    {
        $db = \Config\Database::connect();
        $region = $this->normalizeRegion($data['RegionCode'] ?? null);

        $sql = "
            EXEC dbo.usp_HT_DeleteAnnualLimitFactor
                @AnnualLimitFactorId = ?,
                @AnnualLimit         = ?,
                @RegionCode          = ?
        ";

        $query = $db->query($sql, [
            $data['AnnualLimitFactorId'] ?? null,
            $data['AnnualLimit'] ?? null,
            $region,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete annual limit factor');
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

    public function saveAnnualLimitFactor($data)
    {
        return $this->writeAnnualLimitFactor($data, 0);
    }

    public function updateAnnualLimitFactor($data)
    {
        return $this->writeAnnualLimitFactor($data, 1);
    }

    private function writeAnnualLimitFactor($data, $isUpdate)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_saveAnnualLimitFactor
                @AnnualLimit   = ?,
                @Factors       = ?,
                @EffectiveFrom = ?,
                @IsUpdate      = ?
        ";

        $query = $db->query($sql, [
            $data['AnnualLimit'] ?? null,
            $data['Factors'] ?? null,
            $data['EffectiveFrom'] ?? null,
            (int) $isUpdate,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save annual limit factor');
        }

        return $this->collectIdRows($query);
    }

    private function collectIdRows($query)
    {
        $rows = [];

        foreach ($query->getResultArray() ?: [] as $row) {
            if (! empty($row['Id'])) {
                $rows[] = $this->mapFactorRow($row);
            }
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    if (! empty($row['Id'])) {
                        $rows[] = $this->mapFactorRow($row);
                    }
                }
            }
        }

        return $rows;
    }

    private function classify($row, &$summary, &$rows, &$networks)
    {
        if (array_key_exists('FilteredCount', $row) && array_key_exists('TotalPages', $row)) {
            $summary = $row;
            return;
        }

        if (array_key_exists('AnnualLimit', $row) || array_key_exists('AdjustmentFactor', $row)) {
            $rows[] = $this->mapFactorRow($row);
            return;
        }

        $mapped = $this->mapNetworkRow($row);
        $code   = $mapped['Code'] ?? $mapped['NetworkCode'] ?? null;
        if ($code !== null && $code !== '') {
            $networks[] = $mapped;
        }
    }

    private function mapFactorRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        $row['RegionCode'] = $this->rowRegion($row);

        return $row;
    }

    private function mapNetworkRow($row)
    {
        if (! is_array($row) || $row === []) {
            return $row;
        }

        $code = $row['Code'] ?? $row['code'] ?? $row['NetworkCode'] ?? $row['networkCode'] ?? null;
        $name = $row['Name'] ?? $row['name'] ?? $row['NetworkName'] ?? $row['networkName'] ?? $code;

        $row['Code']        = $code;
        $row['Name']        = $name;
        $row['NetworkCode'] = $code;
        $row['NetworkName'] = $name;
        $row['RegionCode']  = $this->rowRegion($row);

        return $row;
    }

    private function networksFromFactors(array $rows)
    {
        $networks = [];
        $seen     = [];

        foreach ($rows as $row) {
            $code = $row['NetworkCode'] ?? $row['Code'] ?? null;
            if ($code === null || $code === '' || isset($seen[$code])) {
                continue;
            }

            $seen[$code] = true;
            $networks[]  = $this->mapNetworkRow([
                'Id'          => $row['NetworkId'] ?? $row['Id'] ?? null,
                'Code'        => $code,
                'Name'        => $row['NetworkName'] ?? $row['Name'] ?? $code,
                'RegionCode'  => $row['RegionCode'] ?? null,
            ]);
        }

        return $networks;
    }

    private function filterByRegion(array $rows, $region)
    {
        if ($region === null || $region === '') {
            return $rows;
        }

        $matched = [];
        foreach ($rows as $row) {
            if ($this->rowRegion($row) === $region) {
                $matched[] = $row;
            }
        }

        return $matched;
    }

    private function rowRegion($row)
    {
        $region = is_array($row)
            ? ($row['RegionCode'] ?? $row['regionCode'] ?? null)
            : null;

        if ($region === null || $region === '') {
            return 'DUBAI';
        }

        return strtoupper(trim((string) $region));
    }

    private function normalizeRegion($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = strtoupper(trim((string) $value));

        return $value === '' ? null : $value;
    }
}
