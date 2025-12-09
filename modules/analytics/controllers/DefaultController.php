<?php

namespace app\modules\analytics\controllers;

use Yii;
use yii\web\Controller;
use app\models\Results;
use app\models\Tests;
use app\models\User;
use app\models\Answers;
use Mpdf\Mpdf;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $totalUsers = User::find()->where(['role' => 0])->count();
        $totalTests = Tests::find()->count();
        $totalResults = Results::find()->count();
        
        $allResults = Results::find()->with('test.questions')->all();
        
        $totalCorrect = 0;
        $totalIncorrect = 0;
        $totalQuestionsInResults = 0;
        
        foreach ($allResults as $result) {
            $testQuestionCount = count($result->test->questions);
            $totalQuestionsInResults += $testQuestionCount;
            
            $maxScore = $testQuestionCount;
            
            $totalCorrect += $result->score;
            
            $totalIncorrect += ($testQuestionCount - $result->score);
        }
        
        $totalAnswers = $totalCorrect + $totalIncorrect;
        $successRate = $totalAnswers > 0 ? round(($totalCorrect / $totalAnswers) * 100, 1) : 0;
        
        $tests = Tests::find()
            ->with([
                'questions',
                'results' => function($query) {
                    $query->with('answers');
                }
            ])
            ->all();
        
        $activeUsers = User::find()
            ->where(['role' => 0])
            ->andWhere(['exists', Results::find()->where('user_id = users.id')])
            ->count();
        
        $activityRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;
        
        $users = User::find()
            ->where(['role' => 0])
            ->with([
                'results' => function($query) {
                    $query->with('test.questions', 'answers');
                }
            ])
            ->all();
        
        $allScores = Results::find()->select('score')->column();
        $averageScore = !empty($allScores) ? round(array_sum($allScores) / count($allScores), 1) : 0;
        
        return $this->render('index', [
            'totalUsers' => $totalUsers,
            'totalTests' => $totalTests,
            'totalResults' => $totalResults,
            'totalCorrect' => $totalCorrect,
            'totalIncorrect' => $totalIncorrect,
            'totalAnswers' => $totalAnswers,
            'successRate' => $successRate,
            'averageScore' => $averageScore,
            'correctPercentage' => $totalAnswers > 0 ? ($totalCorrect / $totalAnswers) * 100 : 0,
            'incorrectPercentage' => $totalAnswers > 0 ? ($totalIncorrect / $totalAnswers) * 100 : 0,
            'tests' => $tests,
            'users' => $users,
            'activeUsers' => $activeUsers,
            'activityRate' => $activityRate,
            'testsPerUser' => $totalUsers > 0 ? round($totalResults / $totalUsers, 1) : 0,
            'totalQuestionsInResults' => $totalQuestionsInResults,
        ]);
    }
    
    public function actionExport()
    {
        return $this->render('export');
    }
    
    public function actionExportPdf()
    {
        $totalUsers = User::find()->where(['role' => 0])->count();
        $totalTests = Tests::find()->count();
        $totalResults = Results::find()->count();
        $totalCorrect = Answers::find()->where(['is_correct' => 1])->count();
        $totalIncorrect = Answers::find()->where(['is_correct' => 0])->count();
        $totalAnswers = $totalCorrect + $totalIncorrect;
        $successRate = $totalAnswers > 0 ? round(($totalCorrect / $totalAnswers) * 100, 1) : 0;
        
        $allScores = Results::find()->select('score')->column();
        $averageScore = !empty($allScores) ? round(array_sum($allScores) / count($allScores), 1) : 0;
        
        $activeUsers = User::find()
            ->where(['role' => 0])
            ->andWhere(['exists', Results::find()->where('user_id = users.id')])
            ->count();
        
        $activityRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;
        
        // Получаем данные для таблиц
        $tests = Tests::find()->all();
        $users = User::find()
            ->where(['role' => 0])
            ->with('results')
            ->all();
        
        // Создаем массив $stats
        $stats = [
            'totalUsers' => $totalUsers,
            'totalTests' => $totalTests,
            'totalResults' => $totalResults,
            'totalCorrect' => $totalCorrect,
            'totalIncorrect' => $totalIncorrect,
            'successRate' => $successRate,
            'averageScore' => $averageScore,
            'activeUsers' => $activeUsers,
            'activityRate' => $activityRate,
            'testsPerUser' => $totalUsers > 0 ? round($totalResults / $totalUsers, 1) : 0,
            'tests' => $tests,
            'users' => $users,
        ];
        
        $html = $this->renderPartial('_pdf_template', [
            'stats' => $stats, // Передаем массив $stats
            'date' => date('d.m.Y'),
        ]);
        
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        
        $filename = "Аналитика_тестирования_" . date('Y-m-d') . ".pdf";
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $mpdf->Output($filename, 'D');
        
        return;
    }
    
    public function actionExportExcel()
    {
        $totalUsers = User::find()->where(['role' => 0])->count();
        $totalTests = Tests::find()->count();
        $totalResults = Results::find()->count();
        $totalCorrect = Answers::find()->where(['is_correct' => 1])->count();
        $totalIncorrect = Answers::find()->where(['is_correct' => 0])->count();
        $totalAnswers = $totalCorrect + $totalIncorrect;
        $successRate = $totalAnswers > 0 ? round(($totalCorrect / $totalAnswers) * 100, 1) : 0;
        
        $allScores = Results::find()->select('score')->column();
        $averageScore = !empty($allScores) ? round(array_sum($allScores) / count($allScores), 1) : 0;
        
        $activeUsers = User::find()
            ->where(['role' => 0])
            ->andWhere(['exists', Results::find()->where('user_id = users.id')])
            ->count();
        
        $activityRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;
        
        // Получаем данные для таблиц
        $tests = Tests::find()->all();
        $users = User::find()
            ->where(['role' => 0])
            ->with('results')
            ->all();
        
        // Создаем массив $stats
        $stats = [
            'totalUsers' => $totalUsers,
            'totalTests' => $totalTests,
            'totalResults' => $totalResults,
            'totalCorrect' => $totalCorrect,
            'totalIncorrect' => $totalIncorrect,
            'successRate' => $successRate,
            'averageScore' => $averageScore,
            'activeUsers' => $activeUsers,
            'activityRate' => $activityRate,
            'testsPerUser' => $totalUsers > 0 ? round($totalResults / $totalUsers, 1) : 0,
            'tests' => $tests,
            'users' => $users,
        ];
        
        $html = $this->renderPartial('_excel_template', [
            'stats' => $stats, // Передаем массив $stats
            'date' => date('d.m.Y'),
        ]);
        
        $filename = "Аналитика_тестирования_" . date('Y-m-d') . ".xls";
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/vnd.ms-excel');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        return $html;
    }
    
    public function actionTestDetail($id)
    {
        $test = Tests::find()
            ->where(['id' => $id])
            ->with([
                'questions',
                'results' => function($query) {
                    $query->with(['answers', 'user']);
                }
            ])
            ->one();
        
        if (!$test) {
            throw new \yii\web\NotFoundHttpException('Тест не найден');
        }
        
        $testCorrect = 0;
        $testIncorrect = 0;
        $totalScore = 0;
        $userStats = [];
        
        foreach ($test->results as $result) {
            $userCorrect = 0;
            $userIncorrect = 0;
            
            foreach ($result->answers as $answer) {
                if ($answer->is_correct) {
                    $testCorrect++;
                    $userCorrect++;
                } else {
                    $testIncorrect++;
                    $userIncorrect++;
                }
            }
            
            $userTotal = $userCorrect + $userIncorrect;
            $userRate = $userTotal > 0 ? round(($userCorrect / $userTotal) * 100, 1) : 0;
            $totalScore += $result->score;
            
            $userStats[] = [
                'user' => $result->user,
                'correct' => $userCorrect,
                'incorrect' => $userIncorrect,
                'rate' => $userRate,
                'score' => $result->score,
                'date' => date('d.m.Y H:i', $result->created_at),
            ];
        }
        
        $testTotal = $testCorrect + $testIncorrect;
        $testRate = $testTotal > 0 ? round(($testCorrect / $testTotal) * 100, 1) : 0;
        $avgScore = count($test->results) > 0 ? round($totalScore / count($test->results), 1) : 0;
        
        return $this->render('test-detail', [
            'test' => $test,
            'testCorrect' => $testCorrect,
            'testIncorrect' => $testIncorrect,
            'testTotal' => $testTotal,
            'testRate' => $testRate,
            'avgScore' => $avgScore,
            'userStats' => $userStats,
        ]);
    }
    
    public function actionUserDetail($id)
    {
        $user = User::find()
            ->where(['id' => $id, 'role' => 0])
            ->with([
                'results' => function($query) {
                    $query->with(['test', 'answers']);
                }
            ])
            ->one();
        
        if (!$user) {
            throw new \yii\web\NotFoundHttpException('Сотрудник не найден');
        }
        
        $userCorrect = 0;
        $userIncorrect = 0;
        $testStats = [];
        $totalScore = 0;
        
        foreach ($user->results as $result) {
            $testCorrect = 0;
            $testIncorrect = 0;
            
            foreach ($result->answers as $answer) {
                if ($answer->is_correct) {
                    $userCorrect++;
                    $testCorrect++;
                } else {
                    $userIncorrect++;
                    $testIncorrect++;
                }
            }
            
            $testTotal = $testCorrect + $testIncorrect;
            $testRate = $testTotal > 0 ? round(($testCorrect / $testTotal) * 100, 1) : 0;
            $totalScore += $result->score;
            
            $testStats[] = [
                'test' => $result->test,
                'correct' => $testCorrect,
                'incorrect' => $testIncorrect,
                'rate' => $testRate,
                'score' => $result->score,
                'date' => date('d.m.Y H:i', $result->created_at),
            ];
        }
        
        $userTotal = $userCorrect + $userIncorrect;
        $userRate = $userTotal > 0 ? round(($userCorrect / $userTotal) * 100, 1) : 0;
        $avgScore = count($user->results) > 0 ? round($totalScore / count($user->results), 1) : 0;
        
        return $this->render('user-detail', [
            'user' => $user,
            'userCorrect' => $userCorrect,
            'userIncorrect' => $userIncorrect,
            'userTotal' => $userTotal,
            'userRate' => $userRate,
            'avgScore' => $avgScore,
            'testStats' => $testStats,
            'totalResults' => count($user->results),
        ]);
    }
}