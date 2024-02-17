<?php

namespace Controllers;

use League\Plates\Engine;
use Models\EditModel;
use Models\UserModel;
use Models\UserValidate;
use Repository\QueryBuilder;
use Tamtamchik\SimpleFlash\Flash;

class UserController
{
    private UserModel $userModel;

    public function __construct(
        UserValidate $userValidate,
        Flash $flash,
        QueryBuilder $queryBuilder,
        Engine $templates,
        EditModel $editModel)
    {
        $this->userModel = new UserModel($userValidate, $flash, $queryBuilder, $templates, $editModel);
    }

    /**
     * @return void
     */
    public function getRegForm(): void
    {
        $this->userModel->printRegForm();
    }

    /**
     * @return void
     */
    public function setRegData(): void
    {
        $this->userModel->registration();
    }

    /**
     * @return void
     */
    public function getLoginForm(): void
    {
        $this->userModel->printLoginForm();
    }

    /**
     * @return void
     */
    public function getLogin(): void
    {
        $this->userModel->login();
    }

    /**
     * @return void
     */
    public function getUsersList(): void
    {
       $this->userModel->getAllUsers();
    }

    /**
     * @param $vars
     * @return void
     */
    public function getUserById($vars): void
    {
        $this->userModel->getUser($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function deleteUser($vars): void
    {
        $this->userModel->delete($vars);
    }

    /**
     * @return void
     */
    public function create(): void
    {
        $this->userModel->createUser();
    }

    /**
     * @return void
     */
    public function createNewUser(): void
    {
        $this->userModel->newUserCreate();
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $this->userModel->logoutUser();
    }
}
