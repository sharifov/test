<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserAssign\entity\PhoneLineUserAssign */

$this->title = 'Create Phone Line User Assign';
$this->params['breadcrumbs'][] = ['label' => 'Phone Line User Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-user-assign-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
