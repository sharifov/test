<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue */

$this->title = 'Create Lead Business Extra Queue';
$this->params['breadcrumbs'][] = ['label' => 'Lead Business Extra Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-business-extra-queue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
