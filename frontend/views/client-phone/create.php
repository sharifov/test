<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ClientPhone */

$this->title = 'Create Client Phone';
$this->params['breadcrumbs'][] = ['label' => 'Client Phones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-phone-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
