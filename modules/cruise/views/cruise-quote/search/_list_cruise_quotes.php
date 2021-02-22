<?php

/* @var $this yii\web\View */
/* @var $dataCruise array */
/* @var $index int */
/* @var $key int */
/* @var $cruise Cruise */

/* @var $existsQuote array */

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

    <div class="quote">
        <div class="quote__heading">
            <div class="quote__heading-left">
        <span class="quote__id">
          <strong>#<?= $key + 1 ?></strong>
        </span>
                <span class="quote__vc">
        <?= $dataCruise['cruiseLine']['name'] ?>, <?= $dataCruise['length'] ?> days
        </span>
            </div>
            <div class="quote__heading-right">
        <span class="quote__vc">

            <?php if (!empty($dataCruise['cabins'][0]['price'])) : ?>
                <span class="mr-1">
                <strong>
                  From:
                </strong>
              </span>

                <strong class="text-success">
                $<?= $dataCruise['cabins'][0]['price'] ?>
              </strong>

            <?php endif; ?>
        </span>
            </div>

        </div>

        <div class="quote__wrapper">
            <div class="">
                <div class="row">
                    <div class="col-3">
                        <img
                                src="<?= $dataCruise['ship']['shipImage']['standard'] ?>"
                                alt="liner-model" class="img-thumbnail">
                    </div>
                    <div class="col-9">
                        <h5 class="mb-2">
                            <img src="<?= $dataCruise['cruiseLine']['logoImage']['standard'] ?>"
                                 alt="<?= $dataCruise['cruiseLine']['name'] . ', ' . $dataCruise['ship']['name'] ?> height="
                                 20" class="mr-1"><span class="mr-1"><?= $dataCruise['ship']['name'] ?></span></h5>
                        <div class="text-secondary" style="font-size: 14px">
                            <div class="mb-2">
                                <i class="fa fa-map-marker"></i>
                                <span><strong>Destination:</strong> <?= $dataCruise['itinerary']['destination']['destination'] ?> (<?= $dataCruise['itinerary']['destination']['subDestination'] ?></span>
                            </div>
                            <div class="mb-2">
                      <span>
                        <i class="fa fa-calendar"></i>
                        <strong>Dates:</strong> <?= date('F j, Y', strtotime($dataCruise['departureDate'])) ?> - <?= date('F j, Y', strtotime($dataCruise['returnDate'])) ?>
                            </div>
                            <?php if (!empty($dataCruise['itinerary']['locations'])) : ?>
                                <div class="mb-2">
                                    <i class="fa fa-ship"></i>
                                    <strong>Itinerary:</strong>
                                    <ul class="list-inline d-inline">
                                        <?php foreach ($dataCruise['itinerary']['locations'] as $keyLocation => $location) : ?>
                                            <li class="list-inline-item">
                                                <?= $location['location']['name'] ?>
                                                (<?= $location['location']['countryName'] ?>)
                                                <?php
                                                if ($keyLocation + 1 < count($dataCruise['itinerary']['locations'])) {
                                                    echo '→';
                                                }
                                                ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ($dataCruise['cabins']) : ?>
                    <table class="table table-bordered table-sm" style="font-size: 14px">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th></th>
                            <th>Cabin</th>
                            <th>Experience</th>
                            <th>Travelers</th>
                            <th>Price</th>
                            <th class="text-right"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($dataCruise['cabins'] as $keyCabin => $cabin) : ?>
                            <?php $isExist = in_array($dataCruise['id'] . $cabin['code'], $existsQuote, false); ?>
                            <tr id="tr-cruise-quote-<?= ($dataCruise['id'] . $cabin['code']) ?>"
                                <?php
                                if ($isExist) {
                                    echo ' class="table-success"';
                                }
                                ?>
                            >
                                <th><?= $keyCabin + 1 ?></th>
                                <td>
                                    <img src="<?= $cabin['imgUrl'] ?>" class="img-thumbnail"
                                         style="max-width: 70px; max-height: 70px;">
                                </td>
                                <td>
                                    <div><?= $cabin['name'] ?></div>
                                </td>
                                <td><?= $cabin['experience'] ?></td>
                                <td>
                                    <?php if ($cruise->getAdults()) : ?>
                                        <span class="ml-2"><?= $cruise->getAdults() ?> <i
                                                    class="fa fa-user text-secondary"></i></span>
                                    <?php endif; ?>
                                    <?php if ($cruise->getChildren()) : ?>
                                        <span class="ml-2"><?= $cruise->getChildren() ?> <i
                                                    class="fa fa-child text-secondary"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?= $cabin['price'] ?></td>
                                <td>
                                    <?php if ($isExist) : ?>
                                    <button
                                            class="btn btn-sm btn-success btn-add-cruise-quote"
                                            disabled="disabled"
                                    ><i class="fa fa-check"></i> Added
                                    </button>
                                </td>
                                    <?php else : ?>
                                    <button
                                            class="btn btn-sm btn-success btn-add-cruise-quote"
                                            data-url="<?= Url::to(['/cruise/cruise-quote/add-ajax?cruiseId=' . $cruise->crs_id]) ?>"
                                            data-cruise-quote-id="<?= $dataCruise['id'] ?>"
                                            data-cabin-code="<?= $cabin['code'] ?>"
                                    >Add quote
                                    </button></td>
                                    <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>
    </div>

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