<?php

namespace common\models;

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
 *
 * @property Employee $cssCreatedUser
 * @property Cases $cssCs
 * @property Employee $cssUpdatedUser
 */
class CaseSale extends \yii\db\ActiveRecord
{

	public const PASSENGER_MEAL = [
		"AVML - Vegetarian/Hindu" => "AVML - Vegetarian/Hindu",
		"BBML - Baby" => "BBML - Baby",
		"BLML - Bland" => "BLML - Bland",
		"CHML - Child" => "CHML - Child",
		"CNML - Chicken (LY only)" => "CNML - Chicken (LY only)",
		"CHCK - Chicken (AA only)" => "CHCK - Chicken (AA only)",
		"DBML - Diabetic" => "DBML - Diabetic",
		"FPML - Fruit Platter" => "FPML - Fruit Platter",
		"GFML - Gluten intolerant," => "GFML - Gluten intolerant,",
		"HNML - Hindu (non-vegerarian)" => "HNML - Hindu (non-vegerarian)",
		"IVML - Indian Vegetarian (UA only)" => "IVML - Indian Vegetarian (UA only)",
		"JPML - Japanese (LH only)" => "JPML - Japanese (LH only)",
		"KSML - Kosher" => "KSML - Kosher",
		"LCML - Low Calorie" => "LCML - Low Calorie",
		"LFML - Low Fat" => "LFML - Low Fat",
		"LSML - Low Salt" => "LSML - Low Salt",
		"MOML - Moslem" => "MOML - Moslem",
		"NFML - No Fish (LH only)" => "NFML - No Fish (LH only)",
		"NLML - Low Lactose" => "NLML - Low Lactose",
		"OBML - Japanese Obento (UA only)" => "OBML - Japanese Obento (UA only)",
		"RVML - Vegeratian Raw" => "RVML - Vegeratian Raw",
		"SFML - Sea Food" => "SFML - Sea Food",
		"SPML - Special meal, specify ~" => "SPML - Special meal, specify ~",
		"VGML - Vegetarian Vegan" => "VGML - Vegetarian Vegan",
		"VJML - Vegetarian Jain" => "VJML - Vegetarian Jain",
		"VOML - Vagetarian Oriental" => "VOML - Vagetarian Oriental",
		"VLML - Vegetarian Lacto-Ovo" => "VLML - Vegetarian Lacto-Ovo",
		"NOML - No Meal" => "NOML - No Meal",
		"NSML - No Salt/Sodium" => "NSML - No Salt/Sodium",
		"PFML - Peanut Free" => "PFML - Peanut Free"
	];

	public const PASSENGER_WHEELCHAIR = [
		"WCOB - Wheelchair on board" => "WCOB - Wheelchair on board",
		"WCHR - Wheelchair airport departure hall" => "WCHR - Wheelchair airport departure hall",
		"WCHS - Wheelchair Up/Down stairs" => "WCHS - Wheelchair Up/Down stairs",
		"WCHC - Passenger must be carried" => "WCHC - Passenger must be carried"
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
        return new CaseSaleQuery(get_called_class());
    }
}
