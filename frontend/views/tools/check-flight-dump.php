<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $dump string */


$this->title = 'Check Flight dump ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-flight-dump">

    <h1><?= Html::encode($this->title) ?></h1>


    <div class="col-md-4">
        <?= Html::beginForm() ?>
        <?= Html::textarea('dump', $dump, ['rows' => 10, 'style' => 'width: 100%']) ?><br><br>
        <?= Html::submitButton('Check Flight', ['class' => 'btn btn-primary']) ?>
        <?= Html::endForm() ?>
    </div>


    <div class="col-md-8">
        <h2>Parse dump:</h2>
        <?php if ($data): ?>
            <pre>
            <?php \yii\helpers\VarDumper::dump($data, 10, true) ?>
            </pre>
        <?php endif; ?>
    </div>


</div>