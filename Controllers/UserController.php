<?php

namespace Controllers;

use Models\UserModel;

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
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
}
