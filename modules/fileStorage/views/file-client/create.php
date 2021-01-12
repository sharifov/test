<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileClient\FileClient */

$this->title = 'Create File Client';
$this->params['breadcrumbs'][] = ['label' => 'File Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-client-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
