<?php

namespace Araneum\Bundle\MainBundle\Service;

class ApplicationCheckerService
{
    public function checkApplication($id)
    {
        return true;
    }

    public function checkConnection($id)
    {
        return true;
    }

    public function checkCluster($id)
    {
        return true;
    }
}