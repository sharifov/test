<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\userStatus\UserStatus */

$this->title = 'Create User Status';
$this->params['breadcrumbs'][] = ['label' => 'User Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-status-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
