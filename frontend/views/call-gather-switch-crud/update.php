<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallGatherSwitch */

$this->title = 'Update Call Gather Switch: ' . $model->cgs_ccom_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Gather Switches', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cgs_ccom_id, 'url' => ['view', 'cgs_ccom_id' => $model->cgs_ccom_id, 'cgs_step' => $model->cgs_step, 'cgs_case' => $model->cgs_case]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-gather-switch-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
