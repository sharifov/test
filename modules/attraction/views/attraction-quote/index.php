<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $searchModel modules\attraction\models\search\AttractionQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attraction Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attraction-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Attraction Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'atnq_id',
            'atnq_attraction_id',
            'atnq_hash_key',
            'atnq_product_quote_id',
            'atnq_booking_id',
            'atnq_attraction_name',
            'atnq_supplier_name',
            'atnq_type_name',
            'atnq_availability_date',
            //'atnq_json_response',
            [
                'attribute' => 'atnq_json_response',
                'value' => static function (\modules\attraction\models\AttractionQuote $model) {
                    $resultStr = '-';
                    if ($model->atnq_json_response) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($model->atnq_json_response)),
                            80,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($model->atnq_json_response, 10, true);
                        $detailBox = '<div id="detail_' . $model->atnq_id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->atnq_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
                'format' => 'raw'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => 'Response',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
]);
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        
        let id = $(this).data('idt');
        let detailEl = $('#detail_' + id);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Response Attraction Quote (' + id + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
?>

