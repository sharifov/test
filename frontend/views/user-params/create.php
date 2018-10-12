<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserParams */

$this->title = 'Create User Params';
$this->params['breadcrumbs'][] = ['label' => 'User Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-params-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
