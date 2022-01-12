<?php

use common\models\ClientPhone;
use src\helpers\ErrorsToStringHelper;
use src\model\contactPhoneList\entity\ContactPhoneList;
use src\model\contactPhoneList\service\PhoneNumberService;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m211223_122113_clean_contact_phone_list
 */
class m211223_122113_clean_contact_phone_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $time_start = microtime(true);

        $query = ContactPhoneList::find()
            ->orWhere(['LIKE', 'cpl_phone_number', '++'])
            ->orWhere(['LIKE', 'cpl_phone_number', ' '])
            ->orWhere(['LIKE', 'cpl_phone_number', '-']);

        $count = $query->count();
        $processed = $duplicates = 0;
        $errors = $warnings = [];

        Console::startProgress(0, $count);

        foreach ($query->all() as $contactPhoneList) {
            $phoneNumberService = new PhoneNumberService($contactPhoneList->cpl_phone_number);

            try {
                $isDuplicate = ContactPhoneList::find()
                    ->orWhere(['cpl_phone_number' => $phoneNumberService->getCleanedPhoneNumber()])
                    ->orWhere(['cpl_uid' => $phoneNumberService->getUid()])
                    ->limit(1)
                    ->exists();

                if ($isDuplicate) {
                    $contactPhoneList->delete();
                    $duplicates++;
                    continue;
                }

                ClientPhone::updateAll(
                    ['cp_cpl_uid' => null],
                    ['cp_cpl_uid' => $contactPhoneList->cpl_uid]
                );

                $contactPhoneList->cpl_phone_number = $phoneNumberService->getCleanedPhoneNumber();
                $contactPhoneList->cpl_uid = $phoneNumberService->getUid();

                if (!$contactPhoneList->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($contactPhoneList));
                }

                ContactPhoneList::updateAll(
                    [
                        'cpl_phone_number' => $phoneNumberService->getCleanedPhoneNumber(),
                        'cpl_uid' => $phoneNumberService->getUid()
                    ],
                    ['cpl_id' => $contactPhoneList->cpl_id]
                );

                $processed++;
                Console::updateProgress($processed, $count);
            } catch (\RuntimeException | \DomainException $throwable) {
                $warnings[] = [
                    'message' => $throwable->getMessage(),
                    'phone' => $contactPhoneList->cpl_phone_number,
                    'cleanedPhone' => $phoneNumberService->getCleanedPhoneNumber(),
                ];
            } catch (\Throwable $throwable) {
                $errors[] = [
                    'message' => $throwable->getMessage(),
                    'phone' => $contactPhoneList->cpl_phone_number,
                    'cleanedPhone' => $phoneNumberService->getCleanedPhoneNumber(),
                ];
            }
        }

        Console::endProgress(false);

        if ($warnings) {
            \Yii::info($warnings, 'info\OneTimeController:actionCleanContactPhoneList:Warnings');
            echo Console::renderColoredString('%r --- Warnings count(' . count($warnings) . '). Please see logs. %n'), PHP_EOL;
        }
        if ($errors) {
            \Yii::info($errors, 'info\OneTimeController:actionCleanContactPhoneList:Errors');
            echo Console::renderColoredString('%r --- Errors count(' . count($errors) . '). Please see logs. %n'), PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Processed: %w[' . $processed . '/' . $count . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Duplicates: %w[' . $duplicates . '] %g %n'), PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211223_122113_clean_contact_phone_list cannot be reverted.\n";
    }
}
