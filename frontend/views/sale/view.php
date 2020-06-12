<?php

use common\models\Airline;
use common\models\CaseSale;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use sales\guards\cases\CaseManageSaleInfoGuard;
use sales\model\saleTicket\entity\SaleTicket;
use sales\model\saleTicket\useCase\sendEmail\SaleTicketHelper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $csId int */
/* @var $itemKey int */
/* @var $caseSaleModel common\models\CaseSale */
/* @var $caseModel sales\entities\cases\Cases */
/* @var $additionalData array */
/* @var $dataProviderCc yii\data\ActiveDataProvider */

if(Yii::$app->request->isPjax) {

    $this->params['breadcrumbs'][] = ['label' => 'Sales', 'url' => ['search']];
    $this->params['breadcrumbs'][] = $this->title;
}

$title = 'Sale ID: ' . $data['saleId'] . ', BookId: ' . $data['bookingId'];

$caseGuard = Yii::createObject(CaseManageSaleInfoGuard::class);
if (!empty($caseSaleModel)) {
    $canManageSaleInfo =  $caseGuard->canManageSaleInfo($caseSaleModel, Yii::$app->user->identity, $data['passengers'] ?? []);
} else {
    $canManageSaleInfo = true;
}

$saleTicketGenerateEmail = Url::toRoute(['/sale-ticket/ajax-send-email', 'case_id' => !empty($caseModel) ? $caseModel->cs_id : 0, 'sale_id' => $data['saleId'], 'booking_id' => $data['bookingId']]);

