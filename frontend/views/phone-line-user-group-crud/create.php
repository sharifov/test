<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserGroup\entity\PhoneLineUserGroup */

$this->title = 'Create Phone Line User Group';
$this->params['breadcrumbs'][] = ['label' => 'Phone Line User Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-user-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
