<?php

use yii\bootstrap\Html;

/**
 * @var $availabilityItem array
 * @var $key int
 * @var $attraction \modules\attraction\models\Attraction
 * @var $productKey string
 */
$quoteExist = $attraction->quoteExist($productKey, $availabilityItem['date']);

?>

<tr id="tr-atraction-quote-<?= ($availabilityItem['id']) ?>" class="<?= $quoteExist  ? 'bg-success' : '' ?>">
    <th><?= $key + 1 ?></th>
    <td>
        <div><?= Html::encode($availabilityItem['id']) ?></div>
    </td>
    <td><span class="badge badge-secondary"><?= Html::encode($availabilityItem['date']) ?></span></td>
    <!--<td>
        <span class="ml-2"><i class="fa fa-user"></i> <?/*= (Html::encode($attraction->getAdultsCount())) */?></span>
        <span class="ml-2"><i class="fa fa-child"></i> <?/*= (Html::encode($attraction->getChildCount())) */?></span>
        <span class="ml-2"><i class="fas fa-baby"></i> <?/*= (Html::encode($attraction->getInfantsCount())) */?></span>
    </td>-->
    <td><?= Html::encode(empty($availabilityItem['guidePriceFormattedText'])) ? ' - ' : $availabilityItem['guidePriceFormattedText'] ?></td>
    <td class="text-right">
        <?php if ($quoteExist) : ?>
            <span class="badge badge-white">Added</span>
        <?php else : ?>
            <?= \yii\bootstrap4\Html::a('<i class="fa fa-plus"></i> Get Options', null, [
                'data-url' => \yii\helpers\Url::to(['/attraction/attraction-quote/check-availability-ajax', 'atn_id' => $attraction->atn_id]),
                'data-availability-key' => $availabilityItem['id'] ?? '',
                'data-atn-id' => $attraction->atn_id,
                'class' => 'btn btn-success btn-sm btn-availability-quote'
            ]) ?>
        <?php endif; ?>
    </td>
</tr>
<tr>
    <td id="<?= $availabilityItem['id'] ?>" colspan="6"></td>
</tr>


