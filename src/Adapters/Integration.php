<?php

namespace Zenapply\HRIS\Adapters;

abstract class Integration
{
    protected $creds;

    abstract protected function getClient();
}