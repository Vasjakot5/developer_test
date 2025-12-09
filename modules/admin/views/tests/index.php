<?php

use app\models\Tests;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var app\models\TestsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Управление тестами';

$totalTests = $dataProvider->getTotalCount();
?>
<div class="tests-index">

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Создать тест', ['create'], ['class' => 'btn btn-dt', 'style'=>'width: 150px']) ?>
    </div>

    <?php if ($totalTests == 0): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <img src="./imgs/info.png" alt="Success" style="vertical-align: middle; margin-bottom: 5px; height: 20px;"> Тестов пока нет
        </div>
    <?php endif; ?>

    <div class="card1">
        <div class="card-body">
            <?php if ($totalTests > 0): ?>
            <div class="row">
                <?php foreach ($dataProvider->getModels() as $model): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="test-card">
                        <div class="test-card-header text-center">
                            <h5 class="test-title"><?= StringHelper::truncateWords(Html::encode($model->title), 8, '...') ?></h5>
                        </div>
                        
                        <div class="test-card-body">
                            <div class="test-meta-item">
                                <i class="fas fa-clock"></i> 
                                Время прохождения: <?= Html::encode($model->time_limit_minutes) ?> мин
                            </div>
                            
                            <div class="test-meta-item">
                                <i class="fas fa-calendar-plus"></i> 
                                Создан: <?= Yii::$app->formatter->asDate($model->created_at, 'dd.MM.yyyy') ?>
                            </div>
                            
                            <div class="test-meta-item">
                                <i class="fas fa-calendar-check"></i> 
                                Обновлен: <?= Yii::$app->formatter->asDate($model->updated_at, 'dd.MM.yyyy') ?>
                            </div>
                        </div>
                        
                        <div class="test-card-footer">
                            <div class="test-actions text-center" style="display: flex; justify-content: center;">
                                <div class="form-group" style="display: inline-flex; white-space: nowrap;">
                                    <?= Html::a('Просмотр', ['view', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-dt', 
                                        'style' => 'width: 100px; border-radius: 5px 0 0 5px; margin-right: 0; border-right: 0;'
                                    ]) ?>
                                    <?= Html::a('Редактировать', ['update', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-outline-dt', 
                                        'style' => 'width: 115px; border-radius: 0; margin: 0; border-right: 0; border-left: 0;'
                                    ]) ?>
                                    <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-danger',
                                        'style' => 'width: 100px; border-radius: 0 5px 5px 0; margin-left: 0;',
                                        'data' => [
                                            'confirm' => 'Вы уверены, что хотите удалить этот тест?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Всего тестов: <b><?= $dataProvider->getPagination()->getOffset() + $dataProvider->getCount() ?></b>
                </div>
                <?= \yii\widgets\LinkPager::widget([
                    'pagination' => $dataProvider->getPagination(),
                    'options' => ['class' => 'pagination'],
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
    </div>

</div>