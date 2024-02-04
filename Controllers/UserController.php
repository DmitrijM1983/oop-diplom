<?php

namespace Controllers;

use Models\UserValidate;
use Repository\QueryBuilder;

class UserController
{
    private QueryBuilder $queryBuilder;
    private UserValidate $userValidate;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->userValidate = new UserValidate();
    }

    public function getUsersList()
    {
       $this->queryBuilder->getAllUsers();
    }

    public function getUserById($vars)
    {
        $this->queryBuilder->getOneUser($vars);
    }

    public function editUser($vars)
    {
        $this->queryBuilder->edit($vars);
    }

    public function insertData()
    {
        $this->queryBuilder->insert();
    }

    public function updateUser($vars)
    {
        $params = $_POST;
        $this->queryBuilder->update($vars, $params);
    }

    public function userSecurity($vars)
    {
        $this->queryBuilder->updateSecurity($vars);
    }

    public function updateUserSecurity($vars)
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $emailValidate = $this->userValidate->checkEmail($email);
        $passwordValidate = $this->userValidate->checkPassword($password, $password2);
        if ($emailValidate && $passwordValidate) {
            $this->queryBuilder->updateSecurity($vars, $password);
        }
    }
}