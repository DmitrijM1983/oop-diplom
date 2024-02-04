<?php

namespace Repository;
use Aura\SqlQuery\QueryFactory;
use Models\UserModel;
use PDO;
use Models\UserValidate;

class QueryBuilder
{
    private QueryFactory $queryFactory;
    private \PDO $pdo;
    private UserValidate $userValidate;

    public function __construct()
    {
        $this->queryFactory = new QueryFactory('mysql');
        $this->pdo = new \PDO('mysql:host=127.0.0.1;dbname=marlin;charset=utf8', 'root', '');
        $this->userValidate = new UserValidate();
    }

    /**
     * @return bool|array
     */
    public function getAllUsers()
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users');
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $users = $sth->fetchAll(PDO::FETCH_OBJ);
        $userModel = new UserModel();
        $userModel->printUsers($users);
    }

    public function getOneUser($vars)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users')->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUserbyEmail($email)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users')->where('email = :email') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute(['email'=> $email]);
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    public function edit($vars)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users')->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        $user = $sth->fetchAll(PDO::FETCH_OBJ);
        $userModel = new UserModel();
        $userModel->editUserData($user);
    }

    public function update($vars, $params)
    {
        $update = $this->queryFactory->newUpdate();
        $update->table('users')->cols($params)->where("id = {$vars['id']}")->bindValues($params);
        $sth = $this->pdo->prepare($update->getStatement());
        $sth->execute($update->getBindValues());
        header('Location:/users');
    }

    public function insert()
    {

        $insert = $this->queryFactory->newInsert();
        $insert->into('users')->cols(['username'=>'Vasya Pchelka', 'email'=>'salupa', 'password'=>'govno']);
        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
    }

    public function updateSecurity($vars)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users')->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        $user = $sth->fetchAll(PDO::FETCH_OBJ);
        $userModel = new UserModel();
        $userModel->editSecurity($user);
    }

    public function updateSecurityUser($vars, $params)
    {
        $email = $params['email'];
        $password = $params['password'];
        $password2 = $params['password2'];
        $emailValidate = $this->userValidate->checkEmail($email, $this);
        $passwordValidate = $this->userValidate->checkPassword($password, $password2);

        if ($emailValidate && $passwordValidate) {
            $params = ['email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)];
            $update = $this->queryFactory->newUpdate();
            $update->table('users')->cols($params)->where("id = {$vars['id']}")->bindValues($params);
            $sth = $this->pdo->prepare($update->getStatement());
            $sth->execute($update->getBindValues());
            header('Location:/users');
        }
    }

    public function getUserStatus($vars)
    {
        $user = $this->getOneUser($vars);
        $userModel = new UserModel();
        $userModel->printStatusUser($user);
    }

    public function getUserMedia($vars)
    {
        $user = $this->getOneUser($vars);
        $userModel = new UserModel();
        $userModel->printMediaUser($user);
    }
}

