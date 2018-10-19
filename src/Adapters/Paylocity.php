<?php

namespace Zenapply\HRIS\Adapters;

use Zenapply\Common\Interfaces\Employable;
use Zenapply\Common\Interfaces\HRIS;
use Zenapply\Common\Interfaces\Onboard;
use Zenapply\HRIS\Exceptions\HRISException;

class Paylocity extends Integration implements HRIS
{
    public function hire(Employable $applicant, Onboard $onboard)
    {
        $payload = [
            'firstName' => $applicant->getFirstName(),
            'status' => [
                'hireDate'  => $onboard->getHiredDate(),
                'employeeStatus' => 'A', // A (Active), L (Leave of Absence), T (Terminated). 
            ],
            'departmentPosition' => [
                'employeeType' => $onboard->getEmployeeType(), // RFT (Regular Full Time), RPT (Regular Part Time), SNL (Seasonal), TFT (Temporary Full Time), TPT (Temporary Part Time). 
                'positionCode' => $onboard->getPositionId(),
                'costCenter1'  => $onboard->getLocationId(),
            ],
            'homeAddress' => [
                'emailAddress'  => $applicant->getEmail(),
                'phone'         => $applicant->getPhone(),
                'lastName'      => $applicant->getLastName(),
                'address1'      => $applicant->getAddressLine1(),
                'address2'      => $applicant->getAddressLine2(),
                'city'          => $applicant->getCity(),
                'state'         => $applicant->getState(),
                'postalCode'    => $applicant->getZip(),
            ],
        ];

        $client = $this->getClient();

        $response = $client->addEmployee($payload);

        return $response;
    }

    protected function getClient()
    {
        $client = new PaylocityClient($this->creds->getAccountIdentifier());
        $client->setSecretKey($this->creds->getClientSecret());
        return $client;
    }
}