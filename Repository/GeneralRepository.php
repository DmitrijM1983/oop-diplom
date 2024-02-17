<?php

namespace Repository;
use Aura\SqlQuery\QueryFactory;
use PDO;

class GeneralRepository
{
    protected QueryFactory $queryFactory;
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->queryFactory = new QueryFactory('mysql');
        $this->pdo = $pdo;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getRoleUser(int $id): mixed
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
     * @param array $vars
     * @return void
     */
    public function deleteUserById(array $vars): void
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
     * @param array $params
     * @return void
     */
    public function insertCookie(array $params): void
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into('user_cookie')->cols($params);
        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
    }

    /**
     * @param array $vars
     * @param string $table
     * @return mixed
     */
    public function getCookie(array $vars, string $table): mixed
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from("{$table}")->where('user_id = :user_id') ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($vars);
        return $sth->fetch(PDO::FETCH_OBJ);
    }
}

