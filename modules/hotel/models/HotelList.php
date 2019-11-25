<?php

namespace modules\hotel\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "hotel_list".
 *
 * @property int $hl_id
 * @property int|null $hl_code
 * @property string|null $hl_hash_key
 * @property string $hl_name
 * @property string|null $hl_star
 * @property string|null $hl_category_name
 * @property string|null $hl_destination_code
 * @property string|null $hl_destination_name
 * @property string|null $hl_zone_name
 * @property int|null $hl_zone_code
 * @property string|null $hl_country_code
 * @property string|null $hl_state_code
 * @property string|null $hl_description
 * @property string|null $hl_address
 * @property string|null $hl_postal_code
 * @property string|null $hl_city
 * @property string|null $hl_email
 * @property string|null $hl_web
 * @property string|null $hl_phone_list
 * @property string|null $hl_image_list
 * @property string|null $hl_image_base_url
 * @property string|null $hl_board_codes
 * @property string|null $hl_segment_codes
 * @property float|null $hl_latitude
 * @property float|null $hl_longitude
 * @property int|null $hl_ranking
 * @property string|null $hl_service_type
 * @property string|null $hl_last_update
 * @property string|null $hl_created_dt
 * @property string|null $hl_updated_dt
 *
 * @property HotelQuote[] $hotelQuotes
 */
class HotelList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotel_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hl_code', 'hl_zone_code', 'hl_ranking'], 'integer'],
            [['hl_name'], 'required'],
            [['hl_description', 'hl_address'], 'string'],
            [['hl_phone_list', 'hl_image_list', 'hl_board_codes', 'hl_segment_codes', 'hl_last_update', 'hl_created_dt', 'hl_updated_dt'], 'safe'],
            [['hl_latitude', 'hl_longitude'], 'number'],
            [['hl_hash_key'], 'string', 'max' => 32],
            [['hl_name', 'hl_destination_name', 'hl_zone_name', 'hl_city', 'hl_web'], 'string', 'max' => 150],
            [['hl_star'], 'string', 'max' => 2],
            [['hl_category_name'], 'string', 'max' => 40],
            [['hl_destination_code', 'hl_country_code', 'hl_state_code'], 'string', 'max' => 5],
            [['hl_postal_code'], 'string', 'max' => 10],
            [['hl_email', 'hl_image_base_url'], 'string', 'max' => 160],
            [['hl_service_type'], 'string', 'max' => 30],
            [['hl_code'], 'unique'],
            [['hl_hash_key'], 'unique'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['hl_created_dt', 'hl_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['hl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hl_id' => 'ID',
            'hl_code' => 'Code',
            'hl_hash_key' => 'Hash Key',
            'hl_name' => 'Name',
            'hl_star' => 'Star',
            'hl_category_name' => 'Category Name',
            'hl_destination_code' => 'Destination Code',
            'hl_destination_name' => 'Destination Name',
            'hl_zone_name' => 'Zone Name',
            'hl_zone_code' => 'Zone Code',
            'hl_country_code' => 'Country Code',
            'hl_state_code' => 'State Code',
            'hl_description' => 'Description',
            'hl_address' => 'Address',
            'hl_postal_code' => 'Postal Code',
            'hl_city' => 'City',
            'hl_email' => 'Email',
            'hl_web' => 'Web',
            'hl_phone_list' => 'Phone List',
            'hl_image_list' => 'Image List',
            'hl_image_base_url' => 'Image Base Url',
            'hl_board_codes' => 'Board Codes',
            'hl_segment_codes' => 'Segment Codes',
            'hl_latitude' => 'Latitude',
            'hl_longitude' => 'Longitude',
            'hl_ranking' => 'Ranking',
            'hl_service_type' => 'Service Type',
            'hl_last_update' => 'Last Update',
            'hl_created_dt' => 'Created Dt',
            'hl_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotelQuotes()
    {
        return $this->hasMany(HotelQuote::class, ['hq_hotel_list_id' => 'hl_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\query\HotelListQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\hotel\models\query\HotelListQuery(get_called_class());
    }
}
