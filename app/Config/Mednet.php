<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Mednet extends BaseConfig
{
    public string $username;
    public string $password;
    public string $insuranceCoNumber;

    public string $policyWriteUrl;
    public string $jobStatusUrl;
    public string $ecardUrl;
    public string $salesEntityNumber;

    public function __construct()
    {
        parent::__construct();

        $this->username = (string) env('mednet.username', '');
        $this->password = (string) env('mednet.password', '');
        $this->insuranceCoNumber = (string) env('mednet.insuranceCoNumber', '69');
        $this->salesEntityNumber = (string) env('mednet.salesEntityNumber', '');

        $this->policyWriteUrl = (string) env('mednet.policyWriteUrl', '');
        $this->jobStatusUrl = (string) env('mednet.jobStatusUrl', '');
        $this->ecardUrl = (string) env('mednet.ecardUrl', '');
    }
}
