<?php

use modules\hotel\models\search\HotelQuoteSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\Hotel */
///* @var $dataProviderQuotes \yii\data\ActiveDataProvider */


\yii\web\YiiAsset::register($this);


$searchModel = new HotelQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['HotelQuoteSearch']['hq_hotel_id'] = $model->ph_id;
$dataProviderQuotes = $searchModel->searchProduct($params);

?>
<div class="hotel-view-search">

<!--    <h2>Hotel Request ID: --><?//= Html::encode($model->ph_id) ?><!--</h2>-->


    <div class="row">
        <div class="col-md-5">
<!--        <p>-->
<!--            --><?//= Html::a('<i class="fa fa-edit"></i> Update Request', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel/update-ajax', 'id' => $model->ph_id]), 'data-hotel-id' => $model->ph_id, 'class' => 'btn btn-warning btn-update-hotel-request']) ?>
<!--        </p>-->
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ph_id',
                //'ph_product_id',
                'ph_check_in_dt:date',
                'ph_check_out_dt:date',
                'ph_destination_code',
                'ph_min_star_rate',
                'ph_max_star_rate',
                'ph_max_price_rate',
                'ph_min_price_rate',
            ],
        ]) ?>
    </div>
        <div class="col-md-7">
<!--        <p>-->
<!--            --><?//= Html::a('<i class="fa fa-plus"></i> Add Room', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel-room/create-ajax', 'id' => $model->ph_id]), 'data-hotel-id' => $model->ph_id, 'class' => 'btn btn-success btn-add-hotel-room']) ?>
<!--        </p>-->
        <?php Pjax::begin(['id' => 'pjax-hotel-rooms-' . $model->ph_id]); ?>

        <?php if ($model->hotelRooms):?>
            <?php foreach ($model->hotelRooms as $rk => $room): ?>

            <div class="x_panel">
                <div class="x_title">
                    <h2>
<!--                        <i class="fa fa-check-square-o"></i> -->
                        <?=($rk + 1)?>. <span title="RoomID: <?=Html::encode($room->hr_id)?>">Room</span><?=$room->hr_room_name ? ': ' . Html::encode($room->hr_room_name) : ''?>
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <?= \yii\bootstrap4\Html::a('<i class="fa fa-edit warning"></i> Update', null, [
                                'data-url' => \yii\helpers\Url::to(['/hotel/hotel-room/update-ajax', 'id' => $room->hr_id]),
                                'class' => 'btn-update-hotel-room'
                            ])?>
                            <?//=\yii\bootstrap4\Html::a('<i class="fa fa-remove"></i>', ['hotel-room/delete-ajax', 'id' => $room->hr_id], ['class' => 'btn btn-danger btn-sm'])?>
                        </li>

                        <?//php if ($is_manager) : ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                            <div class="dropdown-menu" role="menu">

                                <?= \yii\bootstrap4\Html::a('<i class="fa fa-remove"></i> Delete room', null, [
                                    'data-url' => \yii\helpers\Url::to(['/hotel/hotel-room/delete-ajax']),
                                    'data-room-id' => $room->hr_id,
                                    'data-hotel-id' => $model->ph_id,
                                    'class' => 'dropdown-item text-danger btn-delete-hotel-room'
                                ]) ?>


                            </div>
                        </li>
                        <?//php endif; ?>
                        <li>
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">
                    <div class="col-md-12">
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
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>


        <?php Pjax::end(); ?>
    </div>
    </div>

<!--    <div class="row">-->
<!--        <div class="col-md-12">-->
<!--            <p>-->
<!--                --><?//= Html::a('<i class="fa fa-search"></i> Search Quotes', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel-quote/search-ajax', 'id' => $model->ph_id]), 'data-hotel-id' => $model->ph_id, 'class' => 'btn btn-warning btn-search-hotel-quotes']) ?>
<!--            </p>-->
<!--        </div>-->
<!--    </div>-->

    <div class="row">
        <div class="col-md-12">
            <?= $this->render('_view_product_quote_list', [
                'hotelProduct' => $model,
                'dataProviderQuotes' => $dataProviderQuotes
            ]) ?>
        </div>
    </div>



</div>