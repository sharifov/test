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
                    <?php if (isset($attractionQuote->atnq_product_details_json['product']['previewImage']['url'])) : ?>
                    <img src="<?= $attractionQuote->atnq_product_details_json['product']['previewImage']['url'] ?>"
                         alt="Preview" class="img-thumbnail">
                    <?php else : ?>
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g transform="translate(1 1)"> <g>
                                    <g>
                                        <path d="M255-1C114.2-1-1,114.2-1,255s115.2,256,256,256s256-115.2,256-256S395.8-1,255-1z M255,16.067
                                c63.054,0,120.598,24.764,163.413,65.033l-65.336,64.802L334.36,97.987c-0.853-2.56-4.267-5.12-7.68-5.12H185.027
                                c-3.413,0-5.973,1.707-7.68,5.12L156.013,152.6h-48.64c-17.067,0-30.72,13.653-30.72,30.72v168.96
                                c0,17.067,13.653,30.72,30.72,30.72h6.653l-34.26,33.981C40.285,374.319,16.067,317.354,16.067,255
                                C16.067,123.587,123.587,16.067,255,16.067z M314.733,255c0,33.28-26.453,59.733-59.733,59.733
                                c-13.563,0-25.99-4.396-35.957-11.854l84.125-83.438C310.449,229.34,314.733,241.616,314.733,255z M195.267,255
                                c0-33.28,26.453-59.733,59.733-59.733c13.665,0,26.174,4.467,36.179,12.028l-84.183,83.495
                                C199.613,280.852,195.267,268.487,195.267,255z M303.374,195.199C290.201,184.558,273.399,178.2,255,178.2
                                c-42.667,0-76.8,34.133-76.8,76.8c0,18.17,6.206,34.779,16.61,47.877l-63.576,63.057H106.52c-7.68,0-13.653-5.973-13.653-13.653
                                V183.32c0-7.68,5.973-13.653,13.653-13.653h54.613c3.413,0,6.827-2.56,7.68-5.12l21.333-54.613h129.707l19.404,49.675
                                L303.374,195.199z M206.848,314.974C219.987,325.509,236.703,331.8,255,331.8c42.667,0,76.8-34.133,76.8-76.8
                                c0-18.068-6.138-34.592-16.436-47.655l37.988-37.678h49.274c7.68,0,13.653,5.973,13.653,13.653v168.96
                                c0,7.68-5.973,13.653-13.653,13.653H155.469L206.848,314.974z M255,493.933c-62.954,0-120.415-24.686-163.208-64.843L138.262,383
                                H403.48c17.067,0,30.72-13.653,31.573-30.72V183.32c0-17.067-13.653-30.72-30.72-30.72H370.56l59.865-59.376
                                c39.368,42.639,63.509,99.521,63.509,161.776C493.933,386.413,386.413,493.933,255,493.933z"/>
                                <path d="M383,186.733c-9.387,0-17.067,7.68-17.067,17.067c0,9.387,7.68,17.067,17.067,17.067s17.067-7.68,17.067-17.067
                                C400.067,194.413,392.387,186.733,383,186.733z"/> </g> </g> </g> <g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                        </svg>
                    <?php endif; ?>
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