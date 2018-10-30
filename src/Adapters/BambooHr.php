<?php

namespace Zenapply\HRIS\Adapters;

use Zenapply\Common\Interfaces\Employable;
use Zenapply\Common\Interfaces\HRIS;
use Zenapply\Common\Interfaces\CredentialsApiToken;
use Zenapply\Common\Interfaces\Onboard;
use Zenapply\HRIS\Exceptions\HRISException;
use BambooHR\API\BambooAPI as BambooHrClient;

class BambooHr extends Integration implements HRIS
{
    public function __construct(CredentialsApiToken $creds)
    {
        $this->creds = $creds;
    }    

    public function hire(Employable $applicant, Onboard $onboard)
    {
        $payload = array_merge($onboard->getPayload(), [
            'hireDate'  => $onboard->getHiredDate(),
            'homeEmail' => $applicant->getEmail(),
            'homePhone' => $applicant->getPhone(),
            'firstName' => $applicant->getFirstName(),
            'lastName'  => $applicant->getLastName(),
            'address1'  => $applicant->getAddressLine1(),
            'address2'  => $applicant->getAddressLine2(),
            'city'      => $applicant->getCity(),
            'state'     => $applicant->getState(),
            'zip'       => $applicant->getZip(),
        ]);

        $client = $this->getClient();

        $response = $client->addEmployee($fields);

        if ($response->isError()) {
            $error = [];
            $error[] = $response->statusCode;
            $error[] = "BambooHR Error";
            $error[] = $response->getErrorMessage();
            throw new HRISException(implode(" - ", $error), 1);
        }

        return $response;
    }

    public function getClient()
    {
        $client = new BambooHrClient($this->creds->getAccountIdentifier());
        $client->setSecretKey($this->creds->getApiToken());
        return $client;
    }
}