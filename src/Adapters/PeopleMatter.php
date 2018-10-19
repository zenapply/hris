<?php

namespace Zenapply\HRIS\Adapters;

use Zenapply\Common\Interfaces\Employable;
use Zenapply\Common\Interfaces\HRIS;
use Zenapply\Common\Interfaces\Onboard;
use Zenapply\PeopleMatter\Models\BusinessUnit as PeopleMatterBusinessUnit;
use Zenapply\PeopleMatter\Models\Job as PeopleMatterJob;
use Zenapply\PeopleMatter\Models\Person as PeopleMatterPerson;
use Zenapply\PeopleMatter\PeopleMatter as PeopleMatterClient;
use Carbon\Carbon;

class PeopleMatter extends Integration implements HRIS
{
    public function hire(Employable $applicant, Onboard $onboard = null)
    {
        $employee = $this->getClient()->getEmployee($applicant->getEmail());
        $person = new PeopleMatterPerson([
            "FirstName" => $applicant->getFirstName(),
            "LastName" => $applicant->getLastName(),
            "EmailAddress" => $applicant->getEmail(),
            "Id" => is_object($employee) ? $employee->Id : null,
        ]);

        $result = $this->getClient()->hire(
            $person, 
            new PeopleMatterJob($onboard->getPositionId()),
            new PeopleMatterBusinessUnit($onboard->getLocationId()), 
            $onboard->getEmployeeType(), 
            new Carbon($onboard->getHiredDate())
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

    protected function getClient()
    {
        return new PeopleMatterClient($this->creds->getClientId(), $this->creds->getClientSecret(), $this->creds->getAccountIdentifier(), "api.peoplematter.com");
    }    
}