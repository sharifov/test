<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyHistory */

$this->title = $model->cur_his_code;
$this->params['breadcrumbs'][] = ['label' => 'Currency Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="currency-history-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'cur_his_code',
            'cur_his_base_rate',
            'cur_his_app_rate',
            'cur_his_app_percent',
            'cur_his_created',
            'cur_his_main_created_dt',
            'cur_his_main_updated_dt',
            'cur_his_main_synch_dt',
        ],
    ]) ?>

</div>
