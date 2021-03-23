<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileOrder\FileOrder */

$this->title = 'Update File Order';
$this->params['breadcrumbs'][] = ['label' => 'File Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fo_id, 'url' => ['view', 'id' => $model->fo_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-Order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
