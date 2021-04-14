<?php

namespace sales\forms\cases;

use common\models\Language;
use sales\entities\cases\Cases;
use yii\base\Model;

/**
 * Class CasesClientUpdateForm
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $caseGid
 * @property string|null $locale
 * @property string|null $marketingCountry
 */
class CasesClientUpdateForm extends Model
{
    public $first_name;
    public $last_name;
    public $middle_name;
    public $locale;
    public $marketingCountry;

    public $caseGid;

    /**
     * CasesChangeStatusForm constructor.
     * @param Cases $case
     * @param array $config
     */
    public function __construct(Cases $case, $config = [])
    {
        parent::__construct($config);
        $this->caseGid = $case->cs_gid;
        if ($client = $case->client) {
            $this->first_name = $client->first_name;
            $this->last_name = $client->last_name;
            $this->middle_name = $client->middle_name;
            $this->locale = $client->cl_locale;
            $this->marketingCountry = $client->cl_marketing_country;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['first_name', 'required'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'min' => 3, 'max' => 100],
            [['first_name', 'last_name', 'middle_name'], 'match', 'pattern' => "/^[a-z-\s\']+$/i"],
            [['first_name', 'last_name', 'middle_name', 'locale', 'marketingCountry'], 'filter', 'filter' => 'trim'],

            ['locale', 'string', 'max' => 5],
            [['locale'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['locale' => 'language_id']],

            ['marketingCountry', 'string', 'max' => 2],
            ['marketingCountry', 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'middle_name' => 'Middle name',
            'locale' => 'Locale',
            'marketingCountry' => 'Market country'
        ];
    }
}
