<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserProjectParams */

$this->title = 'Create User Project Params';
$this->params['breadcrumbs'][] = ['label' => 'User Project Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-project-params-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
