<?php

namespace Models;
use League;
use Repository\QueryBuilder;
use \Tamtamchik\SimpleFlash\Flash;

class UserModel
{
    private QueryBuilder $queryBuilder;
    private UserValidate $userValidate;
    private League\Plates\Engine $templates;
    private StatusModel $statusModel;
    public Flash $flash;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->userValidate = new UserValidate();
        $this->templates = new League\Plates\Engine('views');
        $this->statusModel = new StatusModel();
        $this->flash = new Flash();
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
        $user = $this->queryBuilder->getOneUser($vars);
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
     * @param $name
     * @param $tmp
     * @param $vars
     * @return void
     */
    public function setNewImage($name, $tmp, $vars): void
    {
        $fileName = $this->queryBuilder->getOneUser($vars)[0]->image;
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        $image = 'img/demo/avatars/avatar-' . uniqid() . '.' . $name;
        move_uploaded_file($tmp, $image);
        $this->queryBuilder->update($vars, ['image'=>$image]);
    }

    /**
     * @param $vars
     * @return void
     */
    public function delete($vars): void
    {
        $query = new QueryBuilder();
        $fileName = $query->getOneUser($vars)[0]->image;
        if ($fileName) {
            unlink($fileName);
        }

        $query->deleteUserById($vars);
        if ($_SESSION['id'] == $vars['id']) {
            header('Location: /start_page.php');
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
                $status = $this->statusModel->setStatusParam($_POST);
                $params['status'] = $status;
                $this->queryBuilder->insert($params);
                $user = $this->queryBuilder->getUser($_POST['email']);
                $this->setNewImage($_FILES['image']['name'], $_FILES['image']['tmp_name'], ['id' => $user->id]);
                $this->flash->message('Пользователь успешно добавлен!', 'success');
                header('Location: /users');
                exit;
            } else {
                $this->createUser();
                exit;
            }
        }

        if ($this->userValidate->validateSuccess) {
            $params = $_POST;
            $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $status = $this->statusModel->setStatusParam($_POST);
            $params['status'] = $status;
            $this->queryBuilder->insert($params);
            $this->flash->message('Пользователь успешно добавлен!', 'success');
            header('Location:users');
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
}