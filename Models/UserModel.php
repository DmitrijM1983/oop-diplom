<?php

namespace Models;
use Controllers\UserController;
use League;
use Repository\GeneralRepository;
use Repository\UserRepository;
use \Tamtamchik\SimpleFlash\Flash;

class UserModel
{
    private GeneralRepository $generalRepository;
    private UserRepository $userRepository;
    private UserValidate $userValidate;
    private EditModel $editModel;
    private UserController $userController;
    public Flash $flash;
    private string $role;

    public function __construct(
        UserValidate $userValidate,
        Flash $flash,
        GeneralRepository $generalRepository,
        EditModel $editModel,
        UserController $userController,
        UserRepository $userRepository)
    {
        $this->generalRepository = $generalRepository;
        $this->userValidate = $userValidate;
        $this->flash = $flash;
        $this->editModel = $editModel;
        $this->userController = $userController;
        $this->userRepository = $userRepository;
    }

    /**
     * @return void
     */
    public function registration(): void
    {
        $params['username'] = $_POST['username'];
        $params['email'] = $_POST['email'];
        $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $this->userRepository->insert($params);
        $this->flash->message("Вы успешно зарегистрированы! Введите email и пароль для входа!", 'success');
        $_SESSION['email'] = $params['email'];
        $this->userController->getLoginForm();
    }

    /**
     * @return void
     */
    public function login(): void
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $this->userRepository->getUser($email);
        if ($user) {
            $checkPassword = password_verify($password, $user->password);
            if ($checkPassword) {
                if(isset($_POST['remember'])) {
                    $cookie = $this->generalRepository->getCookie(['user_id'=>$user->id], 'user_cookie');
                    if ($cookie) {
                        $hash = $cookie->hash;
                    } else {
                        $hash = hash('sha256', uniqid());
                        $this->generalRepository->insertCookie(['user_id'=>$user->id, 'hash'=>$hash]);
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
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getRole($id): mixed
    {
       return $this->role = $this->generalRepository->getRoleUser($id);
    }

    /**
     * @return array|bool
     */
    public function getAllUsers(): bool|array
    {
       return $this->userRepository->getUsers();
    }

    /**
     * @param $vars
     * @return array
     */
    public function getUser($vars): array
    {
        return $this->userRepository->getOne($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function delete($vars): void
    {
        $fileName = $this->userRepository->getOne($vars)[0]->image;
        if ($fileName) {
            unlink($fileName);
        }

        $this->generalRepository->deleteUserById($vars);
        if ($_SESSION['id'] == $vars['id']) {
            $_SESSION = [];
            $this->userController->logout();
        } else {
            header('Location: /users');
        }
    }

    /**
     * @return void
     */
    public function newUserCreate(): void
    {
        if (!empty($_FILES) && $_FILES['image']['size'] > 0) {
            $checkFile = $this->checkFile($_FILES);
            if ($checkFile) {
                $params = $_POST;
                $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $status = $this->editModel->setStatusParam($_POST);
                $params['status'] = $status;
                $this->userRepository->insert($params);
                $user = $this->userRepository->getUser($_POST['email']);
                $this->editModel->setNewImage($_FILES['image']['name'], $_FILES['image']['tmp_name'], ['id' => $user->id]);
                $this->flash->message('Пользователь успешно добавлен!', 'success');
                header('Location: /users');
                exit;
            } else {
                $this->userController->create();
            }
        } else {
            $params = $_POST;
            $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $status = $this->editModel->setStatusParam($_POST);
            $params['status'] = $status;
            $this->userRepository->insert($params);
            $this->flash->message('Пользователь успешно добавлен!', 'success');
            header('Location: /users');
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
}