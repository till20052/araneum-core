<?php

namespace Araneum\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AraneumUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }

}
