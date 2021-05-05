<?php

namespace modules\order\migrations;

use modules\hotel\models\HotelQuoteRoom;
use Yii;
use yii\db\Migration;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class m210405_132759_alter_tbl_hotel_quote_room
 */
class m210405_132759_alter_tbl_hotel_quote_room extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%hotel_quote_room}}', 'hqr_cancellation_policies', $this->json());
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%hotel_quote_room}}');
        $hotelQuoteRooms = HotelQuoteRoom::find()->all();
        $errors = [];
        foreach ($hotelQuoteRooms as $hotelQuoteRoom) {
            $hotelRoom = $hotelQuoteRoom->hqrHotelQuote;

            if ($hotelRoom) {
                $originSearchData = $hotelRoom->hq_origin_search_data;

                if ($originSearchData) {
                    foreach ($originSearchData['rates'] ?? [] as $rate) {
                        if ($rate['key'] === $hotelQuoteRoom->hqr_key && !empty($rate['cancellationPolicies'])) {
                            $hotelQuoteRoom->hqr_cancellation_policies = $rate['cancellationPolicies'];
                            if (!$hotelQuoteRoom->save()) {
                                $errors[] = [
                                    'key' => $rate['key'],
                                    'hotelQuoteRoomId' => $hotelQuoteRoom->hqr_id,
                                    'message' => $hotelQuoteRoom->getErrorSummary(true)[0]
                                ];
                            }
                        }
                    }
                }
            }
        }

        if ($errors) {
            Console::stdout('Several cancellation policies of hotel quote rooms are not formatted');
            Console::moveCursorNextLine();
            Console::stdout(VarDumper::dumpAsString($errors));
        }

        $this->dropColumn('{{%hotel_quote_room}}', 'hqr_cancel_from_dt');
        $this->dropColumn('{{%hotel_quote_room}}', 'hqr_cancel_amount');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%hotel_quote_room}}', 'hqr_cancellation_policies');
        $this->addColumn('{{%hotel_quote_room}}', 'hqr_cancel_amount', $this->decimal(10, 2));
        $this->addColumn('{{%hotel_quote_room}}', 'hqr_cancel_from_dt', $this->dateTime());
    }
}
