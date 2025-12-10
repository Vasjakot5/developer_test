<?php

namespace app\models;

use Yii;

class Answers extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'answers';
    }

    public function rules()
    {
        return [
            [['question_id', 'answer_text'], 'required'],
            [['question_id'], 'integer'],
            [['answer_text'], 'string'],
            [['is_correct'], 'boolean'],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::class, 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_correct' => 'Правильный ответ',
            'question_id' => 'Вопрос',
            'answer_text' => 'Текст ответа',
        ];
    }

    public function getQuestion()
    {
        return $this->hasOne(Questions::class, ['id' => 'question_id']);
    }
}