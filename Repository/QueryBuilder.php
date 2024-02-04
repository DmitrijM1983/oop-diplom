<?php

namespace Repository;
use Aura\SqlQuery\QueryFactory;
use Models\UserModel;
use PDO;

class QueryBuilder
{
    private QueryFactory $queryFactory;
    private \PDO $pdo;

    public function __construct()
    {
        $this->queryFactory = new QueryFactory('mysql');
        $this->pdo = new \PDO('mysql:host=127.0.0.1;dbname=marlin;charset=utf8', 'root', '');
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
        $user = $sth->fetchAll(PDO::FETCH_OBJ);
        $userModel = new UserModel();
        $userModel->printUser($user);
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
//        $password = password_hash($password, PASSWORD_DEFAULT);
//exit;
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users')->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        $user = $sth->fetchAll(PDO::FETCH_OBJ);
        $userModel = new UserModel();
        $userModel->editSecurity($user);
    }

}

