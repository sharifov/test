<?php

use yii\db\Migration;

/**
 * Class m201221_141845_add_languages_tbl_language
 */
class m201221_141845_add_languages_tbl_language extends Migration
{

    public $langs = [
        [
            'language_id' => 'en-AU',
            'language'  => 'en',
            'country'  => 'au',
            'name'  => 'English (Australia)',
            'name_ascii' => 'English (Australia)',
            'status' => 0
        ],
        [
            'language_id' => 'en-CA',
            'language'  => 'en',
            'country'  => 'ca',
            'name'  => 'English (Canada)',
            'name_ascii' => 'English (Canada)',
            'status' => 0
        ],
        [
            'language_id' => 'es-AR',
            'language'  => 'es',
            'country'  => 'ar',
            'name'  => 'Español (Argentina)',
            'name_ascii' => 'Spanish (Argentina)',
            'status' => 0
        ],
        [
            'language_id' => 'es-CO',
            'language'  => 'es',
            'country'  => 'co',
            'name'  => 'Español (Colombia)',
            'name_ascii' => 'Spanish (Colombia)',
            'status' => 0
        ],
        [
            'language_id' => 'es-MX',
            'language'  => 'es',
            'country'  => 'mx',
            'name'  => 'Español (Mexicano)',
            'name_ascii' => 'Spanish (Mexico)',
            'status' => 0
        ],
        [
            'language_id' => 'es-US',
            'language'  => 'es',
            'country'  => 'us',
            'name'  => 'Español (USA)',
            'name_ascii' => 'Spanish (USA)',
            'status' => 0
        ]
    ];



    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->langs as $lang) {
            $this->insert('{{%language}}', $lang);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->langs as $lang) {
            $this->delete('{{%language}}', ['language_id' => $lang['language_id']]);
        }
    }


}
