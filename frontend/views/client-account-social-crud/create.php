<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientAccountSocial\entity\ClientAccountSocial */

$this->title = 'Create Client Account Social';
$this->params['breadcrumbs'][] = ['label' => 'Client Account Socials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-account-social-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
