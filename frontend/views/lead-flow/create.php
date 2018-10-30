<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LeadFlow */

$this->title = 'Create Lead Flow';
$this->params['breadcrumbs'][] = ['label' => 'Lead Flows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-flow-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
