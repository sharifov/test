<?php

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


    <div class="row">
        <div class="col-md-5">
        <p>
            <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel/update-ajax', 'id' => $model->ph_id]), 'data-hotel-id' => $model->ph_id, 'class' => 'btn btn-warning btn-update-hotel-request']) ?>
        </p>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'ph_id',
                //'ph_product_id',
                'ph_check_in_date:date',
                'ph_check_out_date:date',
                'ph_destination_code',
                'ph_min_star_rate',
                'ph_max_star_rate',
                'ph_max_price_rate',
                'ph_min_price_rate',
            ],
        ]) ?>
    </div>
        <div class="col-md-7">
        <p>
            <?= Html::a('<i class="fa fa-plus"></i> Add Room', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel-room/create-ajax', 'id' => $model->ph_id]), 'data-hotel-id' => $model->ph_id, 'class' => 'btn btn-success btn-add-hotel-room']) ?>
        </p>
        <?php Pjax::begin(['id' => 'pjax-hotel-rooms-' . $model->ph_id]); ?>

        <?php if ($model->hotelRooms):?>
            <?php foreach ($model->hotelRooms as $rk => $room): ?>
                <div class="col-md-12">
                    <h4><?=($rk + 1)?>. <span title="RoomID: <?=Html::encode($room->hr_id)?>">Room</span> <?=$room->hr_room_name ? ': ' . Html::encode($room->hr_room_name) : ''?>
                        <?= \yii\bootstrap4\Html::a('<i class="fa fa-edit"></i> Update', null, [
                            'data-url' => \yii\helpers\Url::to(['/hotel/hotel-room/update-ajax', 'id' => $room->hr_id]),
                            'class' => 'btn btn-outline-warning btn-sm btn-update-hotel-room'
                        ])?>

                        <?//=\yii\bootstrap4\Html::a('<i class="fa fa-remove"></i>', ['hotel-room/delete-ajax', 'id' => $room->hr_id], ['class' => 'btn btn-danger btn-sm'])?>

                        <?= \yii\bootstrap4\Html::a('<i class="fa fa-remove"></i> Delete', null, [
                                'data-url' => \yii\helpers\Url::to(['/hotel/hotel-room/delete-ajax']),
                                'data-room-id' => $room->hr_id,
                                'data-hotel-id' => $model->ph_id,
                                'class' => 'btn btn-outline-danger btn-sm btn-delete-hotel-room'
                        ]) ?>

                    </h4>
                    <hr>
                    <?php if ($room->hotelRoomPaxes):?>
                        <table class="table table-bordered">
                            <thead>
                            <tr class=" bg-info">
                                <th>Nr.</th>
                                <th>Type</th>
                                <th>Age</th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($room->hotelRoomPaxes as $nr => $pax): ?>
                            <tr>
                                <td title="Pax Id: <?=Html::encode($pax->hrp_id)?>"><?=($nr + 1)?>. Pax</td>
                                <td><b><?=Html::encode($pax->getPaxTypeName())?></b></td>
                                <td><?=$pax->hrp_age ?: '-'?></td>
                                <td><?=Html::encode($pax->hrp_first_name)?> <?=Html::encode($pax->hrp_last_name)?></td>
                                <td><?=$pax->hrp_dob ? date('Y-M-d', strtotime($pax->hrp_dob)) : '-'?></td>
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
    </div>

    <div class="row">
        <div class="col-md-12">
            <p>
                <?= Html::a('<i class="fa fa-search"></i> Search Quotes', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel-quote/search-ajax', 'id' => $model->ph_id]), 'data-hotel-id' => $model->ph_id, 'class' => 'btn btn-warning btn-search-hotel-quotes']) ?>
            </p>
        </div>
    </div>
</div>

<?php
//$updateHotelRequestUrl = \yii\helpers\Url::to();

//$deleteRoomUrl = \yii\helpers\Url::to(['/hotel/hotel-room/delete-ajax']);

$js = <<<JS


    $('body').off('click', '.btn-update-hotel-request').on('click', '.btn-update-hotel-request', function (e) {
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
        return false;
    });

    $('body').off('click', '.btn-add-hotel-room').on('click', '.btn-add-hotel-room', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add Room request');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
        return false;
    });
    
    $('body').off('click', '.btn-update-hotel-room').on('click', '.btn-update-hotel-room', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Room request');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
        return false;
    });
    
    
     $('body').off('click', '.btn-search-hotel-quotes').on('click', '.btn-search-hotel-quotes', function (e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Search Hotel Quotes');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            $('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
        return false;
    });
    
    
    
    
    
    
    $('body').off('click', '.btn-delete-hotel-room').on('click', '.btn-delete-hotel-room', function(e) {
        
        if(!confirm('Are you sure you want to delete this room?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let roomId = $(this).data('room-id');
      let hotelId = $(this).data('hotel-id');
      let url = $(this).data('url');
     
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': roomId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: delete room',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  $.pjax.reload({
                      container: '#pjax-hotel-rooms-' + hotelId
                  });
                  new PNotify({
                        title: 'The room was successfully removed',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            //btnSubmit.prop('disabled', false);
            //btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
            //alert( "complete" );
            $('#preloader').addClass('d-none');
        });
      // return false;
    });
    
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'update-hotel-request-js');