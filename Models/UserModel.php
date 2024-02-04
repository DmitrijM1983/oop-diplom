<?php

namespace Models;
use League;
use Repository\QueryBuilder;

class UserModel
{
    public function printUsers(array $data)
    {
        $templates = new League\Plates\Engine('views');
        echo $templates->render('users', $data);
    }

    public function printUser(array $data)
    {
        $templates = new League\Plates\Engine('views');
        echo $templates->render('page_profile', $data);
    }

    public function editUserData(array $data)
    {
        $templates = new League\Plates\Engine('views');
        echo $templates->render('edit', $data);
    }

    public function editSecurity($vars)
    {
        $templates = new League\Plates\Engine('views');
        echo $templates->render('security', $vars);
    }

    public function printStatusUser(array $data)
    {
        $templates = new League\Plates\Engine('views');
        echo $templates->render('status', $data);
    }

    public function setStatusParam($vars, $status)
    {
        if ($status === 'Онлайн') {
            $newStatus = 'online';
        }
        if ($status === 'Отошел') {
            $newStatus = 'moved away';
        }
        if ($status === 'Не беспокоить') {
            $newStatus = 'do not disturb';
        }
        $query = new QueryBuilder();
        $query->update($vars, ['status'=>$newStatus]);
    }

    public function printMediaUser(array $data)
    {
        $templates = new League\Plates\Engine('views');
        echo $templates->render('media', $data);
    }

    public function setNewImage($vars, $name, $tmp)
    {
        $query = new QueryBuilder();
        $fileName = $query->getOneUser($vars)[0]->image;
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        $image = 'img/demo/avatars/avatar-' . uniqid() . '.' . $name;
        move_uploaded_file($tmp, $image);
        $query->update($vars, ['image'=>$image]);
    }
}