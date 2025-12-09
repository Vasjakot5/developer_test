<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="tests-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true])->label('Название теста') ?>

    <?= $form->field($model, 'time_limit_minutes')->textInput(['type' => 'number', 'min' => 1])->label('Время на прохождение (минуты)') ?>

    <div class="form-group" style="display: flex; justify-content: center;">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-dt', 
            'style' => 'width: 110px; border-radius: 5px 0 0 5px; margin-right: 0; border-right: 0;'
        ]) ?>
        <?= Html::a('Отмена', ['index'], [
            'class' => 'btn btn-outline-dt', 
            'style' => 'width: 110px; border-radius: 0 5px 5px 0; margin-left: 0;'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>