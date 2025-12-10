<?php
use yii\helpers\Html;

if (isset($result)) {
    $this->title = 'Результаты теста: ' . $result->test->title;
    $userName = isset($result->user) ? $result->user->name : 'Неизвестно';
    ?>
    <div class="results-view">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?= Html::encode($this->title) ?></h4>
                <?php if (Yii::$app->user->identity->isAdmin()): ?>
                 <h4 class="mb-0">Имя пользователя: <?= Html::encode($userName) ?></h4>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Информация о тесте</h5>
                        <p><strong>Тест:</strong> <?= Html::encode($result->test->title) ?></p>
                        <p><strong>Дата прохождения:</strong> <?= date('d.m.Y H:i', $result->created_at) ?></p>
                        <?php if (Yii::$app->user->identity->isAdmin()): ?>
                            <p><strong>Сотрудник:</strong> <?= Html::encode($userName) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h5>Результат</h5>
                        <p><strong>Правильных ответов:</strong> 
                            <span class="badge bg-<?= count($questions) > 0 && ($result->score / count($questions)) >= 0.7 ? 'success' : (count($questions) > 0 && ($result->score / count($questions)) >= 0.5 ? 'warning' : 'danger') ?>">
                                <?= $result->score ?> из <?= count($questions) ?>
                            </span>
                        </p>
                        <p><strong>Процент:</strong> 
                            <?= count($questions) > 0 ? round(($result->score / count($questions)) * 100, 1) : 0 ?>%
                        </p>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Ответы на вопросы</h5>
                
                <?php $questionNumber = 1; ?>
                <?php foreach ($questions as $question): ?>
                    <?php 
                    $userAnswer = $userAnswers[$question->id] ?? null;
                    $userAnswerArray = $userAnswer ? explode(', ', $userAnswer->answer_text) : [];
                    
                    $correctAnswers = [];
                    foreach ($question->answers as $answer) {
                        if ($answer->is_correct) {
                            $correctAnswers[] = $answer->answer_text;
                        }
                    }
                    
                    $isCorrect = false;
                    if ($question->type == 3) {
                        $isCorrect = true;
                    } elseif ($question->type == 1) {
                        $isCorrect = $userAnswer && in_array($userAnswer->answer_text, $correctAnswers);
                    } elseif ($question->type == 2) {
                        if ($userAnswer && count($userAnswerArray) == count($correctAnswers)) {
                            $allCorrect = true;
                            foreach ($userAnswerArray as $userAns) {
                                if (!in_array($userAns, $correctAnswers)) {
                                    $allCorrect = false;
                                    break;
                                }
                            }
                            $isCorrect = $allCorrect;
                        }
                    }
                    ?>
                    
                    <div class="card mb-3 border-<?= $isCorrect ? 'success' : 'danger' ?>">
                        <div class="card-header bg-<?= $isCorrect ? 'success' : 'danger' ?> text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Вопрос <?= $questionNumber ?>: <?= Html::encode($question->question_text) ?>
                                    <small class="text-light">(<?= $question->getTypeName() ?>)</small>
                                </h6>
                                <span class="badge bg-light text-<?= $isCorrect ? 'success' : 'danger' ?>">
                                    <?= $isCorrect ? 'Правильно' : 'Неправильно' ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Ваш ответ:</strong></p>
                                        <div class="alert alert-<?= $isCorrect ? 'success' : 'danger' ?> mb-0">
                                            <?php if ($userAnswer): ?>
                                                <?php if ($question->type == 2): ?>
                                                    <?= implode(', ', $userAnswerArray) ?>
                                                <?php else: ?>
                                                    <?= Html::encode($userAnswer->answer_text) ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <em>Нет ответа</em>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Правильные ответы:</strong></p>
                                        <div class="alert alert-success mb-0">
                                            <?php if ($question->type == 3): ?>
                                                <em>Текстовый ответ - всегда правильный(наусмотрение)</em>
                                            <?php else: ?>
                                                <?= implode(', ', $correctAnswers) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <p class="mb-1"><strong>Все варианты ответов:</strong></p>
                                <div class="list-group">
                                    <?php foreach ($question->answers as $answer): ?>
                                        <?php 
                                        $isUserSelected = false;
                                        if ($userAnswer) {
                                            if ($question->type == 2) {
                                                $isUserSelected = in_array($answer->answer_text, $userAnswerArray);
                                            } else {
                                                $isUserSelected = ($userAnswer->answer_text == $answer->answer_text);
                                            }
                                        }
                                        ?>
                                        <div class="list-group-item <?= $answer->is_correct ? 'list-group-item-success' : ($isUserSelected ? 'list-group-item-danger' : '') ?>">
                                            <div class="form-check">
                                                <?php if ($question->type == 1): ?>
                                                    <input class="form-check-input" type="radio" 
                                                        <?= $isUserSelected ? 'checked' : '' ?>
                                                        disabled>
                                                <?php elseif ($question->type == 2): ?>
                                                    <input class="form-check-input" type="checkbox" 
                                                        <?= $isUserSelected ? 'checked' : '' ?>
                                                        disabled>
                                                <?php endif; ?>
                                                <label class="form-check-label w-100">
                                                    <?= Html::encode($answer->answer_text) ?>
                                                    <?php if ($answer->is_correct): ?>
                                                        <span class="badge bg-success float-end">✓ Правильный ответ</span>
                                                    <?php elseif ($isUserSelected && !$answer->is_correct): ?>
                                                        <span class="badge bg-danger float-end">✗ Выбран неправильно</span>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php $questionNumber++; ?>
                <?php endforeach; ?>

                <div class="mt-4 text-center">
                    <div class="btn-group" role="group">
                        <?= Html::a('Вернуться к результатам', ['site/results'], ['class' => 'btn btn-dt', 'style'=>'width: 250px']) ?>
                        <?php if (Yii::$app->user->identity->isAdmin()): ?>
                            <?= Html::a('Назад в кабинет', ['site/cabinet'], ['class' => 'btn btn-outline-dt', 'style'=>'width: 150px']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    $this->title = 'Мои результаты тестов';
    ?>
    <div class="results-index">
        <div class="card">
            <div class="card-header">
                <h4><?= Html::encode($this->title) ?></h4>
            </div>
            <div class="card-body">
                <?php if ($results): ?>
                    <div class="list-group">
                        <?php foreach ($results as $result): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <?= Html::a(Html::encode($result->test->title), ['site/results', 'id' => $result->id], [
                                                'class' => 'text-decoration-none'
                                            ]) ?>
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <small class="me-3"><?= date('d.m.Y H:i', $result->created_at) ?></small>
                                        <?= Html::a('Просмотр', ['site/results', 'id' => $result->id], [
                                            'class' => 'btn btn-sm btn-outline-primary'
                                        ]) ?>
                                    </div>
                                </div>
                                <p class="mb-1 mt-2">
                                    Результат: <strong><?= $result->score ?></strong>/<?= count($result->test->questions) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Нет результатов тестов.
                    </div>
                <?php endif; ?>
                
                <div class="mt-4 text-center">
                    <?= Html::a('Назад в кабинет', ['site/cabinet'], ['class' => 'btn btn-dt']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}