<?php
/**
 * @var $project \common\models\Project
 * @var $quotes \common\models\Quote[]
 * @var $agentName string
 * @var $origin string
 * @var $destination string
 * @var $tripType string
 * @var $employee \common\models\Employee
 * @var $userProjectParams \common\models\UserProjectParams
 */
?>
<!------------------------
--------   Intro   -------
------------------------->

<tr style="padding: 0; text-align: left; vertical-align: top;">
    <td class="block full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; background-color: #fff; border-bottom: 16px solid #F2F6F7; border-collapse: collapse !important; border-radius: 4px; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 32px; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
        <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
            <tr style="padding: 0; text-align: left; vertical-align: top;">
                <td class="intro-text text" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                    <h1 class="h1" style="color: #212121; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 28px; font-weight: bolder; letter-spacing: -0.5px; line-height: 40px; margin: 0; margin-bottom: 7px; padding: 0; text-align: left;">Dear customer,</h1>
                    <p style="font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; line-height: 24px; margin: 0; margin-bottom: 0; padding: 0; text-align: left;">
                        My name is <?= $agentName ?>, I’m your personal travel expert. Below you will
                        find information on the itinerary from <?= $origin ?> to <?= $destination ?>
                        designed for your needs. To Ensure delivery of future email
                        correspondence, please add
                        <a href="mailto:<?= $userProjectParams->upp_email ?>" style="color: #806bff; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none;"><?= $userProjectParams->upp_email ?></a> to your
                        address book.
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>


<!------------------------
--------   Offers  -------
------------------------->

<tr style="padding: 0; text-align: left; vertical-align: top;">
    <td class="full-wd offers block" style="-moz-hyphens: auto; -webkit-hyphens: auto; background-color: #fff; border-bottom: 16px solid #F2F6F7; border-collapse: collapse !important; border-radius: 4px; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 32px; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
        <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">

            <!--Request start-->
            <tr style="padding: 0; text-align: left; vertical-align: top;">
                <td class="request-title" colspan="2" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                    <h1 class="h1 align-center" style="color: #212121; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 28px; font-weight: bolder; letter-spacing: -0.5px; line-height: 33px; margin: 0; margin-bottom: 6px; padding: 0; text-align: center; width: 100%;"><?= $origin ?> - <?= $destination ?></h1></td>
            </tr>
            <tr style="padding: 0; text-align: left; vertical-align: top;">
                <td class="request-options" colspan="2" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #8E9399; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; letter-spacing: 2px; line-height: 1.3; margin: 0; padding: 0; text-align: center; text-transform: uppercase; vertical-align: top; word-wrap: break-word;"><?= $tripType ?>, <?= $leadCabin?>, <?= $nrPax?> Travellers
                </td>
            </tr>


            <!--Offer start-->
