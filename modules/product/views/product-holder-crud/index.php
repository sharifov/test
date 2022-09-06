<?php

use modules\product\src\entities\productHolder\ProductHolder;
use src\helpers\email\MaskEmailHelper;
use src\helpers\phone\MaskPhoneHelper;
use src\helpers\text\MaskStringHelper;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productHolder\search\ProductHolderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Holders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-holder-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Holder', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-product-holder', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ph_id',
            'ph_product_id',
            [
                'attribute' => 'ph_first_name',
                'value' => static function (ProductHolder $model) {
                    return MaskStringHelper::masking($model->ph_first_name);
                }
            ],
            'ph_last_name',
            'ph_middle_name',
            [
                'attribute' => 'ph_email',
                'value' => static function (ProductHolder $model) {
                    return MaskEmailHelper::masking($model->ph_email);
                }
            ],
            [
                'attribute' => 'ph_phone_number',
                'value' => static function (ProductHolder $model) {
                    return MaskPhoneHelper::masking($model->ph_phone_number);
                }
            ],
            'ph_created_dt:byUserDateTime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
