<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callNote\entity\CallNote */

$this->title = 'Update Call Note: ' . $model->cn_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cn_id, 'url' => ['view', 'id' => $model->cn_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
