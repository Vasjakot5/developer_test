<?php

namespace app\modules\admin\controllers;

use app\models\Questions;
use app\models\QuestionsSearch;
use app\models\Answers;
use app\models\UserAnswers;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class QuestionsController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new QuestionsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $answers = $model->answers;
        $answer = new Answers();

        if ($this->request->isPost && $answer->load($this->request->post())) {
            $answer->question_id = $id;
            if ($answer->save()) {
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('view', [
            'model' => $model,
            'answers' => $answers,
            'answer' => $answer,
        ]);
    }

    public function actionCreate($test_id)
    {
        $model = new Questions();
        $model->test_id = $test_id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $question = $this->findModel($id);
        $test_id = $question->test_id;
        $question->delete();

        return $this->redirect(['tests/manage-questions', 'id' => $test_id]);
    }

    public function actionDeleteAnswer($id, $answer_id)
    {
        $answer = Answers::findOne($answer_id);
        if ($answer && $answer->question_id == $id) {
            $answer->delete();
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    protected function findModel($id)
    {
        if (($model = Questions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Вопрос не найден.');
    }
}