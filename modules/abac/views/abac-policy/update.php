<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\forms\AbacPolicyForm */
/* @var $ap modules\abac\src\entities\AbacPolicy */

$this->title = 'Update Policy: "' . $model->ap_object . '" (' . $model->ap_id . ') ';
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ap_object, 'url' => ['view', 'id' => $model->ap_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="abac-policy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ap' => $ap
    ]) ?>

</div>
