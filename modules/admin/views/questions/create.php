<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Questions $model */

$this->title = 'Create Questions';
?>
<div class="questions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
