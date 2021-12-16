<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\contactPhoneList\entity\ContactPhoneList */

$this->title = 'Update Contact Phone List: ' . $model->cpl_id;
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cpl_id, 'url' => ['view', 'id' => $model->cpl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="contact-phone-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
