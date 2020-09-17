<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\PhoneLineCommand */

$this->title = 'Update Phone Line Command: ' . $model->plc_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Line Commands', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->plc_id, 'url' => ['view', 'id' => $model->plc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-line-command-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
