<?php

namespace Repository;
use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder
{
    private QueryFactory $queryFactory;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->queryFactory = new QueryFactory('mysql');
        $this->pdo = $pdo;
    }

    /**
     * @return bool|array
     */
    public function getUsers(): bool|array
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users');
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param $vars
     * @param $table
     * @return array|false
     */
    public function getOne($vars, $table): bool|array
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from("{$table}")->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getUser($value): mixed
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

    /**
     * @param $id
     * @return mixed
     */
    public function getRoleUser($id): mixed
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
        return$sth->fetch(PDO::FETCH_OBJ)->permissions;
    }

    /**
     * @param $vars
     * @param $params
     * @return bool
     */
    public function update($vars, $params): bool
    {
        $update = $this->queryFactory->newUpdate();
        $update->table('users')->cols($params)->where("id = {$vars['id']}")->bindValues($params);
        $sth = $this->pdo->prepare($update->getStatement());
        return $sth->execute($update->getBindValues());
    }

    /**
     * @param $params
     * @return bool
     */
    public function insert($params): bool
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into('users')->cols($params);
        $sth = $this->pdo->prepare($insert->getStatement());
        return $sth->execute($insert->getBindValues());
    }

    /**
     * @param $vars
     * @return void
     */
    public function deleteUserById($vars): void
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

    /**
     * @param $params
     * @return void
     */
    public function insertCookie($params): void
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into('user_cookie')->cols($params);
        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
    }

    /**
     * @param $vars
     * @param $table
     * @return mixed
     */
    public function getCookie($vars, $table): mixed
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from("{$table}")->where('user_id = :user_id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        return $sth->fetch(PDO::FETCH_OBJ);
    }
}

