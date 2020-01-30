<?php
/**
 * @var $this View
 * @var $quote ProductQuote
 * @var $flightQuote FlightQuote
 * @var $priceData array
 */

use kartik\editable\Editable;
use modules\flight\models\FlightQuote;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\web\View;

?>
<table class="table table-striped table-prices" id="quote-prices-<?= $quote->pq_id?>">
	<thead>
	<tr>
		<th>Pax</th>
		<th>Q</th>
		<th>NP, $</th>
		<th>Mkp, $</th>
		<th>Ex Mkp, $</th>
		<th>SP, $</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($priceData['prices'] as $paxCode => $price):?>
		<tr>
			<th><?= $paxCode?></th>
			<td>x <?= $price['tickets']?></td>
			<td><?= number_format($price['net'] / $price['tickets'], 2) ?></td>
			<td><?= number_format($price['mark_up'] / $price['tickets'], 2) ?></td>
			<td><?php if(ProductQuoteStatus::isNews($quote->pq_status_id)):?>
					<?= Editable::widget([
						'name'=>'extra_markup['.strtolower($paxCode).']['.$quote->pq_id.']',
						'asPopover' => false,
						'pjaxContainerId' => 'pjax-quote_prices-'.$quote->pq_id,
						'value' => number_format($price['extra_mark_up'] / $price['tickets'], 2),
						'header' => 'Extra markup',
						'size'=>'sm',
						'inputType' => Editable::INPUT_TEXT,
						'buttonsTemplate' => '{submit}',
						'pluginEvents' => ["editableSuccess" => "function(event, val, form, data) { $.pjax.reload({container: '#pjax-quote_prices-{$quote->pq_id}', async: false}); $('#quote_profit_{$quote->pq_id}').popover('hide').popover('dispose');$.pjax.reload({container: '#pjax-quote_estimation_profit-{$quote->pq_id}', async: false});$('#quote_profit_{$quote->pq_id}').popover();}",],
						'inlineSettings' => [
							'templateBefore' => '<div class="editable-pannel">{loading}',
							'templateAfter' => '{buttons}{close}</div>'],
						'options' => ['class'=>'form-control','style'=>'width:50px;', 'placeholder'=>'Enter extra markup','resetButton' => '<i class="fa fa-ban"></i>']
					]) ?>
				<?php else:?>
					<?= number_format($price['extra_mark_up'] / $price['tickets'], 2)?>
				<?php endif;?>
			</td>
			<td><?= number_format($price['selling'] / $price['tickets'], 2) ?></td>
		</tr>
	<?php endforeach;?>
	</tbody>
	<tfoot>
	<tr>
		<th>Total</th>
		<td><?= $priceData['total']['tickets']?></td>
		<td><?= number_format($priceData['total']['net'], 2)?></td>
		<td><?= number_format($priceData['total']['mark_up'], 2)?></td>
		<td class="total-markup-<?= $quote->pq_id ?>"><?= number_format($priceData['total']['extra_mark_up'], 2)?></td>
		<td class="total-sellingPrice-<?= $quote->pq_id ?>"><?= number_format($priceData['total']['selling'], 2)?></td>
	</tr>
	</tfoot>
</table>