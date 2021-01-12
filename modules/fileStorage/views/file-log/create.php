<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileLog\FileLog */

$this->title = 'Create File Log';
$this->params['breadcrumbs'][] = ['label' => 'File Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
