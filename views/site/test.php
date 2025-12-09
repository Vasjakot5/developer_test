<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $test->title;
?>

<div class="site-test">
    <h1><?= Html::encode($test->title) ?></h1>
    
    <div class="alert alert-info mb-4">
        <strong>Время:</strong> <span id="timer"><?= $test->time_limit_minutes ?>:00</span>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'test-form',
        'action' => ['test', 'id' => $test->id],
    ]); ?>
    
    <?php foreach ($questions as $index => $question): ?>
        <div class="question-card">
            <div class="question-header">
                <div>
                    <span class="question-number"><?= $index + 1 ?></span>
                    <strong><?= Html::encode($question->question_text) ?></strong>
                </div>
            </div>
            
            <?php if ($question->type == 1): ?>
                <?php foreach ($question->answers as $answer): ?>
                    <div class="radio mb-2">
                        <label>
                            <?= Html::radio("TestForm[answers][{$question->id}]", false, [
                                'value' => $answer->id
                            ]) ?>
                            <?= Html::encode($answer->answer_text) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            
            <?php elseif ($question->type == 2): ?>
                <?php foreach ($question->answers as $answer): ?>
                    <div class="checkbox mb-2">
                        <label>
                            <?= Html::checkbox("TestForm[answers][{$question->id}][]", false, [
                                'value' => $answer->id
                            ]) ?>
                            <?= Html::encode($answer->answer_text) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            
            <?php else: ?>
                <?= Html::textarea("TestForm[answers][{$question->id}]", '', [
                    'class' => 'form-control',
                    'rows' => 3
                ]) ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="form-group mt-4">
        <div class="btn-group" role="group">
            <?= Html::submitButton('Завершить тест', [
                'class' => 'btn btn-dt',
                'style' => 'height: 40px; width: 150px; display: flex; align-items: center; justify-content: center; padding: 0 15px;'
            ]) ?>
            <?= Html::a('Назад', ['cabinet'], [
                'class' => 'btn btn-outline-dt',
                'style' => 'height: 40px; width: 100px; display: flex; align-items: center; justify-content: center; padding: 0 15px;'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    
    <script>
    let time = <?= $test->time_limit_minutes * 60 ?>;
    const timer = document.getElementById('timer');
    
    setInterval(() => {
        time--;
        let minutes = Math.floor(time / 60);
        let seconds = time % 60;
        timer.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        
        if (time <= 0) {
            document.getElementById('test-form').submit();
        }
    }, 1000);
    </script>
</div>