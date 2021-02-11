<?php

/* @var $this yii\web\View */
/* @var $dataCruise array */
/* @var $index int */
/* @var $key int */
/* @var $cruise Cruise */

use modules\cruise\src\entity\cruise\Cruise;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;

//$roomDataProvider = new ArrayDataProvider([
//    'allModels' => $dataHotel['rooms'] ?? [],
//    'pagination' => [
//        'pageSize' => 15,
//        'pageParam' => 'qh-page' . $key
//    ],
//]);
//


?>

<div class="quote__wrapper">
    <div class="offer">
        <div class="row">
            <div class="col col-12">
                <div class="d-flex">
                    <div class="offer__preview px-3">
                        <img
                                src="<?= $dataCruise['ship']['shipImage']['standard'] ?>"
                                alt="liner-model" class="img-thumbnail">
                    </div>
                    <div class="offer__description w-100">
                        <div class="offer__item-brand d-flex flex-column mb-3">
                            <h5 class="mb-0">
                                <img height="20px" src="<?= $dataCruise['cruiseLine']['logoImage']['standard'] ?>" alt="<?= $dataCruise['cruiseLine']['name'] . ', ' . $dataCruise['ship']['name'] ?>" class="cruise-line-logo">
                                <?= $dataCruise['ship']['name'] ?>
                            </h5>
                        </div>
                        <ul class="offer__option-list list-unstyled mb-4">
                            <li class="offer__option mb-2">
                                <div class="d-flex">
                                    <div>
                                        <b class="offer-option__key text-secondary">Destination</b>: <?= $dataCruise['itinerary']['destination']['destination'] ?> (<?= $dataCruise['itinerary']['destination']['subDestination'] ?>)
                                    </div>
                                    <div class="ml-4">
                                        <b class="offer-option__key text-secondary">Dates</b>:
                                        <span class="offer-option__value"><?= date('F j, Y', strtotime($dataCruise['departureDate'])) ?> - <?= date('F j, Y', strtotime($dataCruise['returnDate']))?></span>
                                    </div>
                                </div>
                            </li>
                            <?php if (!empty($dataCruise['itinerary']['locations'])) : ?>
                                <li class="offer__option d-flex">
                                    <b class="offer-option__key text-secondary">Itinerary</b>:
                                    <ul class="offer-option__value list-unstyled d-flex offer__itinerary-list flex-wrap">
                                        <?php foreach ($dataCruise['itinerary']['locations'] as $location) : ?>
                                            <li>
                                                <span><?= $location['location']['name']?> (<?= $location['location']['countryName']?>)</span>
                                            </li>
                                        <?php endforeach;?>
                                    </ul>
                                </li>
                            <?php endif;?>
                        </ul>
                        <?php if ($dataCruise['cabins']) : ?>
                            <h6 class="mb-3">Choose cabin experience</h6>
                            <ul class="list-unstyled mb-0 offer__cabin-table">

                                <li>
                                    <table class="table mb-0">
                                        <tbody>
                                        <?php foreach ($dataCruise['cabins'] as $cabin) : ?>
                                            <tr id="tr-cruise-quote-<?= ($dataCruise['id'] . $cabin['code']) ?>">
                                                <td style="width: 200px"><img width="150px" src="<?= $cabin['imgUrl'] ?>"></td>
                                                <td><?= $cabin['name'] ?></td>
                                                <td class="text-right">$<?= $cabin['price'] ?></td>
                                                <td>
                                                    <button
                                                            class="btn btn-sm btn-success btn-add-cruise-quote"
                                                            data-url="<?= Url::to(['/cruise/cruise-quote/add-ajax?cruiseId=' . $cruise->crs_id])?>"
                                                            data-cruise-quote-id="<?= $dataCruise['id'] ?>"
                                                            data-cabin-code="<?= $cabin['code'] ?>"
                                                    >Add quote</button>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </li>
                            </ul>
                        <?php endif;?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
    <hr>
<?php /*
<table class="table table-striped table-bordered">
    <tr>
        <td style="width: 70px">
            <?=($key + 1)?>
        </td>
        <td title="code: <?=\yii\helpers\Html::encode($dataCruise['cruiseLine']['code'])?>">
            <i class="fa fa-ship"></i> <b><?=\yii\helpers\Html::encode($dataCruise['cruiseLine']['name'])?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?php //php \yii\helpers\VarDumper::dump($model, 3, true)?>

            <?php \yii\widgets\Pjax::begin(['timeout' => 15000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false]); ?>
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $roomDataProvider,
                'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered',
                ],
                'emptyText' => '<div class="text-center">Not found rooms</div><br>',
                'itemView' => function ($modelRoom, $key, $index, $widget) use ($dataHotel, $hotelSearch) {
                    return $this->render('_list_hotel_room_quotes', ['dataRoom' => $modelRoom, 'dataHotel' => $dataHotel, 'index' => $index, 'key' => $key, 'hotelSearch' => $hotelSearch]);
                },
                //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
                'itemOptions' => [
                    //'class' => 'item',
                    'tag' => false,
                ],
            ]) ?>

            <?php \yii\widgets\Pjax::end(); ?>
        </td>
    </tr>
</table>

*/ ?>