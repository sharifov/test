<?php
/**
 * @var $this View
 * @var $quote ProductQuote
 * @var $flightQuote FlightQuote
 * @var $priceData FlightQuotePriceDataDTO
 */

use kartik\editable\Editable;
use modules\flight\models\FlightQuote;
use modules\flight\src\helpers\FlightQuotePriceDataDTO;
use modules\product\src\entities\productQuote\ProductQuote;
use yii\helpers\Url;
use yii\web\View;

?>
<table class="table table-bordered table-striped" id="quote-prices-<?= $quote->pq_id?>">
	<thead>
        <tr>
            <th>Pax</th>
            <th>Q</th>
            <th>NP, $</th>
            <th>Mkp, $</th>
            <th>Ex Mkp, $</th>
            <th>SFP, %</th>
            <th>SFP, $</th>
            <th>SP, $</th>
            <th>CSP, <?= $quote->pqClientCurrency->cur_symbol ?></th>
        </tr>
	</thead>
	<tbody>
	<?php foreach ($priceData->prices as $paxCode => $price):?>
        <?php $count = $price->tickets ?: 1; ?>
		<tr>
			<th><?= $paxCode ?></th>
			<td>x <?= $count ?></td>
			<td><?= number_format($price->net / $count, 2) ?></td>
			<td><?= number_format($price->markUp / $count, 2) ?></td>
			<td><?php if($quote->isNew()):?>
					<?= Editable::widget([
						'name'=>'extra_markup['.strtoupper($paxCode).']['.$flightQuote->fq_id.']',
						'asPopover' => false,
						'pjaxContainerId' => 'pjax-quote_prices-'.$quote->pq_id,
						'value' => number_format($price->extraMarkUp / $count, 2),
						'header' => 'Extra markup',
						'size'=>'sm',
						'inputType' => Editable::INPUT_TEXT,
						'buttonsTemplate' => '{submit}',
						'pluginEvents' => ["editableSuccess" => "function(event, val, form, data) { $.pjax.reload({container: '#pjax-quote_prices-{$flightQuote->fq_id}', async: false}); $('#quote_profit_{$flightQuote->fq_id}').popover('hide').popover('dispose');$.pjax.reload({container: '#pjax-quote_estimation_profit-{$flightQuote->fq_id}', async: false});$('#quote_profit_{$flightQuote->fq_id}').popover();}",],
						'inlineSettings' => [
							'templateBefore' => '<div class="editable-pannel">{loading}',
							'templateAfter' => '{buttons}{close}</div>'],
						'options' => ['class'=>'form-control','style'=>'width:50px;', 'placeholder'=>'Enter extra markup','resetButton' => '<i class="fa fa-ban"></i>'],
                        'formOptions' => [
                                'action' => Url::toRoute(['/flight/flight-quote/ajax-update-agent-markup'])
                        ]
					]) ?>
				<?php else:?>
					<?= number_format($price->extraMarkUp / $count, 2)?>
				<?php endif;?>
			</td>
			<td><?= number_format($priceData->serviceFeePercent, 2) ?> %</td>
			<td><?= number_format($price->serviceFee / $count, 2) ?> </td>
			<td><?= number_format($price->selling / $count, 2) ?></td>
			<td><?= number_format($price->clientSelling / $count, 2) ?></td>
		</tr>
	<?php endforeach;?>
	</tbody>
	<tfoot>
	<tr>
		<th>Total</th>
		<td><?= $priceData->total->tickets?></td>
		<td><?= number_format($priceData->total->net, 2)?></td>
		<td><?= number_format($priceData->total->markUp, 2)?></td>
		<td class="total-markup-<?= $quote->pq_id ?>"><?= number_format($priceData->total->extraMarkUp, 2)?></td>
		<td><?= number_format($priceData->serviceFeePercent, 2) ?> %</td>
		<td><?= number_format($priceData->total->serviceFeeSum, 2) ?></td>
		<td class="total-sellingPrice-<?= $quote->pq_id ?>"><?= number_format($priceData->total->selling, 2)?></td>
		<td class="total-sellingPrice-<?= $quote->pq_id ?>"><?= number_format($priceData->total->clientSelling, 2)?></td>
	</tr>
	</tfoot>
</table>