<?php

namespace Models;
use Repository\QueryBuilder;
use League;
use Tamtamchik\SimpleFlash\Flash;

class SecurityModel
{
    private QueryBuilder $queryBuilder;
    private UserValidate $userValidate;
    private League\Plates\Engine $templates;
    public Flash $flash;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->userValidate = new UserValidate();
        $this->templates = new League\Plates\Engine('views');
        $this->flash = new Flash();
    }

    /**
     * @param $vars
     * @return void
     */
    public function securityUpdate($vars): void
    {
        $user = $this->queryBuilder->getOneUser( $vars);
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
}