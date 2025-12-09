<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

$this->title = 'Управление ответами: ' . substr($model->question_text, 0, 50) . '...';
?>
<style>
    .equal-height-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .equal-height-card .card-body {
        flex: 1;
    }
    .answers-container {
        max-height: 400px;
        overflow-y: auto;
        margin-bottom: 15px;
    }
</style>
<div class="questions-view">

    <div class="card">
        <div class="card-header">
            <h1 class="card-title"><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4 equal-height-card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Информация о вопросе</h4>
                        </div>
                        <div class="card-body">
                            <?= DetailView::widget([
                                'model' => $model,
                                'options' => ['class' => 'table table-striped table-bordered detail-view'],
                                'attributes' => [
                                    [
                                        'attribute' => 'question_text',
                                        'label' => 'Текст вопроса'
                                    ],
                                    [
                                        'attribute' => 'type',
                                        'value' => $model->getTypeName(),
                                        'label' => 'Тип вопроса'
                                    ],
                                ],
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card equal-height-card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Список ответов (<?= count($answers) ?>)</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($answers): ?>
                                <div class="answers-container">
                                    <div class="list-group">
                                        <?php foreach ($answers as $ans): ?>
                                            <div class="list-group-item <?= $ans->is_correct ? 'list-group-item-success' : '' ?>">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <?= Html::encode($ans->answer_text) ?>
                                                        <?php if ($ans->is_correct): ?>
                                                            <p class="mb-0"><small>Статус: верный</small></p>
                                                        <?php elseif (!$ans->is_correct): ?>
                                                            <p class="mb-0"><small>Статус: неверный</small></p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="btn-group btn-group-sm ml-2">
                                                        <?= Html::a('Удалить', ['delete-answer', 'id' => $model->id, 'answer_id' => $ans->id], [
                                                            'class' => 'btn btn-danger',
                                                            'data' => [
                                                                'confirm' => 'Удалить этот ответ?',
                                                                'method' => 'post'
                                                            ],
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <img src="./imgs/info.png" alt="Success" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> 
                                    Тестов пока нет
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Добавить ответ</h4>
                        </div>
                        <div class="card-body">
                            <?php $form = ActiveForm::begin(); ?>

                            <div class="row">
                                <div class="col-md-8">
                                    <?= $form->field($answer, 'answer_text')->textarea(['rows' => 2])->label('Текст ответа') ?>
                                </div>
                                <div class="col-md-4">
                                    <?php if ($model->type != 3): ?>
                                        <?= $form->field($answer, 'is_correct')->checkbox() ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= Html::submitButton('Добавить ответ', ['class' => 'btn btn-dt', 'style' => 'width: 160px']) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4" style="display: flex;">
                <?= Html::a('Вернуться к вопросам', ['tests/manage-questions', 'id' => $model->test_id], [
                    'class' => 'btn btn-dt', 
                    'style' => 'width: 200px; border-radius: 5px 0 0 5px; margin-right: 0; border-right: 0;'
                ]) ?>
                <?= Html::a('К тесту', ['tests/view', 'id' => $model->test_id], [
                    'class' => 'btn btn-outline-dt', 
                    'style' => 'width: 110px; border-radius: 0 5px 5px 0; margin-left: 0;'
                ]) ?>
            </div>
        </div>
    </div>
</div>