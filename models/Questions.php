<?php

namespace app\models;

use Yii;

class Questions extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'questions';
    }

    public function rules()
    {
        return [
            [['test_id', 'question_text', 'type'], 'required'],
            [['test_id', 'type'], 'integer'],
            [['question_text'], 'string'],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tests::class, 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'test_id' => 'Test ID',
            'question_text' => 'Текст вопроса',
            'type' => 'Тип вопроса',
        ];
    }

    public function getTypeName()
    {
        $types = [
            1 => 'Один вариант',
            2 => 'Несколько вариантов', 
            3 => 'Текстовый ответ'
        ];
        
        return $types[$this->type] ?? 'Неизвестно';
    }

    public function getAnswers()
    {
        return $this->hasMany(Answers::class, ['question_id' => 'id']);
    }

    public function getTest()
    {
        return $this->hasOne(Tests::class, ['id' => 'test_id']);
    }
}