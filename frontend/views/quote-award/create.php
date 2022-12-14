<?php

/**
 * @var $lead Lead
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

use common\models\Currency;
use common\models\Lead;
use modules\featureFlag\FFlag;
use yii\helpers\Url;
use common\models\QuotePrice;
use yii\web\View;

$enableGdsParsers = \Yii::$app->params['settings']['enable_gds_parsers_for_create_quote'];

$paxCntTypes = [
    QuotePrice::PASSENGER_ADULT => $lead->adults,
    QuotePrice::PASSENGER_CHILD => $lead->children,
    QuotePrice::PASSENGER_INFANT => $lead->infants
];
/** @fflag FFlag::FF_KEY_AWARD_ENABLE, Award Enable */
$enableAward = Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_AWARD_ENABLE);
$this->title = 'Add Quote (Lead #' . $lead->id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Lead #' . $lead->id, 'url' => ['lead/view', 'gid' => $lead->gid]];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="x_panel">
        <div class="x-content">
            <div class="">
                <h2 class="h2"><?= $this->title ?></h2>
            </div>
            <div class="alternatives__item quote-award_wrap">
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

if ($enableAward) {
    $this->render('/quote-award/parts/_js_award', ['lead' => $lead]);
}