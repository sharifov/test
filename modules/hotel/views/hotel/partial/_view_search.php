<?php

use modules\hotel\models\search\HotelRoomSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\Hotel */
/* @var $dataProviderRooms \yii\data\ActiveDataProvider */


\yii\web\YiiAsset::register($this);

//$searchModel = new HotelRoomSearch();
//$dataProvider = $searchModel->search(Yii::$app->request->queryParams);


?>
<div class="hotel-view-search">

    <h2>Hotel Request ID: <?= Html::encode($model->ph_id) ?></h2>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel/update-ajax', 'id' => $model->ph_id]), 'data-hotel-id' => $model->ph_id, 'class' => 'btn btn-warning btn-update-hotel-request']) ?>
        <?= Html::a('<i class="fa fa-plus"></i> Add Room', ['hotel-room/create-ajax', 'id' => $model->ph_id], ['data-hotel-id' => $model->ph_id, 'class' => 'btn btn-success btn-add-hotel-room']) ?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->ph_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'ph_id',
            //'ph_product_id',
            'ph_check_in_date',
            'ph_check_out_date',
            'ph_destination_code',
            'ph_min_star_rate',
            'ph_max_star_rate',
            'ph_max_price_rate',
            'ph_min_price_rate',
        ],
    ]) ?>



    <?php Pjax::begin(); ?>

    <?php if ($model->hotelRooms):?>
        <?php foreach ($model->hotelRooms as $room): ?>
            <div class="col-md-12">
                <h3>Room <?=Html::encode($room->hr_id)?> <?=Html::encode($room->hr_room_name)?> <?=\yii\bootstrap4\Html::a('<i class="fa fa-edit"></i>', ['hotel-room/update-ajax', 'id' => $model->hr_id], ['class' => 'btn btn-warning'])?></h3>
                <?php if ($room->hotelRoomPaxes):?>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Pax Id</th>
                            <th>Type</th>
                            <th>Age</th>
                            <th>First name</th>
                            <th>Last name</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($room->hotelRoomPaxes as $pax): ?>
                        <tr>
                            <td>Pax <?=Html::encode($pax->hrp_id)?></td>
                            <td><?=Html::encode($pax->hrp_type_id)?></td>
                            <td><?=Html::encode($pax->hrp_age)?></td>
                            <td><?=Html::encode($pax->hrp_first_name)?></td>
                            <td><?=Html::encode($pax->hrp_last_name)?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <?php Pjax::end(); ?>

</div>

<?php
//$updateHotelRequestUrl = \yii\helpers\Url::to();

$js = <<<JS
    
    
    $(document).on('click', '.btn-update-hotel-request', function (e) {
        e.preventDefault();
        
        let updateHotelRequestUrl = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-sm');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Hotel request');
        modal.find('.modal-body').load(updateHotelRequestUrl, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
        
        /* $.ajax({
            type: 'post',
            data: {'gds': $('#gds-selector').val()},
            url: url,
            success: function (data) {
                $('#preloader').addClass('d-none');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            },
            error: function (error) {
               // var obj = JSON.parse(error.data); // $.parseJSON( e.data );
                $('#preloader').addClass('d-none');
                console.error(error.responseText);
                
                alert('Server Error: ' + error.statusText);
            }
        });
            
        new PNotify({
            title: 'Error: notes',
            type: 'error',
            text: 'Notes for Expert cannot be blank',
            hide: true
        });*/
            
        return false;
        
    });
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'update-hotel-request-js');