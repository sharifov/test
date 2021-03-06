<?php

use modules\attraction\models\search\AttractionQuoteSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\Attraction */
///* @var $dataProviderQuotes \yii\data\ActiveDataProvider */


\yii\web\YiiAsset::register($this);


$searchModel = new AttractionQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['AttractionQuoteSearch']['atnq_attraction_id'] = $model->atn_id;
$dataProviderQuotes = $searchModel->searchProduct($params);
?>

<div class="attraction-view-search">
    <div class="row">
        <div class="col-md-12">
            <h5 title="atn_id: <?= $model->atn_id?>"> Destination:  (<?=Html::encode($model->atn_destination_code)?>)  <?=Html::encode($model->atn_destination)?></h5>
        </div>
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title">
                        <b>Travelers</b>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <?= \yii\bootstrap4\Html::a('<i class="fa fa-edit warning"></i> Update', null, [
                                    'data-url' => \yii\helpers\Url::to(['/hotel/hotel-room/update-ajax', 'id' => 18 /*$room->hr_id*/]),
                                    'class' => 'btn-update-hotel-room'
                                ])?>
                                <?php //=\yii\bootstrap4\Html::a('<i class="fa fa-remove"></i>', ['hotel-room/delete-ajax', 'id' => $room->hr_id], ['class' => 'btn btn-danger btn-sm'])?>
                            </li>
                            <li>
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="display: block">
                        <div class="col-md-12">
                            <?php if ($model->attractionPaxes) :?>
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
                                    <?php foreach ($model->attractionPaxes as $nr => $pax) : ?>
                                        <tr>
                                            <td title="Pax Id: <?=Html::encode($pax->atnp_id)?>"><?=($nr + 1)?>. Pax</td>
                                            <td><b><?=Html::encode($pax->getPaxTypeName())?></b></td>
                                            <td><?=$pax->atnp_age ?: '-'?></td>
                                            <td><?=Html::encode($pax->atnp_first_name)?> <?=Html::encode($pax->atnp_last_name)?></td>
                                            <td><?=$pax->atnp_dob ? date('Y-M-d', strtotime($pax->atnp_dob)) : '-'?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'atn_date_from:date',
                        'atn_date_to:date',
                    ],
                ]) ?>
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $this->render('_view_product_quote_list', [
                'attractionProduct' => $model,
                'dataProviderQuotes' => $dataProviderQuotes
            ]) ?>
        </div>
    </div>
</div>
