<?php

/**
 * @var $this yii\web\View
 * @var $name string
 * @var $message string
 * @var $exception \yii\web\HttpException
 */

use yii\helpers\Html;

$this->title = $name;
//$textColor = $exception->statusCode === 404 ? "text-yellow" : "text-red";
?>

<div class="d-flex justify-content-center align-items-center" style="width: 100%;">
    <div class="col-middle">
        <div class="text-center text-center">
            <h1 class="error-number"><?= $exception->statusCode ?></h1>
            <h3><?= nl2br(Html::encode($message)) ?></h3>
            <br><br>
            <p>
                The above error occurred while the Web server was processing your request.
            </p>
            <p>
                Please contact us if you think this is a server error. Thank you.
            </p>
            <br>
            <p>
                <?=Html::a('<i class="fa fa-home"></i> Home page', ['site/index'], ['class' => 'btn btn-default'])?>
                <?=Html::a('<i class="fa fa-refresh"></i> Refresh page', $_SERVER['REQUEST_URI'], ['class' => 'btn btn-primary'])?>
            </p>
        </div>
    </div>
</div>
