<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileOrder\FileOrder */

$this->title = 'Create File Order';
$this->params['breadcrumbs'][] = ['label' => 'File Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
