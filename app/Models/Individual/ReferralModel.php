<?php

namespace App\Models\Individual;

use CodeIgniter\Model;

class ReferralModel extends Model
{
    protected $DBGroup = 'default';

    public function getReferralRules($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetReferralRules
                @Search   = ?,
                @Active   = ?,
                @Page     = ?,
                @PageSize = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['Active'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 10,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch referral rules');
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
            'rules'   => $rows,
        ];
    }

    public function getReferralRuleById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetReferralRuleById
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch referral rule');
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

    public function saveReferralRule($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SaveReferralRule
                @Id           = ?,
                @RuleName     = ?,
                @VisaMode     = ?,
                @VisaEmirates = ?,
                @MinAge       = ?,
                @MaxAge       = ?,
                @Gender       = ?,
                @MafClean     = ?,
                @Action       = ?,
                @DisplayOrder = ?,
                @Active       = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
            $data['RuleName'] ?? null,
            $data['VisaMode'] ?? null,
            $data['VisaEmirates'] ?? null,
            $data['MinAge'] ?? null,
            $data['MaxAge'] ?? null,
            $data['Gender'] ?? 'ALL',
            $data['MafClean'] ?? 1,
            $data['Action'] ?? null,
            $data['DisplayOrder'] ?? null,
            $data['Active'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save referral rule');
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

    public function updateReferralRule($data)
    {
        return $this->saveReferralRule($data);
    }

    public function deleteReferralRule($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_DeleteReferralRule
                @Id = ?
        ";

        $query = $db->query($sql, [
            $data['Id'] ?? null,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to delete referral rule');
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

        if (array_key_exists('Active', $row)) {
            $row['Active'] = ((int) $row['Active']) === 1;
        }

        if (array_key_exists('MafClean', $row)) {
            $row['MafClean'] = ((int) $row['MafClean']) === 1;
        }

        return $row;
    }
}
