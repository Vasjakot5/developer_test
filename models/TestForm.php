<?php

namespace app\models;

use Yii;
use yii\base\Model;

class TestForm extends Model
{
    public $answers = [];

    public function rules()
    {
        return [
            ['answers', 'safe'],
        ];
    }

    public function submitTest($testId, $userId)
    {
        
        $result = new Results();
        $result->user_id = $userId;
        $result->test_id = $testId;
        $result->score = $this->calculateScore($testId);
        $result->start_time = date('Y-m-d H:i:s');
        $result->end_time = date('Y-m-d H:i:s');
        $result->created_at = time();
        return $result->save();
    }

    private function calculateScore($testId)
    {
        $score = 0;
        $questions = Questions::find()
            ->where(['test_id' => $testId])
            ->with('answers')
            ->all();

        foreach ($questions as $question) {
            $questionId = $question->id;
    
            if (!isset($this->answers[$questionId])) {
                continue;
            }

        
            if ($question->type == 3) {
                $answerText = trim($this->answers[$questionId]);
                if (!empty($answerText)) {
                    $score++;
                }
                continue;
            }

            $userAnswer = $this->answers[$questionId];
            
            if ($question->type == 1) {
                $correctAnswerId = null;
                foreach ($question->answers as $answer) {
                    if ($answer->is_correct) {
                        $correctAnswerId = $answer->id;
                        break;
                    }
                }
                
                if ($correctAnswerId !== null && $userAnswer == $correctAnswerId) {
                    $score++;
                }
            }
            elseif ($question->type == 2) {
                if (!is_array($userAnswer) || empty($userAnswer)) {
                    continue;
                }
                
                $correctAnswerIds = [];
                $allAnswersCorrect = true;
                
                foreach ($question->answers as $answer) {
                    if ($answer->is_correct) {
                        $correctAnswerIds[] = $answer->id;
                    }
                }

                if (empty($correctAnswerIds)) {
                    continue;
                }
                
                foreach ($userAnswer as $selectedId) {
                    if (!in_array($selectedId, $correctAnswerIds)) {
                        $allAnswersCorrect = false;
                        break;
                    }
                }
                
                if ($allAnswersCorrect) {
                    foreach ($correctAnswerIds as $correctId) {
                        if (!in_array($correctId, $userAnswer)) {
                            $allAnswersCorrect = false;
                            break;
                        }
                    }
                }
                
                if ($allAnswersCorrect) {
                    $score++;
                }
            }
        }

        return $score;
    }
}