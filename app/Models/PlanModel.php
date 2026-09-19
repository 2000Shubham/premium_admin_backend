<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanModel extends Model
{
    protected $DBGroup = 'default';

    public function savePlan($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_Plan_Save
                @PlanCode       = ?,
                @PlanName       = ?,
                @Description    = ?,
                @RegionCode     = ?,
                @CopyFromPlanId = ?,
                @EffectiveFrom  = ?,
                @EffectiveTo    = ?,
                @IsActive       = ?
        ";

        $query = $db->query($sql, [
            $data['PlanCode'],
            $data['PlanName'],
            $data['Description'] ?? null,
            $data['RegionCode'] ?? 'DUBAI',
            $data['CopyFromPlanId'] ?? null,
            $data['EffectiveFrom'] ?? null,
            $data['EffectiveTo'] ?? null,
            $data['IsActive'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save plan');
        }

        return $this->savePlanResult($query);
    }

    public function updatePlan($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_UpdatePlan
                @PlanId        = ?,
                @PlanName      = ?,
                @Description   = ?,
                @RegionCode    = ?,
                @EffectiveFrom = ?,
                @EffectiveTo   = ?,
                @IsActive      = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'],
            $data['PlanName'],
            $data['Description'] ?? null,
            $data['RegionCode'] ?? 'DUBAI',
            $data['EffectiveFrom'] ?? null,
            $data['EffectiveTo'] ?? null,
            $data['IsActive'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to update plan');
        }

        return $this->savePlanResult($query);
    }

    public function updatePremiumRates($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_UpdatePremiumRates
                @PlanId           = ?,
                @RegionCode       = ?,
                @EffectiveFrom    = ?,
                @EffectiveTo      = ?,
                @IsActive         = ?,
                @BasePremiumRates = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'],
            $data['RegionCode'] ?? null,
            $data['EffectiveFrom'] ?? null,
            $data['EffectiveTo'] ?? null,
            $data['IsActive'] ?? 1,
            $this->jsonParam($data['BasePremiumRates'] ?? null),
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to update premium rates');
        }

        return $this->savePlanResult($query);
    }

    public function updateMaternityRates($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_UpdateMaternityRates
                @PlanId         = ?,
                @EffectiveFrom  = ?,
                @EffectiveTo    = ?,
                @IsActive       = ?,
                @MaternityRates = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'],
            $data['EffectiveFrom'] ?? null,
            $data['EffectiveTo'] ?? null,
            $data['IsActive'] ?? 1,
            $this->jsonParam($data['MaternityRates'] ?? null),
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to update maternity rates');
        }

        return $this->savePlanResult($query);
    }

    public function updateBenefitPremiums($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_UpdateBenefitPremiums
                @PlanId          = ?,
                @BenefitType     = ?,
                @EffectiveFrom   = ?,
                @EffectiveTo     = ?,
                @IsActive        = ?,
                @BenefitPremiums = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'],
            $data['BenefitType'] ?? null,
            $data['EffectiveFrom'] ?? null,
            $data['EffectiveTo'] ?? null,
            $data['IsActive'] ?? 1,
            $this->jsonParam($data['BenefitPremiums'] ?? null),
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to update benefit premiums');
        }

        return $this->savePlanResult($query);
    }

    public function saveHtPlan($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            DECLARE @STATUSCODE INT, @STATUSMESSAGE VARCHAR(200), @PLANID INT;

            EXEC dbo.HT_SavePlan
                @STATUSCODE       = @STATUSCODE OUTPUT,
                @STATUSMESSAGE    = @STATUSMESSAGE OUTPUT,
                @PLANID           = @PLANID OUTPUT,
                @PLANCODE         = ?,
                @PLANNAME         = ?,
                @DESCRIPTION      = ?,
                @REGIONCODE       = ?,
                @COPYFROMPLANID   = ?,
                @EFFECTIVEFROM    = ?,
                @EFFECTIVETO      = ?,
                @ISACTIVE         = ?,
                @MATERNITYRATES   = ?,
                @BENEFITPREMIUMS  = ?,
                @BASEPREMIUMRATES = ?;

            SELECT @STATUSCODE AS StatusCode, @STATUSMESSAGE AS StatusMessage, @PLANID AS PlanId;
        ";

        $query = $db->query($sql, [
            $data['PlanCode'],
            $data['PlanName'],
            $data['Description'] ?? null,
            $data['RegionCode'] ?? 'DUBAI',
            $data['CopyFromPlanId'] ?? null,
            $data['EffectiveFrom'] ?? null,
            $data['EffectiveTo'] ?? null,
            $data['IsActive'] ?? 1,
            $this->jsonParam($data['MaternityRates'] ?? null),
            $this->jsonParam($data['BenefitPremiums'] ?? null),
            $this->jsonParam($data['BasePremiumRates'] ?? null),
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save plan');
        }

        return $this->savePlanResult($query);
    }

    private function savePlanResult($query)
    {
        $statusRow = null;
        $planRow   = null;

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classifySaveRow($row, $statusRow, $planRow);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classifySaveRow($row, $statusRow, $planRow);
                }
            }
        }

        if ($statusRow && $planRow) {
            return array_merge($planRow, $statusRow);
        }

        return $statusRow ?: $planRow ?: [
            'StatusCode'    => 400,
            'StatusMessage' => 'Unable to save plan',
            'PlanId'        => null,
        ];
    }

    private function classifySaveRow($row, &$statusRow, &$planRow)
    {
        if (array_key_exists('StatusCode', $row) || array_key_exists('StatusMessage', $row)) {
            $statusRow = $row;
        }

        if (! empty($row['PlanId']) && (isset($row['PlanCode']) || isset($row['PlanName']))) {
            $planRow = $row;
        }
    }

    private function jsonParam($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        $json = json_encode($value, JSON_UNESCAPED_UNICODE);

        return $json === false ? null : $json;
    }

    public function savePremiumRate($data)
    {
        $db = \Config\Database::connect();
    
        $sql = "
            DECLARE @BaseRateId BIGINT;
    
            EXEC dbo.usp_HT_BasePremiumRate_Save
                @PlanId = ?,
                @AgeBandId = ?,
                @Gender = ?,
                @BasePremium = ?,
                @RegionCode = ?,
                @EffectiveFrom = ?,
                @EffectiveTo = ?,
                @IsActive = ?,
                @BaseRateId = @BaseRateId OUTPUT;
    
            SELECT @BaseRateId AS BaseRateId;
        ";
    
        $query = $db->query($sql, [
            $data['PlanId'],
            $data['AgeBandId'],
            $data['Gender'],
            $data['BasePremium'],
            $data['RegionCode'] ?? 'DUBAI',
            $data['EffectiveFrom'] ?? null,
            $data['EffectiveTo'] ?? null,
            $data['IsActive'] ?? 1
        ]);
    
        return $query->getResultArray();
    }

    public function saveMaternityRate($data)
{
    $db = \Config\Database::connect();

    $sql = "
        DECLARE @MaternityRateId BIGINT;

        EXEC dbo.usp_HT_MaternityRate_Save
            @PlanId          = ?,
            @AgeBandId       = ?,
            @Premium         = ?,
            @EffectiveFrom   = ?,
            @EffectiveTo     = ?,
            @IsActive        = ?,
            @MaternityRateId = @MaternityRateId OUTPUT;

        SELECT @MaternityRateId AS MaternityRateId;
    ";

    $query = $db->query($sql, [
        $data['PlanId'],
        $data['AgeBandId'],
        $data['Premium'],
        $data['EffectiveFrom'] ?? null,
        $data['EffectiveTo'] ?? null,
        $data['IsActive'] ?? 1
    ]);

        return $query->getResultArray();
    }

    public function saveBenefitPremiums($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_SavePlanBenefitPremium
                @PlanId          = ?,
                @BenefitPremiums = ?,
                @BenefitType     = ?,
                @EffectiveFrom   = ?,
                @EffectiveTo     = ?,
                @IsActive        = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'],
            $this->jsonParam($data['BenefitPremiums'] ?? null),
            $data['BenefitType'] ?? null,
            $data['EffectiveFrom'] ?? null,
            $data['EffectiveTo'] ?? null,
            $data['IsActive'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to save benefit premiums');
        }

        return $query;
    }

    public function getPremiumRates($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_BasePremiumRate_Get
                @PlanId     = ?,
                @PlanCode   = ?,
                @RegionCode = ?,
                @AsOfDate   = ?,
                @ActiveOnly = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['RegionCode'] ?? 'DUBAI',
            $data['AsOfDate'] ?? null,
            $data['ActiveOnly'] ?? 1,
        ]);

        return $query->getResultArray();
    }

    public function getMaternityRates($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetMaternityRate
                @PlanId     = ?,
                @PlanCode   = ?,
                @AsOfDate   = ?,
                @ActiveOnly = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['AsOfDate'] ?? null,
            $data['ActiveOnly'] ?? 1,
        ]);

        return $query->getResultArray();
    }

    public function getBenefitPremiums($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetPlanBenefitPremium
                @PlanId      = ?,
                @PlanCode    = ?,
                @BenefitType = ?,
                @AsOfDate    = ?,
                @ActiveOnly  = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['BenefitType'] ?? null,
            $data['AsOfDate'] ?? null,
            $data['ActiveOnly'] ?? 1,
        ]);

        return $query->getResultArray();
    }

    public function getPlanReview($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetPlanReview
                @PlanId   = ?,
                @PlanCode = ?,
                @Activate = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['Activate'] ?? 0,
        ]);

        return $query->getResultArray();
    }

    public function getPlans($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetPlans
                @Search     = ?,
                @RegionCode = ?,
                @IsActive   = ?,
                @Page       = ?,
                @PageSize   = ?
        ";

        $query = $db->query($sql, [
            $data['Search'] ?? null,
            $data['RegionCode'] ?? null,
            $data['IsActive'] ?? null,
            $data['Page'] ?? 1,
            $data['PageSize'] ?? 5,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch plans');
        }

        return $this->getPlansResult($query);
    }

    private function getPlansResult($query)
    {
        $summary = null;
        $plans   = [];

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classifyPlanListRow($row, $summary, $plans);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classifyPlanListRow($row, $summary, $plans);
                }
            }
        }

        return [
            'summary' => $summary,
            'plans'   => $plans,
        ];
    }

    private function classifyPlanListRow($row, &$summary, &$plans)
    {
        if (array_key_exists('TotalPlans', $row) && array_key_exists('FilteredCount', $row)) {
            $summary = $row;
            return;
        }

        if (! empty($row['PlanId'])) {
            $plans[] = $row;
        }
    }

    public function getPlanById($data)
    {
        $db = \Config\Database::connect();

        $sql = "
            EXEC dbo.usp_HT_GetPlanById
                @PlanId     = ?,
                @PlanCode   = ?,
                @RegionCode = ?,
                @AsOfDate   = ?,
                @ActiveOnly = ?
        ";

        $query = $db->query($sql, [
            $data['PlanId'] ?? null,
            $data['PlanCode'] ?? null,
            $data['RegionCode'] ?? null,
            $data['AsOfDate'] ?? null,
            $data['ActiveOnly'] ?? 1,
        ]);

        if ($query === false) {
            $error = $db->error();
            throw new \RuntimeException($error['message'] ?? 'Unable to fetch plan');
        }

        return $this->getPlanByIdResult($query);
    }

    private function getPlanByIdResult($query)
    {
        $out = [
            'plan'             => null,
            'baseRates'        => [],
            'maternityRates'   => [],
            'benefitPremiums'  => [],
        ];

        foreach ($query->getResultArray() ?: [] as $row) {
            $this->classifyPlanByIdRow($row, $out);
        }

        $stmt = $query->resultID ?? null;
        if ($stmt) {
            while (sqlsrv_next_result($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $this->classifyPlanByIdRow($row, $out);
                }
            }
        }

        return $out;
    }

    private function classifyPlanByIdRow($row, &$out)
    {
        $type = $row['ResultSet'] ?? '';

        if ($type === 'Plan') {
            $out['plan'] = $row;
            return;
        }
        if ($type === 'BaseRates') {
            $out['baseRates'][] = $row;
            return;
        }
        if ($type === 'MaternityRates') {
            $out['maternityRates'][] = $row;
            return;
        }
        if ($type === 'BenefitPremiums') {
            $out['benefitPremiums'][] = $row;
        }
    }
}