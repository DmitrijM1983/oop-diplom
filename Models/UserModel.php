<?php

namespace Models;
use Controllers\UserController;
use League;
use Repository\GeneralRepository;
use Repository\UserRepository;
use \Tamtamchik\SimpleFlash\Flash;

class UserModel
{
    private string $role;

    public function __construct(
        private readonly UserValidate $userValidate,
        private readonly Flash $flash,
        private readonly GeneralRepository $generalRepository,
        private readonly EditModel $editModel,
        private readonly UserRepository $userRepository,
    ) {

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
    }

    /**
     * @param int $id
     * @param string $userName
     * @return void
     */
    public function login(int $id, string $userName): void
    {
        if(isset($_POST['remember'])) {
            $cookie = $this->generalRepository->getCookie(['user_id'=>$id], 'user_cookie');
            if ($cookie) {
                $hash = $cookie->hash;
            } else {
                $hash = hash('sha256', uniqid());
                $this->generalRepository->insertCookie(['user_id'=>$id, 'hash'=>$hash]);
            }
            setcookie('hash', $hash, time() + 604800, '/');
        }
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $userName;
        $_SESSION['login'] = true;
        $this->getRole($id);
        if ($this->role === 'admin') {
            $this->flash->message("Привет, {$userName}, ты администратор!");
            $_SESSION['admin'] = true;
        } else {
            $this->flash->message("Привет, {$userName}!");
            $_SESSION['admin'] = false;
        }
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getRole(int $id): mixed
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
     * @param array $vars
     * @return array
     */
    public function getUser(array $vars): array
    {
        return $this->userRepository->getOne($vars);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function delete(array $vars): void
    {
        $fileName = $this->userRepository->getOne($vars)[0]->image;
        if ($fileName) {
            unlink($fileName);
        }

        $this->generalRepository->deleteUserById($vars);
    }

    /**
     * @return bool
     */
    public function newUserCreateWithImage(): bool
    {
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
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    public function newUserCreate(): void
    {
        $params = $_POST;
        $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $status = $this->editModel->setStatusParam($_POST);
        $params['status'] = $status;
        $this->userRepository->insert($params);
        $this->flash->message('Пользователь успешно добавлен!', 'success');
    }

    /**
     * @param array $fileData
     * @return bool
     */
    public function checkFile(array $fileData): bool
    {
        $name = $fileData['image']['name'];
        $size = $fileData['image']['size'];
        return $this->userValidate->checkImage($name, $size);
    }
}