<?php
use yii\widgets\ActiveForm;
use frontend\models\LeadForm;
use yii\helpers\Html;
use common\models\Lead;
use common\models\LeadFlightSegment;


$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
/**
 * @var $this \yii\web\View
 * @var $formLeadModel ActiveForm
 * @var $leadForm LeadForm
 */

$formId = sprintf('%s-form', $leadForm->getLead()->formName());
if ($leadForm->mode != $leadForm::VIEW_MODE) {
    $js = <<<JS

    //----Switch Form Tabs
    function switchTabs(inputSel) {
        $(".js-mc-row:visible").first().find('.lead-remove-segment-button').addClass('hidden');
        //mc
        if (inputSel === 'mc') {
            $('#lead-new-segment-button').removeClass('hidden');
            $(".js-mc-row:hidden").show();
            $('.js-tab').addClass('sl-itinerary-form__tab--mc').removeClass('sl-itinerary-form__tab--ow').removeClass('sl-itinerary-form__tab--rt');
        }//ow
        else if (inputSel === 'ow'){
            if($(".js-mc-row:visible").length > 1){
                $(".js-mc-row:visible").each(function(idx, elm){ if(idx > 0) $(elm).remove(); });
            }
            $('#lead-new-segment-button').addClass('hidden');
            $('.js-tab').addClass('sl-itinerary-form__tab--ow').removeClass('sl-itinerary-form__tab--mc').removeClass('sl-itinerary-form__tab--rt');
        }//rt
        else{
            if($(".js-mc-row:visible").length > 2){
                $(".js-mc-row:visible").each(function(idx, elm){ if(idx > 1) $(elm).remove(); });
            }else if($(".js-mc-row:visible").length == 1){
                $('#lead-new-segment-button').trigger('click');
            }else if($(".js-mc-row:visible").length == 0){
                $('#lead-new-segment-button').trigger('click');
                $('#lead-new-segment-button').trigger('click');
            }

            $(".js-mc-row:visible:eq(1) .origin").val($(".js-mc-row:eq(0) .destination").val()).trigger('change');
            $(".js-mc-row:visible:eq(1) .destination").val($(".js-mc-row:visible:eq(0) .origin").val()).trigger('change');

            $(".js-mc-row:visible:eq(0) .destination").on("change",function(){
                $(".js-mc-row:visible:eq(1) .origin").val($(this).val()).trigger('change');
            });
            $(".js-mc-row:visible:eq(0) .origin").on("change",function(){
                $(".js-mc-row:visible:eq(1) .destination").val($(this).val()).trigger('change');
            });

            $(".js-mc-row:visible:eq(0) .depart-date").on("change",function(){
                var dtStr = $(this).val();
                var newdate;
                if(dtStr != '' && $(".js-mc-row:visible:eq(1) .depart-date").val() == '' ) {
                    newdate = moment(dtStr, "DD-MMM-YYYY").add(7, 'days');
                    $(".js-mc-row:visible:eq(1) .depart-date").val(newdate.format('DD-MMM-YYYY')).trigger('change');
                }
            });

            $('#lead-new-segment-button').addClass('hidden');
            $('.js-tab').addClass('sl-itinerary-form__tab--rt').removeClass('sl-itinerary-form__tab--mc').removeClass('sl-itinerary-form__tab--ow');
        }
    }

    $("#$formId input[type='radio']").change(function () {
        switchTabs(this.id);
    });

    $(function(){
        switchTabs($("#$formId input[type='radio']:checked").attr('id'));
    });
JS;
    $this->registerJs($js);
}
?>

