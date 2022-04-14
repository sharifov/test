<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\emailQuote\entity\EmailQuote */

$this->title = 'Update Email Quote: ' . $model->eq_id;
$this->params['breadcrumbs'][] = ['label' => 'Email Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->eq_id, 'url' => ['view', 'eq_id' => $model->eq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
