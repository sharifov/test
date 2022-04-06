<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\emailQuote\entity\EmailQuote */

$this->title = $model->eq_id;
$this->params['breadcrumbs'][] = ['label' => 'Email Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="email-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'eq_id' => $model->eq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'eq_id' => $model->eq_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'eq_id',
            'eq_email_id:email',
            'eq_quote_id',
            'eq_created_dt:byUserDateTime',
            'eq_created_by:username',
        ],
    ]) ?>

</div>
