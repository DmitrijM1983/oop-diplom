<?php

namespace Controllers;

use League\Plates\Engine;
use Models\EditModel;
use Models\UserValidate;
use Repository\UserRepository;
use Tamtamchik\SimpleFlash\Flash;

class EditController
{
    private EditModel $editModel;
    private Engine $templates;
    private UserValidate $userValidate;

    public function __construct(
        UserValidate $userValidate,
        Engine $templates,
        Flash $flash,
        UserRepository $userRepository)
    {
        $this->editModel = new EditModel($userValidate, $flash, $userRepository);
        $this->templates = $templates;
        $this->userValidate = $userValidate;
    }

    /**
     * @param $vars
     * @return void
     */
    public function editUser($vars): void
    {
        $user = $this->editModel->userEdit($vars);
        echo $this->templates->render('edit', $user);
    }

    /**
     * @param $vars
     * @return void
     */
    public function updateUser($vars): void
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
        }  else {
            header("Location: /edit/{$vars['id']}");
        }
    }

    /**
     * @param $vars
     * @return void
     */
    public function getImage($vars): void
    {
        $image = $this->editModel->getUserImage($vars);
        echo $this->templates->render('media', $image);
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
        $user = $this->editModel->securityUpdate($vars);
        echo $this->templates->render('security', $user);
    }

    /**
     * @param $vars
     * @return void
     */
    public function updateUserSecurity($vars): void
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
        } else {
            header("Location: /security/{$vars['id']}");
        }
    }

    /**
     * @param $vars
     * @return void
     */
    public function getStatus($vars): void
    {
        $status = $this->editModel->getCurrentStatus($vars);
        echo $this->templates->render('status', $status);
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