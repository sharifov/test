<?php

namespace src\entities\email;

use Yii;
use src\helpers\email\TextConvertingHelper;
use yii\helpers\Html;
use src\model\BaseActiveRecord;
use src\helpers\text\StringHelper;

/**
 * This is the model class for table "email_body".
 *
 * @property int $embd_id
 * @property string|null $embd_email_subject
 * @property string|null $embd_email_body_text
 * @property string|null $embd_email_data
 * @property string|null $embd_hash
 *
 * @property EmailBlob $emailBlob
 * @property Email $email
 */
class EmailBody extends BaseActiveRecord
{
    public function rules(): array
    {
        return [
            ['embd_email_body_text', 'string'],

            ['embd_email_data', 'safe'],

            ['embd_email_subject', 'string', 'max' => 255],

            ['embd_hash', 'string', 'max' => 32],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'embd_id' => 'ID',
            'embd_email_subject' => 'Subject',
            'embd_email_body_text' => 'Body Text',
            'embd_email_data' => 'Email Data',
            'embd_hash' => 'Hash',
        ];
    }

    public static function tableName(): string
    {
        return 'email_body';
    }

    public function getEmailBlob(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailBlob::class, ['embb_body_id' => 'embd_id']);
    }

    public function getEmail(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Email::class, ['e_body_id' => 'embd_id']);
    }

    public function getEmailData()
    {
        return !empty($this->embd_email_data) ? $this->embd_email_data : null;
    }

    public function getBodyHtml(): ?string
    {
        return (!empty($this->emailBlob->embb_email_body_blob)) ? TextConvertingHelper::unCompress($this->emailBlob->embb_email_body_blob) : '';
    }

    public static function getDraftSubject($projectName = '', $userName = ''): string
    {
        return '✈️ ' . $projectName . ' - ' . $userName;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function getReSubject($str = ''): string
    {
        $str = trim($str);
        if (strpos($str, 'Re:', 0) === false && strpos($str, 'Re[', 0) === false) {
            return 'Re:' . $str;
        } else {
            preg_match_all('/Re\[([\d]+)\]:/i', $str, $m);
            if ($m && is_array($m) && isset($m[0], $m[1])) {
                if (count($m[0]) > 1) {
                    $cnt = 0;
                    foreach ($m[0] as $repl) {
                        if (isset($m[0][$cnt + 1])) {
                            $from = '/' . preg_quote($repl, '/') . '/';
                            $str = preg_replace($from, '', $str, 1);
                            $str = preg_replace("/(.*?)$repl/i", '', $str, 1);
                        }
                        $cnt++;
                    }
                }
            }
            $str = preg_replace("/(.*?)Re\[([\d]+)\]:/i", 'Re[$2]: ', $str, 1);
            if (mb_substr($str, 0, 3, 'utf-8') === 'Re:') {
                $str = preg_replace("/(Re:)/i", 'Re[1]:', $str, 1);
            } elseif (preg_match('/Re\[([\d]+)\]:/i', $str, $matches)) {
                if (isset($matches[0], $matches[1])) {
                    $newVal = $matches[1] + 1;
                    $str = preg_replace('/Re\[([\d]+)\]:/i', 'Re[' . $newVal . ']:', $str, 1);
                }
            }
        }
        $str = preg_replace("/ {2,}/", " ", $str);

        return trim($str);
    }

    public static function getReBodyHtml($emailTo, $userName, $bodyHtml): string
    {
        return '<!DOCTYPE html><html><head><title>Redactor</title><meta charset="UTF-8"/><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /></head><body><p>Hi ' . Html::encode($emailTo) . '!</p><blockquote>' . nl2br(StringHelper::stripHtmlTags($bodyHtml)) . '</blockquote><p>The best regards, <br>' . Html::encode($userName) . '</p></body></html>';
    }
}
