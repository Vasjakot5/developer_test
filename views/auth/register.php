<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Регистрация';
?>
<div class="auth">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, заполните все обязательные поля:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin([
                'id' => 'register-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
                    'inputOptions' => ['class' => 'col-lg-3 form-control'],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <div class="form-group">
                <div>
                    <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-dt', 'name' => 'auth-button']) ?>
                </div>
            </div>
                <div class="text-center mt-3">
                    <p>Уже зарегистрированы? <?= Html::a('Войти', ['auth/login'], ['class' => 'btn btn-link p-0']) ?></p>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
