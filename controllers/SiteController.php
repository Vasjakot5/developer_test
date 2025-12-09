<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Tests;
use app\models\TestForm;
use app\models\Questions;
use app\models\Results;
use app\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'cabinet', 'test', 'login', 'register'],
                'rules' => [
                    [
                        'actions' => ['index', 'cabinet', 'test'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/auth/login']);
        }

        $user = Yii::$app->user->identity;
        
        if ($user->isAdmin()) {
            $recentAllResults = Results::find()
                ->with(['test', 'user'])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(5)
                ->all();
            
            $totalUsers = User::find()->where(['role' => 0])->count();
            $totalTests = Tests::find()->count();
            $totalResults = Results::find()->count();
            
            $results = Results::find()->with('answers')->all();
            $totalCorrect = 0;
            $totalIncorrect = 0;
            
            foreach ($results as $result) {
                foreach ($result->answers as $answer) {
                    if ($answer->is_correct) {
                        $totalCorrect++;
                    } else {
                        $totalIncorrect++;
                    }
                }
            }
            
            $totalAnswers = $totalCorrect + $totalIncorrect;
            $successRate = $totalAnswers > 0 ? round(($totalCorrect / $totalAnswers) * 100, 1) : 0;
            
            return $this->render('index', [
                'user' => $user,
                'recentAllResults' => $recentAllResults,
                'totalUsers' => $totalUsers,
                'totalTests' => $totalTests,
                'totalResults' => $totalResults,
                'totalCorrect' => $totalCorrect,
                'totalIncorrect' => $totalIncorrect,
                'successRate' => $successRate,
            ]);
        }
        
        $tests = Tests::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(3)
            ->all();
        
        $recentResults = Results::find()
            ->where(['user_id' => $user->id])
            ->with('test')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
            ->all();
        
        return $this->render('index', [
            'user' => $user,
            'tests' => $tests,
            'recentResults' => $recentResults,
        ]);
    }

    public function actionCabinet()
    {
        $completed = Results::find()
            ->select('test_id')
            ->where(['user_id' => Yii::$app->user->id])
            ->column();
        
        $tests = Tests::find()
            ->where(['NOT IN', 'id', $completed])
            ->all();
            
        $results = Results::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with('test')
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $allResults = Results::find()
            ->with(['test', 'user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(20)
            ->all();

        return $this->render('cabinet', [
            'tests' => $tests,
            'results' => $results,
            'user' => Yii::$app->user->identity,
            'allResults' => $allResults,
        ]);
    }

    public function actionTest($id)
    {
        $test = Tests::findOne($id);
        if (!$test) {
            throw new NotFoundHttpException('Тест не найден.');
        }

        $questions = Questions::find()
            ->where(['test_id' => $id])
            ->with('answers')
            ->all();

        $model = new TestForm();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            
            if ($model->submitTest($id, Yii::$app->user->id)) {
                Yii::$app->session->setFlash('success', 
                    '<img src="./imgs/success.png" alt="Success" style="vertical-align: middle; margin-right: 10px; height: 20px;">' . 
                    'Тест завершен!');
                return $this->redirect(['cabinet']);
            } 
        }

        return $this->render('test', [
            'test' => $test,
            'questions' => $questions,
            'model' => $model,
        ]);
    }

    public function actionResults()
    {
        $results = Results::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with('test')
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('results', [
            'results' => $results,
        ]);
    }
}
