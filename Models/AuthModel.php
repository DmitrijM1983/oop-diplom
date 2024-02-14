<?php

namespace Models;

use Repository\Connection;
use Repository\QueryBuilder;
use League;
use Tamtamchik\SimpleFlash\Flash;

class AuthModel
{
    private League\Plates\Engine $templates;
    private UserValidate $userValidate;
    private QueryBuilder $queryBuilder;
    public string $role;
    public Flash $flash;

    public function __construct()
    {
        $this->templates = new League\Plates\Engine('views');
        $this->userValidate = new UserValidate();
        $this->queryBuilder = new QueryBuilder(Connection::getConnect());
        $this->flash = new Flash();
    }

    /**
     * @return void
     */
    public function printRegForm(): void
    {
        echo $this->templates->render('page_register');
    }

    /**
     * @return void
     */
    public function registration(): void
    {
        $rules = [
            'username' =>
                [
                    'required' => true,
                    'min' => 3,
                    'max' => 25,
                    'unique' => 'users'
                ],
            'email' =>
                [
                    'required' => true,
                    'email' => true,
                    'min' => 8,
                    'max' => 25,
                    'unique' => 'users'
                ],
            'password' =>
                [
                    'required' => true,
                    'min' => 5
                ],
            'password2' =>
                [
                    'required' => true,
                    'matches' => 'password'
                ]
        ];

        $this->userValidate->checkData($_POST, $rules);
        if ($this->userValidate->validateSuccess) {
            $params['username'] = $_POST['username'];
            $params['email'] = $_POST['email'];
            $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $this->queryBuilder->insert($params);
            $this->flash->message("Вы успешно зарегистрированы! Введите email и пароль для входа!", 'success');
            $_SESSION['email'] = $params['email'];
            header('Location: /login');
        } else {
            header('Location: /registration');
        }
    }

    /**
     * @return void
     */
    public function printLoginForm(): void
    {
        echo $this->templates->render('page_login');
    }

    /**
     * @return void
     */
    public function login(): void
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $rules = [
            'email' =>
                [
                    'required' => true,
                    'email' => true
                ],
            'password' =>
                [
                    'required' => true
                ]
        ];

        $this->userValidate->checkData($_POST, $rules);
        if ($this->userValidate->validateSuccess) {
            $user = $this->queryBuilder->getUser($email);
            if ($user) {
                $checkPassword = password_verify($password, $user->password);
                if ($checkPassword) {
                    if(isset($_POST['remember'])) {
                        $cookie = $this->queryBuilder->getCookie(['user_id'=>$user->id], 'user_cookie');
                        if ($cookie) {
                            $hash = $cookie->hash;
                        } else {
                            $hash = hash('sha256', uniqid());
                            $this->queryBuilder->insertCookie(['user_id'=>$user->id, 'hash'=>$hash]);
                        }
                        setcookie('hash', $hash, time() + 604800, '/');
                    }
                    $_SESSION['id'] = $user->id;
                    $_SESSION['username'] = $user->username;
                    $this->getRole($user->id);
                    if ($this->role === 'admin') {
                        $this->flash->message("Привет, {$user->username}, ты администратор!");
                    } else {
                        $this->flash->message("Привет, {$user->username}!");
                    }
                    header('Location: users');
                } else {
                    $this->flash->message('Логин или пароль не верны!', 'error');
                    header('Location: /login');
                }
            } else {
                $this->flash->message('Логин или пароль не верны!', 'error');
                header('Location: /login');
            }
        } else {
            header('Location: /login');
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getRole($id): mixed
    {
       return $this->role = $this->queryBuilder->getRoleUser($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function isAdmin($id): bool
    {
        if ($this->getRole($id) === 'admin') {
            return true;
        }
        return false;
    }
}