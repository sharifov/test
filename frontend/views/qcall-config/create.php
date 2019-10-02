<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QcallConfig */

$this->title = 'Create Qcall Config';
$this->params['breadcrumbs'][] = ['label' => 'Qcall Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qcall-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
