<?php

namespace modules\attraction\models;

/**
 * This is the model class for table "attraction_quote_options".
 *
 * @property int $atqo_id
 * @property int $atqo_attraction_quote_id
 * @property string|null $atqo_answered_value
 * @property string|null $atqo_label
 * @property int|null $atqo_is_answered
 * @property string|null $atqo_answer_formatted_text
 *
 * @property AttractionQuote $atqoAttractionQuote
 */
class AttractionQuoteOptions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attraction_quote_options';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atqo_attraction_quote_id'], 'required'],
            [['atqo_attraction_quote_id', 'atqo_is_answered'], 'integer'],
            [['atqo_answered_value', 'atqo_label'], 'string', 'max' => 40],
            [['atqo_answer_formatted_text'], 'string', 'max' => 255],
            [['atqo_attraction_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttractionQuote::class, 'targetAttribute' => ['atqo_attraction_quote_id' => 'atnq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atqo_id' => 'ID',
            'atqo_attraction_quote_id' => 'Attraction Quote ID',
            'atqo_answered_value' => 'Answered Value',
            'atqo_label' => 'Label',
            'atqo_is_answered' => 'Is Answered',
            'atqo_answer_formatted_text' => 'Answer',
        ];
    }

    /**
     * Gets query for [[AtqoAttractionQuote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtqoAttractionQuote()
    {
        return $this->hasOne(AttractionQuote::class, ['atnq_id' => 'atqo_attraction_quote_id']);
    }
}
