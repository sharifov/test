<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroupSet */

$this->title = 'Update User Group Set: ' . $model->ugs_id;
$this->params['breadcrumbs'][] = ['label' => 'User Group Sets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ugs_id, 'url' => ['view', 'id' => $model->ugs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-group-set-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
