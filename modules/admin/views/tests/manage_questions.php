<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Управление вопросами: ' . $test->title;
?>
<div class="tests-questions">

    <div class="card">
        <div class="card-header">
            <h1 class="card-title"><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Добавить вопрос</h4>
                        </div>
                        <div class="card-body">
                            <?php $form = ActiveForm::begin(); ?>

                            <?= $form->field($question, 'question_text')->textarea(['rows' => 3])->label('Текст вопроса') ?>
                            
                            <?= $form->field($question, 'type')->dropDownList([
                                1 => 'Один вариант ответа',
                                2 => 'Несколько вариантов',
                                3 => 'Текстовый ответ'
                            ])->label('Тип вопроса') ?>

                            <div class="form-group">
                                <?= Html::submitButton('Добавить вопрос', ['class' => 'btn btn-dt', 'style'=>"width: 160px"]) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Список вопросов (<?= count($questions) ?>)</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($questions): ?>
                                <div class="list-group">
                                    <?php foreach ($questions as $q): ?>
                                        <div class="list-group-item mb-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <strong><?= Html::encode($q->question_text) ?></strong><br>
                                                    <small class="text-muted">
                                                        Тип: <?= $q->getTypeName() ?>
                                                        <?php $answerCount = count($q->answers); ?>
                                                        <?php if ($answerCount > 0): ?>
                                                            • Ответов: <?= $answerCount ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <div class="btn-group btn-group-sm ml-2">
                                                    <?= Html::a('Ответы', ['questions/view', 'id' => $q->id], [
                                                        'class' => 'btn btn-dt'
                                                    ]) ?>
                                                    <?= Html::a('Удалить', ['delete-question', 'id' => $test->id, 'question_id' => $q->id], [
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
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <img src="./imgs/info.png" alt="Success" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> Вопросов пока нет.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4" style="display: flex;">
                <?= Html::a('Завершить', ['view', 'id' => $test->id], [
                    'class' => 'btn btn-dt', 
                    'style' => 'width: 110px; border-radius: 5px 0 0 5px; margin-right: 0; border-right: 0;'
                ]) ?>
                <?= Html::a('Вернуться к тестам', ['index'], [
                    'class' => 'btn btn-outline-dt', 
                    'style' => 'width: 110px; border-radius: 0 5px 5px 0; margin-left: 0; width: 180px'
                ]) ?>
            </div>
        </div>
    </div>

</div>