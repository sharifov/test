<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyHistory */

$this->title = $model->ch_code;
$this->params['breadcrumbs'][] = ['label' => 'Currency Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="currency-history-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('Update', ['update', 'ch_code' => $model->ch_code, 'ch_created_date' => $model->ch_created_date], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Delete', ['delete', 'ch_code' => $model->ch_code, 'ch_created_date' => $model->ch_created_date], [
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
            'ch_code',
            'ch_base_rate',
            'ch_app_rate',
            'ch_app_percent',
            'ch_created_date',
            'ch_main_created_dt',
            'ch_main_updated_dt',
            'ch_main_synch_dt',
        ],
    ]) ?>

</div>
