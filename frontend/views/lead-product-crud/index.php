<?php

use common\components\grid\DateTimeColumn;
use common\components\i18n\Formatter;
use src\model\leadProduct\entity\LeadProduct;
use src\model\leadProduct\entity\search\LeadProductSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel LeadProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-lead-order', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'lp_lead_id',
                'value' => static function (LeadProduct $leadProduct) {
                    return (new Formatter())->asLead($leadProduct->lead);
                },
                'format' => 'raw'
            ],
            'lp_product_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
