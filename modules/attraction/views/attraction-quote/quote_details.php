<?php

use yii\helpers\VarDumper;
use yii\helpers\Html;

/**
 * @var $quoteDetails array
 * @var $productId int
 */

?>
<script>
    pjaxReload({container: '#pjax-product-' + '<?= $productId ?>'});
</script>
<?php if (!empty($quoteDetails['optionList']['nodes'])) : ?>
    <div class="row">
        <div class="col-md-4">
            <h2>Selected Options</h2>
            <table class="table table-bordered caption-top">
                <thead>
                <tr class=" bg-info">
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Is Answered</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($quoteDetails['optionList']['nodes'] as $index => $option) : ?>
                    <tr>
                        <td> <?= $option['label'] ?> </td>
                        <td> <?= $option['answerFormattedText'] ?> </td>
                        <td> <?= $option['isAnswered'] ? '<span class="label-success label">Yes<span>' : '<span class="label-danger label">No<span>' ?> </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Selected Pricing Categories</h2>
        <table class="table table-bordered">
            <thead>
            <tr class=" bg-info">
                <th>Nr.</th>
                <th>Label</th>
                <th>Min Participants</th>
                <th>Max Participants</th>
                <th>Min Age</th>
                <th>Max Age</th>
                <th>Is Valid</th>
                <th>Quantity</th>
                <th>Price per Unit</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($quoteDetails['pricingCategoryList']['nodes'])) : ?>
                <?php foreach ($quoteDetails['pricingCategoryList']['nodes'] as $nr => $pax) : ?>
                    <?php if ($pax['value'] != 0) : ?>
                        <tr>
                            <td title="Pax Id: <?= Html::encode($pax['id']) ?>"><?= ($nr + 1) ?>. Pricing Category</td>
                            <td><b><?= Html::encode($pax['label']) ?></b></td>
                            <td><?= Html::encode($pax['minParticipants']) ?></td>
                            <td><?= Html::encode($pax['maxParticipants']) ?></td>
                            <td><?= Html::encode($pax['minAge']) ?></td>
                            <td><?= Html::encode($pax['maxAge']) ?></td>
                            <td><?= $pax['isValid'] ? '<span class="label-success label">Yes<span>' : '<span class="label-danger label">No<span>' ?></td>
                            <td><?= Html::encode($pax['value']) ?></td>
                            <td><?= Html::encode($pax['priceFormattedText']) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="7"></td>
                    <td>Total Price</td>
                    <td><?= Html::encode($quoteDetails['pricingCategoryList']['priceTotalFormattedText']) ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
