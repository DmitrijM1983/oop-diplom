<?php

namespace Models;

use League;
use Repository\UserRepository;
use Tamtamchik\SimpleFlash\Flash;

class EditModel
{
    public function __construct(
        private  readonly UserValidate $userValidate,
        private  readonly Flash $flash,
        private  readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @param array $vars
     * @return array
     */
    public function userEdit(array $vars): array
    {
        return $this->userRepository->getOne($vars);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function userUpdate(array $vars): void
    {
        $this->userRepository->update($vars, $_POST);
        $this->flash->message('Профиль успешно обновлен!', 'success');
    }

    /**
     * @param array $vars
     * @return array
     */
    public function getUserImage(array $vars): array
    {
        return $this->userRepository->getOne($vars);
    }

    /**
     * @param array $fileData
     * @param array|null $vars
     * @return bool
     */
    public function checkFile(array $fileData, array $vars = null): bool
    {
        $name = $fileData['image']['name'];
        $tmp = $fileData['image']['tmp_name'];
        $size = $fileData['image']['size'];
        $checkImage =  $this->userValidate->checkImage($name, $size);
        if ($checkImage) {
            $this->setNewImage($name, $tmp, $vars);
            $this->flash->message('Профиль успешно обновлен!', 'success');
            return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @param string $tmp
     * @param array $vars
     * @return void
     */
    public function setNewImage(string $name, string $tmp, array $vars): void
    {
        $fileName = $this->userRepository->getOne($vars)[0]->image;
        if ($fileName) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
        $image = 'img/demo/avatars/avatar-' . uniqid() . '.' . $name;
        move_uploaded_file($tmp, $image);
        $this->userRepository->update($vars, ['image'=>$image]);
    }

    /**
     * @param array $vars
     * @return array
     */
    public function securityUpdate(array $vars): array
    {
        return $this->userRepository->getOne($vars);
    }

    /**
     * @param array $vars
     * @return void
     */
    public function updateSecurity(array $vars): void
    {
        $params =
            [
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ];
        $this->userRepository->update($vars, $params);
        $this->flash->message('Профиль успешно обновлен!', 'success');
    }

    /**
     * @param array $vars
     * @return array
     */
    public function getCurrentStatus(array $vars): array
    {
        return $this->userRepository->getOne($vars);
    }

    /**
     * @param array $status
     * @param array|null $vars
     * @return string|null
     */
    public function setStatusParam(array $status, array $vars = null): ?string
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
        $this->userRepository->update($vars, ['status'=>$newStatus]);
        return null;;
    }
}