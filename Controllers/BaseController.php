<?php

namespace Controllers;

use JetBrains\PhpStorm\NoReturn;

class BaseController
{
    /**
     * @param $page
     * @return void
     */
    #[NoReturn] public function redirect($page): void
    {
        header("Location: {$page}");
        exit();
    }
}