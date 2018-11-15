<?php

namespace Zenapply\HRIS\Adapters;

use Zenapply\Common\Interfaces\Employable;
use Zenapply\Common\Interfaces\HRIS;
use Zenapply\Common\Interfaces\Onboard;
use Zenapply\Common\Interfaces\CredentialsOAuth2;
use Zenapply\HRIS\Paylocity\Paylocity as PaylocityClient;

class Paylocity extends Integration implements HRIS
{
    protected $version;
    protected $host;
    protected $public_key;

    public function __construct(CredentialsOAuth2 $creds, $public_key, $host = PaylocityClient::PRODUCTION_URL, $version = PaylocityClient::VERSION_1)
    {
        $this->creds = $creds;
        $this->public_key = $public_key;
        $this->host = $host;
        $this->version = $version;
    }    

    public function hire(Employable $applicant, Onboard $onboard)
    {
        $payload = [
            'address1' => $applicant->getAddressLine1(),
            'address2' => $applicant->getAddressLine2(),
            'autoPayType' => null,
            'baseRate' => null,
            'city' => $applicant->getCity(),
            'costCenter1' => $onboard->getHrisCostCenter1(),
            'costCenter2' => $onboard->getHrisCostCenter2(),
            'costCenter3' => $onboard->getHrisCostCenter3(),
            'defaultHours' => null,
            'employeeStatus' => null,
            'employmentType' => $onboard->getEmployeeType(), // RFT (Regular Full Time), RPT (Regular Part Time), SNL (Seasonal), TFT (Temporary Full Time), TPT (Temporary Part Time). 
            'federalFilingStatus' => null,
            'firstName' => $applicant->getFirstName(),
            'hireDate' => $onboard->getHiredDate(),
            'homePhone' => null,
            'lastName' => $applicant->getLastName(),
            'maritalStatus' => null,
            'payFrequency' => null,
            'payType' => $onboard->getPayType(), // Hourly or Salary
            'personalEmailAddress' => $applicant->getEmail(),
            'personalMobilePhone' => $applicant->getPhone(),
            'ratePer' => null,
            'salary' => null,
            'sex' => null,
            'ssn' => null,
            'state' => $applicant->getState(),
            'stateFilingStatus' => null,
            'suiState' => null,
            'taxForm' => null,
            'taxState' => null,
            'userName' => $applicant->getUsername(),
            'workEmailAddress' => null,
            'zip' => $applicant->getZip(),
        ];

        $client = $this->getClient();
        $response = $client->companies($this->creds->getAccountIdentifier())->onboarding->employees->post(array_filter($payload));
        
        return $response;
    }

    public function getClient()
    {
        $client = new PaylocityClient($this->creds->getClientId(), $this->creds->getClientSecret(), $this->public_key, $this->host, $this->version);
        return $client;
    }
}