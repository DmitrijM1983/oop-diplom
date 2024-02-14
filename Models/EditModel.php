<?php

namespace Models;

use Repository\Connection;
use Repository\QueryBuilder;
use League;
use Tamtamchik\SimpleFlash\Flash;

class EditModel
{
    private QueryBuilder $queryBuilder;
    private League\Plates\Engine $templates;
    private UserValidate $userValidate;
    public  Flash $flash;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder(Connection::getConnect());
        $this->userValidate = new UserValidate();
        $this->templates = new League\Plates\Engine('views');
        $this->flash = new Flash();
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
}