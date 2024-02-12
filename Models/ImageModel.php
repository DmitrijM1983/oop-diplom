<?php

namespace Models;

use Repository\QueryBuilder;
use League;
use Tamtamchik\SimpleFlash\Flash;

class ImageModel
{
    private QueryBuilder $queryBuilder;
    private League\Plates\Engine $templates;
    private UserValidate $userValidate;
    public Flash $flash;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->templates = new League\Plates\Engine('views');
        $this->userValidate = new UserValidate();
        $this->flash = new Flash();
    }

    /**
     * @param $vars
     * @return void
     */
    public function getUserImage($vars): void
    {
        $user = $this->queryBuilder->getOneUser($vars);
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
        $fileName = $this->queryBuilder->getOneUser($vars)[0]->image;
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        $image = 'img/demo/avatars/avatar-' . uniqid() . '.' . $name;
        move_uploaded_file($tmp, $image);
        $this->queryBuilder->update($vars, ['image'=>$image]);
    }
}