<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\forms\AbacPolicyForm */
/* @var $ap modules\abac\src\entities\AbacPolicy */

$this->title = 'Create Abac Policy';
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-policy-create">

    <h1><i class="fa fa-plus-circle"></i> <?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ap' => $ap
    ]) ?>

</div>
