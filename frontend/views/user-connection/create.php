<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserConnection */

$this->title = 'Create User Connection';
$this->params['breadcrumbs'][] = ['label' => 'User Connections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-connection-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
