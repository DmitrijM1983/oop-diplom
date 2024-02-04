<?php

namespace Models;

use Repository\QueryBuilder;

class UserValidate
{
    public function checkEmail($email, QueryBuilder $queryBuilder)
    {
        $user = $queryBuilder->getUserbyEmail($email);
        if (!$user) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
            echo 'Это не емэйл а шляпа!';
        } else {
            echo 'Эта почта занята!';
        }
    }

    public function checkPassword(string $password, string $password2 = null)
    {
        if ($password === '') {
            echo 'Пароль не может быть пустым!';
            exit;
        }
        if (strlen($password) <= 3) {
            echo 'Пароль должен быть больше 3 символов!';
            exit;
        }
        if ($password2 != null || $password2 === '') {
            if ($password != $password2) {
                echo 'Пароли не совпадают!';
                exit;
            } else {
               return true;
            }
        }
    }

    public function checkImage($vars, $name, $tmp, $size)
    {
        $name = explode('.', $name);
        $name = $name[1];
        if ($name === 'png' || $name === 'jpg' && $size < 9000000) {
            $userModel = new UserModel();
            $userModel->setNewImage($vars, $name, $tmp);
        }
    }
}