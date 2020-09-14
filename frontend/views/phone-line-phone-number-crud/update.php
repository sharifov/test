<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber */

$this->title = 'Update Phone Line Phone Number: ' . $model->plpn_line_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Line Phone Numbers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->plpn_line_id, 'url' => ['view', 'plpn_line_id' => $model->plpn_line_id, 'plpn_pl_id' => $model->plpn_pl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-line-phone-number-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
