<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class RegisterForm extends Model
{
    public $email;
    public $name;
    public $password;
    public $role = 0;

    public function rules()
    {
        return [
            [['password', 'name', 'email'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class],
            [['name'], 'match', 'pattern' => '/^[а-яёА-ЯЁ\- ]+$/u', 'message' => 'Имя может содержать только кириллица и пробелы'],
            ['password', 'string', 'min' => 6],
            ['role', 'default', 'value'=> 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'password' => 'Пароль',
        ];
    }

    public function register()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->setPassword($this->password);
        return $user->save();
    }
}