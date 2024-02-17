<?php

namespace Controllers;

use League\Plates\Engine;
use Models\EditModel;
use Models\UserModel;
use Models\UserValidate;
use Repository\GeneralRepository;
use Repository\UserRepository;
use Tamtamchik\SimpleFlash\Flash;

class UserController
{
    private UserModel $userModel;
    private Engine $templates;
    private UserValidate $userValidate;

    public function __construct(
        UserValidate $userValidate,
        Flash $flash,
        GeneralRepository $generalRepository,
        Engine $templates,
        EditModel $editModel,
        UserRepository $userRepository)
    {
        $this->userModel = new UserModel($userValidate, $flash, $generalRepository, $editModel, $this, $userRepository);
        $this->templates = $templates;
        $this->userValidate = $userValidate;
    }

    /**
     * @return void
     */
    public function getRegForm(): void
    {
        echo $this->templates->render('page_register');
    }

    /**
     * @return void
     */
    public function setRegData(): void
    {
        $rules = [
            'username' => [
                'required' => true,
                'min' => 3,
                'max' => 25,
                'unique' => 'users'
            ],
            'email' => [
                'required' => true,
                'email' => true,
                'min' => 8,
                'max' => 25,
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

        $this->userValidate->checkData($_POST, $rules);
        if ($this->userValidate->validateSuccess) {
            $this->userModel->registration();
        } else {
            $this->getRegForm();
        }
    }

    /**
     * @return void
     */
    public function getLoginForm(): void
    {
        echo $this->templates->render('page_login');
    }

    /**
     * @return void
     */
    public function getLogin(): void
    {
        $rules = [
            'email' => [
                'required' => true,
                'email' => true
            ],
            'password' => [
                'required' => true
            ]
        ];

        $this->userValidate->checkData($_POST, $rules);
        if ($this->userValidate->validateSuccess) {
            $this->userModel->login();
        }   else {
        header('Location: /login');
        }
    }

    /**
     * @return void
     */
    public function getUsersList(): void
    {
       $users = $this->userModel->getAllUsers();
       echo $this->templates->render('users', $users);
    }

    /**
     * @param $vars
     * @return void
     */
    public function getUserById($vars): void
    {
        $user = $this->userModel->getUser($vars);
        echo $this->templates->render('page_profile', $user);
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
        echo $this->templates->render('create_user');
    }

    /**
     * @return void
     */
    public function createNewUser(): void
    {
        $rules = [
            'username' => [
                'required' => true,
                'min' => 3,
                'max' => 25,
                'unique' => 'users'
            ],
            'email' => [
                'required' => true,
                'email' => true,
                'min' => 8,
                'max' => 25,
                'unique' => 'users'
            ],
            'password' => [
                'required' => true,
                'min' => 5
            ]
        ];

        $this->userValidate->checkData($_POST, $rules);
        if ($this->userValidate->validateSuccess) {
            $this->userModel->newUserCreate();
        }  else {
            $this->create();
        }
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        echo $this->templates->render('start_page');
    }
}
