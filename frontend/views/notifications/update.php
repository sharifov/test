<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Notifications */

$this->title = Yii::t('notifications', 'Update {modelClass}: ', [
    'modelClass' => 'Notifications',
]) . $model->n_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('notifications', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->n_id, 'url' => ['view', 'id' => $model->n_id]];
$this->params['breadcrumbs'][] = Yii::t('notifications', 'Update');
?>
<div class="notifications-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
