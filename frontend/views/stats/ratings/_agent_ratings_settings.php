<?php
use yii\helpers\Html;
use common\components\ChartTools;

$currentWeek = date("Y-m-d H:i",ChartTools::getWeek('0 week')['start']) .' - '. date("Y-m-d H:i", ChartTools::getWeek('0 week')['end']);
$lastWeek = date("Y-m-d H:i",ChartTools::getWeek('-1 week')['start']) .' - '. date("Y-m-d H:i", ChartTools::getWeek('-1 week')['end']);
$currentMonth = date("Y-m-d H:i",ChartTools::getCurrentMonth()['start']) .' - '. date("Y-m-d H:i", ChartTools::getCurrentMonth()['end']);

$js = <<<JS

$('#period').on('change', function() {  
    var period = this.value;
     $.ajax({
            url: 'agent-ratings',
            type: 'POST',
            data: {'period' : this.value},
            success: function(data) { 
                 let result = $("#agent-leader-board").append(data).find("#agent-leader-board").html();
                 $("#agent-leader-board").html(result); 
                 
                 $("#showPeriod").html()
                 if (period === 'currentWeek'){
                     $("#showPeriod").html('<span> $currentWeek </span>');
                 }
                 if (period === 'lastWeek'){
                     $("#showPeriod").html('<span> $lastWeek </span>');
                 }
                 if (period === 'currentMonth'){
                     $("#showPeriod").html('<span> $currentMonth </span>');
                 }                 
            }
     });
});

JS;
$this->registerJs($js);
?>
<div class="col-md-12">
    <div class="x_panel">
        <div class="row col-md-6">
            <div class="col-md-2">
                <select id="period" class="select2_single form-control" tabindex="-1">
                    <option value="currentWeek">Current Week</option>
                    <option value="lastWeek">Last Week</option>
                    <option value="currentMonth">Current Month</option>
                </select>
            </div>
            <div id="showPeriod" class="col-md-5 label label-info label-large">
                <span> <?= $currentWeek ?> </span>
            </div>
        </div>
    </div>
</div>

<!--<div class="col-md-12">
    <div class="x_panel" style="height: auto">
        <div class="x_title">
            <h2>Leader Board Settings</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none">

            <div class="form-group row col-md-9">
                <div class="col-md-2">
                    <select class="select2_single form-control" tabindex="-1">
                        <option></option>
                        <option value="0">Current Week</option>
                        <option value="1">Last Week</option>
                        <option value="2">Current Month</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <div class="checkbox">
                        <?/*= Html::checkbox('final_profit', true, ['id' => 'finalProfit', 'label' => 'AGENT BY FINAL PROFIT']) */?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="checkbox">
                        <?/*= Html::checkbox('sold_leads', true, ['id' => 'soldLeads', 'label' => 'AGENT BY SOLD LEADS']) */?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="checkbox">
                        <?/*= Html::checkbox('per_pax', true, ['id' => 'perPax', 'label' => 'AGENT BY PROFIT PER PAX']) */?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="checkbox">
                        <?/*= Html::checkbox('tips', true, ['id' => 'tips', 'label' => 'AGENT BY TIPS']) */?>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
-->