$pjaxCaseSaleTicketContainerId = 'pjax-case-sale-tickets-'.$caseSaleModel->css_cs_id.'-'.$caseSaleModel->css_sale_id;
?>
<div class="sale-view">
    <h3><?= Html::encode($title) ?></h3>
    <div class="row">
        <div class="col-md-12">
            <div class="error-dump"></div>
        </div>

        <?php if (!empty($additionalData) && $additionalData['withFareRules'] === 0) :?>
            <div class="col-md-12">
                <?php echo Html::a(
                    'Check Fare rules',
                    ['sale/view', 'h' => $additionalData['hash'], 'wfr' => 1],
                    ['class' => 'btn btn-info']
                ) ?>
            </div>
        <?php endif ?>

        <div class="col-md-3">
            <h2>General</h2>
            <table class="table table-bordered table-hover table-striped">

                <tr>
                    <th>Sale Id</th>
                    <td><?=Html::encode($data['saleId'])?></td>
                </tr>
                <tr>
                    <th>Flight Status</th>
                    <td><?=Html::encode($data['flightStatus'] ?? '')?></td>
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

        <div class="col-md-9">
            <?php if (!empty($caseSaleModel) && $saleTicket = $caseSaleModel->cssSaleTicket): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2>Sale Tickets</h2>
                            <?= Html::a('<i class="fa fa-envelope"></i> Send Email', $saleTicketGenerateEmail, ['class' => 'btn btn-success sale-ticket-generate-email-btn report-send-email-'.$caseSaleModel->css_sale_id, 'title' => SaleTicketHelper::getTitleForSendEmailBtn($saleTicket), 'data-pjax' => 0, 'data-credit-card-exist' => $dataProviderCc->totalCount]) ?>
                        </div>
                        <?php Pjax::begin(['id' => $pjaxCaseSaleTicketContainerId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]) ?>
                        <table class="table table-bordered table-hover">
                            <tr>
                                <th>Last/First Name</th>
                                <th>Ticket Number</th>
                                <th>Record Locator</th>
                                <th>Original FOP</th>
                                <th>Charge System</th>
                                <th>Airline Penalty</th>
                                <th>Penalty Amount</th>
                                <th>Selling</th>
                                <th>Service Fee</th>
                                <th>Recal Commission</th>
                                <th>Markup</th>
                                <th>Upfront Charge</th>
                                <th>Refundable Amount</th>
                            </tr>
							<?php
                            /** @var $saleTicket SaleTicket[] */
                            foreach($saleTicket as $key => $ticket): ?>
                                <tr>
                                    <td><?=Html::encode($ticket->st_client_name)?></td>
                                    <td><?=Html::encode($ticket->st_ticket_number)?></td>
                                    <td><?=Html::encode($ticket->st_record_locator)?></td>
                                    <td><?=Html::encode($ticket->getFormattedOriginalFop())?></td>
                                    <td><?=Html::encode($ticket->st_charge_system)?></td>
                                    <td><?=Html::encode(SaleTicket::getPenaltyTypeName($ticket->st_penalty_type))?></td>
                                    <td>
										<?php if (!$canManageSaleInfo):
											echo Editable::widget([
												'model' => $ticket,
												'attribute' => empty($ticket->st_refund_waiver) ? 'st_penalty_amount' : 'st_refund_waiver',
												'header' => 'Penalty Amount',
												'asPopover' => false,
												'inputType' => Editable::INPUT_HTML5,
												'formOptions' => [ 'action' => [Url::to(['/sale-ticket/ajax-sale-ticket-edit-info/', 'st_id' => $ticket->st_id])] ],
												'options' => [
													'id' => 'sale-ticket-penalty-amount-'.$key . '-' . $ticket->st_case_sale_id
												],
												'pluginEvents' => [
													'editableSuccess' => 'function (event, val, form, data) {
                                                        pjaxReload({container: "#'.$pjaxCaseSaleTicketContainerId.'"});
                                                    }',
												],
											]);
										else:
											echo Html::encode(empty($ticket->st_refund_waiver) ? $ticket->st_penalty_amount : $ticket->st_refund_waiver);
										endif;
										?>
                                    </td>
                                    <td><?=Html::encode($ticket->st_selling)?></td>
                                    <td><?=Html::encode($ticket->st_service_fee)?></td>
                                    <td>
										<?php if (!$canManageSaleInfo):
											echo Editable::widget([
												'model' => $ticket,
												'attribute' => 'st_recall_commission',
												'header' => 'Recall Commission',
												'asPopover' => false,
												'inputType' => Editable::INPUT_HTML5,
												'formOptions' => [ 'action' => [Url::to(['/sale-ticket/ajax-sale-ticket-edit-info/', 'st_id' => $ticket->st_id])] ],
												'options' => [
													'id' => 'sale-ticket-recall-commission-'.$key . '-' . $ticket->st_case_sale_id
												],
												'pluginEvents' => [
													'editableSuccess' => 'function (event, val, form, data) {
                                                        pjaxReload({container: "#'.$pjaxCaseSaleTicketContainerId.'"});
                                                    }',
												],
											]);
										else:
											echo Html::encode($ticket->st_recall_commission);
										endif;
										?>
                                    </td>
                                    <td>
										<?php if (!$canManageSaleInfo):
											echo Editable::widget([
												'model' => $ticket,
												'attribute' => 'st_markup',
												'header' => 'Markup',
												'asPopover' => false,
												'inputType' => Editable::INPUT_HTML5,
												'formOptions' => [ 'action' => [Url::to(['/sale-ticket/ajax-sale-ticket-edit-info/', 'st_id' => $ticket->st_id])] ],
                                                'options' => [
                                                    'id' => 'sale-ticket-markup-'.$key . '-' . $ticket->st_case_sale_id
                                                ],
												'pluginEvents' => [
                                                    'editableSuccess' => 'function (event, val, form, data) {
                                                        pjaxReload({container: "#'.$pjaxCaseSaleTicketContainerId.'"});
                                                    }',
												],
											]);
										else:
											echo Html::encode($ticket->st_markup);
										endif;
										?>
                                    </td>

                                    <td><?=Html::encode($ticket->st_upfront_charge)?></td>
                                    <td><?=Html::encode($ticket->st_refundable_amount)?></td>
                                </tr>
							<?php endforeach;?>
                        </table>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-5">

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

                    <h2>Customer Information</h2>
                    <div style="width: 100%; overflow-x: auto;">
                        <?php if(!empty($data['customerInfo'])): ?>
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Phone number</th>
                                    <th>Email</th>
                                </tr>
                                <tr>
                                    <td><?=Html::encode($data['customerInfo']['firstName'] ?? '')?></td>
                                    <td><?=Html::encode($data['customerInfo']['lastName'] ?? '')?></td>
                                    <td><?=Html::encode($data['customerInfo']['phoneNumber'] ?? '')?></td>
                                    <td><?=Html::encode($data['email'] ?? '')?></td>
                                </tr>
                            </table>
                        <?php endif;?>
                    </div>

                </div>

                <div class="col-md-7">
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

                    <?php
                        if (!empty($csId)) {
                            echo $this->render('partial/_sale_credit_card', [
                                'csId' => $csId,
                                'saleId' => $data['saleId'],
                                'dataProvider' => $dataProviderCc,
                                'caseSaleModel' => $caseSaleModel,
                                'caseModel' => $caseModel
                            ]);
                        }
                    ?>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Passengers</h2>
			<?php if( !empty($data['passengers']) ): ?>
