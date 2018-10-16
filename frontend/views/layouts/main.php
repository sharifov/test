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
    <?php $this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']); ?>
    <title><?= Html::encode(Yii::$app->name) ?> - <?= Html::encode($this->title) ?></title>
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
                <div class="page-header__general">
                    <?php
                    if (Yii::$app->controller->id == 'lead' && Yii::$app->controller->action->id = 'queue') {
                        echo Html::a('Create New Lead', Url::to(['lead/create']), ['class' => 'btn btn-action']);
                    }
                    if (Yii::$app->controller->action->id = 'queue' && Yii::$app->request->get('type') == 'follow-up') {
                        $showAll = Yii::$app->request->cookies->getValue(\common\models\Lead::getCookiesKey(), true);
                        $btnClass = (!$showAll)
                            ? 'btn-warning' : 'btn-success';
                        $btnText = (!$showAll)
                            ? 'Show All' : 'Show Unprocessed';
                        $btnUrl = Url::to(['lead/unprocessed', 'show' => !$showAll]);
                        echo Html::a($btnText, $btnUrl, [
                            'class' => 'btn ' . $btnClass,
                            'style' => 'margin-left: 10px;'
                        ]);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div id="main-container" class="container">
        <?= \yii\widgets\Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
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

<!-- MODAL ERROR WINDOWS -->
<div class="modal modal-danger fade in" id="modal-error" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title">Attention!</h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<div class="modal modal-events fade" id="modal-report-info" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Sold Leads
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
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
