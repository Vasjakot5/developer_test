<?php

use app\models\Results;
use app\models\Tests;
use app\models\User;
use yii\helpers\Html;

$this->title = 'Аналитика тестирования';

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
    $totalCorrect += $result->score;
    $totalIncorrect += ($testQuestionCount - $result->score);
}

$totalAnswers = $totalCorrect + $totalIncorrect;

$successRate = 0;
$correctPercentage = 0;
$incorrectPercentage = 0;

if ($totalAnswers > 0) {
    $successRate = round(($totalCorrect / $totalAnswers) * 100, 1);
    $correctPercentage = ($totalCorrect / $totalAnswers) * 100;
    $incorrectPercentage = ($totalIncorrect / $totalAnswers) * 100;
}

$tests = Tests::find()
    ->with([
        'questions',
        'results'
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
            $query->with(['test.questions']);
        }
    ])
    ->all();

?>

<div class="analytics-default-index">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h4><?= Html::encode($this->title) ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Всего сотрудников:</strong> <?= $totalUsers ?></p>
                    <p><strong>Всего тестов:</strong> <?= $totalTests ?></p>
                    <p><strong>Пройдено тестов:</strong> <?= $totalResults ?></p>
                    <p><strong>Активных сотрудников:</strong> <?= $activeUsers ?> (<?= $activityRate ?>%)</p>
                </div>
            </div>
            
            <?php if ($totalAnswers > 0): ?>
            <div class="card">
                <div class="card-header">
                    <h5>Распределение ответов</h5>
                </div>
                <div class="card-body text-center">
                    <div style="position: relative; width: 200px; height: 200px; margin: 0 auto;">
                        <div style="
                            width: 200px;
                            height: 200px;
                            border-radius: 50%;
                            background: conic-gradient(
                                #28a745 0deg <?= $correctPercentage * 3.6 ?>deg,
                                #dc3545 <?= $correctPercentage * 3.6 ?>deg 360deg
                            );
                        "></div>
                        <div style="
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            background: white;
                            width: 120px;
                            height: 120px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-direction: column;
                        ">
                            <div style="font-size: 28px; font-weight: bold; color: #495057;"><?= $successRate ?>%</div>
                            <div style="font-size: 12px; color: #6c757d;">успеваемость</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: center; gap: 30px; margin-top: 20px;">
                        <div>
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <div style="width: 20px; height: 20px; background: #28a745; margin-right: 10px; border-radius: 3px;"></div>
                                <span>Правильные (<?= $totalCorrect ?>)</span>
                            </div>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 20px; height: 20px; background: #dc3545; margin-right: 10px; border-radius: 3px;"></div>
                                <span>Неправильные (<?= $totalIncorrect ?>)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="btn-group mb-2 mb-md-0" role="group">
                        <button type="button" class="btn btn-dt active" onclick="showTab('summary')">Сводка</button>
                        <button type="button" class="btn btn-outline-dt" onclick="showTab('tests')">По тестам</button>
                        <button type="button" class="btn btn-outline-dt" onclick="showTab('users')">По сотрудникам</button>
                    </div>
                    <?= Html::a('Формирование отчётов', ['default/export'], [
                        'class' => 'btn btn-dt w-md-auto',
                        'style' => 'width: 200px;'
                    ]) ?>
                </div>
                
                <div class="card-body">
                    <div id="summary-tab">
                        <h5>Общие показатели</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Активность</h6>
                                        <p class="card-text" style="font-size: 32px; font-weight: bold; color: #007bff;">
                                            <?= $totalUsers > 0 ? round($totalResults / $totalUsers, 1) : 0 ?>
                                        </p>
                                        <small>тестов на сотрудника</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Качество</h6>
                                        <p class="card-text" style="font-size: 32px; font-weight: bold; color: #28a745;">
                                            <?= $successRate ?>%
                                        </p>
                                        <small>правильных ответов</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Охват</h6>
                                        <p class="card-text" style="font-size: 32px; font-weight: bold; color: #ffc107;">
                                            <?= 
                                                ($totalUsers > 0 && $totalTests > 0) 
                                                ? round(($totalResults / ($totalTests * $totalUsers)) * 100, 1) 
                                                : 0 
                                            ?>%
                                        </p>
                                        <small>от максимального</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Ошибки</h6>
                                        <p class="card-text" style="font-size: 32px; font-weight: bold; color: #dc3545;">
                                            <?= $totalIncorrect ?>
                                        </p>
                                        <small>неправильных ответов</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">Прохождение тестов</h5>
                        <?php if (!empty($tests)): ?>
                            <?php foreach ($tests as $test): ?>
                                <?php
                                $resultsCount = count($test->results);
                                $maxUsers = $totalUsers > 0 ? $totalUsers : 1;
                                $progress = min(($resultsCount / $maxUsers) * 100, 100);
                                ?>
                                <div class="mb-3">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span><?= Html::encode($test->title) ?></span>
                                        <span><?= $resultsCount ?>/<?= $totalUsers ?></span>
                                    </div>
                                    <div style="height: 10px; background: #e9ecef; border-radius: 5px; overflow: hidden;">
                                        <div style="height: 100%; width: <?= $progress ?>%; background: #17a2b8;"></div>
                                    </div>
                                    <div style="font-size: 12px; color: #6c757d; margin-top: 2px;">
                                        <?= round($progress) ?>% сотрудников прошли
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <img src="./imgs/info.png" alt="Info" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> 
                                Нет данных о тестах
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div id="tests-tab" style="display: none;">
                        <?php if (!empty($tests)): ?>
                            <div class="list-group">
                                <?php foreach ($tests as $test): ?>
                                    <?php
                                    $testCorrect = 0;
                                    $testIncorrect = 0;
                                    $testQuestionCount = count($test->questions);
                                    $testTotalAttempts = count($test->results);

                                    foreach ($test->results as $result) {
                                        $testCorrect += $result->score;
                                        $testIncorrect += ($testQuestionCount - $result->score);
                                    }
                                    
                                    $testTotal = $testCorrect + $testIncorrect;
                                    $testRate = $testTotal > 0 ? round(($testCorrect / $testTotal) * 100, 1) : 0;
                                    ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-1"><?= Html::encode($test->title) ?></h6>
                                            <span class="badge badge-primary"><?= $testQuestionCount ?> вопросов</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <small>Пройдено: <?= $testTotalAttempts ?> раз</small>
                                            <small>Лимит: <?= $test->time_limit_minutes ?> мин</small>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small>Правильных: <strong><?= $testCorrect ?></strong></small>
                                                <div style="height: 5px; background: #e9ecef; border-radius: 3px; overflow: hidden; margin-top: 2px;">
                                                    <div style="height: 100%; width: <?= $testTotal > 0 ? ($testCorrect / $testTotal * 100) : 0 ?>%; background: #28a745;"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <small>Неправильных: <strong><?= $testIncorrect ?></strong></small>
                                                <div style="height: 5px; background: #e9ecef; border-radius: 3px; overflow: hidden; margin-top: 2px;">
                                                    <div style="height: 100%; width: <?= $testTotal > 0 ? ($testIncorrect / $testTotal * 100) : 0 ?>%; background: #dc3545;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small>Успеваемость: <strong><?= $testRate ?>%</strong></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <img src="./imgs/info.png" alt="Info" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> 
                                Нет данных о тестах
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div id="users-tab" style="display: none;">
                        <?php if (!empty($users)): ?>
                            <div class="list-group">
                                <?php foreach ($users as $user): ?>
                                    <?php
                                    $userResults = $user->results;
                                    $userCorrect = 0;
                                    $userIncorrect = 0;
                                    $userScore = 0;
                                    $userTotalQuestions = 0;

                                    foreach ($userResults as $result) {
                                        $testQuestionCount = count($result->test->questions);
                                        $userTotalQuestions += $testQuestionCount;
                                        $userScore += $result->score;
                                        $userCorrect += $result->score;
                                        $userIncorrect += ($testQuestionCount - $result->score);
                                    }

                                    $userTotal = $userCorrect + $userIncorrect;
                                    $userRate = $userTotal > 0 ? round(($userCorrect / $userTotal) * 100, 1) : 0;
                                    $avgScore = count($userResults) > 0 ? round($userScore / count($userResults), 1) : 0;
                                    ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?= Html::encode($user->name ?? $user->email) ?></h6>
                                                <small class="text-muted"><?= $user->email ?></small>
                                            </div>
                                            <span class="badge badge-info">Тестов: <?= count($userResults) ?></span>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4">
                                                <small>Средний балл: <strong><?= $avgScore ?></strong></small>
                                            </div>
                                            <div class="col-md-4">
                                                <small>Успеваемость: <strong><?= $userRate ?>%</strong></small>
                                            </div>
                                            <div class="col-md-4">
                                                <small>Ответов: <strong><?= $userTotal ?></strong></small>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-6">
                                                <div style="height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden; margin-top: 5px;">
                                                    <div style="height: 100%; width: <?= $userRate ?>%; background: #28a745;"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <small class="text-muted">Последний: <?= !empty($userResults) ? date('d.m.Y', end($userResults)->created_at) : '—' ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <img src="./imgs/info.png" alt="Info" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> 
                                Нет данных о сотрудниках
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    document.getElementById('summary-tab').style.display = 'none';
    document.getElementById('tests-tab').style.display = 'none';
    document.getElementById('users-tab').style.display = 'none';
    
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    const buttons = document.querySelectorAll('.card-header .btn-group .btn');
    buttons.forEach(btn => {
        btn.classList.remove('btn-dt', 'active');
        btn.classList.add('btn-outline-dt');
    });
    
    event.target.classList.remove('btn-outline-dt');
    event.target.classList.add('btn-dt', 'active');
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('summary-tab').style.display = 'block';
});
</script>