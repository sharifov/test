<?php

namespace sales\services\client;

use common\models\Client;
use common\models\Project;
use yii\base\Model;

/**
 * Class ClientCreateForm
 * @property string $firstName
 * @property string $middleName
 * @property string $lastName
 * @property string $uuid
 * @property string $rcId
 * @property int|null $projectId
 * @property int|null $typeCreate
 * @property int|string $ip
 * @property string|null $locale
 * @property string|null $marketingCountry
 */
class ClientCreateForm extends Model
{

    public $id;
    public $firstName;
    public $middleName;
    public $lastName;
    public $uuid;
    public $rcId;
    public $projectId;
    public $typeCreate;
    public $ip;
    public $locale;
    public $marketingCountry;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            ['firstName', 'required'],
            ['id', 'integer'],
            [['firstName', 'middleName', 'lastName'], 'string', 'max' => 100],
            [['firstName', 'middleName', 'lastName'], 'match', 'pattern' => "/^[a-z-\s\']+$/i"],
            [['firstName', 'middleName', 'lastName', 'marketingCountry'], 'filter', 'filter' => 'trim'],
            ['uuid', 'string', 'max' => 36],
            ['rcId', 'string', 'max' => 50],

            ['projectId', 'default', 'value' => null],
            ['projectId', 'integer'],
            ['projectId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['projectId', 'exist', 'skipOnError'  => true, 'targetClass' => Project::class, 'targetAttribute' => ['projectId' => 'id']],

            ['typeCreate', 'integer'],
            ['typeCreate', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['typeCreate', 'in', 'range' => array_keys(Client::TYPE_CREATE_LIST)],

            ['ip', 'default', 'value' => null],
            ['ip', 'string', 'max' => 39],

            ['locale', 'default', 'value' => null],
            ['locale', 'string', 'max' => 5],

            ['locale', 'string', 'max' => 5],
            ['marketingCountry', 'string', 'max' => 10],
            ['marketingCountry', 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],
        ];
    }

    public static function createWidthDefaultName(): self
    {
        $form = new self();
        $form->firstName = 'ClientName';
        return $form;
    }
}
