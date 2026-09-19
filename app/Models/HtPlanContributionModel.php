<?php

namespace App\Models;

use CodeIgniter\Model;

class HtPlanContributionModel extends Model
{
    protected $table            = 'tbl_HT_PlanContribution';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'PartnerId',
        'CedantId',
        'PlanId',
        'MinSI',
        'MaxSI',
        'CoverType',
        'SubCoverType',
        'CoverageAreaId',
        'NetworkId',
        'MinAge',
        'MaxAge',
        'Gender',
        'MaritalStatus',
        'RelationShip',
        'PolicyPeriod',
        'MinSumAssured',
        'MaxSumAssured',
        'ContributionType',
        'Rate',
        'FixedContribution',
        'MinimumContribution',
        'MaternityContribution',
        'NetFixedContribution',
        'ContributionPeriod',
        'Active',
        'FromDate',
        'ToDate',
        'CurrencyCode',
        'MinGroupCount',
        'MaxGroupCount',
        'LoadingME',
        'LoadingSMO',
        'LoadingBasmah',
        'AggregateLimitId',
        'CoverageType',
        'PlanType',
        'CoPayment',
        'Deductible',
        'Pharmacy',
    ];

    /**
     * @param array<string, mixed> $plan
     *
     * @return array{StatusCode: int, StatusMessage: string, NewId: int}
     */
    public function insertViaProcedure(array $plan): array
    {
        $this->db->initialize();

        $statusCode    = 0;
        $statusMessage = '';
        $newId         = 0;

        $sql = '{CALL dbo.proc_InsertPlanContribution(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)}';

        $params = [
            [&$statusCode, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_INT],
            [&$statusMessage, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_VARCHAR(200)],
            [&$newId, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_INT],
            [$plan['PartnerId'] ?? 0, SQLSRV_PARAM_IN],
            [$plan['CedantId'] ?? null, SQLSRV_PARAM_IN],
            [$plan['PlanId'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MinSI'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MaxSI'] ?? null, SQLSRV_PARAM_IN],
            [$plan['CoverType'] ?? null, SQLSRV_PARAM_IN],
            [$plan['SubCoverType'] ?? null, SQLSRV_PARAM_IN],
            [$plan['CoverageAreaId'] ?? null, SQLSRV_PARAM_IN],
            [$plan['NetworkId'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MinAge'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MaxAge'] ?? null, SQLSRV_PARAM_IN],
            [$plan['Gender'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MaritalStatus'] ?? null, SQLSRV_PARAM_IN],
            [$plan['RelationShip'] ?? null, SQLSRV_PARAM_IN],
            [$plan['PolicyPeriod'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MinSumAssured'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MaxSumAssured'] ?? null, SQLSRV_PARAM_IN],
            [$plan['ContributionType'] ?? null, SQLSRV_PARAM_IN],
            [$plan['Rate'] ?? null, SQLSRV_PARAM_IN],
            [$plan['FixedContribution'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MinimumContribution'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MaternityContribution'] ?? null, SQLSRV_PARAM_IN],
            [$plan['NetFixedContribution'] ?? null, SQLSRV_PARAM_IN],
            [$plan['ContributionPeriod'] ?? null, SQLSRV_PARAM_IN],
            [$plan['Active'] ?? 1, SQLSRV_PARAM_IN],
            [$plan['FromDate'] ?? null, SQLSRV_PARAM_IN],
            [$plan['ToDate'] ?? null, SQLSRV_PARAM_IN],
            [$plan['CurrencyCode'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MinGroupCount'] ?? null, SQLSRV_PARAM_IN],
            [$plan['MaxGroupCount'] ?? null, SQLSRV_PARAM_IN],
            [$plan['LoadingME'] ?? null, SQLSRV_PARAM_IN],
            [$plan['LoadingSMO'] ?? null, SQLSRV_PARAM_IN],
            [$plan['LoadingBasmah'] ?? null, SQLSRV_PARAM_IN],
            [$plan['AggregateLimitId'] ?? null, SQLSRV_PARAM_IN],
            [$plan['CoverageType'] ?? null, SQLSRV_PARAM_IN],
            [$plan['PlanType'] ?? null, SQLSRV_PARAM_IN],
            [$plan['CoPayment'] ?? null, SQLSRV_PARAM_IN],
            [$plan['Deductible'] ?? null, SQLSRV_PARAM_IN],
            [$plan['Pharmacy'] ?? null, SQLSRV_PARAM_IN],
        ];

        $stmt = sqlsrv_query($this->db->connID, $sql, $params);

        if ($stmt === false) {
            $errors = sqlsrv_errors() ?: [];
            $message = 'Stored procedure failed.';

            if (isset($errors[0]['message'])) {
                $message = trim((string) $errors[0]['message']);
            }

            return [
                'StatusCode'    => 500,
                'StatusMessage' => $message,
                'NewId'         => 0,
            ];
        }

        while (sqlsrv_next_result($stmt)) {
            // Drain remaining result sets so OUTPUT params are populated.
        }

        sqlsrv_free_stmt($stmt);

        return [
            'StatusCode'    => (int) $statusCode,
            'StatusMessage' => (string) $statusMessage,
            'NewId'         => (int) $newId,
        ];
    }
}
