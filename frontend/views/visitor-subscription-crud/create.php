<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\visitorSubscription\entity\VisitorSubscription */

$this->title = 'Create Visitor Subscription';
$this->params['breadcrumbs'][] = ['label' => 'Visitor Subscriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-subscription-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
