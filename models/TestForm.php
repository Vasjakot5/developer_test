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
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $result = new Results();
            $result->user_id = $userId;
            $result->test_id = $testId;
            $result->score = 0;
            $result->created_at = time();
            $result->start_time = date('Y-m-d H:i:s');
            $result->end_time = date('Y-m-d H:i:s');
            
            if (!$result->save()) {
                return false;
            }
            
            $questions = Questions::find()
                ->where(['test_id' => $testId])
                ->with('answers')
                ->all();
            
            $correctAnswersCount = 0;
            
            foreach ($questions as $question) {
                $userAnswerValue = $this->answers[$question->id] ?? '';
                
                $userAnswer = new UserAnswers();
                $userAnswer->result_id = $result->id;
                $userAnswer->question_id = $question->id;
                
                if ($question->type == 3) {
                    $userAnswer->answer_text = (string)$userAnswerValue;
                    
                    if (!$userAnswer->save()) {
                        return false;
                    }
                    
                    if (!empty(trim($userAnswerValue))) {
                        $correctAnswersCount++;
                    }
                    continue;
                }
                
                if ($question->type == 1) {
                    if (is_numeric($userAnswerValue)) {
                        $answer = Answers::findOne($userAnswerValue);
                        $userAnswer->answer_text = $answer ? $answer->answer_text : '';
                    } else {
                        $userAnswer->answer_text = (string)$userAnswerValue;
                    }
                    
                    if (!$userAnswer->save()) {
                        return false;
                    }
                    
                    $correctAnswer = Answers::find()
                        ->where(['question_id' => $question->id, 'is_correct' => 1])
                        ->one();
                    
                    if ($correctAnswer) {
                        $isCorrect = false;
                        if (is_numeric($userAnswerValue)) {
                            $isCorrect = ($userAnswerValue == $correctAnswer->id);
                        } else {
                            $isCorrect = ($userAnswer->answer_text == $correctAnswer->answer_text);
                        }
                        
                        if ($isCorrect) {
                            $correctAnswersCount++;
                        }
                    }
                    
                } elseif ($question->type == 2) {
                    $answerIds = is_array($userAnswerValue) ? $userAnswerValue : [];
                    $answerTexts = [];
                    
                    foreach ($answerIds as $answerId) {
                        $answer = Answers::findOne($answerId);
                        if ($answer) {
                            $answerTexts[] = $answer->answer_text;
                        }
                    }
                    
                    $userAnswer->answer_text = implode(', ', $answerTexts);
                    
                    if (!$userAnswer->save()) {
                        return false;
                    }
                    
                    $correctAnswers = Answers::find()
                        ->where(['question_id' => $question->id, 'is_correct' => 1])
                        ->all();
                    
                    if (count($correctAnswers) > 0) {
                        $allCorrect = true;
                        
                        foreach ($answerIds as $answerId) {
                            $isCorrectAnswer = false;
                            foreach ($correctAnswers as $correct) {
                                if ($answerId == $correct->id) {
                                    $isCorrectAnswer = true;
                                    break;
                                }
                            }
                            if (!$isCorrectAnswer) {
                                $allCorrect = false;
                                break;
                            }
                        }
                        
                        if ($allCorrect && count($answerIds) == count($correctAnswers)) {
                            $correctAnswersCount++;
                        }
                    }
                }
            }
            
            $result->score = $correctAnswersCount;
            if (!$result->save()) {
                return false;
            }
            
            $transaction->commit();
            return true;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
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