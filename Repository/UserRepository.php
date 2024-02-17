<?php

namespace Repository;

use PDO;

class UserRepository extends GeneralRepository
{
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
     * @param string $value
     * @return mixed
     */
    public function getUser(string $value): mixed
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
     * @param array $vars
     * @return array|false
     */
    public function getOne(array $vars): bool|array
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from('users')->where('id = :id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        return $sth->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param array $vars
     * @param array $params
     * @return bool
     */
    public function update(array $vars, array $params): bool
    {
        $update = $this->queryFactory->newUpdate();
        $update->table('users')->cols($params)->where("id = {$vars['id']}")->bindValues($params);
        $sth = $this->pdo->prepare($update->getStatement());
        return $sth->execute($update->getBindValues());
    }

    /**
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into('users')->cols($params);
        $sth = $this->pdo->prepare($insert->getStatement());
        return $sth->execute($insert->getBindValues());
    }
}