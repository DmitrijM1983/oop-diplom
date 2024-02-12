<?php

namespace Controllers;

use Models\AuthModel;

class AuthController
{
    private AuthModel $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    /**
     * @return void
     */
    public function getRegForm(): void
    {
        $this->authModel->printRegForm();
    }

    /**
     * @return void
     */
    public function setRegData(): void
    {
        $this->authModel->registration();
    }

    /**
     * @return void
     */
    public function getLoginForm(): void
    {
        $this->authModel->printLoginForm();
    }

    /**
     * @return void
     */
    public function getLogin(): void
    {
        $this->authModel->login();
    }
}