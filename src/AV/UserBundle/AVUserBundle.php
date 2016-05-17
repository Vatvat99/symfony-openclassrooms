<?php

namespace AV\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AVUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
