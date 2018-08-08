<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header>
    <?= $this->render('@common/views/layouts/_navBar.php') ?>
</header>

<main class="main-content item-view-page">
    <div class="page-header">
        <div class="container-fluid">
            <div class="page-header__wrapper">
                <h2 class="page-header__title"><?= Yii::$app->params['appName'] ?></h2>
                <?php
                if (Yii::$app->controller->id == 'lead' && Yii::$app->controller->action->id = 'queue') :
                    ?>
                    <div class="page-header__general">
                        <?= Html::a('Create New Lead', Url::to(['lead/create']), ['class' => 'btn btn-action']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="main-container" class="container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer__copyright">
            <small><?= date('Y') ?> © <?= Yii::$app->params['appName'] ?></small>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
