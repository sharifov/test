<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserAssign\entity\PhoneLineUserAssign */

$this->title = 'Update Phone Line User Assign: ' . $model->plus_line_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Line User Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->plus_line_id, 'url' => ['view', 'plus_line_id' => $model->plus_line_id, 'plus_user_id' => $model->plus_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-line-user-assign-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
