<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserGroupAssign */

$this->title = 'Create User Group Assign';
$this->params['breadcrumbs'][] = ['label' => 'User Group Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-group-assign-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
