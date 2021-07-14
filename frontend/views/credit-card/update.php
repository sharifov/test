<?php

use frontend\models\form\CreditCardForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model CreditCardForm */
/* @var $modelCc common\models\CreditCard */

$this->title = 'Update Credit Card: ' . $model->cc_id;
$this->params['breadcrumbs'][] = ['label' => 'Credit Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cc_id, 'url' => ['view', 'id' => $model->cc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="credit-card-update">

    <h1>Update Credit Card: <?= Html::encode($modelCc->cc_display_number) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelCc' => $modelCc,
    ]) ?>

</div>
