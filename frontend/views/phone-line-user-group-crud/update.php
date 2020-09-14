<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserGroup\entity\PhoneLineUserGroup */

$this->title = 'Update Phone Line User Group: ' . $model->plug_line_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Line User Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->plug_line_id, 'url' => ['view', 'plug_line_id' => $model->plug_line_id, 'plug_ug_id' => $model->plug_ug_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-line-user-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
