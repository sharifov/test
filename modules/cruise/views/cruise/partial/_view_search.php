<?php

use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruiseQuote\search\CruiseQuoteSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model Cruise */

\yii\web\YiiAsset::register($this);


$searchModel = new CruiseQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['CruiseQuoteSearch']['crq_cruise_id'] = $model->crs_id;
$dataProviderQuotes = $searchModel->searchProduct($params);

?>
<div class="cruise-view-search">
    
    <div class="row">
            <div class="col-md-12">

                <h5 title="crs_id: <?=$model->crs_id?>"> Destination: (<?=Html::encode($model->crs_destination_code)?>) <?=Html::encode($model->crs_destination_label)?> </h5>
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'crs_departure_date_from:date',
                            'crs_arrival_date_to:date',
                        ],
                    ]) ?>
                </div>
                
            </div>


            <div class="col-md-12">

                <?php Pjax::begin(['id' => 'pjax-cruise-cabins-' . $model->crs_id]); ?>

                <?php if ($model->cabins) :?>
                    <?php foreach ($model->cabins as $rk => $cabin) : ?>
                    <div class="col-md-6">
                        <div class="x_panel">
                        <div class="x_title">
                            <b>
        <!--                        <i class="fa fa-check-square-o"></i> -->
                                <?=($rk + 1)?>. <span title="CabinID: <?=Html::encode($cabin->crc_id)?>">Cabin</span><?=$cabin->crc_name ? ': ' . Html::encode($cabin->crc_name) : ''?> |
                                <?=$cabin->getAdultCount() ? '<i class="fa fa-user"></i> ' . $cabin->getAdultCount() : ''?>
                                <?=$cabin->getChildrenCount() ? ', <i class="fa fa-child"></i> ' . $cabin->getChildrenCount() : ''?>
                            </b>
                            <ul class="nav navbar-right panel_toolbox">


                                <?php //php if ($is_manager) : ?>
                                <!--                    <li>-->
                                <!--                        --><?php //=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
                                <!--                    </li>-->

                                <?php //php endif; ?>
                                <li>
                                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" style="display: block">
                            <div class="col-md-12">
                                <?php if ($cabin->paxes) :?>
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
                                        <?php foreach ($cabin->paxes as $nr => $pax) : ?>
                                        <tr>
                                            <td title="Pax Id: <?=Html::encode($pax->crp_id)?>"><?=($nr + 1)?>. Pax</td>
                                            <td><b><?=Html::encode($pax->getPaxTypeName())?></b></td>
                                            <td><?=$pax->crp_age ?: '-'?></td>
                                            <td><?=Html::encode($pax->crp_first_name)?> <?=Html::encode($pax->crp_last_name)?></td>
                                            <td><?=$pax->crp_dob ? date('Y-M-d', strtotime($pax->crp_dob)) : '-'?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>


                <?php Pjax::end(); ?>
            </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model->product,
                    'attributes' => [
                        'pr_market_price',
                        'pr_client_budget',
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <!--    <div class="row">-->
<!--        <div class="col-md-12">-->
<!--            <p>-->
<!--                --><?php //= Html::a('<i class="fa fa-search"></i> Search Quotes', null, ['data-url' => \yii\helpers\Url::to(['/cruise/cruise-quote/search-ajax', 'id' => $model->ph_id]), 'data-cruise-id' => $model->ph_id, 'class' => 'btn btn-warning btn-search-cruise-quotes']) ?>
<!--            </p>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="row">-->
<!--        <div class="col-md-12">-->
    <?= $this->render('_view_product_quote_list', [
        'cruiseProduct' => $model,
        'dataProviderQuotes' => $dataProviderQuotes
    ]) ?>
<!--        </div>-->
<!--    </div>-->

</div>