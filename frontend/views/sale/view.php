<?php

use common\models\CaseSale;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $csId int */
/* @var $caseSaleModel common\models\CaseSale */


if(Yii::$app->request->isPjax) {

    $this->params['breadcrumbs'][] = ['label' => 'Sales', 'url' => ['search']];
    $this->params['breadcrumbs'][] = $this->title;
}

$title = 'Sale ID: ' . $data['saleId'] . ', BookId: ' . $data['bookingId'];

//$isAgent = Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id);

?>
<div class="sale-view">
    <h3><?= Html::encode($title) ?></h3>
    <div class="row">
        <div class="col-md-12">
            <div class="error-dump"></div>
        </div>

        <div class="col-md-3">
            <h2>General</h2>
            <table class="table table-bordered table-hover table-striped">

                <tr>
                    <th>Sale Id</th>
                    <td><?=Html::encode($data['saleId'])?></td>
                </tr>
                <tr>
                    <th>Confirmation Number (Booking Id)</th>
                    <td><?=Html::encode($data['bookingId'])?></td>
                </tr>
                <tr>
                    <th>PNR</th>
                    <td><?=Html::encode($data['pnr'])?></td>
                </tr>
                <tr>
                    <th>Charge Type</th>
                    <td><?=Html::encode($data['chargeType'])?></td>
                </tr>
                <tr>
                    <th>Fare Type</th>
                    <td><?=Html::encode($data['fareType'])?></td>
                </tr>
                <tr>
                    <th>GDS</th>
                    <td><?=Html::encode($data['gds'])?></td>
                </tr>
                <tr>
                    <th>PCC</th>
                    <td><?=Html::encode($data['pcc'])?></td>
                </tr>
                <tr>
                    <th>validating Carrier</th>
                    <td><?=Html::encode($data['validatingCarrier'])?></td>
                </tr>
                <tr>
                    <th>Consolidator</th>
                    <td><?=Html::encode($data['consolidator'])?></td>
                </tr>
                <tr>
                    <th>Project</th>
                    <td><span class="label label-default"><?=Html::encode($data['project'])?></span></td>
                </tr>
                <tr>
                    <th>Trip Type</th>
                    <td><?=Html::encode($data['tripType'])?></td>
                </tr>
                <tr>
                    <th>Created</th>
                    <td><?=Yii::$app->formatter->asDatetime(strtotime($data['created']))?></td>
                </tr>
            </table>


        </div>

        <div class="col-md-4">

            <h2>Processing Teams Status</h2>
            <?php if(isset($data['processingTeamsStatus']) && $data['processingTeamsStatus']): ?>
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Type</th>
                        <th>Value</th>
                    </tr>
                    <?php foreach($data['processingTeamsStatus'] as $pStatusKey => $pStatusValue): ?>
                        <tr>
                            <td><?=Html::encode($pStatusKey)?></td>
                            <td><?=Html::encode($pStatusValue)?></td>
                        </tr>
                    <?php endforeach;?>
                </table>
            <?php endif;?>

            <h2>Notes</h2>
            <div style="width: 100%;overflow-x: auto;">
                <?php if(isset($data['notes']) && $data['notes']): ?>
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>Created</th>
                            <th>Message</th>
                            <th>Agent</th>
                            <th>Team</th>
                        </tr>
                        <?php foreach($data['notes'] as $note): ?>
                            <tr>
                                <td><?=Yii::$app->formatter->asDatetime(strtotime($note['created']))?></td>
                                <td><?=Html::encode($note['message'])?></td>
                                <td><?=Html::encode($note['agent'])?></td>
                                <td><?=Html::encode($note['team'])?></td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                <?php endif;?>
            </div>

        </div>

        <div class="col-md-5">
            <h2>Price</h2>
            <?php if(isset($data['price']) && $data['price']): ?>

                <?php if(isset($data['price']['priceQuotes']) && $data['price']['priceQuotes']): ?>
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Pax Type</th>
                        <th>Selling</th>
                        <th>Net</th>
                        <th>Fare</th>
                        <th>Taxes</th>
                        <th>Mark Up</th>
                        <th>Over Cap</th>
                        <th>Source Fee</th>
                    </tr>
                    <?php foreach($data['price']['priceQuotes'] as $paxType => $price): ?>
                        <tr>
                            <td><?=Html::encode($paxType)?></td>
                            <td><?=Html::encode($price['selling'])?></td>
                            <td><?=Html::encode($price['net'])?></td>
                            <td><?=Html::encode($price['fare'])?></td>
                            <td><?=Html::encode($price['taxes'])?></td>
                            <td><?=Html::encode($price['mark_up'])?></td>
                            <td><?=Html::encode($price['over_cap'])?></td>
                            <td><?=Html::encode($price['source_fee'])?></td>
                        </tr>
                    <?php endforeach;?>
                </table>


                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>Amount Charged</th>
                            <td><?=($data['price']['amountCharged'])?></td>
                        </tr>
                        <tr>
                            <th>Profit</th>
                            <td><?=number_format($data['price']['profit'], 2)?> <?=Html::encode($data['price']['currency'])?></td>
                        </tr>
                    </table>

                <?php endif;?>
            <?php endif;?>

            <h2>Auth List</h2>
            <?php if(isset($data['authList']) && $data['authList']): ?>
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <th>Created</th>
                        <th>Auth system</th>
                        <th>For what</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Message</th>
                        <th>CC Number</th>
                    </tr>
                    <?php foreach($data['authList'] as $list): ?>
                        <tr>
                            <td><?=Yii::$app->formatter->asDatetime(strtotime($list['created']))?></td>
                            <td><?=Html::encode($list['auth_system'])?></td>
                            <td><?=Html::encode($list['for_what'])?></td>
                            <td><?=number_format($list['amount'], 2)?></td>
                            <td><?=Html::encode($list['status'])?></td>
                            <td><?=Html::encode($list['message'])?></td>
                            <td><?=Html::encode($list['ccNumber'])?></td>
                        </tr>
                    <?php endforeach;?>
                </table>
            <?php endif;?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <h2>Passengers</h2>
			<?php if(isset($data['passengers']) && $data['passengers']): ?>
                <table class="table table-bordered table-hover" id="passengers">
                    <thead>
                    <tr>
                        <th>First name</th>
                        <th>Ticket number</th>
                        <th>Type</th>
                        <th>Birth date</th>
                        <th>Gender</th>
