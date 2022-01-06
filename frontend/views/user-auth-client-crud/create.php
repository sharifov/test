<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\userAuthClient\entity\UserAuthClient */

$this->title = 'Create Auth Client';
$this->params['breadcrumbs'][] = ['label' => 'Auth Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-client-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
