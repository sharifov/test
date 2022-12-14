<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PaymentMethodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Methods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-method-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Payment Method', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pm_id',
            'pm_name',
            'pm_key',
            'pm_short_name',
            'pm_enabled:boolean',
            [
                'attribute' => 'pm_category_id',
                'value' => static function (\common\models\PaymentMethod $model) {
                    return $model->getCategoryName();
                },
                'filter' => \common\models\PaymentMethod::getCategoryList()
            ],
            'pm_updated_user_id:UserName',
            //'pm_updated_dt:ByUserDateTime',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pm_updated_dt'
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
