<?php

namespace Controllers;

use Models\SecurityModel;

class SecurityController
{
    private SecurityModel $securityModel;

    public function __construct()
    {
        $this->securityModel = new SecurityModel();
    }

    /**
     * @param $vars
     * @return void
     */
    public function userSecurity($vars): void
    {
        $this->securityModel->securityUpdate($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function updateUserSecurity($vars): void
    {
        $this->securityModel->updateSecurity($vars);
    }
}