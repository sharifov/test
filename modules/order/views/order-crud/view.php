<?php

use kdn\yii2\JsonEditor;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\order\src\entities\order\Order */

$this->title = $model->or_id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->or_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->or_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
      <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'or_id',
                'or_gid',
                'or_uid',
                'or_name',
                'orLead:lead',
                'or_project_id:projectName',
                'or_description:ntext',
                'or_status_id:orderStatus',
                'or_pay_status_id:orderPayStatus',
                'or_app_total',
                'or_app_markup',
                'or_agent_markup',
                'or_client_total',
                'or_client_currency',
                'or_client_currency_rate',
                'or_profit_amount',
                'orOwnerUser:userName',
                'orCreatedUser:userName',
                'orUpdatedUser:userName',
                'or_created_dt:byUserDateTime',
                'or_updated_dt:byUserDateTime',
            ],
        ]) ?>
      </div>
      <div class="col-md-6">
          <div>
              <h3>Leads</h3>
              <?= \yii\grid\GridView::widget([
                  'dataProvider' => new \yii\data\ArrayDataProvider([
                      'allModels' => $model->leadOrder
                  ]),
                  'columns' => [
                      'lead:lead',
                      'lo_create_dt:byUserDateTime'
                  ]
              ]) ?>
          </div>
          <div>
              <h3>Cases</h3>
              <?= \yii\grid\GridView::widget([
                  'dataProvider' => new \yii\data\ArrayDataProvider([
                      'allModels' => $model->caseOrder
                  ]),
                  'columns' => [
                      'cases:case',
                      'co_create_dt:byUserDateTime'
                  ]
              ]) ?>
          </div>
        <h4><?=$model->getAttributeLabel('or_request_data')?></h4>
        <pre>
            <?php
                echo VarDumper::dumpAsString(($model->or_request_data), 10, true);
            ?>
        </pre>
      </div>
    </div>

</div>
