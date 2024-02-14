<?php

namespace Models;

use Repository\Connection;
use Repository\QueryBuilder;
use League;
use Tamtamchik\SimpleFlash\Flash;

class ImageModel
{
    private QueryBuilder $queryBuilder;
    private League\Plates\Engine $templates;
    private UserValidate $userValidate;
    public Flash $flash;
    private UserModel $userModel;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder(Connection::getConnect());
        $this->templates = new League\Plates\Engine('views');
        $this->userValidate = new UserValidate();
        $this->flash = new Flash();
        $this->userModel = new UserModel();
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
            $this->userModel->setNewImage($name, $tmp, $vars);
            $this->flash->message('Профиль успешно обновлен!', 'success');
            header("Location: /users");
        } else {
            header("Location: /media/{$vars['id']}");
        }
    }
}