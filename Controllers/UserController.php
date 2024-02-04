<?php

namespace Controllers;

use Models\UserModel;
use Models\UserValidate;
use Repository\QueryBuilder;

class UserController
{
    private QueryBuilder $queryBuilder;
    private UserModel $userModel;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->userModel = new UserModel();
    }

    public function getUsersList()
    {
       $this->queryBuilder->getAllUsers();
    }

    public function getUserById($vars)
    {
        $user = $this->queryBuilder->getOneUser($vars);
        if ($user) {
            $this->userModel->printUser($user);
        }
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
        $params = $_POST;
        $this->queryBuilder->updateSecurityUser($vars, $params);
    }

    public function getStatus($vars)
    {
        $this->queryBuilder->getUserStatus($vars);
    }

    public function setStatus($vars)
    {
        $status = $_POST['status'];
        $this->userModel->setStatusParam($vars, $status);
    }
}