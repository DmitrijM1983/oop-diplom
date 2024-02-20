<?php

namespace Controllers;

use League\Plates\Engine;
use Models\EditModel;
use Models\UserValidate;

class EditController extends BaseController
{
    public function __construct(
       private readonly UserValidate $userValidate,
       private readonly Engine $templates,
       private readonly EditModel $editModel
    ) {
    }

    /**
     * @param array $vars
     * @return void
     */
    public function editUser(array $vars): void
    {
        $user = $this->editModel->userEdit($vars);
        echo $this->templates->render('edit', $user);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function updateUser(array $vars): void
    {
        $rules = [
            'username' => [
                'required' => true,
                'min' => 3,
                'max' => 25,
                'unique' => 'users'
            ]
        ];

        $this->userValidate->checkData($_POST, $rules, $vars);
        if ($this->userValidate->validateSuccess) {
            $this->editModel->userUpdate($vars);
            $this->redirect('/users');
        }  else {
            $this->editUser($vars);
        }
    }

    /**
     * @param array $vars
     * @return void
     */
    public function getImage(array $vars): void
    {
        $image = $this->editModel->getUserImage($vars);
        echo $this->templates->render('media', $image);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function setImage(array $vars): void
    {
        $check = $this->editModel->checkFile($_FILES, $vars);
        if ($check) {
            $this->redirect('/users');
        }
        $this->getImage($vars);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function userSecurity(array $vars): void
    {
        $user = $this->editModel->securityUpdate($vars);
        echo $this->templates->render('security', $user);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function updateUserSecurity(array $vars): void
    {

        $rules = [
            'email' => [
                'required' => true,
                'email' => true,
                'min' => 8,
                'max' => 40,
                'unique' => 'users'
            ],
            'password' => [
                'required' => true,
                'min' => 5
            ],
            'password2' => [
                'required' => true,
                'matches' => 'password'
            ]
        ];

        $this->userValidate->checkData($_POST, $rules, $vars);
        if ($this->userValidate->validateSuccess) {
            $this->editModel->updateSecurity($vars);
            $this->redirect('/users');
        } else {
            $this->userSecurity($vars);
        }
    }

    /**
     * @param array $vars
     * @return void
     */
    public function getStatus(array $vars): void
    {
        $status = $this->editModel->getCurrentStatus($vars);
        echo $this->templates->render('status', $status);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function setStatus(array $vars): void
    {
        $this->editModel->setStatusParam($_POST, $vars);
        $this->redirect('/users');
    }
}