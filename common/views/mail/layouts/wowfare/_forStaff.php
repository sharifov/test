<?php
/**
 * @var $project \common\models\Project
 * @var $agentName string
 * @var $employee \common\models\Employee
 * @var $body string
 * @var $templateType string
 * @var $userProjectParams \common\models\UserProjectParams
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml" style="background: #f3f3f3; min-height: 100%;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers</title>
    <!--Styles-->
    <style>
        @media (max-width: 479px) {
            td.col {
                display: inline-block;
                width: 100%;
            }
            td.col.col-md-6 {
                width: 100%;
            }
        }
        @media (max-width: 479px) {
            .header {
                padding-top: 30px !important;
            }
            .wrapper-inner table {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
            }
            .wrapper-inner {
                padding: 0 10px !important;
                width: 0 !important;
                min-width: 0 !important;
                max-width: 0 !important;
            }
            .block {
                padding: 15px !important;
                border-radius: 0 !important;
            }
            td.sales-contacts td.col:first-child {
                margin-bottom: 24px !important;
            }
            .footer {
                padding: 10px 16px 24px 16px !important;
            }
            .intro-text .h1 {
                font-size: 20px !important;
            }
        }
    </style>

    <style>
        @media (max-width: 479px) {
            .request-options {
                letter-spacing: 1px;
            }
            .request-title .h1 {
                font-size: 20px;
                line-height: 24px;
                padding-top: 5px;
            }
            .airline-logo {
                width: 55px !important;
                display: block !important;
            }
            .airline-logo-img {
                width: 20px !important;
                height: 20px !important;
                margin: 0 auto 1px !important;
            }
            .baggage-text span {
                display: none !important;
            }
            .city {
                display: none !important;
            }
            .time,
            .iata {
                display: block !important;
                font-size: 14px !important;
            }
            .iata {
                margin-right: 0 !important;
            }
            .duration-stops {
                padding: 0 15px !important;
            }
            .duration, .stops {
                color: #bbc2c5 !important;
            }
            .trip {
                padding: 8px !important;
            }
            .from, .top {
                width: auto !important;
            }
            .depart-date {
                display: block !important;
                font-size: 12px !important;
                padding: 0 !important;
                text-align: center !important;
            }
            .trip > table {
                position: relative !important;
            }
            .offer-price-note {
                letter-spacing: 0.5px !important;
                font-size: 11px !important;
            }
            .offer-price-value {
                font-size: 20px !important;
            }
            .block {
                border-bottom-width: 10px !important;
            }
            td.offer-footer {
                padding: 6px 8px !important;
            }
            .offer-price, .offer-details {
                padding-top: 30px !important;
            }
        }
    </style>
</head>
<body style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: none; background-color: #F2F6F7; box-sizing: border-box; color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; line-height: 1.3; margin: 0; min-width: 100%; padding: 0; text-align: left; width: 100% !important;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="body" style="background: #F2F6F7; border-collapse: collapse; border-spacing: 0; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; height: 100%; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
    <tr style="padding: 0; text-align: left; vertical-align: top;">
        <td class="wrapper-inner" align="center" valign="top" style="-moz-hyphens: auto; -webkit-hyphens: auto; background: #F2F6F7; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; max-width: 600px; min-width: 600px; padding: 0 20px; text-align: left; vertical-align: top; width: 600px; word-wrap: break-word;">
            <table width="600" align="center" border="0" cellpadding="0" cellspacing="0" class="container content-wrapper main-content" style="background: #fefefe; background-color: transparent; border-collapse: collapse; border-spacing: 0; margin: 0 auto; max-width: 600px; min-width: 600px; padding: 0; text-align: inherit; vertical-align: top; width: 600px;">
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">

                            <!------------------------
                            -----------Header---------
                            ------------------------->

                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <td class="header align-center full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; background: transparent; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 20px 20px 15px; text-align: center; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                                    <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: center; vertical-align: top; width: 100% !important;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <td class="full-wd logo-wrapper" valign="middle" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; padding-bottom: 10px; text-align: center; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                                                <img src="<?= $project->link ?>/theme/wowfare/images/email/logo.png" alt="<?= $project->name ?>" width="160" height="50" class="logo" style="-ms-interpolation-mode: bicubic; clear: both; display: block; height: 50px; margin: 0 auto; max-width: 100%; outline: none; text-decoration: none; width: 160px;">
                                            </td>
                                        </tr>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <td class="header-menu" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #8E9399; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 18px; margin: 0; padding: 0; text-align: center; vertical-align: top; word-wrap: break-word;">Book flights online the right way</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

							<?= $body ?>


                        </table>
                    </td>
                </tr>

                <!------------------------
                ----   Sales Footer   ----
                ------------------------->

                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="block full-wd sales-footer" style="-moz-hyphens: auto; -webkit-hyphens: auto; background-color: #fff; border-bottom: 16px solid #F2F6F7; border-collapse: collapse !important; border-radius: 4px; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 32px; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <td class="full-wd sales-intro" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; padding-bottom: 30px; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                                    <h3 class="h3" style="color: #212121; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 20px; font-weight: bolder; line-height: 28px; margin: 0; margin-bottom: 16px; padding: 0; text-align: left;">Sincerely, your agent <?= $agentName ?></h3>
                                    <div class="text" style="font-size: 14px; line-height: 24px;">If you have additional questions or requests, you can contact me by phone, or simply replying to this email.</div>
                                </td>
                            </tr>
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <td class="full-wd sales-contacts" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                                    <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <td class="col col-md-6" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; padding-right: 16px; text-align: left; vertical-align: top; width: 50%; word-wrap: break-word;">
                                                <h4 class="h4" style="color: #212121; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bolder; line-height: 19px; margin: 0; margin-bottom: 12px; padding: 0; text-align: left;"><?= $agentName ?></h4>
                                                <div class="text" style="font-size: 14px; line-height: 24px;">
                                                    <a href="tel:<?php if ($userProjectParams !== null && !empty($userProjectParams->upp_phone_number)) {
                                                        echo $userProjectParams->upp_phone_number;
                                                    } else {
                                                        echo $project->contactInfo->phone;
                                                    } ?>" style="color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none;"><?php if ($userProjectParams !== null && !empty($userProjectParams->upp_phone_number)) {
                                                        echo $userProjectParams->upp_phone_number;
                                                    } else {
                                                        echo $project->contactInfo->phone;
                                                    } ?></a><br>
                                                    <a href="mailto:<?= $userProjectParams->upp_email ?>" style="color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none;"><?= $userProjectParams->upp_email ?></a>
                                                </div>
                                            </td>
                                            <td class="col col-md-6" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; padding-right: 16px; text-align: left; vertical-align: top; width: 50%; word-wrap: break-word;">
                                                <h4 class="h4" style="color: #212121; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bolder; line-height: 19px; margin: 0; margin-bottom: 12px; padding: 0; text-align: left;">General Line</h4>
                                                <div class="text" style="font-size: 14px; line-height: 24px;">
                                                    <a href="tel:<?= $project->contactInfo->phone ?>" style="color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none;"><?= $project->contactInfo->phone ?></a><br>
                                                    <a href="mailto:<?= $project->contactInfo->email ?>" style="color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none;"><?= $project->contactInfo->email ?></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>


            </table>     <!--End of main-->
        </td>   <!--...-->
    </tr>   <!--...-->

    <!------------------------
    -------   Footer   -------
    ------------------------->

    <tr style="padding: 0; text-align: left; vertical-align: top;">
        <td class="full-wd footer align-center" align="center" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 30px 16px 60px 16px; text-align: center; vertical-align: top; width: 100% !important; word-wrap: break-word;">
            <table class="full-wd" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: center; vertical-align: top; width: 100% !important;">
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="full-wd trustpilot" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: center; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        <a href="https://www.trustpilot.com/review/wowfare.com" class="trustpilot-link" style="color: #806bff; display: inline-block; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 20px; padding: 0; text-align: left; text-decoration: none;">
                            <img src="<?= $project->link ?>/images/email/trustpilot.png" alt="" class="trustpilot-logo" width="126" height="61" style="-ms-interpolation-mode: bicubic; border: none; clear: both; display: block; height: 61px; margin: 0 auto; max-width: 100%; outline: none; text-decoration: none; width: 126px;">
                        </a>
                    </td>
                </tr>
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="footer-info full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #87929D; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; margin: 0; padding: 0; text-align: center; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        Sent by <a href="<?= $project->link ?>" style="color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 24px; margin: 0; padding: 0; text-align: left; text-decoration: underline;"><?= $project->name ?></a>
                    </td>
                </tr>
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="footer-menu full-wd" style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: center; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        <a href="<?= $project->link ?>/unsubscribe" class="footer-menu-link" style="color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 24px; margin: 0; margin-left: 1px; margin-right: 5px; padding: 0; text-align: left; text-decoration: underline;">Unsubscribe</a>&#183;
                        <a href="<?= $project->link ?>/care" class="footer-menu-link" style="color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 24px; margin: 0; margin-left: 1px; margin-right: 5px; padding: 0; text-align: left; text-decoration: underline;">Help</a>&#183;
                        <a href="<?= $project->link ?>/policy" class="footer-menu-link" style="color: #4C5259; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 24px; margin: 0; margin-left: 1px; margin-right: 5px; padding: 0; text-align: left; text-decoration: underline;">Privacy policy</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>



</body>
</html>