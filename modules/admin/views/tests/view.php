<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
?>
<div class="tests-view">

    <div class="card">
        <div class="card-header">
            <h1 class="card-title"><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="card-body">
            <div class="mb-4" style="display: flex;">
                <?= Html::a('Редактировать', ['update', 'id' => $model->id], [
                    'class' => 'btn btn-dt', 
                    'style' => 'width:150px; border-radius: 5px 0 0 5px; margin-right: 0; border-right: 0;'
                ]) ?>
                <?= Html::a('Управление вопросами', ['manage-questions', 'id' => $model->id], [
                    'class' => 'btn btn-outline-dt',
                    'style' => 'width:220px; border-radius: 0; margin-right: 0; border-right: 0; border-left: 0;'
                ]) ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'style' => 'border-radius: 0 5px 5px 0; margin-left: 0;',
                    'data' => [
                        'confirm' => 'Удалить этот тест?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Информация о тесте</h4>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped table-bordered detail-view'],
                        'attributes' => [
                            'title',
                            'time_limit_minutes',
                            [
                                'attribute' => 'created_at',
                                'value' => function($model) {
                                    return date('d.m.Y H:i', $model->created_at);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'updated_at',
                                'value' => function($model) {
                                    return date('d.m.Y H:i', $model->updated_at);
                                },
                                'format' => 'raw',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Вопросы теста (<?= count($questions) ?>)</h4>
                </div>
                <div class="card-body">
                    <?php if ($questions): ?>
                        <div class="list-group">
                            <?php foreach ($questions as $question): ?>
                                <div class="list-group-item mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <strong><?= Html::encode($question->question_text) ?></strong><br>
                                            <small class="text-muted">
                                                Тип: <?= $question->getTypeName() ?>
                                                <?php $answerCount = count($question->answers); ?>
                                                <?php if ($answerCount > 0): ?>
                                                    • Ответов: <?= $answerCount ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="btn-group btn-group-sm ml-2">
                                            <?= Html::a('Ответы', ['questions/view', 'id' => $question->id], [
                                                'class' => 'btn btn-dt'
                                            ]) ?>
                                            <?= Html::a('Удалить', ['questions/delete', 'id' => $question->id], [
                                                'class' => 'btn btn-danger',
                                                'data' => [
                                                    'confirm' => 'Удалить этот вопрос?',
                                                    'method' => 'post',
                                                ],
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Вопросов пока нет.</p>
                        <div class="mt-2">
                            <?= Html::a('Добавить вопросы', ['manage-questions', 'id' => $model->id], ['class' => 'btn btn-dt', 'style'=>'width: 180px']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4" style="display: flex;">
                <?= Html::a('Создать новый тест', ['create'], [
                    'class' => 'btn btn-dt', 
                    'style' => 'width:180px;border-radius: 5px 0 0 5px; margin-left: 0;'
                ]) ?>
                <?= Html::a('Вернуться к тестам', ['index'], [
                    'class' => 'btn btn-outline-dt', 
                    'style' => 'width:180px;border-radius: 0 5px 5px 0; margin-right: 0;'
                ]) ?>
            </div>
        </div>
    </div>

</div>