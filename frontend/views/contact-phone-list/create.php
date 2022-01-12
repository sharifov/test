<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneList\entity\ContactPhoneList */

$this->title = 'Create Contact Phone List';
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-phone-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
