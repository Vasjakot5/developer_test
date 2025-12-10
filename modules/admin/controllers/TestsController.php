<?php

namespace app\modules\admin\controllers;

use app\models\Tests;
use app\models\TestsSearch;
use app\models\Questions;
use app\models\Answers;
use app\models\UserAnswers;
use app\models\Results;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use Yii;

class TestsController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => \yii\filters\AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->identity->IsAdmin();
                            }
                        ],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new TestsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $questions = $model->questions;
        
        return $this->render('view', [
            'model' => $model,
            'questions' => $questions,
        ]);
    }

    public function actionCreate()
    {
        $model = new Tests();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->created_at = time();
                $model->updated_at = time();
                
                if ($model->save()) {
                    return $this->redirect(['manage-questions', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->updated_at = time();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionManageQuestions($id)
    {
        $test = $this->findModel($id);
        $question = new Questions();
        $questions = $test->questions;

        if ($this->request->isPost && $question->load($this->request->post())) {
            $question->test_id = $id;
            if ($question->save()) {
                return $this->redirect(['manage-questions', 'id' => $id]);
            }
        }

        return $this->render('manage_questions', [
            'test' => $test,
            'question' => $question,
            'questions' => $questions,
        ]);
    }

    public function actionDeleteQuestion($id, $question_id)
    {
        $question = Questions::findOne($question_id);
        if ($question && $question->test_id == $id) {
            UserAnswers::deleteAll(['question_id' => $question_id]);
            Answers::deleteAll(['question_id' => $question_id]);
            $question->delete();
        }
        
        return $this->redirect(['manage-questions', 'id' => $id]);
    }

    public function actionDelete($id)
    {
        $test = $this->findModel($id);
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $questions = Questions::find()->where(['test_id' => $id])->all();
            
            foreach ($questions as $question) {
                UserAnswers::deleteAll(['question_id' => $question->id]);
                
                Answers::deleteAll(['question_id' => $question->id]);
            }
            
            Questions::deleteAll(['test_id' => $id]);
            
            Results::deleteAll(['test_id' => $id]);
            
            $test->delete();
            
            $transaction->commit();
            
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
        
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Tests::findOne($id)) !== null) {
            return $model;
        }
        
        throw new NotFoundHttpException('Тест не найден.');
    }
}