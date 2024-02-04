<?php

namespace Models;
use League;

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
}