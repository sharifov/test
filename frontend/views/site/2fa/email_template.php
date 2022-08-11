<?php

/**
 * @var string $emailSubject
 * @var string $code
 * @var string $secondsRemain
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>
    </title>
</head>
<body>

<table class="full-wd" width="600"
       style="border-collapse:separate;box-shadow: 0px 1px 3px rgba(26, 49, 71, 0.12), 0px 1px 2px rgba(26, 49, 71, 0.08);background-color: #fff;border: 1px solid #E5E9F2;border-radius:4px;border-spacing:0;font-family:Roboto,Arial,Helvetica,sans-serif;font-size:14px;font-weight:normal;hyphens:auto;line-height:24px;margin-bottom:16px;padding:0;vertical-align:top;width:100%;">
    <tr style="font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0; text-align: left; vertical-align: top;">
        <td style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 24px; padding-right: 16px; text-align: left; vertical-align: top; width: 24px; word-wrap: break-word;">
            <table class="full-wd" width="600"
                   style="border-collapse:collapse;border-spacing:0;font-family:Roboto,Arial,Helvetica,sans-serif;font-size:14px;font-weight:normal;hyphens:auto;line-height:24px;margin-bottom:0;padding:0;vertical-align:top;width:100%;">

                <!-- Hero Image, Flush : BEGIN -->
                <tr style="font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0; text-align: left; vertical-align: top;">
                    <td class="hero-image" align="center"
                        style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0 0 24px; text-align: center; vertical-align: top; word-wrap: break-word;">
                        <img src="https://comms.dev.travel-dev.com/imgs/system/keys-image.png" class="align-center" width="160"
                             alt="" border="0"
                             style="-ms-interpolation-mode: bicubic; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;">
                    </td>
                </tr>
                <!-- Hero Image, Flush : END -->


                <tr style="font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0; text-align: left; vertical-align: top;">
                    <td class="content"
                        style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0; text-align: center; vertical-align: top; word-wrap: break-word;">
                        <h1 class="align-center"
                            style="color: #262E36; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 24px; font-weight: 700; letter-spacing: -.5px; line-height: 32px; margin: 0 0 8px; margin-top: 0; padding: 0; text-align: center;">
                            Your Two-Factor verification code
                        </h1>
                        <p class="align-center top-main-text"
                           style="color:#4D5A6A;font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 22px;margin-top:0; margin-bottom: 24px; text-align: center;">
                            <span style="display: block;text-align: center">Good on you for keeping your account secure</span>
                            Here is your authentication code
                        </p>
                    </td>
                </tr>

                <!-- Button : BEGIN -->
                <tr style="font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0; text-align: left; vertical-align: top;">
                    <td style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0 20px 0; text-align: center; vertical-align: top; word-wrap: break-word;">
                        <table class="btn" align="center" role="presentation" cellspacing="0" cellpadding="0" border="0"
                               style="border-collapse: collapse; border-spacing: 0; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; margin: 0 auto 24px; padding: 0; text-align: center; vertical-align: top; width: auto;">
                            <tr style="font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0; text-align: left; vertical-align: top;">
                                <td class="button-td button-td-primary btn-brds"
                                    style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; text-align: center; vertical-align: top; word-wrap: break-word;">
                                    <span style="display: block;max-width:156px;border: 1px solid #E0E3EB;background-color:#F5F7F9;border-radius: 8px;margin: 0 auto;padding:8px 16px;color: #262E36;font-size: 24px;line-height: 32px;letter-spacing: 3px;font-weight: 700;">
                                        <?= $code ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- Button : END -->

                <tr style="font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0; text-align: left; vertical-align: top;">
                    <td class="content"
                        style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 24px; padding: 0; text-align: center; vertical-align: top; word-wrap: break-word;">
                        <p class="align-center top-main-text"
                           style="color:#4D5A6A;font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 16px;margin-top:0; margin-bottom: 24px; text-align: center;">
                            <span style="display: block;text-align: center">This code expires in <?= $secondsRemain ?> seconds, but you can generate another by logging in again.</span>

                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- end content-->
<body>
</html>
