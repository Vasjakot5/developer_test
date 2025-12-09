<?php

namespace app\models;

use Yii;

class Tests extends \yii\db\ActiveRecord
{
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = time();
            }
            $this->updated_at = time();
            return true;
        }
        return false;
    }

    public static function tableName()
    {
        return 'tests';
    }

    public function rules()
    {
        return [
            [['title', 'time_limit_minutes'], 'required'],
            [['title'], 'string', 'max' => 100],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название теста',
            'time_limit_minutes' => 'Время на прохождение (минуты)',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }

    public function getQuestions()
    {
        return $this->hasMany(Questions::class, ['test_id' => 'id']);
    }

    public function getResults()
    {
        return $this->hasMany(Results::class, ['test_id' => 'id']);
    }
}