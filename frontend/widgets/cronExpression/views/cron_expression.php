<?php

/**
 * @var View $this
 * @var Model $model
 * @var string $attribute
 * @var []] $options
 */

use frontend\helpers\JsonHelper;
use frontend\widgets\cronExpression\CronExpressionAssets;
use frontend\widgets\cronExpression\DayExpressionDto;
use frontend\widgets\cronExpression\MonthExpressionDto;
use frontend\widgets\cronExpression\WeekdayExpressionDto;
use frontend\widgets\cronExpression\YearExpressionDto;
use yii\base\Model;
use yii\web\View;

CronExpressionAssets::register($this);

$dayInputRadioName = $model->formName() . '[' . $attribute . ']';
$cronExpressionAppId = 'cron-expression-app-' . $attribute;
?>

<div id="<?= $cronExpressionAppId ?>">
  <div class="row">
      <div class="col-md-12">
    <div class="col-md-4">
      <day-input ref="day" v-bind:day-expression='<?= JsonHelper::encode(DayExpressionDto::EXPRESSION_LIST) ?>'
                 v-bind:expression-format='<?= JsonHelper::encode(DayExpressionDto::EXPRESSION_FORMAT) ?>'
                 v-bind:default-input-radio="<?= DayExpressionDto::EXPRESSION_EVERY_DAY ?>"
                 v-bind:input-radio-name="'expressionDay<?= $attribute ?>'"
      ></day-input>
    </div>
    <div class="col-md-4">

      <month-input ref="month" v-bind:month-expression='<?= JsonHelper::encode(MonthExpressionDto::EXPRESSION_LIST) ?>'
                   v-bind:expression-format='<?= JsonHelper::encode(MonthExpressionDto::EXPRESSION_FORMAT) ?>'
                   v-bind:default-input-radio="<?= MonthExpressionDto::EXPRESSION_EVERY_MONTH ?>"
                   v-bind:input-radio-name="'expressionMonth<?= $attribute ?>'"
      ></month-input>
    </div>
    <div class="col-md-4">

      <weekday-input ref="weekday" v-bind:weekday-expression='<?= JsonHelper::encode(WeekdayExpressionDto::EXPRESSION_LIST) ?>'
                   v-bind:expression-format='<?= JsonHelper::encode(WeekdayExpressionDto::EXPRESSION_FORMAT) ?>'
                   v-bind:default-input-radio="<?= WeekdayExpressionDto::EXPRESSION_EVERY_WEEKDAY ?>"
                   v-bind:input-radio-name="'expressionWeekday<?= $attribute ?>'"
      ></weekday-input>
    </div>
      <?php if (!empty($options['year'])) : ?>
        <div class="col-md-3">
      <year-input ref="year" v-bind:year-expression='<?= JsonHelper::encode(YearExpressionDto::EXPRESSION_LIST) ?>'
                     v-bind:expression-format='<?= JsonHelper::encode(YearExpressionDto::EXPRESSION_FORMAT) ?>'
                     v-bind:default-input-radio="<?= YearExpressionDto::EXPRESSION_EVERY_YEAR ?>"
                     v-bind:input-radio-name="'expressionYear<?= $attribute ?>'"
                     v-bind:years='<?= JsonHelper::encode(YearExpressionDto::getYearsRange()) ?>'
      ></year-input>
    </div>
      <?php endif; ?>
      </div>
  </div>

  <div class="form-group">
    <input type="text" class="form-control" @keyup="parse" name="<?= $model->formName() . '[' . $attribute . ']' ?>" v-model="expression">
  </div>
</div>

<?php

$js = <<<JS
    initCronExpressionApp('$cronExpressionAppId', '{$model->$attribute}');
JS;

$this->registerJs($js, View::POS_END);



