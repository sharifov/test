<?php

use sales\model\emailList\entity\EmailList;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EmailList */

$this->title = 'Create Email List';
$this->params['breadcrumbs'][] = ['label' => 'Email Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
