<?php

namespace Controllers;

use JetBrains\PhpStorm\NoReturn;
use League\Plates\Engine;
use Models\UserModel;
use Models\UserValidate;
use Repository\UserRepository;
use Tamtamchik\SimpleFlash\Flash;

class UserController extends BaseController
{
    public function __construct(
        private readonly UserValidate $userValidate,
        private readonly Engine $engine,
        private readonly UserModel $userModel,
        private readonly UserRepository $userRepository,
        private readonly Flash $flash
    ) {
    }

    /**
     * @return void
     */
    public function getRegForm(): void
    {
        echo $this->engine->render('page_register');
    }

    /**
     * @return void
     */
    #[NoReturn] public function setRegData(): void
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
            $this->redirect('/login');
        }
        $this->redirect('/registration');
    }

    /**
     * @return void
     */
    public function getLoginForm(): void
    {
        echo $this->engine->render('page_login');
    }

    /**
     * @return void
     */
    #[NoReturn] public function getLogin(): void
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
        $email = $_POST['email'];
        $password = $_POST['password'];
        if ($this->userValidate->validateSuccess) {
            $user = $this->userRepository->getUser($email);
            if ($user) {
                $checkPassword = password_verify($password, $user->password);
                if ($checkPassword) {
                    $id = $user->id;
                    $userName = $user->username;
                    $this->userModel->login($id, $userName);
                    $this->redirect('users');
                } else {
                    $this->flash->message('Логин или пароль не верны!', 'error');
                    $this->getLoginForm();
                }
            } else {
                $this->flash->message('Логин или пароль не верны!', 'error');
                $this->getLoginForm();
            }
        }
        $this->redirect('/login');;
    }

    /**
     * @return void
     */
    public function getUsersList(): void
    {
       $users = $this->userModel->getAllUsers();
       echo $this->engine->render('users', $users);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function getUserById(array $vars): void
    {
        $user = $this->userModel->getUser($vars);
        echo $this->engine->render('page_profile', $user);
    }

    /**
     * @param array $vars
     * @return void
     */
    #[NoReturn] public function deleteUser(array $vars): void
    {
        $this->userModel->delete($vars);

        if ($_SESSION['id'] == $vars['id']) {
            $_SESSION = [];
            $this->redirect('/views/start_page.php');
        } else {
            $this->redirect('/users');
        }
    }

    /**
     * @return void
     */
    public function create(): void
    {
        echo $this->engine->render('create_user');
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
            if (!empty($_FILES) && $_FILES['image']['size'] > 0) {
                $newUser = $this->userModel->newUserCreateWithImage();
                if ($newUser) {
                    $this->redirect('users');
                } else {
                    $this->create();
                }
            } else {
                $this->userModel->newUserCreate();
                $this->redirect('users');
            }
        }  else {
            $this->create();
        }
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        echo $this->engine->render('start_page');
    }
}
