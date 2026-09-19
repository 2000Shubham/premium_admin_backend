<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->options('(:any)', static function () {
    return service('response')->setStatusCode(204);
});

//rotuing to call login/authentication api
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
    $routes->post('get-age-bands', 'AgeBandController::getAgeBands');
    $routes->post('get-age-band-by-id', 'AgeBandController::getAgeBandById');
    $routes->post('save-age-band', 'AgeBandController::saveAgeBand');
    $routes->post('update-age-band', 'AgeBandController::updateAgeBand');
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
    $routes->post('get-base-rates', 'BaseRateController::getBaseRates');
    $routes->post('get-base-rate-dropdowns', 'BaseRateController::getDropdowns');
    $routes->post('get-base-rate-by-id', 'BaseRateController::getById');
    $routes->post('save-base-rate', 'BaseRateController::saveBaseRate');
    $routes->post('update-base-rate', 'BaseRateController::updateBaseRate');
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
    $routes->post('get-maternity-rates', 'MaternityController::getMaternityRates');
    $routes->post('get-maternity-rate-by-id', 'MaternityController::getById');
    $routes->post('save-maternity-rate', 'MaternityController::saveMaternityRate');
    $routes->post('update-maternity-rate', 'MaternityController::updateMaternityRate');
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
    $routes->post('get-networks', 'NetworkController::getNetworks');
    $routes->post('get-network-by-id', 'NetworkController::getById');
    $routes->post('save-network', 'NetworkController::saveNetwork');
    $routes->post('update-network', 'NetworkController::updateNetwork');
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
    $routes->post('get-annual-limit-factors', 'AnnualLimitController::getAnnualLimitFactors');
    $routes->post('get-annual-limit-factor-by-id', 'AnnualLimitController::getById');
    $routes->post('save-annual-limit-factor', 'AnnualLimitController::saveAnnualLimitFactor');
    $routes->post('update-annual-limit-factor', 'AnnualLimitController::updateAnnualLimitFactor');
});
