<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileUser\FileUser */

$this->title = 'Create File User';
$this->params['breadcrumbs'][] = ['label' => 'File Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
