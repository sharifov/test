<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project_email_templates".
 *
 * @property int $id
 * @property int $project_id
 * @property string $type
 * @property string $subject
 * @property string $template
 * @property string $layout_path
 * @property string $created
 * @property string $updated
 *
 * @property Project $project
 */
class ProjectEmailTemplate extends \yii\db\ActiveRecord
{
    const
        TYPE_EMAIL_OFFER = '_offer',
        TYPE_EMAIL_TICKET = '_email_ticket',
        TYPE_PDF_TICKET = '_pdf_ticket',
        TYPE_PDF_INVOICE = '_pdf_invoice';

    const
        TYPE_SALES_CONTACT_SHARE = '_email_sales_contact_share',
        TYPE_SALES_ADDITIONAL_INFO = '_email_sales_additional_info',
        TYPE_SALES_DID_YOU_GET_MY_QUOTE = '_email_sales_did_you_get_my_quote',
        TYPE_SALES_FREE_FORM = '_email_sales_free_form',
        TYPE_SALES_TESTIMONIALS = '_email_sales_testimonials';


    public static function getTypesForSellers()
    {
        $types = [];
        foreach (self::getTypes() as $key => $type) {
            if (strpos($key, '_email_sales') !== false) {
                $types[$key] = $type;
            }
        }
        return $types;
    }

    public static function getTypes($type = null)
    {
        $mapping = [
            self::TYPE_EMAIL_OFFER => 'Email Offer',
            self::TYPE_EMAIL_TICKET => 'Email Ticket',
            self::TYPE_PDF_TICKET => 'PDF Ticket',
            self::TYPE_PDF_INVOICE => 'PDF Invoice',
            self::TYPE_SALES_CONTACT_SHARE => 'For Sellers - Contact Share',
            self::TYPE_SALES_ADDITIONAL_INFO => 'For Sellers - Additional Info',
            self::TYPE_SALES_DID_YOU_GET_MY_QUOTE => 'For Sellers - Did you get my quote?',
            self::TYPE_SALES_FREE_FORM => 'For Sellers - Free form',
            self::TYPE_SALES_TESTIMONIALS => 'For Sellers - Testimonials'
        ];

        if ($type === null) {
            return $mapping;
        } else {
            return isset($mapping[$type])
                ? $mapping[$type] : null;
        }
    }

    public static function getEmailsLayout()
    {
        $layouts = [];
        $dir = dirname(Yii::getAlias('@app')) . '/common/views/mail/layouts';
        $items = scandir($dir);
        foreach ($items as $item) {
            if (in_array($item, ['.', '..'])) {
                continue;
            }
            if (is_dir(sprintf('%s/%s', $dir, $item))) {
                foreach (scandir(sprintf('%s/%s', $dir, $item)) as $layout) {
                    if (in_array($layout, ['.', '..'])) {
                        continue;
                    }
                    $layouts[sprintf('%s/%s/%s', $dir, $item, $layout)] = sprintf('%s/%s', $item, explode('.', $layout)[0]);
                }
            }
        }
        return $layouts;
    }

    public static function getMessageBody($template, $params = [])
    {
        $placeholders = [];
        foreach ((array)$params as $name => $value) {
            $placeholders['{' . $name . '}'] = $value;
        }

        return ($placeholders === []) ? $template : strtr($template, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_email_templates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id'], 'integer'],
            [['type', 'subject', 'template'], 'required'],
            [['subject', 'template'], 'trim'],
            [['template'], 'string'],
            [['created', 'updated'], 'safe'],
            [['type', 'subject', 'layout_path'], 'string', 'max' => 255],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'type' => 'Type',
            'subject' => 'Subject',
            'template' => 'Template',
            'layout_path' => 'Layout Path',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    public function afterValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        $this->template = urlencode($this->template);
        $this->subject = urlencode($this->subject);

        parent::afterValidate();
    }

    public function afterFind()
    {
        $this->template = urldecode(urldecode($this->template));
        $this->subject = urldecode(urldecode($this->subject));

        parent::afterFind();
    }
}
