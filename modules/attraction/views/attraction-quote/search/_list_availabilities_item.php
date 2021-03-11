<?php

use yii\bootstrap\Html;

/**
 * @var $availabilityItem array
 * @var $key int
 * @var $attractionSearch \modules\attraction\models\Attraction
 */

?>

<tr id="tr-atraction-quote-<?= ($availabilityItem['id']) ?>" class="tr-hotel-quote-<?= ($availabilityItem['id']) ?> <?= $quoteExist = false ? 'bg-success' : '' ?>">
    <th><?= $key + 1 ?></th>
    <td>
        <div><?= Html::encode($availabilityItem['id']) ?></div>
    </td>
    <td><span class="badge badge-secondary"><?= Html::encode($availabilityItem['date']) ?></span></td>
    <td>
        <span class="ml-2"><i class="fa fa-user"></i> <?= (Html::encode($attractionSearch->getAdultsCount())) ?></span>
        <span class="ml-2"><i class="fa fa-child"></i> <?= (Html::encode($attractionSearch->getChildCount())) ?></span>
        <span class="ml-2"><i class="fas fa-baby"></i> <?= (Html::encode($attractionSearch->getInfantsCount())) ?></span>
    </td>
    <!--<td colspan="2">$<?php /*=number_format(Html::encode($test = 100 ?? 0), 2)*/ ?></td>-->
    <!--<td><?/*= Html::encode($availabilityItem['guidePriceFormattedText']) */?></td>-->
    <td><?= Html::encode(empty($availabilityItem['guidePriceFormattedText'])) ? /*rand(20, 350)*/  ' - ' : $availabilityItem['guidePriceFormattedText'] ?></td> <!--this line is for presetation only-->
    <td class="text-right">
        <?php if ($quoteExist = false) : ?>
            <span class="badge badge-white">Added</span>
        <?php else : ?>
            <?= \yii\bootstrap4\Html::a('<i class="fa fa-plus"></i> Get Options', null, [
                'data-url' => \yii\helpers\Url::to(['/attraction/attraction-quote/check-availability-ajax', 'atn_id' => $attractionSearch->atn_id]),
                'data-availability-key' => $availabilityItem['id'] ?? '',
                'class' => 'btn btn-success btn-sm btn-availability-quote'
            ]) ?>
        <?php endif; ?>

        <?php if ($quoteExist = false) : ?>
            <span class="badge badge-white">Added</span>
        <?php else : ?>
            <?php /*= \yii\bootstrap4\Html::a('<i class="fa fa-plus"></i> add Quote', null, [
                'data-url' => \yii\helpers\Url::to(['/attraction/attraction-quote/add-ajax', 'atn_id' => $attractionSearch->atn_id]),
                //'data-quote-key' => $availabilityItem['id'] ?? '',
                'data-quote-key' => $availabilityItem['presentation_product_id'] ?? '', //for presentation only
                'data-date' => $availabilityItem['date'], //for presentation only
                'class' => 'btn btn-success btn-sm btn-add-attraction-quote'
            ]) */?>
        <?php endif; ?>
    </td>
</tr>
<tr>
    <td id="<?= $availabilityItem['id'] ?>" colspan="6"></td>
</tr>


