<?php

namespace Models;

use Repository\QueryBuilder;
use League;

class StatusModel
{
    private QueryBuilder $queryBuilder;
    private League\Plates\Engine $templates;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->templates = new League\Plates\Engine('views');
    }

    /**
     * @param $vars
     * @return void
     */
    public function getCurrentStatus($vars): void
    {
        $user = $this->queryBuilder->getOneUser($vars);
        $this->printStatusUser($user);
    }

    /**
     * @param array $data
     * @return void
     */
    public function printStatusUser(array $data): void
    {
        echo $this->templates->render('status', $data);
    }

    /**
     * @param $status
     * @param $vars
     * @return string|null
     */
    public function setStatusParam($status, $vars = null): ?string
    {
        if ($status['status'] === 'Онлайн') {
            $newStatus = 'online';
        }
        if ($status['status'] === 'Отошел') {
            $newStatus = 'moved away';
        }
        if ($status['status'] === 'Не беспокоить') {
            $newStatus = 'do not disturb';
        }
        if ($vars === null) {
            return $newStatus;
        }
        $this->queryBuilder->update($vars, ['status'=>$newStatus]);
        header('Location: /users');
        return null;
    }
}