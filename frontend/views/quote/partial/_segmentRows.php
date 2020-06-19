<?php

/**
 * @var $segments []
 * @var $baggage []
 */

use common\models\Airport;
use yii\bootstrap\Html;

?>
<table class="table table-bordered table-lm">
    <tbody>
    <?php foreach ($segments as $key => $segment) : ?>
        <tr>
            <td>Segment <?php echo $key+1 ?></td>
            <td><?php echo $segment['airlineName'] ?></td>
            <td>
                <?php echo $segment['carrier'] ?>&nbsp;
                <?php echo $segment['flightNumber'] ?>
            </td>
            <td>
                <?php echo $segment['departureDateTime']->format('g:i A M d') ?>&nbsp;
                <?php echo Airport::findOne($segment['departureAirport'])->getCityName() ?>&nbsp;
                <?php echo $segment['departureAirport'] ?>
            </td>
            <td>
                <?php echo $segment['arrivalDateTime']->format('g:i A M d') ?>&nbsp;
                <?php echo Airport::findOne($segment['arrivalAirport'])->getCityName() ?>&nbsp;
                <?php echo $segment['arrivalAirport'] ?>
            </td>
        </tr>
        <tr colspan="5">
           <!-- <table class="table table-sm">
                <tr>
                    <td>Baggage Type</td>
                    <td>Pieces</td>
                    <td>Max Size</td>
                    <td>Max Weight</td>
                    <td>Cost</td>
                </tr>
            </table>-->
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

