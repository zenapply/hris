<?php

namespace Zenapply\HRIS\Adapters;

use Zenapply\Common\Interfaces\Employable;
use Zenapply\Common\Interfaces\HRIS;
use Zenapply\Common\Interfaces\Onboard;
use Zenapply\Common\Interfaces\CredentialsBasic;
use Zenapply\HRIS\PeopleMatter\Models\BusinessUnit as PeopleMatterBusinessUnit;
use Zenapply\HRIS\PeopleMatter\Models\Job as PeopleMatterJob;
use Zenapply\HRIS\PeopleMatter\Models\Person as PeopleMatterPerson;
use Zenapply\HRIS\PeopleMatter\PeopleMatter as PeopleMatterClient;
use Carbon\Carbon;

class PeopleMatter extends Integration implements HRIS
{
    public function __construct(CredentialsBasic $creds)
    {
        $this->creds = $creds;
    }    
    
    public function hire(Employable $applicant, Onboard $onboard = null)
    {
        $employee = $this->getClient()->getEmployee($applicant->getEmployeeId());
        $person = new PeopleMatterPerson([
            "FirstName" => $applicant->getFirstName(),
            "LastName" => $applicant->getLastName(),
            "EmailAddress" => $applicant->getEmail(),
            "Id" => is_object($employee) ? $employee->Id : null,
        ]);

        $result = $this->getClient()->hire(
            $person, 
            new PeopleMatterJob(["Id" => $onboard->getHrisPositionId()]),
            new PeopleMatterBusinessUnit(["Id" => $onboard->getHrisLocationId()]), 
            $onboard->getEmployeeType(), 
            $onboard->getHiredDate()
        );
        
        return $result;
    }

    public function getUnits()
    {
        return $this->respond($this->getClient()->getBusinessUnits());
    }

    public function getJobs()
    {
        return $this->respond($this->getClient()->getJobs());
    }

    public function getClient()
    {
        return new PeopleMatterClient($this->creds->getUserName(), $this->creds->getPassword(), $this->creds->getAccountIdentifier(), "api.peoplematter.com");
    }    
}