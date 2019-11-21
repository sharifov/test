<?php
/**
 * @var $this \yii\web\View
 * @var $quote \common\models\Quote
 */

use kartik\editable\Editable;
use yii\helpers\VarDumper;
?>
<?php $priceData = $quote->getPricesData();?>
<table class="table table-striped table-prices" id="quote-prices-<?= $quote->id?>">
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
            <td><?php if($quote->isEditable()):?>
                <?= Editable::widget([
                        'name'=>'extra_markup['.strtolower($paxCode).']['.$quote->id.']',
                        'asPopover' => false,
                        'pjaxContainerId' => 'pjax-quote_prices-'.$quote->id,
                        'value' => number_format($price['extra_mark_up'] / $price['tickets'], 2),
                        'header' => 'Extra markup',
                        'size'=>'sm',
                        'inputType' => Editable::INPUT_TEXT,
                        'buttonsTemplate' => '{submit}',
                        'pluginEvents' => ["editableSuccess" => "function(event, val, form, data) { $.pjax.reload({container: '#pjax-quote_prices-{$quote->id}', async: false}); $('#quote_profit_{$quote->id}').popover('hide').popover('destroy');$.pjax.reload({container: '#pjax-quote_estimation_profit-{$quote->id}', async: false});$('#quote_profit_{$quote->id}').popover();}",],
                        'inlineSettings' => [
                            'templateBefore' => '<div class="editable-pannel">{loading}',
                            'templateAfter' => '{buttons}{close}</div>'],
                        'options' => ['class'=>'form-control','style'=>'width:50px;', 'placeholder'=>'Enter extra markup','resetButton' => '<i class="fa fa-ban"></i>']
                    ]);?>
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
            <td class="total-markup-<?= $quote->uid ?>"><?= number_format($priceData['total']['extra_mark_up'], 2)?></td>
            <td class="total-sellingPrice-<?= $quote->uid ?>"><?= number_format($priceData['total']['selling'], 2)?></td>
        </tr>
	</tfoot>
</table>