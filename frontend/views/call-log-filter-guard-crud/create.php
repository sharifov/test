<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\callLogFilterGuard\entity\CallLogFilterGuard */

$this->title = 'Create Call Log Filter Guard';
$this->params['breadcrumbs'][] = ['label' => 'Call Log Filter Guards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-filter-guard-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
