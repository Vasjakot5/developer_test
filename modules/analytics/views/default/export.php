<?php
use yii\helpers\Html;
$this->title = 'Формирование отчетов';
?>

<div class="analytics-export">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="card">
        <div class="card-header">
            <h5>Форматы отчетов</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <h5 class="card-title">PDF отчет</h5>
                            <p class="card-text">Полный отчет с графиками и таблицами</p>
                            <?= Html::a('Скачать PDF', ['default/export-pdf'], [
                                'class' => 'btn btn-danger btn-lg',
                                'target' => '_blank'
                            ]) ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <h5 class="card-title">Excel таблица</h5>
                            <p class="card-text">Данные в формате XLS для анализа</p>
                            <?= Html::a('Скачать Excel', ['default/export-excel'], [
                                'class' => 'btn btn-success btn-lg'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <?= Html::a('Назад к аналитике', ['default/index'], ['class' => 'btn btn-dt', 'style'=>'width: 170px']) ?>
    </div>
</div>