<?php
foreach ($quotes as $key => $offer) :
    $offerData = $offer->getInfoForEmail();
    ?>
            <tr style="padding: 0; text-align: left; vertical-align: top;">
                <td class="full-wd offer-heading" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                    <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                            <td class="offer-price align-left col col-6" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; padding-bottom: 6px; padding-top: 50px; text-align: left; vertical-align: bottom; width: 50%; word-wrap: break-word;">
                                <span class="offer-price-value" style="color: #212121; font-size: 26px; font-weight: bold; letter-spacing: 0.15px; line-height: 30px;">$<?= $offerData['price']?></span>&nbsp;
                                <span class="offer-price-note" style="color: #8E9399; font-size: 12px; letter-spacing: 2px; text-transform: uppercase;">/ per adult</span></td>
                            <td class="offer-details align-right col col-6" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; padding-bottom: 6px; padding-top: 50px; text-align: right; vertical-align: bottom; width: 50%; word-wrap: break-word;">
                            <a href="<?= $project->link?>/checkout/quote/<?= $offer->uid?>" class="offer-details-link" style="color: #806bff; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 15px; font-weight: bold; letter-spacing: 0.5px; line-height: 24px; margin: 0; padding: 0; text-align: left; text-decoration: none;">View
                                Details</a></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="padding: 0; text-align: left; vertical-align: top;">
                <td class="offer-box" style="-moz-hyphens: auto; -webkit-hyphens: auto; border: 1px solid #DADEF2; border-bottom: none; border-collapse: collapse !important; border-radius: 3px 3px 0 0; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                    <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                        <!--Legs-->
                        <?php foreach ($offerData['trips'] as $segment):?>
                        <?php $airlineLogo = ($segment['airline'] == '')?'/images/email/multiple_airlines.png':'/images/square-carriers/'.strtolower($segment['airline']).'.png';?>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                            <td class="full-wd trip" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-bottom: 1px dashed #ECEFF8; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 18px 16px 16px; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                                <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                                    <tr style="padding: 0; text-align: left; vertical-align: top;">
                                        <td class="airline-logo" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 32px; word-wrap: break-word;">
                                            <img src="<?= $project->link.$airlineLogo?>" alt="<?= $segment['airline']?>" class="airline-logo-img" width="32" height="32" style="-ms-interpolation-mode: bicubic; clear: both; display: block; height: 32px; max-width: 100%; outline: none; text-decoration: none; width: 32px;">
                                        </td>
                                        <td class="depart-date" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #8E9399; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0 10px 0 20px; text-align: left; vertical-align: middle; width: 55px; white-space: nowrap;">
                                            <?= $segment['departureDate']?>
                                        </td>
                                        <td class="from" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: auto; word-wrap: break-word;">
                                            <table class="full-wd align-right" valign="middle" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: right; vertical-align: top; width: 100% !important;">
                                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                                    <td class="full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: right; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                                                        <span class="iata" style="font-size: 16px; line-height: 18px; margin-right: 4px;"><?= $segment['departureAirport']?></span>
                                                        <span class="time" style="font-size: 16px; font-weight: bold; line-height: 18px;"><?= $segment['departureTime']?></span>
                                                    </td>
                                                </tr>
                                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                                    <td class="full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: right; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                                                        <span class="city" style="color: #8E9399; font-size: 14px; line-height: 16px;"><?= $segment['departureCity']?></span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="duration-stops" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0 30px; text-align: left; vertical-align: middle; width: 50px; word-wrap: break-word;">
                                            <table class="full-wd align-center" valign="middle" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: center; vertical-align: top; width: 100% !important;">
                                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                                    <td class="duration" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #8E9399; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 14px; margin: 0; padding: 0; text-align: center; vertical-align: top; word-wrap: break-word;"><?= $segment['duration']?></td>
                                                </tr>
                                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                                    <td class="stops" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #8E9399; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 14px; margin: 0; padding: 0; text-align: center; vertical-align: top; word-wrap: break-word;"><?= $segment['stops']?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="to" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                                            <table class="full-wd align-left" valign="middle" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                                    <td class="full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                                                        <span class="iata" style="font-size: 16px; line-height: 18px; margin-right: 4px; white-space: nowrap;"><?= $segment['arrivalAirport']?><?php if($segment['arrivalDatePlus'] > 0):?>&nbsp;<sup>&#43;<?= $segment['arrivalDatePlus']?></sup><?php endif;?></span>
                                                        <span class="time" style="font-size: 16px; font-weight: bold; line-height: 18px;"><?= $segment['arrivalTime']?></span>
                                                    </td>
                                                </tr>
                                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                                    <td class="full-wd city" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #8E9399; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;"><?= $segment['arrivalCity']?></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </table>
                </td>
            </tr>
            <tr style="padding: 0; text-align: left; vertical-align: top;">
                <td class="offer-footer full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; background: #FBFCFE; border: 1px solid #DADEF2; border-collapse: collapse !important; border-radius: 0 0 3px 3px; border-top: none; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; min-height: 40px; padding: 10px; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                    <span class="baggage-item baggage-hand" style="display: inline-block; padding-right: 20px; vertical-align: middle;">
                                <img src="<?= $project->link?>/images/email/icn-carryon-on.png" alt="1" class="baggage-icon" style="-ms-interpolation-mode: bicubic; clear: both; display: inline-block; height: 20px; letter-spacing: 0.25px; max-width: 100%; outline: none; padding-right: 3px; text-decoration: none; vertical-align: middle; width: 20px;">
                                <span class="baggage-text" style="color: #4C5259; font-size: 12px; line-height: 18px; vertical-align: middle;">1<span> Carryon Bag</span></span>
                    </span>
                    <span class="baggage-item baggage-base" style="display: inline-block; padding-right: 20px; vertical-align: middle;">
                                <img src="<?= $project->link?>/images/email/icn-checked-<?= ($offerData['baggage'])?'on':'off';?>.png" alt="0" class="baggage-icon" style="-ms-interpolation-mode: bicubic; clear: both; display: inline-block; height: 20px; letter-spacing: 0.25px; max-width: 100%; outline: none; padding-right: 3px; text-decoration: none; vertical-align: middle; width: 20px;">
                                <span class="baggage-text" style="color: #4C5259; font-size: 12px; line-height: 18px; vertical-align: middle;"><?= ($offerData['baggage'])?$offerData['baggage']:0?><span> Checked Bag</span></span>
                    </span>
                </td>
            </tr>
            <?php endforeach;?>
        </table>
    </td>
</tr>