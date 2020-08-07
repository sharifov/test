<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\monitor\UserMonitor */

$this->title = 'Update User Monitor: ' . $model->um_id;
$this->params['breadcrumbs'][] = ['label' => 'User Monitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->um_id, 'url' => ['view', 'id' => $model->um_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-monitor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
