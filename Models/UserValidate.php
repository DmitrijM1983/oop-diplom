<?php

namespace Models;

use Repository\UserRepository;
use Tamtamchik\SimpleFlash\Flash;

class UserValidate
{
    public Flash $flash;
    private array $errors = [];
    private UserRepository $userRepository;
    public bool $validateSuccess = false;

    public function __construct(Flash $flash, UserRepository $userRepository)
    {
        $this->flash = $flash;
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $data
     * @param array $items
     * @param array|null $vars
     * @return $this
     */
    public function checkData(array $data, array $items, array $vars = null): object
    {
        foreach ($items as $item=>$rules) {
            foreach ($rules as $rule=>$rule_value) {
                $value = $data[$item];
                if ($rule === 'required' && empty($value)) {
                    $this->flash->message("Поле {$item} не заполнено!", 'error');
                    $this->addError("Ошибка!");
                } elseif (!empty($value)) {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->flash->message("{$item} должен быть не менее {$rule_value} символов!", 'error');
                                $this->addError("Ошибка!");
                            }
                            break;
                        case 'max':
                            if (strlen($value) > $rule_value) {
                                $this->flash->message("{$item} должен быть не более {$rule_value} символов!", 'error');
                                $this->addError("Ошибка!");
                            }
                            break;
                        case 'matches':
                            if ($value != $data[$rule_value]) {
                                $this->flash->message("Пароли не совпадают!", 'error');
                                $this->addError("Ошибка!");
                            }
                            break;
                        case 'unique':
                            $check = $this->userRepository->getUser($data[$item]);
                            if ($vars != null) {
                                if ($check && $check->id != $vars['id']) {
                                    $this->flash->message("Этот {$item} уже используется!", 'error');
                                    $this->addError("Ошибка!");
                                }
                            } elseif ($check) {
                                $this->flash->message("Этот {$item} уже используется!", 'error');
                                $this->addError("Ошибка!");
                            }
                            break;
                        case 'email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->flash->message("Не корректный {$item}!", 'error');
                                $this->addError("Ошибка!");
                            }
                            break;
                        }
                    }
                }
            }
        if (empty($this->errors)) {
            $this->validateSuccess = true;
        }
        return $this;
    }

    /**
     * @param string $name
     * @param int $size
     * @return bool
     */
    public function checkImage(string $name, int $size): bool
    {
        $name = explode('.', $name);
        $name = $name[1];
        if ($name === 'png' || $name === 'jpg' || $name === 'jpeg' && $size < 9000000) {
            return true;
        } else {
            $this->flash->message("Файл не соответствует!", 'error');
            $this->addError("Ошибка!");
            return false;
        }
    }

    /**
     * @param string $error
     * @return void
     */
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}