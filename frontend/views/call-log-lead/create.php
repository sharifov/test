<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogLead\CallLogLead */

$this->title = 'Create Call Log Lead';
$this->params['breadcrumbs'][] = ['label' => 'Call Log Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-lead-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
