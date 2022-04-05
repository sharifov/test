<?php

use frontend\helpers\JsonHelper;
use kdn\yii2\JsonEditor;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChange\ProductQuoteChange */

$this->title = $model->pqc_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-change-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pqc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pqc_id], [
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
                    'pqc_id',
                    'pqc_pq_id',
                    'pqc_case_id',
                    'pqc_gid',
                    'pqc_decision_user:username',
                    [
                        'attribute' => 'pqc_status_id',
                        'value' => static function (ProductQuoteChange $model) {
                            return $model->pqc_status_id ? ProductQuoteChangeStatus::asFormat($model->pqc_status_id) : $model->pqc_status_id;
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'pqc_decision_type_id',
                        'value' => static function (ProductQuoteChange $model) {
                            return $model->pqc_decision_type_id ? ProductQuoteChangeDecisionType::asFormat($model->pqc_decision_type_id) : $model->pqc_decision_type_id;
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'pqc_type_id',
                        'value' => static function (ProductQuoteChange $model) {
                            return $model->pqc_type_id ?
                                ProductQuoteChange::TYPE_LIST[$model->pqc_type_id] ?? 'Undefined' :
                                Yii::$app->formatter->nullDisplay;
                        },
                        'format' => 'raw',
                    ],
                    'pqc_is_automate:booleanByLabel',
                    'pqc_refund_allowed:booleanByLabel',
                    'pqc_created_user_id:username',
                    'pqc_created_dt:byUserDateTime',
                    'pqc_updated_dt:byUserDateTime',
                    'pqc_decision_dt:byUserDateTime',
                ],
            ]) ?>
        </div>

        <div class="col-md-8">
            <pre>
                <?php echo VarDumper::dumpAsString(JsonHelper::decode($model->pqc_data_json), 20, true); ?>
            </pre>
        </div>
    </div>
</div>
