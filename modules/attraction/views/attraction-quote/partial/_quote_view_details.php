<?php

use yii\helpers\Html;
use modules\hotel\assets\HotelAsset;

/**
 * @var $attractionQuote \modules\attraction\models\AttractionQuote
 */

HotelAsset::register($this);
?>

<div class="quote__details">
    <h4 class="trip__subtitle">
        <span class="trip__leg-type">Date</span>
        <span class="trip__leg-date"> <?= Yii::$app->formatter_search->asDatetime(strtotime($attractionQuote->atnq_availability_date), 'EEE d MMM')?></span>
    </h4>

    <div class="quote">
        <div class="quote__wrapper">
            <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <img src="<?= $attractionQuote->atnq_product_details_json['product']['previewImage']['url'] ?>"
                         alt="Preview" class="img-thumbnail">
                </div>
                <div class="col-9">
                    <h5 class="mb-2">
                        <span class="mr-1"><?= Html::encode($attractionQuote->atnq_product_details_json['product']['name']) ?></span>
                    </h5>
                    <div class="mb-4">
                        <span title="Supplier"><i class="fas fa-hands-helping mr-1 text-info"></i><?= Html::encode($attractionQuote->atnq_product_details_json['product']['supplierName'] ?? '') ?></span><br>
                        <span title="min duration"><i class="fa fa-clock mr-2 text-info"></i><?= Html::encode($attractionQuote->atnq_product_details_json['product']['minDuration'] ?? '') ?></span><br>
                        <span title="Max duration"><i class="fa fa-clock mr-2 text-info"></i><?= Html::encode($attractionQuote->atnq_product_details_json['product']['maxDuration'] ?? '') ?></span>
                    </div>
                    <div>
                        <p>
                            <?= Html::encode($attractionQuote->atnq_product_details_json['product']['abstract'] ?? '') ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <table class="table table-bordered mt-3">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Pricing Category</th>
                            <th>Min Age</th>
                            <th>Max Age</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($attractionQuote->attractionQuotePricingCategories as $categoryKey => $category) : ?>
                            <tr>
                                <th><?php echo $categoryKey + 1 ?></th>
                                <td><?= $category['atqpc_label']?></td>
                                <td><?= $category['atqpc_min_age']?></td>
                                <td><?= $category['atqpc_max_age']?></td>
                                <td><?= $category['atqpc_quantity']?></td>
                                <td><?= $category['atqpc_price']?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-6">
                    <table class="table table-bordered mt-3">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Is Answered</th>
                            <th>Answer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($attractionQuote->attractionQuoteOptions as $optionKey => $option) : ?>
                            <tr>
                                <th><?php echo $optionKey + 1 ?></th>
                                <td><?= $option['atqo_label']?></td>
                                <td><?= $option['atqo_is_answered'] ? '<span class="label-success label">Yes</span>' : '<span class="label-success label">No</span>' ?></td>
                                <td><?= $option['atqo_answer_formatted_text']?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>