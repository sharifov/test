<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\user\userActivity\entity\UserActivity */

$this->title = 'Create User Activity';
$this->params['breadcrumbs'][] = ['label' => 'User Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-activity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
