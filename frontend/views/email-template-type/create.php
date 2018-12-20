<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplateType */

$this->title = 'Create Email Template Type';
$this->params['breadcrumbs'][] = ['label' => 'Email Template Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-template-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
