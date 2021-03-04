<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuote */

$this->title = $model->atnq_id;
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="attraction-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->atnq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->atnq_id], [
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
            'atnq_id',
            'atnq_attraction_id',
            'atnq_hash_key',
            'atnq_product_quote_id',
            'atnq_booking_id',
            'atnq_attraction_name',
            'atnq_supplier_name',
            'atnq_type_name',
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
        ],
    ]) ?>

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
