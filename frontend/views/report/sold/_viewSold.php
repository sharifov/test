<?php
/**
 * @var $leads Lead[]
 */

use common\models\Lead;
use yii\helpers\Url;
use yii\bootstrap\Html;
use common\models\Quote;

?>

<div class="sl-events-log">
    <table class="table table-neutral">
        <thead>
        <tr>
            <th>Sale ID</th>
            <th>Created</th>
            <th>Date of Issue</th>
            <th>Profit</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $lead) : ?>
            <tr>
                <td><?= $lead->id ?></td>
                <td><?= $lead->created ?></td>
                <td><?= $lead->updated ?></td>
                <td>
                    <?php
                    $profit = $lead->finalProfit;
                    if ($lead->final_profit === null) {
                        $appliedAlternativeQuotes = $lead->getAppliedAlternativeQuotes();
                        if ($appliedAlternativeQuotes !== null) {
                            $price = $appliedAlternativeQuotes->quotePrice();
                            $profit = Quote::getProfit($price['mark_up'], $price['selling'], $price['fare_type'], $price['isCC']);
                        }
                    }

                    echo sprintf('$%s', number_format($profit, 2));
                    ?>
                </td>
                <td>
                    <?php
                    echo Html::a('Open', [
                        'lead/view',
                        'id' => $lead->id
                    ], [
                        'class' => 'btn btn-primary btn-sm',
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]);
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
