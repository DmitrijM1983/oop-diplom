<?php

namespace Repository;
use Aura\SqlQuery\QueryFactory;
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
    public function getUsers()
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users');
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    public function getOneUser($vars)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users')->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUser($value)
    {
        $select = $this->queryFactory->newSelect();

        $result = strpos($value, '@');
        if ($result === false) {
            $select->cols(['*'])->from('users')->where('username = :username') ;
            $sth = $this->pdo->prepare($select->getStatement());
            $sth->execute(['username'=> $value]);
        } else {
            $select->cols(['*'])->from('users')->where('email = :email') ;
            $sth = $this->pdo->prepare($select->getStatement());
            $sth->execute(['email'=> $value]);
        }
        return $sth->fetch(PDO::FETCH_OBJ);
    }

    public function getRoleUser($id)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users')->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute(['id' => $id]);
        $user = $sth->fetch(PDO::FETCH_OBJ);

        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('groups')->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute(['id' => $user->group_id]);
        return $sth->fetch(PDO::FETCH_OBJ)->permissions;
    }

    public function update($vars, $params)
    {
        $update = $this->queryFactory->newUpdate();
        $update->table('users')->cols($params)->where("id = {$vars['id']}")->bindValues($params);
        $sth = $this->pdo->prepare($update->getStatement());
        return $sth->execute($update->getBindValues());
    }

    public function insert($params)
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into('users')->cols($params);
        $sth = $this->pdo->prepare($insert->getStatement());
        return $sth->execute($insert->getBindValues());
    }

    public function deleteUserById($vars)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from('user_cookie')->where('user_id = :user_id');
        $sth = $this->pdo->prepare($delete->getStatement());
        $sth->execute(['user_id'=>$vars['id']]);

        $delete = $this->queryFactory->newDelete();
        $delete->from('users')->where('id = :id');
        $sth = $this->pdo->prepare($delete->getStatement());
        $sth->execute($vars);
    }

    public function getUserCokie($vars)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('user_cookie')->where('user_id = :user_id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        return $sth->fetch(PDO::FETCH_OBJ);
    }

    public function insertCokie($params)
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into('user_cookie')->cols($params);
        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
    }
}

