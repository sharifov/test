<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserData\entity\LeadUserData */

$this->title = 'Update Lead User Data: ' . $model->lud_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead User Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lud_id, 'url' => ['view', 'lud_id' => $model->lud_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-user-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
