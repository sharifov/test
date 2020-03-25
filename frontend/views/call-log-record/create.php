<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogRecord\CallLogRecord */

$this->title = 'Create Call Log Record';
$this->params['breadcrumbs'][] = ['label' => 'Call Log Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
