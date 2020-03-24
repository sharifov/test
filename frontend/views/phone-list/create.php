<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneList\entity\PhoneList */

$this->title = 'Create Phone List';
$this->params['breadcrumbs'][] = ['label' => 'Phone Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
