<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\UserSiteActivity */

$this->title = 'Create User Site Activity';
$this->params['breadcrumbs'][] = ['label' => 'User Site Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-site-activity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
