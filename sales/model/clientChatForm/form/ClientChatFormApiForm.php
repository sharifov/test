<?php

namespace sales\model\clientChatForm\form;

use common\models\Language;
use sales\model\clientChatForm\entity\ClientChatForm;
use yii\base\Model;

/**
 * Class ClientChatFormApiForm
 *
 * @property string|null $form_key
 * @property string|null $language_id
 * @property int|null $cache
 */
class ClientChatFormApiForm extends Model
{
    public $form_key;
    public $language_id;
    public $cache = 1;

    public function rules(): array
    {
        return [
            [['form_key', 'language_id'], 'required'],
            [['form_key', 'language_id'], 'trim'],

            ['form_key', 'string', 'max' => 100],
            ['form_key', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatForm::class, 'targetAttribute' => ['form_key' => 'ccf_key']],

            ['language_id', 'string', 'max' => 5],
            ['language_id', 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['language_id' => 'language_id']],

            ['cache', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['cache', 'integer'],
            ['cache', 'in', 'range' => [0,1], 'skipOnError' => true],
            ['cache', 'default', 'value' => 1],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
