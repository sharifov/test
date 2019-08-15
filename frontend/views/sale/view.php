<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data array */


if(Yii::$app->request->isAjax) {

    $this->params['breadcrumbs'][] = ['label' => 'Sales', 'url' => ['search']];
    $this->params['breadcrumbs'][] = $this->title;
}

$this->title = 'Sale ID: ' . $data['saleId'] . ', BookId: ' . $data['bookingId'];

//$isAgent = Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id);

?>
<div class="sale-view">
    <h3><?= Html::encode($this->title) ?></h3>
    <div class="row">

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
            <h2>Passengers</h2>
            <?php if(isset($data['passengers']) && $data['passengers']): ?>
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Type</th>
                        <th>First name</th>
                        <th>Middle name</th>
                        <th>Last name</th>
                        <th>Birth date</th>
                        <th>Gender</th>
                        <th>Ticket number</th>
                    </tr>
                <?php foreach($data['passengers'] as $passenger): ?>
                    <tr>
                        <td><?=Html::encode($passenger['type'])?></td>
                        <td><?=Html::encode($passenger['first_name'])?></td>
                        <td><?=Html::encode($passenger['middle_name'])?></td>
                        <td><?=Html::encode($passenger['last_name'])?></td>
                        <td><?=Html::encode($passenger['birth_date'])?></td>
                        <td><?=Html::encode($passenger['gender'])?></td>
                        <td><?=Html::encode($passenger['ticket_number'])?></td>
                    </tr>
                <?php endforeach;?>
                </table>
            <?php endif;?>

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

    <div class="row">
        <?php
            //\yii\helpers\VarDumper::dump($data, 10, true);
        ?>
    </div>
</div>

<?php
/*yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'modal-ip-Header'],
    'id' => 'modal-ip',
    'size' => 'modal-lg',
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);

if($model->request_ip_detail){
    $data = @json_decode($model->request_ip_detail);

    if($data) {
        echo '<pre>';
        \yii\helpers\VarDumper::dump($data, 10, true);
        echo '</pre>';
    }
}
yii\bootstrap\Modal::end();


$jsCode = <<<JS
    $(document).on('click', '#btn_show_modal', function(){
        $('#modal-ip-Header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        $('#modal-ip').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);*/