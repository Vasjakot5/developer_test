<?php
/** @var array $stats */
/** @var string $date */
?>
<h1 style="text-align: center;">Аналитика результатов тестирования</h1>
<h2 style="text-align: center;">Отчет от <?= $date ?></h2>

<h3>Общая статистика</h3>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>Показатель</th>
        <th>Значение</th>
    </tr>
    <tr>
        <td>Всего сотрудников</td>
        <td><?= $stats['totalUsers'] ?></td>
    </tr>
    <tr>
        <td>Всего тестов</td>
        <td><?= $stats['totalTests'] ?></td>
    </tr>
    <tr>
        <td>Пройдено тестов</td>
        <td><?= $stats['totalResults'] ?></td>
    </tr>
    <tr>
        <td>Правильных ответов</td>
        <td><?= $stats['totalCorrect'] ?></td>
    </tr>
    <tr>
        <td>Неправильных ответов</td>
        <td><?= $stats['totalIncorrect'] ?></td>
    </tr>
    <tr>
        <td><strong>Общая успеваемость</strong></td>
        <td><strong><?= $stats['successRate'] ?>%</strong></td>
    </tr>
    <tr>
        <td>Активных сотрудников</td>
        <td><?= $stats['activeUsers'] ?> (<?= $stats['activityRate'] ?>%)</td>
    </tr>
    <tr>
        <td>Тестов на сотрудника</td>
        <td><?= $stats['testsPerUser'] ?></td>
    </tr>
</table>

<h3>Статистика по тестам</h3>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>Тест</th>
        <th>Прохождений</th>
        <th>Охват сотрудников</th>
    </tr>
    <?php foreach ($stats['tests'] as $test): ?>
    <?php 
        $testResults = \app\models\Results::find()->where(['test_id' => $test->id])->count();
        $completionRate = $stats['totalUsers'] > 0 ? round(($testResults / $stats['totalUsers']) * 100, 1) : 0;
    ?>
    <tr>
        <td><?= htmlspecialchars($test->title) ?></td>
        <td><?= $testResults ?></td>
        <td><?= $completionRate ?>%</td>
    </tr>
    <?php endforeach; ?>
</table>

<h3>Активность сотрудников</h3>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>Сотрудник</th>
        <th>Пройдено тестов</th>
        <th>Охват тестов</th>
        <th>Успеваемость</th>
    </tr>
    <?php foreach ($stats['users'] as $user): ?>
    <?php 
        $userResults = $user->results;
        
        $correct = 0;
        $total = 0;
        foreach ($userResults as $result) {
            $testQuestionCount = count($result->test->questions);
            $correct += $result->score;
            $total += $testQuestionCount;
        }
        $userSuccessRate = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
        
        $completionRate = $stats['totalTests'] > 0 ? round((count($userResults) / $stats['totalTests']) * 100, 1) : 0;
    ?>
    <tr>
        <td><?= htmlspecialchars($user->email) ?></td>
        <td><?= count($userResults) ?></td>
        <td><?= $completionRate ?>%</td>
        <td><?= $userSuccessRate ?>%</td>
    </tr>
    <?php endforeach; ?>
</table>