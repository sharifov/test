<?php

namespace common\models;

use common\models\query\CaseSaleQuery;
use sales\entities\cases\Cases;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "case_sale".
 *
 * @property int $css_cs_id
 * @property int $css_sale_id
 * @property string $css_sale_book_id
 * @property string $css_sale_pnr
 * @property int $css_sale_pax
 * @property string $css_sale_created_dt
 * @property array $css_sale_data
 * @property int $css_created_user_id
 * @property int $css_updated_user_id
 * @property string $css_created_dt
 * @property string $css_updated_dt
 * @property int $css_need_sync_bo
 * @property array $css_sale_data_updated
 * @property float|null $css_charged
 * @property float|null $css_profit
 * @property string|null $css_out_departure_airport
 * @property string|null $css_out_arrival_airport
 * @property string|null $css_in_departure_airport
 * @property string|null $css_in_arrival_airport
 * @property string|null $css_charge_type
 * @property string|null $css_out_date
 * @property string|null $css_in_date
 *
 * @property Employee $cssCreatedUser
 * @property Cases $cssCs
 * @property Employee $cssUpdatedUser
 */
class CaseSale extends \yii\db\ActiveRecord
{

	public const PASSENGER_MEAL = [
		"AVML" => "AVML - Vegetarian/Hindu",
		"BBML" => "BBML - Baby",
		"BLML" => "BLML - Bland",
		"CHML" => "CHML - Child",
		"CNML" => "CNML - Chicken (LY only)",
		"CHCK" => "CHCK - Chicken (AA only)",
		"DBML" => "DBML - Diabetic",
		"FPML" => "FPML - Fruit Platter",
		"GFML" => "GFML - Gluten intolerant,",
		"HNML" => "HNML - Hindu (non-vegerarian)",
		"IVML" => "IVML - Indian Vegetarian (UA only)",
		"JPML" => "JPML - Japanese (LH only)",
		"KSML" => "KSML - Kosher",
		"LCML" => "LCML - Low Calorie",
		"LFML" => "LFML - Low Fat",
		"LSML" => "LSML - Low Salt",
		"MOML" => "MOML - Moslem",
		"NFML" => "NFML - No Fish (LH only)",
		"NLML" => "NLML - Low Lactose",
		"OBML" => "OBML - Japanese Obento (UA only)",
		"RVML" => "RVML - Vegeratian Raw",
		"SFML" => "SFML - Sea Food",
		"SPML" => "SPML - Special meal, specify ~",
		"VGML" => "VGML - Vegetarian Vegan",
		"VJML" => "VJML - Vegetarian Jain",
		"VOML" => "VOML - Vagetarian Oriental",
		"VLML" => "VLML - Vegetarian Lacto-Ovo",
		"NOML" => "NOML - No Meal",
		"NSML" => "NSML - No Salt/Sodium",
		"PFML" => "PFML - Peanut Free"
	];

	public const PASSENGER_WHEELCHAIR = [
		"WCOB" => "WCOB - Wheelchair on board",
		"WCHR" => "WCHR - Wheelchair airport departure hall",
		"WCHS" => "WCHS - Wheelchair Up/Down stairs",
		"WCHC" => "WCHC - Passenger must be carried"
	];

	public const PASSENGER_TYPE_BIRTH_DATE_RANGE = [
		'INF' => [
			'min' => 0,
			'max' => 1
		],
		'CHD' => [
			'min' => 2,
			'max' => 11
		],
		'ADT' => [
			'min' => 12,
			'max' => 130
		]
	];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'case_sale';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['css_cs_id', 'css_sale_id', 'css_sale_data'], 'required'],
            [['css_cs_id', 'css_sale_id', 'css_sale_pax', 'css_created_user_id', 'css_updated_user_id'], 'integer'],
            [['css_sale_created_dt', 'css_sale_data', 'css_created_dt', 'css_updated_dt'], 'safe'],
            [['css_sale_book_id', 'css_sale_pnr'], 'string', 'max' => 8],
            [['css_cs_id', 'css_sale_id'], 'unique', 'targetAttribute' => ['css_cs_id', 'css_sale_id']],
            [['css_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['css_created_user_id' => 'id']],
            [['css_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['css_updated_user_id' => 'id']],
            [['css_cs_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['css_cs_id' => 'cs_id']],
            [['css_profit', 'css_charged'], 'number'],
            [['css_out_departure_airport', 'css_out_arrival_airport', 'css_in_departure_airport', 'css_in_arrival_airport'], 'string', 'max' => 3],
            [['css_out_date', 'css_in_date'], 'string'],
            [['css_out_date', 'css_in_date'],  'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['css_charge_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'css_cs_id' => 'Cs ID',
            'css_sale_id' => 'Sale ID',
            'css_sale_book_id' => 'Sale Book ID',
            'css_sale_pnr' => 'Sale Pnr',
            'css_sale_pax' => 'Sale Pax',
            'css_sale_created_dt' => 'Sale Created Dt',
            'css_sale_data' => 'Sale Data',
            'css_created_user_id' => 'Created User ID',
            'css_created_dt' => 'Created Dt',
            'css_updated_user_id' => 'Updated User ID',
            'css_updated_dt' => 'Updated Dt',
            'css_charged' => 'Charged',
            'css_profit' => 'Profit',
            'css_out_departure_airport' => 'Out departure airport',
            'css_out_arrival_airport' => 'Out arrival airport',
            'css_in_departure_airport' => 'In departure airport',
            'css_in_arrival_airport' => 'In arrival airport',
            'css_charge_type' => 'Charge type',
            'css_out_date' => 'Out date',
            'css_in_date' => 'In date',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['css_created_dt', 'css_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['css_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'css_created_user_id',
                'updatedByAttribute' => 'css_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCssCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'css_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCssCs()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'css_cs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCssUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'css_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return CaseSaleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CaseSaleQuery(static::class);
    }
}