<!--            --><?php //echo '<pre>';print_r($data);die; ?>
                <table class="table table-bordered table-hover" id="passengers">
                    <thead>
                    <tr>
                        <?php if(!empty($csId) && $canManageSaleInfo): ?>
                            <th></th>
                        <?php endif; ?>
                        <th>First name</th>
                        <th>Ticket number</th>
                        <th>Type</th>
                        <th>Birth date</th>
                        <th>Gender</th>
                        <th>Meal</th>
                        <th>Wheelchair</th>
                        <th>Frequent Flyer Airline</th>
                        <th>Frequent Flyer</th>
                        <th>KTN</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach($data['passengers'] as $key => $passenger): ?>
                        <tr>
							<?php if(!empty($csId) &&  $canManageSaleInfo): ?>
                                <td style="width: 30px;" class="text-center"><span data-toggle="tooltip" title="<?= $canManageSaleInfo ?>" class="label label-default bg-orange"><i class="fa fa-info"></i></span></td>
							<?php endif; ?>
                            <td>
								<?php
								/*
									$editable = Editable::begin([
										'name' => 'cssSaleData[passengers]['.$key.'][last_name]',
										'asPopover' => false,
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
								if (!$canManageSaleInfo):
                                    $editable = Editable::begin([
                                        'name' => 'cssSaleData[passengers]['.$key.'][birth_date]',
                                        'header' => 'Date of Birth',
                                        'asPopover' => false,
                                        'inputType' => Editable::INPUT_DATE,
                                        'displayValue' => date('d M Y', strtotime($passenger['birth_date'])),
                                        'value' => date('d M Y', strtotime($passenger['birth_date'])),
                                        'formOptions' => [ 'action' => [Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
                                        'options' => [
                                            'convertFormat'=>true,
                                            'pluginOptions'=>[
                                                'format'=>'php:d M Y',
                                                'autoclose'=>true,
    //                                            'type' =>
                                            ],
                                            'class' => 'cssSaleData_passengers_birth_date'
                                        ],
                                        'pluginEvents' => [
                                            'editableSuccess' => 'function (event, val, form, data) {
                                                document.activateButtonSync(data);
                                            }',
                                        ],
                                        'pjaxContainerId' => 'pjax-sale-list'
                                    ]);
								?>
                                <?php  $editable->beforeInput = Html::hiddenInput('cssSaleData[passengers][' .$key. '][type]', Html::encode($passenger['type'])); ?>
                                <?php  Editable::end(); else: ?>
                                    <?= date('d M Y', strtotime($passenger['birth_date'])) ?>
                                <?php endif; ?>
                            </td>
                            <td>
								<?php if (!$canManageSaleInfo):
								    echo Editable::widget([
                                            'name' => 'cssSaleData[passengers]['.$key.'][gender]',
                                            'header' => 'Gender',
                                            'asPopover' => false,
                                            'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                            'data' => ['F' => 'Female', 'M' => 'Male'],
                                            'value' => Html::encode($passenger['gender']),
                                            'formOptions' => [ 'action' => [Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
                                            'pluginEvents' => [
                                                'editableSuccess' => 'function (event, val, form, data) {
                                                    document.activateButtonSync(data);
                                                }',
                                            ],
                                            'pjaxContainerId' => 'pjax-sale-list'
                                        ]);
								    else:
								        echo Html::encode($passenger['gender']);
                                    endif;
								?>
                            </td>
                            <td>
								<?php if (!$canManageSaleInfo) {
                                    echo Editable::widget([
                                        'name' => 'cssSaleData[passengers]['.$key.'][meal]',
                                        'header' => 'Meal',
                                        'asPopover' => false,
                                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                        'data' => CaseSale::PASSENGER_MEAL,
                                        'options' => ['prompt'=>'Select meal...'],
                                        'value' => Html::encode(!empty($passenger['meal']) && is_array($passenger['meal']) ? reset($passenger['meal']) : null),
                                        'formOptions' => [ 'action' => [Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
                                        'pluginEvents' => [
                                            'editableSuccess' => 'function (event, val, form, data) {
                                                document.activateButtonSync(data);
                                            }',
                                        ],
//                                        'placement' => PopoverX::ALIGN_TOP_LEFT,
                                        'pjaxContainerId' => 'pjax-sale-list'
                                    ]);
								} else {
									if (isset($passenger['meal']) && is_array($passenger['meal'])) {
										echo reset($passenger['meal']) ?: '(not set)';
									} else {
										echo !empty($passenger['meal']) ? Html::encode($passenger['meal']) : '(not set)';
									}
								}
								?>
                            </td>
                            <td>
								<?php if(!$canManageSaleInfo) {
									echo Editable::widget([
										'name' => 'cssSaleData[passengers][' . $key . '][wheelchair]',
										'header' => 'Wheelchair',
										'asPopover' => false,
										'inputType' => Editable::INPUT_DROPDOWN_LIST,
										'data' => CaseSale::PASSENGER_WHEELCHAIR,
										'value' => Html::encode(!empty($passenger['wheelchair']) && is_array($passenger['wheelchair']) ? reset($passenger['wheelchair']) : null),
										'formOptions' => ['action' => [Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])]],
										'options' => ['prompt' => 'Select wheelchair...'],
										'pluginEvents' => [
											'editableSuccess' => 'function (event, val, form, data) {
										    document.activateButtonSync(data);
										}',
										],
//										'placement' => PopoverX::ALIGN_TOP_LEFT,
										'pjaxContainerId' => 'pjax-sale-list'
									]);
								} else {
									if (isset($passenger['wheelchair']) && is_array($passenger['wheelchair'])) {
										echo reset($passenger['wheelchair']) ?: '(not set)';
									} else {
										echo !empty($passenger['wheelchair']) ? Html::encode($passenger['wheelchair']) : '(not set)';
									}
								}
								?>
                            </td>
                            <td>
								<?php if(!$canManageSaleInfo && empty($passenger['ff_numbers'])) {
									echo Editable::widget([
										'name' => 'cssSaleData[passengers][' . $key . '][ff_airline]',
										'header' => 'Frequent Flyer Airline',
										'asPopover' => false,
										'inputType' => Editable::INPUT_DROPDOWN_LIST,
										'data' => Airline::getAirlinesMapping(true),
										'value' => Html::encode(!empty($passenger['ff_airline']) ? $passenger['ff_airline'] : $data['validatingCarrier']),
										'formOptions' => ['action' => [Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])]],
										'options' => [],
										'pluginEvents' => [
											'editableSuccess' => 'function (event, val, form, data) {
										        document.activateButtonSync(data);
										    }',
										],
//										'placement' => PopoverX::ALIGN_TOP_LEFT,
										'pjaxContainerId' => 'pjax-sale-list'
									]);
								} else if (!empty($passenger['ff_airline'])) {
								    echo $passenger['ff_airline'];
                                } else if (!empty($passenger['ff_numbers'])) {
								    echo array_key_first($passenger['ff_numbers']);
                                } else {
								    echo '(not set)';
                                }
								?>
                            </td>
                            <td>
                                <?php if(!$canManageSaleInfo && empty($passenger['ff_numbers'])) {
									echo Editable::widget([
										'name' => 'cssSaleData[passengers][' . $key . '][ff_numbers]',
										'header' => 'Frequent Flyer',
										'asPopover' => false,
										'inputType' => Editable::INPUT_TEXT,
										'value' => Html::encode(!empty($passenger['ff_numbers']) && is_array($passenger['ff_numbers']) ? reset($passenger['ff_numbers']) : null),
										'formOptions' => ['action' => [Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])]],
										'pluginEvents' => [
											'editableSuccess' => 'function (event, val, form, data) {
										    document.activateButtonSync(data);
										}',
										],
//										'placement' => PopoverX::ALIGN_TOP_LEFT,
										'pjaxContainerId' => 'pjax-sale-list'
									]);
								} else {
									if (isset($passenger['ff_numbers']) && is_array($passenger['ff_numbers'])) {
										echo reset($passenger['ff_numbers']) ?: '(not set)';
									} else {
										echo !empty($passenger['ff_numbers']) ? Html::encode($passenger['ff_numbers']) : '(not set)';
									}
                                }
								?>
                            </td>
                            <td>
								<?php if(!$canManageSaleInfo) {
                                    echo Editable::widget([
                                        'name' => 'cssSaleData[passengers]['.$key.'][kt_numbers]',
                                        'header' => 'KTN',
                                        'asPopover' => false,
                                        'inputType' => Editable::INPUT_TEXT,
                                        'value' => Html::encode(!empty($passenger['kt_numbers']) && is_array($passenger['kt_numbers']) ? reset($passenger['kt_numbers']) : null),
                                        'formOptions' => [ 'action' => [Url::to(['/cases/ajax-sale-list-edit-info/', 'caseId' => $csId, 'caseSaleId' => $data['saleId']])] ],
                                        'pluginEvents' => [
                                            'editableSuccess' => 'function (event, val, form, data) {
                                                document.activateButtonSync(data);
                                            }',
                                        ],
//                                        'placement' => PopoverX::ALIGN_LEFT,
                                        'pjaxContainerId' => 'pjax-sale-list'
                                    ]);
                                } else {
									if (isset($passenger['kt_numbers']) && is_array($passenger['kt_numbers'])) {
										echo reset($passenger['kt_numbers']) ?: '(not set)';
									} else {
										echo !empty($passenger['kt_numbers']) ? Html::encode($passenger['kt_numbers']) : '(not set)';
									}
                                }
////								Html::encode($passenger['kt_numbers'] ?? null)
								?>
                            </td>
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
                            <th>Departure / Arrival</th>
                            <th>Booking Class</th>
                            <th>Flight Number</th>
                            <th>Status Code</th>
                            <th>Operating Airline</th>
                            <th>Cabin</th>
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

                                <td>
                                    <table class="table table-responsive table-striped table-bordered">
                                        <tr>
                                            <th></th>
                                            <th>Country</th>
                                            <th>City</th>
                                            <th>IATA</th>
                                            <th>AirportName</th>
                                            <th>Date Time</th>
                                        </tr>
                                        <tr>
                                            <th>Departure</th>
                                            <td><?=Html::encode($segment['departureCountry'])?></td>
                                            <td><?=Html::encode($segment['departureCity'])?></td>
                                            <td><span class="label label-default"><?=Html::encode($segment['departureAirport'])?></span></td>
                                            <td><?=Html::encode($segment['departureAirportName'])?></td>
                                            <td><?=Html::encode($segment['departureTime'])?></td>
                                        </tr>
                                        <tr>
                                            <th>Arrival</th>
                                            <td><?=Html::encode($segment['arrivalCountry'])?></td>
                                            <td><?=Html::encode($segment['arrivalCity'])?></td>
                                            <td><span class="label label-default"><?=Html::encode($segment['arrivalAirport'])?></span></td>
                                            <td><?=Html::encode($segment['arrivalAirportName'])?></td>
                                            <td><?=Html::encode($segment['arrivalTime'])?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td><?=Html::encode($segment['bookingClass'])?></td>
                                <td><?=Html::encode($segment['flightNumber'])?></td>
                                <td><?=Html::encode($segment['statusCode'])?></td>
                                <td><?=Html::encode($segment['operatingAirline'])?></td>
                                <td><span class="label label-default"><?=Html::encode($segment['cabin'])?></span></td>
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

    <?php if (!empty($data['fareRules'])): ?>

        <?php
            try {
         ?>

            <h4>Fare Rules</h4>
            <?php foreach ($data['fareRules'] as $rule): ?>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="card">
                            <div class="card-body">

                                <?php foreach ($rule as $key => $value): ?>

                                    <?php if ($key !== 'rules'): ?>
                                        <br> <b><?= $key ?></b>: <?= Html::encode($value) ?>
                                    <?php else: ?>
                                        <?php foreach ($value as $item): ?>
                                            <b>Rules:</b>
                                            <div class="card">
                                                <div class="card-body">
                                                    <?php if (isset($item['details'])): ?>
                                                        <b>Details</b>
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <?php foreach ($item['details'] as $detailKey => $detailValue): ?>
                                                                    <b><?= $detailKey ?></b>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <table class="table table-bordered table-hover table-striped">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>for</th>
                                                                                    <th>title</th>
                                                                                    <th>value</th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                <?php foreach ($detailValue as $elem): ?>
                                                                                    <tr>
                                                                                        <td><?= $elem['for'] ?></td>
                                                                                        <td><?= $elem['title'] ?></td>
                                                                                        <td><?= $elem['value'] ?></td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <br>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row">

                                                                <div class="col-md-6">
                                                                    <table class="table table-bordered table-hover table-striped">

                                                                        <?php if (isset($item['category'])): ?>
                                                                            <tr>
                                                                                <td>category</b></td>
                                                                                <td> <?= Html::encode($item['category']) ?> </td>
                                                                            </tr>
                                                                        <?php endif; ?>

                                                                        <?php if (isset($item['fullText'])): ?>
                                                                            <tr>
                                                                                <td>fullText</b></td>
                                                                                <td> <?= Yii::$app->formatter->format($item['fullText'], 'ntext') ?> </td>
                                                                            </tr>

                                                                        <?php endif; ?>

                                                                        <?php if (isset($item['categoryTitle'])): ?>
                                                                            <tr>
                                                                                <td>categoryTitle</b></td>
                                                                                <td> <?= Html::encode($item['categoryTitle']) ?> </td>
                                                                            </tr>

                                                                        <?php endif; ?>

                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            <?php endforeach; ?>

        <?php

            } catch (Throwable $e) {
                Yii::error($e->getMessage() . VarDumper::dumpAsString($data['fareRules']), 'Parsing:fareRules');
            }

        ?>
    <?php endif; ?>

    <?php
	$url = Url::to(['/cases/ajax-sync-with-back-office/']);

	$js = <<<JS
                    $(".update-to-bo").on('click', function (e) {
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
                                obj.attr('disabled', true).find('i').toggleClass('fa-spin').removeClass('fa-upload').addClass('fa-refresh');
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
                                obj.find('i').toggleClass('fa-spin').removeClass('fa-refresh').addClass('fa-upload');
                                
                                if (!json.error) {
                                    $.pjax.reload({container: '#pjax-sale-list',push: false, replace: false, 'scrollTo': false, timeout: 1000, async: false,});
                                }
                            },
                            error: function (text) {
                                new PNotify({
                                    title: "Error",
                                    type: "error",
                                    text: "Internal Server Error. Try again letter.",
                                    hide: true
                                });
                                obj.removeAttr('disabled').find('i').toggleClass('fa-spin').removeClass('fa-refresh').addClass('fa-upload');
                            }
                        });
                    });

    $('.remove-sale').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        if(!confirm('Are you sure you want to delete this sale?')) {
            return false;
        }  
        
        let obj = $(this),            
            caseSaleId = obj.attr('data-case-sale-id'),
            caseId = obj.attr('data-case-id');
                
        $.ajax({
            url: '/sale/delete-ajax?id=' + caseSaleId,
            type: 'post',
            data: {'sale_id' : caseSaleId, 'case_id' : caseId}, 
            dataType: "json",    
            beforeSend: function () {
                obj.attr('disabled', true).find('i').toggleClass('fa-spin');
                $(obj).closest('.panel').find('.error-dump').html();
            },
            success: function (data) {
                if (data.error) {
                   new PNotify({
                        title: "Error",
                        type: "error",
                        text: data.error,
                        hide: true
                    }); 
                } else {
                    new PNotify({
                        title: "Success",
                        type: "success",
                        text: 'Successfully deleted',
                        hide: true
                    }); 
                    $.pjax.reload({container: '#pjax-sale-list', push: false, replace: false, 'scrollTo': false, timeout: 1000, async: false,});
                }
            },
            error: function (text) {
                new PNotify({
                    title: "Error",
                    type: "error",
                    text: "Internal Server Error. Try again letter.",
                    hide: true
                });
            },
            complete: function () {
                obj.removeAttr('disabled').find('i').toggleClass('fa-spin');
                $(obj).closest('.panel').find('.error-dump').html();
            }
        });
    });
                    
    $('#passengers span[data-toggle="tooltip"]').tooltip();
    
JS;
$this->registerJs($js);
    ?>
</div>