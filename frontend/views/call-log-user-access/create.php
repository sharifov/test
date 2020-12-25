<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogUserAccess\CallLogUserAccess */

$this->title = 'Create Call Log User Access';
$this->params['breadcrumbs'][] = ['label' => 'Call Log User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-user-access-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
