<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\AbacPolicy */

$this->title = 'Create Abac Policy';
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-policy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
