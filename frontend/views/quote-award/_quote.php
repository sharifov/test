<?php

/**
 * @var $lead Lead
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

use common\models\Currency;
use common\models\Lead;
use yii\helpers\Url;
use common\models\QuotePrice;
use yii\web\View;

$enableGdsParsers = \Yii::$app->params['settings']['enable_gds_parsers_for_create_quote'];

$paxCntTypes = [
    QuotePrice::PASSENGER_ADULT => $lead->adults,
    QuotePrice::PASSENGER_CHILD => $lead->children,
    QuotePrice::PASSENGER_INFANT => $lead->infants
];
?>
    <div class="alternatives__item">
        <?php $currencyLead = $lead->leadPreferences->pref_currency ?? Currency::getDefaultCurrencyCode() ?>
        <?php if ($currencyLead !== Currency::getDefaultCurrencyCode()) : ?>
            <div class="quote_exclamation_currency">
                <i class="fa fa-exclamation-circle warning"></i> Lead Currency:
                <strong><?php echo $currencyLead ?></strong>
            </div>
        <?php endif ?>

        <div class="table-wrapper ticket-details-block__table mb-20"
             id="alt-quote-fares-info">

            <div class="js-update-ajax">
                <?= $this->render('parts/_flights', ['model' => $model, 'lead' => $lead, 'tab' => 0]) ?>
            </div>

        </div>
    </div>

<?php
$js = <<<JS
function formatRepo( repo ) {
        if (repo.loading) return repo.text;

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'>" + repo.text + "</div>";
        markup +=	"</div></div>";

        return markup;
    }
JS;
$this->registerJs($js, View::POS_HEAD);