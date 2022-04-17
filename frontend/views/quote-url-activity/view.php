<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Json;
use frontend\models\CommunicationForm;
use common\models\QuoteUrlActivity;

/**
 * @var $this yii\web\View
 * @var $model \common\models\QuoteUrlActivity
 **/

$this->title = $model->qua_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Url Activity', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-url-activity-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'qua_id' => $model->qua_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'qua_id' => $model->qua_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
    try {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'qua_id',
                'qua_uid',
                [
                    'attribute' => 'qua_communication_type',
                    'filter' => CommunicationForm::TYPE_LIST,
                    'value' => static function (QuoteUrlActivity $model): string {
                        return (isset(CommunicationForm::TYPE_LIST[$model->qua_communication_type]))
                            ? CommunicationForm::TYPE_LIST[$model->qua_communication_type]
                            : 'Unknown communication type';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'qua_status',
                    'filter' => QuoteUrlActivity::statusList(),
                    'value' => static function (QuoteUrlActivity $model): string {
                        $quaStatus = QuoteUrlActivity::statusName($model->qua_status);
                        return is_null($quaStatus) ? 'Unknown communication type' : $quaStatus;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'qua_quote_id',
                    'value' => function (QuoteUrlActivity $model) {
                        return Html::a("<i class=\"fa fa-link\"></i> {$model->qua_quote_id}", ['/quotes/view', 'id' => $model->qua_quote_id], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'qua_ext_data',
                    'value' => function (QuoteUrlActivity $model) {
                        $json = Json::decode($model->qua_ext_data);
                        return Html::tag('pre', Json::encode($json, JSON_PRETTY_PRINT), ['style' => 'margin: 0;']);
                    },
                    'format' => 'html'
                ],
                'qua_created_dt:byUserDateTime'
            ],
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', $e->getMessage());
    }
    ?>

</div>
