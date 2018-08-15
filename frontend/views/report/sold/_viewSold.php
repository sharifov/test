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
                    $profit = 0;
                    $appliedAlternativeQuotes = $lead->getAppliedAlternativeQuotes();
                    if ($appliedAlternativeQuotes !== null) {
                        $price = $appliedAlternativeQuotes->quotePrice();
                        $profit = ($price['selling'] * Quote::SERVICE_FEE);
                    }

                    echo sprintf('$%s', number_format($profit, 2));
                    ?>
                </td>
                <td>
                    <?php
                    $url = Url::to([
                        'lead/quote',
                        'type' => 'sold',
                        'id' => $lead->id
                    ]);
                    echo Html::a('Open', $url, [
                        'class' => 'btn btn-action btn-sm',
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
