<?php

namespace Controllers;

use Models\EditModel;

class EditController
{
    private EditModel $editModel;

    public function __construct()
    {
        $this->editModel = new EditModel();
    }

    /**
     * @param $vars
     * @return void
     */
    public function editUser($vars): void
    {
        $this->editModel->userEdit($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function updateUser($vars): void
    {
        $this->editModel->userUpdate($vars, $_POST);
    }
}