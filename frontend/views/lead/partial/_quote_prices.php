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
            <td>x <?= $price['cnt']?></td>
            <td><?= $price['net'] / $price['cnt'] ?></td>
            <td><?= $price['mark_up'] / $price['cnt'] ?></td>
            <td><?php if($quote->isEditable()):?>
                <?= Editable::widget([
                        'name'=>'extra_markup['.strtolower($paxCode).']['.$quote->id.']',
                        'asPopover' => false,
                        'pjaxContainerId' => 'pjax-quote_prices-'.$quote->id,
                        'value' => $price['extra_mark_up'] / $price['cnt'],
                        'header' => 'Extra markup',
                        'size'=>'sm',
                        'inputType' => Editable::INPUT_TEXT,
                        'buttonsTemplate' => '{submit}',
                        'pluginEvents' => ["editableSuccess" => "function(event, val, form, data) { $.pjax.reload({container: '#pjax-quote_prices-{$quote->id}', async: false}); }",],
                        'inlineSettings' => [
                            'templateBefore' => '<div class="editable-pannel">{loading}',
                            'templateAfter' => '{buttons}{close}</div>'],
                        'options' => ['class'=>'form-control','style'=>'width:50px;', 'placeholder'=>'Enter extra markup','resetButton' => '<i class="fas fa-ban"></i>']
                    ]);?>
                <?php else:?>
                	<?= $price['extra_mark_up'] / $price['cnt']?>
                <?php endif;?>
            </td>
            <td><?= $price['selling'] / $price['cnt'] ?></td>
		</tr>
		<?php endforeach;?>
    </tbody>
	<tfoot>
    	<tr>
            <th>Total</th>
            <td><?= $priceData['total']['cnt']?></td>
            <td><?= $priceData['total']['net']?></td>
            <td><?= $priceData['total']['mark_up']?></td>
            <td class="total-markup-<?= $quote->uid ?>"><?= $priceData['total']['extra_mark_up']?></td>
            <td class="total-sellingPrice-<?= $quote->uid ?>"><?= $priceData['total']['selling']?></td>
        </tr>
	</tfoot>
</table>