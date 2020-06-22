<?php

/**
 * @var $segments []
 * @var $baggage []
 */

use common\models\Airport;

?>

<?php foreach ($segments as $key => $segment) : ?>
    <div class="row">
        <div class="col-1 border p-1">
            <strong>Segment <?php echo $key+1 ?></strong>
        </div>
        <div class="col-1 border p-1">
            <?php echo $segment['airlineName'] ?>
        </div>
        <div class="col-1 border p-1">
            <?php echo $segment['carrier'] ?>&nbsp;
            <?php echo $segment['flightNumber'] ?>
        </div>
        <div class="col-3 border p-1">
            <?php echo $segment['departureDateTime']->format('g:i A M d') ?>&nbsp;
            <?php echo Airport::findOne($segment['departureAirport'])->getCityName() ?>&nbsp;
            <?php echo $segment['departureAirport'] ?>
        </div>
        <div class="col border p-1">
            <?php echo $segment['arrivalDateTime']->format('g:i A M d') ?>&nbsp;
            <?php echo Airport::findOne($segment['arrivalAirport'])->getCityName() ?>&nbsp;
            <?php echo $segment['arrivalAirport'] ?>
        </div>
    </div>
    <div class="row">
        <div class="col-1 border p-1">Baggage Type</div>
        <div class="col-1 border p-1">Pieces</div>
        <div class="col-1 border p-1">Max Size</div>
        <div class="col-1 border p-1">Max Weight</div>
        <div class="col-1 border p-1">Cost</div>

        <?php if (isset($segment['baggage']['free_baggage'])) : ?>
            <?php foreach ($segment['baggage']['free_baggage'] as $freeBaggage) : ?>

            <?php endforeach; ?>
        <?php else : ?>

        <?php endif; ?>

    </div>
    <br />
<?php endforeach; ?>


