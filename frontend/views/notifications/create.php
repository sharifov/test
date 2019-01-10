<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Notifications */

$this->title = Yii::t('notifications', 'Create Notifications');
$this->params['breadcrumbs'][] = ['label' => Yii::t('notifications', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
