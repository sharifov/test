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
        <?= \yii\widgets\Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<!-- MODAL EMAIL TEMPLATES -->
<div class="modal fade" id="modal-email-templates" style="display: none;">
    <div class="modal-dialog" role="document" style="width: 1024px;">
        <div class="modal-content">
            <div class="modal-header">
                <?= Html::button('<span>×</span>', [
                    'class' => 'close',
                    'data-dismiss' => 'modal'
                ]) ?>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

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