<div class="panel panel-primary sl-request-wrap">
    <div class="panel-heading collapsing-heading">
        <a data-toggle="collapse" href="#request-form-wrap" class="collapsing-heading__collapse-link"
           aria-expanded="true">

            <!--Flight Details-->
            <div class="sl-request-summary">
                <?php if ($leadForm->getLead()->isNewRecord) : ?>
                    <div class="sl-request-summary__block">
                        <div class="sl-request-summary__locations">
                            <strong>Flight Details</strong>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="sl-request-summary__block">
                        <?php
                        $location = $departing = [];
                        foreach ($leadForm->getLeadFlightSegment() as $key => $_segment) {
                            $location[] = sprintf('%s → %s', $_segment->origin, $_segment->destination);
                            $departing[] = Yii::$app->formatter->asDate(strtotime($_segment->departure));
                        }
                        ?>
                        <div class="sl-request-summary__locations">
                            <strong><?= implode(', ', $location) ?></strong>
                        </div>
                        <div class="sl-request-summary__dates"><?= implode(', ', $departing) ?></div>
                    </div>
                    <div class="sl-request-summary__block">
                        <?php if (!empty($leadForm->getLead()->adults)) : ?>
                            <div>
                                <i class="fa fa-user"></i> <strong><?= $leadForm->getLead()->adults ?></strong>
                                adult
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($leadForm->getLead()->children)) : ?>
                            <div>
                                <i class="fa fa-user"></i> <strong><?= $leadForm->getLead()->children ?></strong>
                                children
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($leadForm->getLead()->infants)) : ?>
                            <div>
                                <i class="fa fa-user"></i> <strong><?= $leadForm->getLead()->infants ?></strong>
                                infants
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <i class="collapsing-heading__arrow"></i>
        </a>
    </div>

    <div class="panel-body collapse in" id="request-form-wrap" aria-expanded="true" style="">
        <div class="sl-itinerary-form">
            <?php $formLeadModel = ActiveForm::begin([
                'enableClientValidation' => false,
                'id' => $formId
            ]); ?>
            <!--region Trip Type-->
            <div class="row sl-itinerary-form__top">
                <div class="col-lg-12">
                    <?= $formLeadModel->field($leadForm->getLead(), 'trip_type', [
                        'options' => [
                            'tag' => false,
                        ]])->label(false)
                        ->radioList(Lead::getFlightTypeList(),
                            [
                                'tag' => 'ul',
                                'class' => 'sl-itinerary-form__trip-type nav nav-tabs js-trip-type',
                                'item' => function ($index, $label, $name, $checked, $value) {
                                    $return = '<li class="radio-tab' . (($checked) ? ' active' : '') . '">';
                                    $return .= '<input type="radio" id="' . strtolower($value) . '" name="' . $name . '" value="' . $value . '" autocomplete="off" ' . (($checked) ? 'checked' : '') . '>';
                                    $return .= '<label for="' . strtolower($value) . '" class="radio-tab__text">' . ucwords($label) . '</label>';
                                    $return .= '</li>';
                                    return $return;
                                },
                            ]) ?>
                </div>
            </div>
            <!--endregion-->

            <div class="sl-itinerary-form__tabs">
                <div class="sl-itinerary-form__tab sl-itinerary-form__tab--rt js-tab" id="lead-segments">
                    <?php
                    foreach ($leadForm->getLeadFlightSegment() as $key => $_segment) {
                        echo $this->render('_formLeadSegment', [
                            'key' => $_segment->isNewRecord
                                ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                                : $_segment->id,
                            'form' => $formLeadModel,
                            'segment' => $_segment,
                        ]);
                    }
                    ?>
                    <!-- new lead segment fields -->
                    <div id="lead-new-segment-block" style="display: none;">
                        <?php $newSegment = new LeadFlightSegment(); ?>
                        <?= $this->render('_formLeadSegment', [
                            'key' => '__id__',
                            'form' => $formLeadModel,
                            'segment' => $newSegment,
                        ]) ?>
                    </div>
                </div>
                <?php ob_start(); // output buffer the javascript to register later ?>
                <script>
                    // add segment button
                    var segment_k = <?php echo isset($key) ? str_replace('new', '', $key) : 1; ?>;
                    $('#lead-new-segment-button').on('click', function () {

                        var startDate = $(".js-mc-row:visible").last().find('.depart-date').val();
                        segment_k += 1;
                        $('#lead-segments').append($('#lead-new-segment-block').html().replace(/__id__/g, 'new' + segment_k));

                        var originId = '<?= strtolower($newSegment->formName()) ?>-new' + segment_k + '-origin_label',
                            destinationId = '<?= strtolower($newSegment->formName()) ?>-new' + segment_k + '-destination_label',
                            departureId = '<?= strtolower($newSegment->formName()) ?>-new' + segment_k + '-departure';

                        $('#' + originId).autocomplete({
                            "autoFocus": true,
                            "source": function (request, response) {
                                $.getJSON('/site/get-airport', {
                                    term: request.term
                                }, response);
                            },
                            "minLength": "2"
                        });

                        $('#' + destinationId).autocomplete({
                            "autoFocus": true,
                            "source": function (request, response) {
                                $.getJSON('/site/get-airport', {
                                    term: request.term
                                }, response);
                            },
                            "minLength": "2"
                        });

                        $('#' + departureId).datepicker({
                            "autoclose": true,
                            "todayHighlight": true,
                            "format": "dd-M-yyyy",
                            "orientation": "top left",
                            "startDate": startDate
                        });

                    });

                    // remove segment button
                    $(document).on('click', '.lead-remove-segment-button', function () {
                        var rowsCnt = $(".js-mc-row:visible").length;
                        if (rowsCnt > 2) {
                            $(this).closest('div.js-mc-row').remove();
                        } else {
                            $('#rt').prop("checked", true);
                            switchTabs('#rt');
                        }
                    });
                </script>
                <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
            </div>

            <div class="btn-wrapper">
                <?php
                $hidden = ($leadForm->getLead()->trip_type != Lead::TRIP_TYPE_MULTI_DESTINATION) ? 'hidden' : '';
                $title = '<span class="btn-icon"><i class="fa fa-plus"></i></span><span>Add Flight</span>';
                echo Html::button($title, [
                    'id' => 'lead-new-segment-button',
                    'class' => 'btn btn-success btn-with-icon js-add-mc-row ' . $hidden,
                ]);
                ?>
            </div>

            <!--Passengers-->
            <div class="row sl-itinerary-form__pax">
                <div class="col-sm-3">
                    <?= $formLeadModel->field($leadForm->getLead(), 'cabin', [
                        //'template' => '{label}<label for="cabin-class" class="select-wrap-label">{input}</label>{error}{hint}'
                    ])->dropDownList(Lead::getCabinList(), [
                        'prompt' => '---'
                    ]) ?>
                </div>
                <div class="col-sm-2">
                </div>
                <div class="col-sm-2">
                    <?= $formLeadModel->field($leadForm->getLead(), 'adults')->textInput([
                        'class' => 'form-control lead-form-input-element',
                        'type' => 'number',
                        'min' => 0,
                        'max' => 9,
                        //'placeholder' => 'Adult'
                    ]) ?>
                </div>
                <div class="col-sm-2">
                    <?= $formLeadModel->field($leadForm->getLead(), 'children')->textInput([
                        'class' => 'form-control lead-form-input-element',
                        'type' => 'number',
                        'min' => 0,
                        'max' => 9,
                        //'placeholder' => 'Child'
                    ]) ?>
                </div>
                <div class="col-sm-2">
                    <?= $formLeadModel->field($leadForm->getLead(), 'infants')->textInput([
                        'class' => 'form-control lead-form-input-element',
                        'type' => 'number',
                        'min' => 0,
                        'max' => 9,
                        //'placeholder' => 'Infant'
                    ]) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
