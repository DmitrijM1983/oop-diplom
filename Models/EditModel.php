<?php

namespace Models;

use Repository\QueryBuilder;
use League;
use Tamtamchik\SimpleFlash\Flash;
use League\Plates\Engine;

class EditModel
{
    private QueryBuilder $queryBuilder;
    private League\Plates\Engine $templates;
    private UserValidate $userValidate;
    public  Flash $flash;

    public function __construct(
        UserValidate $userValidate,
        Engine $templates,
        Flash $flash,
        QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->userValidate = $userValidate;
        $this->templates = $templates;
        $this->flash = $flash;
    }

    /**
     * @param $vars
     * @return void
     */
    public function userEdit($vars): void
    {
        $user = $this->queryBuilder->getOne($vars, 'users');
        $this->editUserData($user);
    }

    /**
     * @param array $data
     * @return void
     */
    public function editUserData(array $data): void
    {
        echo $this->templates->render('edit', $data);
    }

    /**
     * @param $vars
     * @param $params
     * @return void
     */
    public function userUpdate($vars, $params): void
    {
        $rules = [
            'username' =>
                [
                    'required' => true,
                    'min' => 3,
                    'max' => 25,
                    'unique' => 'users'
                ]
            ];

        $this->userValidate->checkData($_POST, $rules, $vars);
        if ($this->userValidate->validateSuccess) {
            $this->queryBuilder->update($vars, $params);
            $this->flash->message('Профиль успешно обновлен!', 'success');
            header('Location: /users');
            exit;
        } else {
            header("Location: /edit/{$vars['id']}");
        }
    }

    /**
     * @param $vars
     * @return void
     */
    public function getUserImage($vars): void
    {
        $user = $this->queryBuilder->getOne($vars, 'users');
        $this->printImageUser($user);
    }

    /**
     * @param array $data
     * @return void
     */
    public function printImageUser(array $data): void
    {
        echo $this->templates->render('media', $data);
    }

    /**
     * @param $fileData
     * @param $vars
     * @return void
     */
    public function checkFile($fileData, $vars = null): void
    {
        $name = $fileData['image']['name'];
        $tmp = $fileData['image']['tmp_name'];
        $size = $fileData['image']['size'];
        $checkImage =  $this->userValidate->checkImage($name, $size);
        if ($checkImage) {
            $this->setNewImage($name, $tmp, $vars);
            $this->flash->message('Профиль успешно обновлен!', 'success');
            header("Location: /users");
        } else {
            header("Location: /media/{$vars['id']}");
        }
    }

    /**
     * @param $name
     * @param $tmp
     * @param $vars
     * @return void
     */
    public function setNewImage($name, $tmp, $vars): void
    {
        $fileName = $this->queryBuilder->getOne($vars, 'users')[0]->image;
        if ($fileName) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
        $image = 'img/demo/avatars/avatar-' . uniqid() . '.' . $name;
        move_uploaded_file($tmp, $image);
        $this->queryBuilder->update($vars, ['image'=>$image]);
    }

    /**
     * @param $vars
     * @return void
     */
    public function securityUpdate($vars): void
    {
        $user = $this->queryBuilder->getOne( $vars, 'users');
        $this->editSecurity($user);
    }

    /**
     * @param $vars
     * @return void
     */
    public function editSecurity($vars): void
    {
        echo $this->templates->render('security', $vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function updateSecurity($vars): void
    {
        $rules = [
            'email' =>
                [
                    'required' => true,
                    'email' => true,
                    'min' => 8,
                    'max' => 40,
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

        $this->userValidate->checkData($_POST, $rules, $vars);
        if ($this->userValidate->validateSuccess) {
            $params =
                [
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
                ];
            $this->queryBuilder->update($vars, $params);
            $this->flash->message('Профиль успешно обновлен!', 'success');
            header('Location: /users');
            exit;
        } else {
            header("Location: /security/{$vars['id']}");
        }
    }

    /**
     * @param $vars
     * @return void
     */
    public function getCurrentStatus($vars): void
    {
        $user = $this->queryBuilder->getOne($vars, 'users');
        $this->printStatusUser($user);
    }

    /**
     * @param array $data
     * @return void
     */
    public function printStatusUser(array $data): void
    {
        echo $this->templates->render('status', $data);
    }

    /**
     * @param $status
     * @param $vars
     * @return string|null
     */
    public function setStatusParam($status, $vars = null): ?string
    {
        if ($status['status'] === 'Онлайн') {
            $newStatus = 'online';
        }
        if ($status['status'] === 'Отошел') {
            $newStatus = 'moved away';
        }
        if ($status['status'] === 'Не беспокоить') {
            $newStatus = 'do not disturb';
        }
        if ($vars === null) {
            return $newStatus;
        }
        $this->queryBuilder->update($vars, ['status'=>$newStatus]);
        header('Location: /users');
        return null;
    }
}