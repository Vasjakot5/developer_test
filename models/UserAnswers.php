<?php

namespace app\models;

use Yii;

class UserAnswers extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_answers';
    }

    public function rules()
    {
        return [
            [['result_id', 'question_id'], 'required'],
            [['result_id', 'question_id'], 'integer'],
            [['answer_text'], 'string', 'max' => 255],
            [['answer_text'], 'default', 'value' => ''],
            [['result_id'], 'exist', 'skipOnError' => true, 'targetClass' => Results::class, 'targetAttribute' => ['result_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::class, 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'result_id' => 'Result ID',
            'question_id' => 'Question ID',
            'answer_text' => 'Answer Text',
        ];
    }

    public function getQuestion()
    {
        return $this->hasOne(Questions::class, ['id' => 'question_id']);
    }

    public function getResult()
    {
        return $this->hasOne(Results::class, ['id' => 'result_id']);
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->answer_text === null) {
                $this->answer_text = '';
            }
            return true;
        }
        return false;
    }
}