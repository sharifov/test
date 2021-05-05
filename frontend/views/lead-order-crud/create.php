<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\leadOrder\entity\LeadOrder */

$this->title = 'Create Lead Order';
$this->params['breadcrumbs'][] = ['label' => 'Lead Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
