<?php

use modules\product\src\grid\columns\ProductQuoteColumn;
use modules\product\src\grid\columns\ProductQuoteStatusActionColumn;
use modules\product\src\grid\columns\ProductQuoteStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\DurationColumn;
use common\components\grid\UserSelect2Column;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteStatusLog\search\ProductQuoteStatusLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Status Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Status Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pqsl_id',
            [
                'class' => ProductQuoteColumn::class,
                'attribute' => 'pqsl_product_quote_id',
                'relation' => 'productQuote',
            ],
            [
                'class' => ProductQuoteStatusColumn::class,
                'attribute' => 'pqsl_start_status_id',
            ],
            [
                'class' => ProductQuoteStatusColumn::class,
                'attribute' => 'pqsl_end_status_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqsl_start_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqsl_end_dt',
            ],
            [
                'class' => DurationColumn::class,
                'attribute' => 'pqsl_duration',
                'startAttribute' => 'pqsl_start_dt',
            ],
            'pqsl_description',
            [
                'class' => ProductQuoteStatusActionColumn::class,
                'attribute' => 'pqsl_action_id',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pqsl_owner_user_id',
                'relation' => 'ownerUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pqsl_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
