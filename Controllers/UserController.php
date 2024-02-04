<?php

namespace Controllers;

use Models\UserModel;
use Models\UserValidate;
use Repository\QueryBuilder;

class UserController
{
    private QueryBuilder $queryBuilder;
    private UserModel $userModel;
    private UserValidate $userValidate;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->userModel = new UserModel();
        $this->userValidate = new UserValidate();
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

    public function getMedia($vars)
    {
        $this->queryBuilder->getUserMedia($vars);
    }

    public function setMedia($vars)
    {
        $name = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $size = $_FILES['image']['size'];
        $this->userValidate->checkImage($vars, $name, $tmp, $size);
    }

    public function deleteUser($vars)
    {
        $this->userModel->delete($vars);
    }
}
