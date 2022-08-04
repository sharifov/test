<?php

use modules\smartLeadDistribution\src\entities\LeadRatingParameter;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\smartLeadDistribution\src\entities\LeadRatingParameter */

$this->title = $model->lrp_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Rating Parameters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-rating-parameter-view col-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lrp_id' => $model->lrp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lrp_id' => $model->lrp_id], [
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
            'lrp_id',
            [
                'attribute' => 'lrp_object',
                'value' => static function (LeadRatingParameter $model) {
                    $obj = SmartLeadDistributionService::getByName($model->lrp_object);

                    return $obj::OPTGROUP_CALL;
                }
            ],
            [
                'attribute' => 'lrp_attribute',
                'value' => static function (LeadRatingParameter $model) {
                    $obj = SmartLeadDistributionService::getDataForField($model->lrp_object, $model->lrp_attribute);

                    return $obj[0]['label'];
                },
            ],
            'lrp_point',
            'lrp_condition',
            'lrp_condition_json',
        ],
    ]) ?>

</div>
