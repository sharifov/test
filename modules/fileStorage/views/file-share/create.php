<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileShare\FileShare */

$this->title = 'Create File Share';
$this->params['breadcrumbs'][] = ['label' => 'File Shares', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-share-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
