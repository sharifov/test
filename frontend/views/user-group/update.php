<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroup */

$this->title = 'Update User Group: ' . $model->ug_name;
$this->params['breadcrumbs'][] = ['label' => 'User Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ug_key, 'url' => ['view', 'id' => $model->ug_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
