<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CallUserAccess */

$this->title = 'Create Call User Access';
$this->params['breadcrumbs'][] = ['label' => 'Call User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-user-access-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