<!--                        <th>Meal</th>-->
<!--                        <th>Wheelchair</th>-->
<!--                        <th>Frequent Fayer</th>-->
<!--                        <th>KTN</th>-->
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach($data['passengers'] as $key => $passenger): ?>
                        <tr>
                            <td>
								<?php
								/*
									$editable = Editable::begin([
										'name' => 'cssSaleData[passengers]['.$key.'][last_name]',
										'asPopover' => true,
										'displayValue' => Html::encode($passenger['last_name'] . ' ' . $passenger['first_name'] . $passenger['middle_name']),
										'inputType' => Editable::INPUT_TEXT,
										'value' => Html::encode($passenger['last_name']),
										'header' => 'Name',
										'formOptions' => [ 'action' => ['/cases/ajax-sale-list-edit-info/' . $csId . '/' . $data['saleId']] ],
									]);
									$form = $editable->getForm();
									$editable->beforeInput = Html::label('Last Name', 'editable_last_name');
									$editable->afterInput = $this->render('partial/_editable_name_field', ['passenger' => $passenger, 'key' => $key]);
									Editable::end();
								*/
								?>
								<?= Html::encode($passenger['first_name'] . ' ' . $passenger['last_name'] . ' ' . $passenger['middle_name']) ?>
                            </td>
                            <td><?=Html::encode($passenger['ticket_number'])?></td>
                            <td><?=Html::encode($passenger['type'])?></td>
                            <td>
								<?php
//                                $editable = Editable::begin([
//									'name' => 'cssSaleData[passengers]['.$key.'][birth_date]',
//									'header' => 'Date of Birth',
//									'asPopover' => true,
//									'inputType' => Editable::INPUT_DATE,
//									'displayValue' => Yii::$app->formatter->asDate(Html::encode($passenger['birth_date']), 'php:d M Y'),
//									'value' => Yii::$app->formatter->asDate(Html::encode($passenger['birth_date']), 'php:d M Y'),
//									'formOptions' => [ 'action' => [\yii\helpers\Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
//									'options' => [
//										'convertFormat'=>true,
//										'pluginOptions'=>[
//                                            'format'=>'php:d M Y',
//                                            'autoclose'=>true,
////                                            'type' =>
//                                        ],
//                                        'class' => 'cssSaleData_passengers_birth_date'
//									],
//									'pluginEvents' => [
//										"editableSuccess"=>"function (event, val, form, data) {
//										    console.log(form);
//										    document.activateButtonSync(data);
//										}",
//									],
//                                    'pjaxContainerId' => 'pjax-sale-list'
//								]);
								?>
                                <?php // $editable->beforeInput = Html::hiddenInput("cssSaleData[passengers][".$key."][type]", Html::encode($passenger['type'])); ?>
                                <?php // Editable::end(); ?>
                                <?= Yii::$app->formatter->asDate(Html::encode($passenger['birth_date']), 'php:d M Y') ?>
                            </td>
                            <td>
								<?=
