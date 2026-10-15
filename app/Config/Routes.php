<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->options('(:any)', static function () {
    return service('response')->setStatusCode(204);
});

$routes->group('auth', ['namespace' => 'App\Controllers\Auth'], static function ($routes) {
    $routes->options('login', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('login', 'AuthController::login');
});

$routes->group('plan', ['namespace' => 'App\Controllers\Plan_management'], static function ($routes) {
    $routes->options('add-plans', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-plan', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-premium-rates', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-maternity-rates', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-benefit-premiums', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('save-plan', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('add-premium-rate', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('add-maternity-rate', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-premium-rates', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-maternity-rates', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-benefit-premiums', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('add-benefit-premium', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-plan-review', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-plans', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-plan-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('delete-plan', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('add-plans', 'PlanController::addPlans');
    $routes->post('update-plan', 'PlanController::updatePlan');
    $routes->post('update-premium-rates', 'PlanController::updatePremiumRates');
    $routes->post('update-maternity-rates', 'PlanController::updateMaternityRates');
    $routes->post('update-benefit-premiums', 'PlanController::updateBenefitPremiums');
    $routes->post('save-plan', 'PlanController::saveHtPlan');
    $routes->post('add-premium-rate', 'PlanController::addPremiumRate');
    $routes->post('add-maternity-rate', 'PlanController::addMaternityRate');
    $routes->post('add-benefit-premium', 'PlanController::addBenefitPremium');
    $routes->get('get-premium-rates', 'PlanController::getPremiumRates');
    $routes->post('get-premium-rates', 'PlanController::getPremiumRates');
    $routes->get('get-maternity-rates', 'PlanController::getMaternityRates');
    $routes->post('get-maternity-rates', 'PlanController::getMaternityRates');
    $routes->get('get-benefit-premiums', 'PlanController::getBenefitPremiums');
    $routes->post('get-benefit-premiums', 'PlanController::getBenefitPremiums');
    $routes->get('get-plan-review', 'PlanController::getPlanReview');
    $routes->post('get-plan-review', 'PlanController::getPlanReview');
    $routes->get('get-plans', 'PlanController::getPlans');
    $routes->post('get-plans', 'PlanController::getPlans');
    $routes->get('get-plan-by-id', 'PlanController::getPlanById');
    $routes->post('get-plan-by-id', 'PlanController::getPlanById');
    $routes->post('delete-plan', 'PlanController::deletePlan');
});

$routes->group('age-band', ['namespace' => 'App\Controllers\Age_band'], static function ($routes) {
    $routes->options('get-age-bands', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('save-age-band', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-age-band', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-age-band-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('delete-age-band', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-age-bands', 'AgeBandController::getAgeBands');
    $routes->post('get-age-band-by-id', 'AgeBandController::getAgeBandById');
    $routes->post('save-age-band', 'AgeBandController::saveAgeBand');
    $routes->post('update-age-band', 'AgeBandController::updateAgeBand');
    $routes->post('delete-age-band', 'AgeBandController::deleteAgeBand');
});

$routes->group('base-rate', ['namespace' => 'App\Controllers\Base_rate'], static function ($routes) {
    $routes->options('get-base-rates', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-base-rate-dropdowns', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-base-rate-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-base-rate', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('save-base-rate', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('delete-base-rate', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-base-rates', 'BaseRateController::getBaseRates');
    $routes->post('get-base-rate-dropdowns', 'BaseRateController::getDropdowns');
    $routes->post('get-base-rate-by-id', 'BaseRateController::getById');
    $routes->post('save-base-rate', 'BaseRateController::saveBaseRate');
    $routes->post('update-base-rate', 'BaseRateController::updateBaseRate');
    $routes->post('delete-base-rate', 'BaseRateController::deleteBaseRate');
});

$routes->group('maternity', ['namespace' => 'App\Controllers\Maternity'], static function ($routes) {
    $routes->options('get-maternity-rates', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-maternity-rate-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('save-maternity-rate', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-maternity-rate', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('delete-maternity-rate', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-maternity-rates', 'MaternityController::getMaternityRates');
    $routes->post('get-maternity-rate-by-id', 'MaternityController::getById');
    $routes->post('save-maternity-rate', 'MaternityController::saveMaternityRate');
    $routes->post('update-maternity-rate', 'MaternityController::updateMaternityRate');
    $routes->post('delete-maternity-rate', 'MaternityController::deleteMaternityRate');
});

$routes->group('network', ['namespace' => 'App\Controllers\Network'], static function ($routes) {
    $routes->options('get-networks', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-network-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('save-network', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-network', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('delete-network', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-networks', 'NetworkController::getNetworks');
    $routes->post('get-network-by-id', 'NetworkController::getById');
    $routes->post('save-network', 'NetworkController::saveNetwork');
    $routes->post('update-network', 'NetworkController::updateNetwork');
    $routes->post('delete-network', 'NetworkController::deleteNetwork');
});

$routes->group('annual-limit', ['namespace' => 'App\Controllers\Annual_limit'], static function ($routes) {
    $routes->options('get-annual-limit-factors', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('get-annual-limit-factor-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('save-annual-limit-factor', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('update-annual-limit-factor', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->options('delete-annual-limit-factor', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-annual-limit-factors', 'AnnualLimitController::getAnnualLimitFactors');
    $routes->post('get-annual-limit-factor-by-id', 'AnnualLimitController::getById');
    $routes->post('save-annual-limit-factor', 'AnnualLimitController::saveAnnualLimitFactor');
    $routes->post('update-annual-limit-factor', 'AnnualLimitController::updateAnnualLimitFactor');
    $routes->post('delete-annual-limit-factor', 'AnnualLimitController::deleteAnnualLimitFactor');
});

$routes->group('adjustment', ['namespace' => 'App\Controllers\Adjustment'], static function ($routes) {
    $routes->options('get-adjustment-factors', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-adjustment-factors', 'AdjustmentController::getAdjustmentFactors');
    $routes->options('get-adjustment-factor-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-adjustment-factor-by-id', 'AdjustmentController::getById');
    $routes->options('save-adjustment-factor', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-adjustment-factor', 'AdjustmentController::saveAdjustmentFactor');
    $routes->options('update-adjustment-factor', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-adjustment-factor', 'AdjustmentController::updateAdjustmentFactor');
    $routes->options('delete-adjustment-factor', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-adjustment-factor', 'AdjustmentController::deleteAdjustmentFactor');
});

$routes->group('benefit', ['namespace' => 'App\Controllers\Benefit'], static function ($routes) {
    $routes->options('get-benefit-premiums', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-benefit-premiums', 'BenefitController::getBenefitPremiums');
    $routes->options('get-benefit-premium-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-benefit-premium-by-id', 'BenefitController::getById');
    $routes->options('save-benefit-premium', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-benefit-premium', 'BenefitController::saveBenefitPremium');
    $routes->options('update-benefit-premium', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-benefit-premium', 'BenefitController::updateBenefitPremium');
    $routes->options('delete-benefit-premium', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-benefit-premium', 'BenefitController::deleteBenefitPremium');
});

$routes->group('calculator', ['namespace' => 'App\Controllers\Calculator'], static function ($routes) {
    $routes->options('calculate-premium', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('calculate-premium', 'CalculatorController::calculatePremium');
    $routes->options('get-genders', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-genders', 'CalculatorController::getGenders');
    $routes->options('get-regions', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-regions', 'CalculatorController::getRegions');
    $routes->options('get-plans', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-plans', 'CalculatorController::getPlans');
    $routes->options('get-networks', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-networks', 'CalculatorController::getNetworks');
    $routes->options('get-annual-limits', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-annual-limits', 'CalculatorController::getAnnualLimits');
    $routes->options('get-diagnostic-options', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-diagnostic-options', 'CalculatorController::getDiagnosticOptions');
    $routes->options('get-ip-copay-options', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-ip-copay-options', 'CalculatorController::getIpCopayOptions');
    $routes->options('get-consultation-options', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-consultation-options', 'CalculatorController::getConsultationOptions');
    $routes->options('get-pharmacy-limit-options', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-pharmacy-limit-options', 'CalculatorController::getPharmacyLimitOptions');
    $routes->options('get-pharmacy-copay-options', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-pharmacy-copay-options', 'CalculatorController::getPharmacyCopayOptions');
    $routes->options('get-tpa-options', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-tpa-options', 'CalculatorController::getTpaOptions');
    $routes->options('get-dental-options', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-dental-options', 'CalculatorController::getDentalOptions');
    $routes->options('get-optical-options', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-optical-options', 'CalculatorController::getOpticalOptions');
});

$routes->group('history', ['namespace' => 'App\Controllers\History'], static function ($routes) {
    $routes->options('get-calculation-history', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-calculation-history', 'HistoryController::getCalculationHistory');
});

$routes->group('dashboard', ['namespace' => 'App\Controllers\Dashboard'], static function ($routes) {
    $routes->options('get-dashboard-summary', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-dashboard-summary', 'DashboardController::getDashboardSummary');
});

$routes->group('individual/coverage', ['namespace' => 'App\Controllers\Individual\Coverage'], static function ($routes) {
    $routes->options('get-coverage-areas', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-coverage-areas', 'CoverageController::getCoverageAreas');

    $routes->options('get-coverage-area-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-coverage-area-by-id', 'CoverageController::getCoverageAreaById');

    $routes->options('save-coverage-area', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-coverage-area', 'CoverageController::saveCoverageArea');

    $routes->options('update-coverage-area', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-coverage-area', 'CoverageController::updateCoverageArea');

    $routes->options('delete-coverage-area', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-coverage-area', 'CoverageController::deleteCoverageArea');
});

$routes->group('individual/network', ['namespace' => 'App\Controllers\Individual\Network'], static function ($routes) {
    $routes->options('get-network-masters', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-network-masters', 'NetworkMasterController::getNetworkMasters');

    $routes->options('get-network-master-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-network-master-by-id', 'NetworkMasterController::getNetworkMasterById');

    $routes->options('save-network-master', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-network-master', 'NetworkMasterController::saveNetworkMaster');

    $routes->options('update-network-master', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-network-master', 'NetworkMasterController::updateNetworkMaster');

    $routes->options('delete-network-master', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-network-master', 'NetworkMasterController::deleteNetworkMaster');
});

$routes->group('individual/limit', ['namespace' => 'App\Controllers\Individual\Limit'], static function ($routes) {
    $routes->options('get-aggregate-limits', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-aggregate-limits', 'AggregateLimitController::getAggregateLimits');

    $routes->options('get-aggregate-limit-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-aggregate-limit-by-id', 'AggregateLimitController::getAggregateLimitById');

    $routes->options('save-aggregate-limit', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-aggregate-limit', 'AggregateLimitController::saveAggregateLimit');

    $routes->options('update-aggregate-limit', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-aggregate-limit', 'AggregateLimitController::updateAggregateLimit');

    $routes->options('delete-aggregate-limit', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-aggregate-limit', 'AggregateLimitController::deleteAggregateLimit');
});

$routes->group('individual/plan-network', ['namespace' => 'App\Controllers\Individual\PlanNetwork'], static function ($routes) {
    $routes->options('get-plan-network-mappings', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-plan-network-mappings', 'PlanNetworkMappingController::getPlanNetworkMappings');

    $routes->options('get-plan-network-mapping-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-plan-network-mapping-by-id', 'PlanNetworkMappingController::getPlanNetworkMappingById');

    $routes->options('save-plan-network-mapping', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-plan-network-mapping', 'PlanNetworkMappingController::savePlanNetworkMapping');

    $routes->options('update-plan-network-mapping', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-plan-network-mapping', 'PlanNetworkMappingController::updatePlanNetworkMapping');

    $routes->options('delete-plan-network-mapping', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-plan-network-mapping', 'PlanNetworkMappingController::deletePlanNetworkMapping');
});

$routes->group('individual/dropdown', ['namespace' => 'App\Controllers\Individual\Dropdown'], static function ($routes) {
    $routes->options('get-plan-dropdowns', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-plan-dropdowns', 'PlanDropdownController::getPlanDropdowns');
});

$routes->group('individual/config', ['namespace' => 'App\Controllers\Individual\Config'], static function ($routes) {
    $routes->options('get-product-configs', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-product-configs', 'ProductConfigController::getProductConfigs');

    $routes->options('get-system-configs', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-system-configs', 'SystemConfigController::getSystemConfigs');

    $routes->options('get-product-config-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-product-config-by-id', 'ProductConfigController::getProductConfigById');

    $routes->options('save-product-config', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-product-config', 'ProductConfigController::saveProductConfig');

    $routes->options('update-product-config', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-product-config', 'ProductConfigController::updateProductConfig');

    $routes->options('delete-product-config', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-product-config', 'ProductConfigController::deleteProductConfig');

    $routes->options('get-system-config-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-system-config-by-id', 'SystemConfigController::getSystemConfigById');

    $routes->options('save-system-config', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-system-config', 'SystemConfigController::saveSystemConfig');

    $routes->options('update-system-config', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-system-config', 'SystemConfigController::updateSystemConfig');

    $routes->options('delete-system-config', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-system-config', 'SystemConfigController::deleteSystemConfig');
});

$routes->group('individual/question', ['namespace' => 'App\Controllers\Individual\Questionnaire'], static function ($routes) {
    $routes->options('get-questionnaires', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-questionnaires', 'QuestionnaireController::getQuestionnaires');

    $routes->options('get-section-dropdowns', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-section-dropdowns', 'QuestionnaireController::getSectionDropdowns');

    $routes->options('get-questionnaire-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-questionnaire-by-id', 'QuestionnaireController::getQuestionnaireById');

    $routes->options('save-questionnaire', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-questionnaire', 'QuestionnaireController::saveQuestionnaire');

    $routes->options('update-questionnaire', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-questionnaire', 'QuestionnaireController::updateQuestionnaire');

    $routes->options('delete-questionnaire', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-questionnaire', 'QuestionnaireController::deleteQuestionnaire');
});

$routes->group('individual/referral', ['namespace' => 'App\Controllers\Individual\Referral'], static function ($routes) {
    $routes->options('get-referral-rules', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-referral-rules', 'ReferralController::getReferralRules');

    $routes->options('get-referral-rule-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-referral-rule-by-id', 'ReferralController::getReferralRuleById');

    $routes->options('save-referral-rule', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-referral-rule', 'ReferralController::saveReferralRule');

    $routes->options('update-referral-rule', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-referral-rule', 'ReferralController::updateReferralRule');

    $routes->options('delete-referral-rule', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-referral-rule', 'ReferralController::deleteReferralRule');
});

$routes->group('individual/contribution', ['namespace' => 'App\Controllers\Individual\Contribution'], static function ($routes) {
    $routes->options('get-plan-contributions', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-plan-contributions', 'PlanContributionController::getPlanContributions');

    $routes->options('get-plan-contribution-by-id', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('get-plan-contribution-by-id', 'PlanContributionController::getPlanContributionById');

    $routes->options('save-plan-contribution', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('save-plan-contribution', 'PlanContributionController::savePlanContribution');

    $routes->options('update-plan-contribution', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('update-plan-contribution', 'PlanContributionController::updatePlanContribution');

    $routes->options('delete-plan-contribution', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->post('delete-plan-contribution', 'PlanContributionController::deletePlanContribution');
});
