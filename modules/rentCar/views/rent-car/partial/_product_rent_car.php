<?php

use modules\product\src\entities\product\Product;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $product Product */

$pjaxId = 'pjax-product-' . $product->pr_id;
?>
<?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 2000])?>
    <div class="x_panel">
        <div class="x_title">
            <h2>
                <a class="collapse-link">
                    <i class="fas fa-car" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                    <span style="color: #53a265" class="product-quote-counter-<?= $product->pr_id ?>" data-value="<?=count($product->productQuotes)?>">
                        <?php if ($product->productQuotes) :?>
                            <sup title="Number of quotes">(<?=count($product->productQuotes)?>)</sup>
                        <?php endif;?>
                    </span>
                </a>
                <?php if ($product->pr_description) :?>
                    <a  id="product_description_<?=$product->pr_id?>"
                        class="popover-class fa fa-info-circle text-info"
                        data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top"
                        data-container="body" title="<?=Html::encode($product->pr_name)?>"
                        data-content='<?=Html::encode($product->pr_description)?>'
                    ></a>
                <?php endif; ?>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                            <?php if ($product->rentCar->prc_pick_up_code) :?>
                                <b><?= Html::encode($product->rentCar->prc_pick_up_code) ?></b>
                            <?php endif; ?>
                        </span>
                    </li>
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                             <?php if ($product->rentCar->prc_pick_up_date) :?>
                                 <b><?= Yii::$app->formatter->asDate(strtotime($product->rentCar->prc_pick_up_date)) ?></b>
                             <?php endif; ?>
                        </span>
                    </li>
                <li>
                    <div style="margin-right: 50px"></div>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars warning"></i> <span class="text-warning">Actions</span></a>
                    <div class="dropdown-menu" role="menu">

                        <h6 class="dropdown-header">P<?=$product->pr_id?> - RC<?= $product->rentCar->prc_id?></h6>

                        <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, [
                            'data-url' => \yii\helpers\Url::to([
                                '/rent-car/rent-car/update-ajax',
                                'id' => $product->rentCar->prc_id
                            ]),
                            'data-model-id' => $product->rentCar->prc_id,
                            'data-product-id' => $product->pr_id,
                            'class' => 'dropdown-item text-warning btn-update-rent-car-request btn-update-request',
                        ]) ?>

                        <?= Html::a('<i class="fa fa-search"></i> Search Quotes', null, [
                            'data-url' => \yii\helpers\Url::to([
                                '/rent-car/rent-car-quote/search-ajax',
                                'id' => $product->rentCar->prc_id
                            ]),
                            'data-model-id' => $product->rentCar->prc_id,
                            'class' => 'dropdown-item text-success btn-search-rent-car-quotes'
                        ]) ?>

                        <div class="dropdown-divider"></div>
                        <?= Html::a('<i class="fa fa-edit"></i> Update Product', null, [
                            'class' => 'dropdown-item text-warning btn-update-product',
                            'data-product-id' => $product->pr_id,
                        ]) ?>
                        <?= Html::a(
                            '<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete Rent Car',
                            null,
                            [
                                'class' => 'dropdown-item text-danger btn-delete-product',
                                'data-product-id' => $product->pr_id
                            ]
                        ) ?>
                    </div>
                </li>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none">

                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-product-search-' . $product->pr_id, 'enablePushState' => false, 'timeout' => 5000])?>
                <?= $this->render('_view_search', [
                    'model' => $product->rentCar,
                ]) ?>
                <?php \yii\widgets\Pjax::end();?>

        </div>
    </div>
<?php \yii\widgets\Pjax::end()?>

<?php

$js = <<<JS

    $('body').off('click', '.btn-update-rent-car-request').on('click', '.btn-update-rent-car-request', function (e) {
        e.preventDefault();
        let updateRentCarRequestUrl = $(this).data('url');
                
        let modal = $('#modal-sm');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Rent Car request');
        modal.find('.modal-body').load(updateRentCarRequestUrl, function(response, status, xhr ) {
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });
    
     $('body').off('click', '.btn-search-rent-car-quotes').on('click', '.btn-search-rent-car-quotes', function (e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Search Ren Car Quotes');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
                $('#preloader').addClass('d-none');
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });    
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'update-rent-car-request-js');