//								Editable::widget([
//									'name' => 'cssSaleData[passengers]['.$key.'][gender]',
//									'header' => 'Gender',
//									'asPopover' => true,
//									'inputType' => Editable::INPUT_DROPDOWN_LIST,
//									'data' => ['F' => 'Female', 'M' => 'Male'],
//									'value' => Html::encode($passenger['gender']),
//									'formOptions' => [ 'action' => [\yii\helpers\Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
//									'pluginEvents' => [
//										"editableSuccess"=>"function (event, val, form, data) {
//										    document.activateButtonSync(data);
//										}",
//									],
//									'pjaxContainerId' => 'pjax-sale-list'
//								]);
								Html::encode($passenger['gender'])
								?>
                            </td>
<!--                            <td>-->
								<?php
//								Editable::widget([
//									'name' => 'cssSaleData[passengers]['.$key.'][meal]',
//									'header' => 'Meal',
//									'asPopover' => true,
//									'inputType' => Editable::INPUT_DROPDOWN_LIST,
//									'data' => CaseSale::PASSENGER_MEAL,
//									'options' => ['prompt'=>'Select meal...'],
//									'value' => Html::encode(!empty($passenger['meal']) && is_array($passenger['meal']) ? reset($passenger['meal']) : null),
//									'formOptions' => [ 'action' => [\yii\helpers\Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
//									'pluginEvents' => [
//										"editableSuccess"=>"function (event, val, form, data) {
//										    document.activateButtonSync(data);
//										}",
//									],
//									'placement' => PopoverX::ALIGN_TOP_LEFT,
//									'pjaxContainerId' => 'pjax-sale-list'
//								]);
//								Html::encode($passenger['meal'] ?? null)
								?>
