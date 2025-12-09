<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Tests $model */

$this->title = 'Редактирование теста: ' . $model->title;
?>
<div class="tests-update">

    <div class="card" style="Width: 540px; margin-top: 60px; margin-left:auto; margin-right:auto;">
        <div class="card-body" style="margin-left: auto; margin-right: auto;">
            <h1 class="card-title card-title-wrap" style="text-align: center"><?= Html::encode($this->title) ?></h1>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>