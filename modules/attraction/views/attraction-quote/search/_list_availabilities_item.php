<?php

use yii\bootstrap\Html;

/**
 * @var $availabilityItem array
 * @var $key int
 */
?>

<tr id="tr-atraction-quote-<?= ($availabilityItem['id']) ?>" class="tr-hotel-quote-<?= ($availabilityItem['id']) ?> <?= $quoteExist = false ? 'bg-success' : '' ?>">
    <th><?= $key + 1 ?></th>
    <td>
        <div><?= Html::encode($availabilityItem['id']) ?></div>
    </td>
    <td><span class="badge badge-secondary"><?= Html::encode($availabilityItem['date']) ?></span></td>
    <td>
        <span class="ml-2"><i class="fa fa-user"></i> <?= (Html::encode('1' ?? 0)) ?></span>
        <span class="ml-2"><i class="fa fa-child"></i> <?= (Html::encode('1' ?? 0)) ?></span>
    </td>
    <!--<td colspan="2">$<?php /*=number_format(Html::encode($test = 100 ?? 0), 2)*/ ?></td>-->
    <td><?= Html::encode($availabilityItem['guidePriceFormattedText']) ?></td>
    <td class="text-right">
        <?php if ($quoteExist = false) : ?>
            <span class="badge badge-white">Added</span>
        <?php else : ?>
            <?= \yii\bootstrap4\Html::a('<i class="fa fa-plus"></i> add Quote', null, [
                'data-url' => \yii\helpers\Url::to(['/attraction/attraction-quote/add-ajax', 'atn_id' => $attractionSearch->atn_id]),
                'data-quote-key' => $availabilityItem['id'] ?? '',
                'class' => 'btn btn-success btn-sm btn-add-attraction-quote'
            ]) ?>
        <?php endif; ?>
    </td>
</tr>


