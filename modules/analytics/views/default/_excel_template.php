<?php
/** @var array $stats */
echo "Показатель,Значение\n";
echo "Сотрудников," . $stats['totalUsers'] . "\n";
echo "Тестов," . $stats['totalTests'] . "\n";
echo "Пройдено," . $stats['totalResults'] . "\n";
echo "Успеваемость %," . $stats['successRate'] . "\n";
echo "Активных," . $stats['activeUsers'] . "\n";
echo "Активность %," . $stats['activityRate'] . "\n";
echo "На сотрудника," . $stats['testsPerUser'] . "\n\n";

echo "Тест,Прохождений,Охват %\n";
foreach ($stats['tests'] as $test) {
    $testResults = \app\models\Results::find()->where(['test_id' => $test->id])->count();
    $completionRate = $stats['totalUsers'] > 0 ? round(($testResults / $stats['totalUsers']) * 100, 1) : 0;
    echo $test->title . "," . $testResults . "," . $completionRate . "\n";
}
echo "\n";

echo "Email,Тестов,Охват %,Успеваемость %\n";
foreach ($stats['users'] as $user) {
    $userResults = $user->results;
    
    $correct = 0;
    $total = 0;
    foreach ($userResults as $result) {
        foreach ($result->answers as $answer) {
            $total++;
            if ($answer->is_correct) $correct++;
        }
    }
    $userSuccessRate = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
    
    $completionRate = $stats['totalTests'] > 0 ? round((count($userResults) / $stats['totalTests']) * 100, 1) : 0;
    
    echo $user->name . "," . count($userResults) . "," . $completionRate . "," . $userSuccessRate . "\n";
}
?>