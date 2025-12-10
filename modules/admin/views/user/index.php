<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Управление ролями';

$totalUsers = $dataProvider->getTotalCount();
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($totalUsers == 0): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <img src="./imgs/info.png" alt="Success" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> Пользователей пока нет.
        </div>
    <?php endif; ?>

    <?php if ($totalUsers > 0): ?>
    <div class="card">
        <div class="card-header">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-dt" onclick="showTab('all')">Все пользователи</button>
                <button type="button" class="btn btn-outline-dt" onclick="showTab('admins')">Администраторы</button>
                <button type="button" class="btn btn-outline-dt" onclick="showTab('employees')">Сотрудники</button>
            </div>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <div id="all-tab">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dataProvider->getModels() as $model): ?>
                            <tr>
                                <td>
                                    <div class="user-name">
                                        <?= Html::encode($model->name) ?>
                                        <?php if (!empty($model->surname)): ?>
                                        <div class="text-muted small"><?= Html::encode($model->surname) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-email">
                                        <?= Html::encode($model->email) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php $roleText = $model->isAdmin() ? 'Администратор' : 'Сотрудник'; ?>
                                    <?php $badgeClass = $model->isAdmin() ? 'badge bg-danger' : 'badge bg-primary'; ?>
                                    <span class="<?= $badgeClass ?>"><?= $roleText ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn <?= !$model->isAdmin() ? 'btn-dt' : 'btn-outline-dt' ?>"
                                                onclick="toggleUserRole(<?= $model->id ?>, 0)">
                                            Сотрудник
                                        </button>
                                        <button type="button" 
                                                class="btn <?= $model->isAdmin() ? 'btn-dt' : 'btn-outline-dt' ?>"
                                                onclick="toggleUserRole(<?= $model->id ?>, 1)">
                                            Админ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($dataProvider->getPagination() && $dataProvider->getPagination()->pageCount > 1): ?>
                    <div class="d-flex justify-content-center mt-3 pb-3">
                        <?= \yii\widgets\LinkPager::widget([
                            'pagination' => $dataProvider->getPagination(),
                            'options' => ['class' => 'pagination mb-0'],
                            'linkOptions' => ['class' => 'page-link'],
                            'pageCssClass' => 'page-item',
                            'prevPageCssClass' => 'page-item',
                            'nextPageCssClass' => 'page-item',
                            'activePageCssClass' => 'active',
                            'disabledPageCssClass' => 'disabled',
                        ]); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div id="admins-tab" style="display: none;">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_filter($dataProvider->getModels(), function($model) { return $model->isAdmin(); }) as $model): ?>
                            <tr>
                                <td>
                                    <div class="user-name">
                                        <?= Html::encode($model->name) ?>
                                        <?php if (!empty($model->surname)): ?>
                                        <div class="text-muted small"><?= Html::encode($model->surname) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-email">
                                        <?= Html::encode($model->email) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger">Администратор</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-outline-dt"
                                                onclick="toggleUserRole(<?= $model->id ?>, 0)">
                                            Сотрудник
                                        </button>
                                        <button type="button" 
                                                class="btn btn-dt"
                                                onclick="toggleUserRole(<?= $model->id ?>, 1)">
                                            Админ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div id="employees-tab" style="display: none;">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_filter($dataProvider->getModels(), function($model) { return !$model->isAdmin(); }) as $model): ?>
                            <tr>
                                <td>
                                    <div class="user-name">
                                        <?= Html::encode($model->name) ?>
                                        <?php if (!empty($model->surname)): ?>
                                        <div class="text-muted small"><?= Html::encode($model->surname) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-email">
                                        <?= Html::encode($model->email) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">Сотрудник</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-dt"
                                                onclick="toggleUserRole(<?= $model->id ?>, 0)">
                                            Сотрудник
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-dt"
                                                onclick="toggleUserRole(<?= $model->id ?>, 1)">
                                            Админ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php if ($totalUsers > 0): ?>
<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Всего пользователей: <b><?= $dataProvider->getPagination()->getOffset() + $dataProvider->getCount() ?></b>
    </div>
</div>
<?php endif; ?>
<script>
function showTab(tabName) {
    document.getElementById('all-tab').style.display = 'none';
    document.getElementById('admins-tab').style.display = 'none';
    document.getElementById('employees-tab').style.display = 'none';
 
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    const buttons = document.querySelectorAll('.card-header .btn-group .btn');
    buttons.forEach(btn => {
        btn.classList.remove('btn-dt');
        btn.classList.add('btn-outline-dt');
    });
    event.target.classList.remove('btn-outline-dt');
    event.target.classList.add('btn-dt');
}

function toggleUserRole(userId, roleValue) {
    const isAdmin = roleValue == 1;
    const confirmMessage = isAdmin 
        ? 'Вы уверены, что хотите назначить пользователя администратором?' 
        : 'Вы уверены, что хотите изменить роль пользователя на сотрудника?';
    
    if (confirm(confirmMessage)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= Url::to(['update-status', 'id' => '']) ?>' + userId;
        form.style.display = 'none';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_csrf';
        csrfToken.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrfToken);
        
        const roleInput = document.createElement('input');
        roleInput.type = 'hidden';
        roleInput.name = 'User[role]';
        roleInput.value = roleValue;
        form.appendChild(roleInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const firstButton = document.querySelector('.card-header .btn-group .btn');
    if (firstButton) {
        firstButton.classList.remove('btn-outline-dt');
        firstButton.classList.add('btn-dt');
    }
});
</script>