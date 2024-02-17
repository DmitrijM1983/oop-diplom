<?php

namespace Models;
use League;
use League\Plates\Engine;
use Repository\QueryBuilder;
use \Tamtamchik\SimpleFlash\Flash;

class UserModel
{
    private QueryBuilder $queryBuilder;
    private UserValidate $userValidate;
    private Engine $templates;
    private EditModel $editModel;
    public Flash $flash;
    private string $role;

    public function __construct(
        UserValidate $userValidate,
        Flash $flash,
        QueryBuilder $queryBuilder,
        Engine $templates,
        EditModel $editModel)
    {
        $this->queryBuilder = $queryBuilder;
        $this->userValidate = $userValidate;
        $this->templates = $templates;
        $this->flash = $flash;
        $this->editModel = $editModel;
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
                    $_SESSION['login'] = true;
                    $this->getRole($user->id);
                    if ($this->role === 'admin') {
                        $this->flash->message("Привет, {$user->username}, ты администратор!");
                        $_SESSION['admin'] = true;
                    } else {
                        $this->flash->message("Привет, {$user->username}!");
                        $_SESSION['admin'] = false;
                    }
                    header('Location: /users');
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
     * @return void
     */
    public function getAllUsers(): void
    {
        $users = $this->queryBuilder->getUsers();
        $this->printUsers($users);
    }

    /**
     * @param $vars
     * @return void
     */
    public function getUser($vars): void
    {
        $user = $this->queryBuilder->getOne($vars, 'users');
        if ($user) {
            $this->printUser($user);
        }
    }

    /**
     * @return void
     */
    public function createUser(): void
    {
        echo $this->templates->render('create_user');
    }

    /**
     * @param array $data
     * @return void
     */
    public function printUsers(array $data): void
    {
        echo $this->templates->render('users', $data);
    }

    /**
     * @param array $data
     * @return void
     */
    public function printUser(array $data): void
    {
        echo $this->templates->render('page_profile', $data);
    }

    /**
     * @param $vars
     * @return void
     */
    public function delete($vars): void
    {
        $fileName = $this->queryBuilder->getOne($vars, 'users')[0]->image;
        if ($fileName) {
            unlink($fileName);
        }

        $this->queryBuilder->deleteUserById($vars);
        if ($_SESSION['id'] == $vars['id']) {
            $this->logoutUser();
        } else {
            header('Location: /users');
        }
    }

    /**
     * @return void
     */
    public function newUserCreate(): void
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
                ]
        ];

        $this->userValidate->checkData($_POST, $rules);
        if (!empty($_FILES) && $_FILES['image']['size'] > 0) {
            $checkFile = $this->checkFile($_FILES);
            if ($this->userValidate->validateSuccess && $checkFile) {
                $params = $_POST;
                $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $status = $this->editModel->setStatusParam($_POST);
                $params['status'] = $status;
                $this->queryBuilder->insert($params);
                $user = $this->queryBuilder->getUser($_POST['email']);
                $this->editModel->setNewImage($_FILES['image']['name'], $_FILES['image']['tmp_name'], ['id' => $user->id]);
                $this->flash->message('Пользователь успешно добавлен!', 'success');
                header('Location: /users');
            } else {
                $this->createUser();
            }
            exit;
        }

        if ($this->userValidate->validateSuccess) {
            $params = $_POST;
            $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $status = $this->editModel->setStatusParam($_POST);
            $params['status'] = $status;
            $this->queryBuilder->insert($params);
            $this->flash->message('Пользователь успешно добавлен!', 'success');
            header('Location: /users');
        } else {
            $this->createUser();
        }
    }

    /**
     * @param $fileData
     * @return bool
     */
    public function checkFile($fileData): bool
    {
        $name = $fileData['image']['name'];
        $size = $fileData['image']['size'];
        return $this->userValidate->checkImage($name, $size);
    }

    /**
     * @return void
     */
    public function logoutUser(): void
    {
        $_SESSION = [];
        echo $this->templates->render('start_page');
    }
}