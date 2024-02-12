<?php

namespace Controllers;

use Models\StatusModel;

class StatusController
{
    private StatusModel $statusModel;

    public function __construct()
    {
        $this->statusModel = new StatusModel();
    }

    /**
     * @param $vars
     * @return void
     */
    public function getStatus($vars): void
    {
        $this->statusModel->getCurrentStatus($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function setStatus($vars): void
    {
        $this->statusModel->setStatusParam($_POST, $vars);
    }
}