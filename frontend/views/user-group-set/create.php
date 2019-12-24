<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroupSet */

$this->title = 'Create User Group Set';
$this->params['breadcrumbs'][] = ['label' => 'User Group Sets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-group-set-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
