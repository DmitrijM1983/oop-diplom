<?php

namespace Models;

class UserValidate
{
    public function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
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
}