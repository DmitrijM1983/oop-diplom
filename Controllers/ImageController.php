<?php

namespace Controllers;

use Models\ImageModel;

class ImageController
{
    private ImageModel $imageModel;

    public function __construct()
    {
        $this->imageModel = new ImageModel();
    }

    /**
     * @param $vars
     * @return void
     */
    public function getImage($vars): void
    {
        $this->imageModel->getUserImage($vars);
    }

    /**
     * @param $vars
     * @return void
     */
    public function setImage($vars): void
    {
        $this->imageModel->checkFile($_FILES, $vars);
    }
}