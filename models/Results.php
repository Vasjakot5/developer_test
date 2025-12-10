<?php

namespace app\models;

use Yii;

class Results extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'results';
    }

    public function rules()
    {
        return [
            [['user_id', 'test_id', 'score', 'start_time', 'end_time', 'created_at'], 'required'],
            [['user_id', 'test_id', 'score', 'created_at'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tests::class, 'targetAttribute' => ['test_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'test_id' => 'Test ID',
            'score' => 'Score',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[UserAnswers]].
     */
    public function getUserAnswers()
    {
        return $this->hasMany(UserAnswers::class, ['result_id' => 'id']);
    }

    /**
     * Gets query for [[Test]].
     */
    public function getTest()
    {
        return $this->hasOne(Tests::class, ['id' => 'test_id']);
    }

    /**
     * Gets query for [[User]].
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}