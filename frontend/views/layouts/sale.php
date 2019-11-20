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
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header>
    <?//= $this->render('@common/views/layouts/_navBar.php') ?>
</header>

<main class="main-content">
    <?= $content ?>
</main>

<div class="modal modal-quote fade" id="get-request-flow-transition" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Lead - Flow Transition
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal modal-quote fade" id="get-quote-status-log" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Quote - Status Log
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal modal-events fade" id="log-events" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Client Request Actions
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
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

<div class="modal modal-quote fade" id="quick-search" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Quick search quotes
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal modal-quote fade" id="create-quote" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add quote!</h4>
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

<div id="preloader" class="overlay d-none">
    <div class="preloader">
        <span class="fa fa-spinner fa-pulse fa-3x fa-fw"></span>
        <div class="preloader__text">Loading...</div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
