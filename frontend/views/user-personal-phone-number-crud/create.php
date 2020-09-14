<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\userPersonalPhoneNumber\entity\UserPersonalPhoneNumber */

$this->title = 'Create User Personal Phone Number';
$this->params['breadcrumbs'][] = ['label' => 'User Personal Phone Numbers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-personal-phone-number-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