<!--                            </td>-->
<!--                            <td>-->
<!--								--><?//=
//								Editable::widget([
//									'name' => 'cssSaleData[passengers]['.$key.'][wheelchair]',
//									'header' => 'Wheelchair',
//									'asPopover' => true,
//									'inputType' => Editable::INPUT_DROPDOWN_LIST,
//									'data' => CaseSale::PASSENGER_WHEELCHAIR,
//									'value' => Html::encode(!empty($passenger['wheelchair']) && is_array($passenger['wheelchair']) ? reset($passenger['wheelchair']) : null),
//									'formOptions' => [ 'action' => [\yii\helpers\Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
//									'options' => ['prompt'=>'Select wheelchair...'],
//									'pluginEvents' => [
//										"editableSuccess"=>"function (event, val, form, data) {
//										    document.activateButtonSync(data);
//										}",
//									],
//									'placement' => PopoverX::ALIGN_TOP_LEFT,
//									'pjaxContainerId' => 'pjax-sale-list'
//								]);
////								Html::encode($passenger['wheelchair'] ?? null)
//								?>
<!--                            </td>-->
<!--                            <td>-->
<!--								--><?//=
//								Editable::widget([
//									'name' => 'cssSaleData[passengers]['.$key.'][ff_numbers]',
//									'header' => 'Frequent Fayer',
//									'asPopover' => true,
//									'inputType' => Editable::INPUT_TEXT,
//									'value' => Html::encode(!empty($passenger['ff_numbers']) && is_array($passenger['ff_numbers']) ? reset($passenger['ff_numbers']) : null),
//									'formOptions' => [ 'action' => [\yii\helpers\Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
//									'pluginEvents' => [
//										"editableSuccess"=>"function (event, val, form, data) {
//										    document.activateButtonSync(data);
//										}",
//									],
//                                    'placement' => PopoverX::ALIGN_TOP_LEFT,
//									'pjaxContainerId' => 'pjax-sale-list'
//								]);
////								Html::encode($passenger['ff_numbers'] ?? null)
//								?>
<!--                            </td>-->
<!--                            <td>-->
<!--								--><?//=
//								Editable::widget([
//									'name' => 'cssSaleData[passengers]['.$key.'][kt_numbers]',
//									'header' => 'KTN',
//									'asPopover' => true,
//									'inputType' => Editable::INPUT_TEXT,
//									'value' => Html::encode(!empty($passenger['kt_numbers']) && is_array($passenger['kt_numbers']) ? reset($passenger['kt_numbers']) : null),
//									'formOptions' => [ 'action' => [\yii\helpers\Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
//									'pluginEvents' => [
//										"editableSuccess"=>"function (event, val, form, data) {
//										    document.activateButtonSync(data);
//										}",
//									],
//                                    'placement' => PopoverX::ALIGN_LEFT,
//									'pjaxContainerId' => 'pjax-sale-list'
//								]);
////								Html::encode($passenger['kt_numbers'] ?? null)
//								?>
<!--                            </td>-->
                        </tr>
					<?php endforeach;?>
                    </tbody>
                </table>
			<?php endif;?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="overflow-x: auto;">
            <?php if(isset($data['itinerary']) && $data['itinerary']): ?>
                <?php foreach($data['itinerary'] as $itNr => $itinerary): ?>
                <h4>Itinerary <?=($itNr + 1)?></h4>
                <?php if($itinerary['segments']): ?>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>Airline</th>
                            <th>Airline Name</th>
                            <th>Main Airline</th>
                            <th>Arrival Airport</th>
                            <th>Arrival Time</th>
                            <th>Departure Airport</th>
                            <th>Departure Time</th>
                            <th>Booking Class</th>
                            <th>Flight Number</th>
                            <th>Status Code</th>
                            <th>Operating Airline</th>
                            <th>Cabin</th>
                            <th>DepartureCity</th>
                            <th>Arrival City</th>
                            <th>Departure Country</th>
                            <th>Arrival Country</th>
                            <th>Departure AirportName</th>
                            <th>Arrival AirportName</th>
                            <th>Flight Duration</th>
                            <th>Layover Duration</th>
                            <th>Airline RecordLocator</th>
                            <th>Air craft</th>
                            <th>Baggage</th>
                        </tr>
                        <?php foreach($itinerary['segments'] as $segment): ?>
                            <tr>
                                <td><?=Html::encode($segment['airline'])?></td>
                                <td><?=Html::encode($segment['airlineName'])?></td>
                                <td><?=Html::encode($segment['mainAirline'])?></td>
                                <td><?=Html::encode($segment['arrivalAirport'])?></td>
                                <td><?=Html::encode($segment['arrivalTime'])?></td>
                                <td><?=Html::encode($segment['departureAirport'])?></td>
                                <td><?=Html::encode($segment['departureTime'])?></td>
                                <td><?=Html::encode($segment['bookingClass'])?></td>
                                <td><?=Html::encode($segment['flightNumber'])?></td>
                                <td><?=Html::encode($segment['statusCode'])?></td>
                                <td><?=Html::encode($segment['operatingAirline'])?></td>
                                <td><?=Html::encode($segment['cabin'])?></td>
                                <td><?=Html::encode($segment['departureCity'])?></td>
                                <td><?=Html::encode($segment['arrivalCity'])?></td>
                                <td><?=Html::encode($segment['departureCountry'])?></td>
                                <td><?=Html::encode($segment['arrivalCountry'])?></td>
                                <td><?=Html::encode($segment['departureAirportName'])?></td>
                                <td><?=Html::encode($segment['arrivalAirportName'])?></td>
                                <td><?=Html::encode($segment['flightDuration'])?></td>
                                <td><?=Html::encode($segment['layoverDuration'])?></td>
                                <td><?=Html::encode($segment['airlineRecordLocator'])?></td>
                                <td><?=Html::encode($segment['aircraft'])?></td>
                                <td><?=Html::encode($segment['baggage'])?></td>
                            </tr>
                        <?php endforeach;?>
                    </table>



                <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        </div>
    </div>
    <?php
	$url = \yii\helpers\Url::to(['/cases/ajax-sync-with-back-office/']);
	$js = <<<JS
                    $(".sync-with-bo").on('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var obj = $(this),
                            caseId = obj.attr('data-case-id'),
                            caseSaleId = obj.attr('data-case-sale-id');
                            
                        $.ajax({
                            type: "post",
                            url: "$url/" + caseId + '/' + caseSaleId,
                            data: {},
                            dataType: "json",
                            beforeSend: function () {
                                obj.attr('disabled', true).find('i').toggleClass('fa-spin');
                                $(obj).closest('.panel').find('.error-dump').html();
                            },
                            success: function (json) {
                                var title = !json.error ? 'Updated' : 'Error',
                                    type = !json.error ? 'success' : 'error',
                                    text = json.message;
                                
                                new PNotify({
                                    title: title,
                                    type: type,
                                    text: text,
                                    hide: true
                                });
                                
                                if (json.errorHtml) {
                                    $(obj).closest('.panel').find('.error-dump').html(json.errorHtml);
                                }
                                
                                if (json.error) {
                                    obj.removeAttr('disabled');    
                                } else {
                                    obj.attr('disabled', true);
                                }
                                obj.find('i').toggleClass('fa-spin');
                            },
                            error: function (text) {
                                new PNotify({
                                    title: "Error",
                                    type: "error",
                                    text: "Internal Server Error. Try again letter.",
                                    hide: true
                                });
                                obj.removeAttr('disabled').find('i').toggleClass('fa-spin');
                            }
                        });
                    });
JS;
$this->registerJs($js);
    ?>
</div>