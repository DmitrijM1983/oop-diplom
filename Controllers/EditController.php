<?php

namespace Controllers;

use League\Plates\Engine;
use Models\EditModel;
use Models\UserModel;
use Models\UserValidate;
use Repository\QueryBuilder;
use Tamtamchik\SimpleFlash\Flash;

class EditController
{
    private EditModel $editModel;

    public function __construct(
        QueryBuilder $queryBuilder,
        UserValidate $userValidate,
        Engine $templates,
        Flash $flash)
    {
        $this->editModel = new EditModel($userValidate, $templates, $flash, $queryBuilder);
    }

    /**
     * @param $vars
     * @return void
     */
    public function editUser($vars): void
    {
        $this->editModel->userEdit($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function updateUser($vars): void
    {
        $this->editModel->userUpdate($vars, $_POST);
    }

    /**
     * @param $vars
     * @return void
     */
    public function getImage($vars): void
    {
        $this->editModel->getUserImage($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function setImage($vars): void
    {
        $this->editModel->checkFile($_FILES, $vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function userSecurity($vars): void
    {
        $this->editModel->securityUpdate($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function updateUserSecurity($vars): void
    {
        $this->editModel->updateSecurity($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function getStatus($vars): void
    {
        $this->editModel->getCurrentStatus($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function setStatus($vars): void
    {
        $this->editModel->setStatusParam($_POST, $vars);
    }
}