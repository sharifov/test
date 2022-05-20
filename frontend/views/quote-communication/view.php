<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;
use frontend\models\CommunicationForm;
use common\models\QuoteCommunication;

/**
 * @var $this yii\web\View
 * @var $model \common\models\QuoteCommunication
 **/

$this->title = $model->qc_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Communication', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-communication-view">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <p>
        <?= Html::a('Update', ['update', 'qc_id' => $model->qc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'qc_id' => $model->qc_id], [
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
                'qc_id',
                'qc_uid',
                [
                    'attribute' => 'qc_communication_type',
                    'filter' => CommunicationForm::TYPE_LIST,
                    'value' => static function (QuoteCommunication $model): string {
                        return (isset(CommunicationForm::TYPE_LIST[$model->qc_communication_type]))
                            ? CommunicationForm::TYPE_LIST[$model->qc_communication_type]
                            : 'Unknown communication type';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'qc_communication_id',
                    'value' => static function (QuoteCommunication $model): string {
                        switch ($model->qc_communication_type) {
                            case CommunicationForm::TYPE_EMAIL:
                                return Html::a('<i class="fa fa-link"></i> ' . $model->qc_communication_id, ['/email/view', 'id' => $model->qc_communication_id], ['target' => '_blank', 'data-pjax' => 0]);
                            case CommunicationForm::TYPE_SMS:
                                return Html::a('<i class="fa fa-link"></i> ' . $model->qc_communication_id, ['/sms/view', 'id' => $model->qc_communication_id], ['target' => '_blank', 'data-pjax' => 0]);
                            case CommunicationForm::TYPE_CHAT:
                                return Html::a('<i class="fa fa-link"></i> ' . $model->qc_communication_id, ['/client-chat-crud/view', 'id' => $model->qc_communication_id], ['target' => '_blank', 'data-pjax' => 0]);
                            default:
                                return 'Unknown communication type';
                        }
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'qc_quote_id',
                    'value' => function (QuoteCommunication $model) {
                        return Html::a('<i class="fa fa-link"></i> ' . $model->qc_quote_id, ['/quotes/view', 'id' => $model->qc_quote_id], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw'
                ],
                'qc_created_dt:byUserDateTime',
                [
                    'attribute' => 'qc_ext_data',
                    'value' => function (QuoteCommunication $model) {
                        $json = (is_null($model->qc_ext_data)) ? [] : Json::decode($model->qc_ext_data);
                        return Html::tag('pre', Json::encode($json, JSON_PRETTY_PRINT), ['style' => 'margin: 0;']);
                    },
                    'format' => 'html'
                ],
                'qc_created_by:username',
            ],
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', Html::encode($e->getMessage()));
    }
    ?>
</div>
