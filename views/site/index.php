<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\Test[] $tests */
/** @var app\models\Results[] $recentResults */
/** @var app\models\Results[] $recentAllResults */

// Для админа добавляем переменные из аналитики
if ($user->isAdmin()) {
    /** @var int $totalUsers */
    /** @var int $totalTests */
    /** @var int $totalResults */
    /** @var int $totalCorrect */
    /** @var int $totalIncorrect */
    /** @var float $successRate */
}

$this->title = 'Главная страница';
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-3 mb-3">
        <h1>Добро пожаловать, <?= Html::encode($user->name) ?>!</h1>
        
        <div class="row justify-content-center mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Статус</h5>
                        <p class="card-text">
                            <?php if (!$user->isAdmin()): ?>
                                <span class="badge bg-primary">Сотрудник</span>
                            <?php elseif ($user->isAdmin()): ?>
                                <span class="badge bg-danger">Администратор</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Email</h5>
                        <p class="card-text"><?= Html::encode($user->email) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="body-content">
        <?php if (!$user->isAdmin()): ?>
            <div class="row">
                <div class="col-lg-8 offset-lg-2 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 text-center">Последние результаты</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recentResults)): ?>
                                <div class="list-group">
                                    <?php foreach ($recentResults as $result): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1"><?= Html::encode($result->test->title) ?></h6>
                                                <small><?= date('d.m.Y', $result->created_at) ?></small>
                                            </div>
                                            <p class="mb-0">
                                                Результат: <strong><?= $result->score ?></strong>/<?= count($result->test->questions) ?>
                                            </p>
                                            <div class="mt-2">
                                                <div style="height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                                    <?php 
                                                    $percentage = count($result->test->questions) > 0 ? ($result->score / count($result->test->questions) * 100) : 0;
                                                    $color = $percentage >= 70 ? '#28a745' : ($percentage >= 50 ? '#ffc107' : '#dc3545');
                                                    ?>
                                                    <div style="height: 100%; width: <?= $percentage ?>%; background: <?= $color ?>;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger text-center">
                                    <img src="./imgs/info.png" alt="Info" style="vertical-align: middle; margin-bottom: 5px; height: 20px;">
                                    Нет результата последнего теста.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Общая статистика</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Всего сотрудников:</strong> <?= $totalUsers ?></p>
                            <p><strong>Всего тестов:</strong> <?= $totalTests ?></p>
                            <p><strong>Пройдено тестов:</strong> <?= $totalResults ?></p>
                            
                            <?php if (isset($activeUsers)): ?>
                            <p><strong>Активных сотрудников:</strong> <?= $activeUsers ?> (<?= $activityRate ?? 0 ?>%)</p>
                            <?php endif; ?>
                            
                            <div class="text-center mt-4">
                                <?= Html::a('Формирование отчётов', ['analytics/default/export'], [
                                    'class' => 'btn btn-dt',
                                    'style' => 'width: 200px;'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Последние результаты тестов</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recentAllResults)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Тест</th>
                                                <th>Пользователь</th>
                                                <th>Результат</th>
                                                <th>Дата</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentAllResults as $result): ?>
                                                <tr>
                                                    <td><?= Html::encode($result->test->title) ?></td>
                                                    <td><?= Html::encode($result->user->name) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= count($result->test->questions) > 0 && ($result->score / count($result->test->questions)) >= 0.7 ? 'success' : (count($result->test->questions) > 0 && ($result->score / count($result->test->questions)) >= 0.5 ? 'warning' : 'danger') ?>">
                                                            <?= $result->score ?>/<?= count($result->test->questions) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('d.m.Y H:i', $result->created_at) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <?= Html::a('Показать все результаты', ['analytics/default'], ['class' => 'btn btn-dt', 'style'=>'width: 200px']) ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger text-center">
                                    <img src="./imgs/info.png" alt="Info" style="vertical-align: middle; margin-bottom: 5px; height: 20px;">
                                    Нет результатов тестов.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>