<?php
use yii\helpers\Html;
use app\models\Results;
$this->title = 'Личный кабинет';
?>

<div class="cabinet-index">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4><?= Html::encode($this->title) ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Имя:</strong> <?= Html::encode($user->name) ?></p>
                    <p><strong>Email:</strong> <?= Html::encode($user->email) ?></p>
                    <?php if (!$user->isAdmin()): ?>
                        <p><strong>Роль:</strong> Сотрудник</p>
                    <?php elseif ($user->isAdmin()): ?>
                        <p><strong>Роль:</strong> Администратор</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="btn-group" role="group">
                        <?php if ($user->isAdmin()): ?>
                            <h4>Панель администрирования</h4>
                        <?php else: ?>
                            <button type="button" class="btn btn-dt" onclick="showTab('tests')">Тесты</button>
                            <button type="button" class="btn btn-outline-dt" onclick="showTab('results')">Результаты</button>
                        <?php endif; ?>
                    </div>
                    <?= Html::a('Выйти', ['/auth/logout'], [
                        'class' => 'btn btn-dt',
                        'data' => ['method' => 'post'],
                        'style' => 'width: 100px'
                    ]) ?>
                </div>
                <div class="card-body">
                    <?php if ($user->isAdmin()): ?>
                        <div id="admin-tab">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Тесты</h5>
                                            <p class="card-text">Создание и редактирование тестов</p>
                                            <?= Html::a('Управление тестами', ['admin/tests'], ['class' => 'btn btn-primary']) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Аналитика и отчётность</h5>
                                            <p class="card-text">Просмотр общей статистики</p>
                                            <?= Html::a('Просмотр статистики', ['analytics/default'], ['class' => 'btn btn-primary']) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 offset-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Роли</h5>
                                            <p class="card-text">изменение ролей сотрудников</p>
                                            <?= Html::a('Управление ролями', ['admin/user'], ['class' => 'btn btn-primary']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="results-tab" style="display: none;">                            
                            <?php if ($allResults): ?>
                                <div class="list-group">
                                    <?php foreach ($allResults as $result): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1"><?= Html::encode($result->test->title) ?></h6>
                                                <small><?= date('d.m.Y H:i', $result->created_at) ?></small>
                                            </div>
                                            <p class="mb-1">
                                                Пользователь: <strong><?= Html::encode($result->user->name) ?></strong><br>
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
                        </div>
                    
                    <?php else: ?>
                        <div id="tests-tab">
                            <?php if ($tests): ?>
                                <div class="row">
                                    <?php foreach ($tests as $test): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?= Html::encode($test->title) ?></h6>
                                                    <p class="card-text">
                                                        <small>Время: <?= $test->time_limit_minutes ?> мин</small><br>
                                                        <small>Вопросов: <?= count($test->questions) ?></small>
                                                    </p>
                                                    <?= Html::a('Начать тест', ['site/test', 'id' => $test->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <img src="./imgs/info.png" alt="Success" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> Нет доступных тестов.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div id="results-tab" style="display: none;">
                            <?php if ($results): ?>
                                <div class="list-group">
                                    <?php foreach ($results as $result): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1"><?= Html::encode($result->test->title) ?></h6>
                                                <small><?= date('d.m.Y H:i', $result->created_at) ?></small>
                                            </div>
                                            <p class="mb-1">
                                                Результат: <strong><?= $result->score ?></strong>/<?= count($result->test->questions) ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <img src="./imgs/info.png" alt="Success" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> Нет результатов тестов.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    <?php if ($user->isAdmin()): ?>
        document.getElementById('admin-tab').style.display = 'none';
    <?php else: ?>
        document.getElementById('tests-tab').style.display = 'none';
    <?php endif; ?>
    document.getElementById('results-tab').style.display = 'none';
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    const buttons = document.querySelectorAll('.card-header .btn-group .btn');
    buttons.forEach(btn => {
        btn.classList.remove('btn-dt');
        btn.classList.add('btn-outline-dt');
    });
    event.target.classList.remove('btn-outline-dt');
    event.target.classList.add('btn-dt');
}
</script>