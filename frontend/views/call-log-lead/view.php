<?php

use sales\model\callLog\entity\callLogLead\CallLogLead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogLead\CallLogLead */

$this->title = $model->cll_cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-log-lead-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'cll_cl_id' => $model->cll_cl_id, 'cll_lead_id' => $model->cll_lead_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'cll_cl_id' => $model->cll_cl_id, 'cll_lead_id' => $model->cll_lead_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'cll_cl_id:callLog',
                    [
                        'attribute' => 'cll_lead_id',
                        'format' => 'lead',
                        'value' => static function (CallLogLead $model) {
                            return $model->lead ?: null;
                        }
                    ],
                    [
                        'attribute' => 'cll_lead_flow_id',
                        'value' => static function (CallLogLead $model) {
                            return Html::a($model->cll_lead_flow_id, Url::to(['/lead-flow/index', 'LeadFlowSearch[id]' => $model->cll_lead_flow_id]));
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
