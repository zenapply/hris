<?php

namespace Zenapply\HRIS\Adapters;

use Zenapply\Core\Interfaces\Credentials;

abstract class Integration
{
    protected $creds;

    abstract protected function getClient();

    public function __construct(Credentials $creds)
    {
        $this->creds = $creds;
    }    
}