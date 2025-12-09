<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => '<img src="./imgs/logo.png" alt="logo" style="vertical-align: middle; margin-right: 10px; height: 20px;">',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand navbar-dark bg-dark fixed-top'],
        'collapseOptions' => ['class' => 'justify-content-end'],
    ]);
    
    $rightNavItems = [];
    
    if (!Yii::$app->user->isGuest) {
        $rightNavItems[] = [
            'label' => '<img src="./imgs/home.png" alt="Home" style="vertical-align: middle; margin-right: 10px; height: 25px;">',
            'url' => ['/site/index'],
            'encode' => false
        ];
        $rightNavItems[] = [
            'label' => '<img src="./imgs/cabinet.png" alt="Cabinet" style="vertical-align: middle; margin-right: 10px; height: 25px;">',
            'url' => ['/site/cabinet'],
            'encode' => false
        ];
    } 
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => $rightNavItems,
    ]);
    
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center" style="margin-right: auto; margin-left: auto">Developer Test <?= date('Y') ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>