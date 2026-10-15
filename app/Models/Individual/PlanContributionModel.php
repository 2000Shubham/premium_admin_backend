<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class PlanContributionModel extends Model
{
    protected $DBGroup = 'default';

    public function getPlanContributions($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetPlanContributions
                @Search         = ?,
                @PlanId         = ?,
                @NetworkId      = ?,
                @CoverageAreaId = ?,
                @Gender         = ?,
                @Active         = ?,
                @Page           = ?,
                @PageSize       = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['PlanId'] ?? null,
            $data['NetworkId'] ?? null,
            $data['CoverageAreaId'] ?? null,
            $data['Gender'] ?? null,
            $data['Active'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch plan contributions');
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
            'summary'        => $summary,
            'contributions'  => $rows,
        ];
    }

    public function getPlanContributionById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetPlanContributionById
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch plan contribution');
        }

        return $this->firstRow($query);
    }

    public function savePlanContribution($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SavePlanContribution
                @Id                    = ?,
                @PlanId                = ?,
                @CoverType             = ?,
                @SubCoverType          = ?,
                @CoverageAreaId        = ?,
                @NetworkId             = ?,
                @AggregateLimitId      = ?,
                @CoverageType          = ?,
                @PlanType              = ?,
                @MinAge                = ?,
                @MaxAge                = ?,
                @Gender                = ?,
                @MaritalStatus         = ?,
                @Relationship          = ?,
                @PolicyPeriod          = ?,
                @MinSI                 = ?,
                @MaxSI                 = ?,
                @MinSumAssured         = ?,
                @MaxSumAssured         = ?,
                @ContributionType      = ?,
                @Rate                  = ?,
                @FixedContribution     = ?,
                @MinimumContribution   = ?,
                @MaternityContribution = ?,
                @NetFixedContribution  = ?,
                @ContributionPeriod    = ?,
                @CurrencyCode          = ?,
                @CoPayment             = ?,
                @Deductible            = ?,
                @Pharmacy              = ?,
                @LoadingME             = ?,
                @LoadingSMO            = ?,
                @LoadingBasmah         = ?,
                @MinGroupCount         = ?,
                @MaxGroupCount         = ?,
                @FromDate              = ?,
                @ToDate                = ?,
                @Active                = ?,
                @PartnerId             = ?,
                @CedantId              = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['PlanId'] ?? null,
            $data['CoverType'] ?? null,
            $data['SubCoverType'] ?? null,
            $data['CoverageAreaId'] ?? null,
            $data['NetworkId'] ?? null,
            $data['AggregateLimitId'] ?? null,
            $data['CoverageType'] ?? null,
            $data['PlanType'] ?? null,
            $data['MinAge'] ?? 0,
            $data['MaxAge'] ?? 0,
            $data['Gender'] ?? null,
            $data['MaritalStatus'] ?? null,
            $data['Relationship'] ?? null,
            $data['PolicyPeriod'] ?? 12,
            $data['MinSI'] ?? 0,
            $data['MaxSI'] ?? 0,
            $data['MinSumAssured'] ?? null,
            $data['MaxSumAssured'] ?? null,
            $data['ContributionType'] ?? 'FIXED',
            $data['Rate'] ?? 0,
            $data['FixedContribution'] ?? 0,
            $data['MinimumContribution'] ?? 0,
            $data['MaternityContribution'] ?? 0,
            $data['NetFixedContribution'] ?? 0,
            $data['ContributionPeriod'] ?? 'ANNUAL',
            $data['CurrencyCode'] ?? 'AED',
            $data['CoPayment'] ?? null,
            $data['Deductible'] ?? 0,
            $data['Pharmacy'] ?? null,
            $data['LoadingME'] ?? 0,
            $data['LoadingSMO'] ?? 0,
            $data['LoadingBasmah'] ?? 0,
            $data['MinGroupCount'] ?? 1,
            $data['MaxGroupCount'] ?? 1,
            $data['FromDate'] ?? null,
            $data['ToDate'] ?? null,
            $data['Active'] ?? 1,
            $data['PartnerId'] ?? 1,
            $data['CedantId'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save plan contribution');
        }

        return $this->firstRow($query);
    }

    public function updatePlanContribution($data)
    {
        return $this->savePlanContribution($data);
    }

    public function deletePlanContribution($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeletePlanContribution
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete plan contribution');
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

        foreach (['FromDate', 'ToDate'] as $key) {
            if (empty($row[$key])) {
                continue;
            }
            if ($row[$key] instanceof \DateTimeInterface) {
                $row[$key] = $row[$key]->format('Y-m-d');
            } elseif (is_string($row[$key]) && preg_match('/^\d{4}-\d{2}-\d{2}/', $row[$key])) {
                $row[$key] = substr($row[$key], 0, 10);
            }
        }

        return $row;
    }
}
