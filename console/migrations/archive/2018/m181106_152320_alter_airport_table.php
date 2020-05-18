<?php
use yii\db\Migration;

/**
 * Class m181106_152320_alter_airport_table
 */
class m181106_152320_alter_airport_table extends Migration
{

    /**
     *
     * {@inheritdoc}
     *
     */
    public function safeUp()
    {
        $airports = \common\models\Airport::find()->orderBy([
            'iata' => SORT_ASC
        ])->all();
        $iata = null;
        foreach ($airports as $airport) {
            if ($iata == $airport->iata) {
                $airport->delete();
            }
            $iata = $airport->iata;
        }

        $this->dropColumn('{{%airports}}', 'state');
        $this->dropColumn('{{%airports}}', 'iaco');
        $this->alterColumn('{{%airports}}', 'id', $this->integer());
        $this->dropPrimaryKey('PRIMARY', '{{%airports}}');
        $this->addPrimaryKey('PRIMARY-iata', '{{%airports}}', 'iata');
        $this->dropColumn('{{%airports}}', 'id');
        $this->alterColumn('{{%airports}}', 'timezone', $this->string(40));
        $this->alterColumn('{{%airports}}', 'dst', $this->smallInteger(6));
        $this->alterColumn('{{%airports}}', 'city', $this->string(40));
        $this->alterColumn('{{%airports}}', 'country', $this->string(40));
        $this->alterColumn('{{%airports}}', 'latitude', $this->decimal(10, 6));
        $this->alterColumn('{{%airports}}', 'longitude', $this->decimal(10, 6));
        $this->alterColumn('{{%airports}}', 'countryId', $this->string(2));
        $this->dropIndex('idx-airports-iata','{{%airports}}');
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('PRIMARY-iata', '{{%airports}}');
        $this->addColumn('{{%airports}}', 'id', $this->primaryKey());
        $this->addColumn('{{%airports}}', 'state', $this->string(20));
        $this->addColumn('{{%airports}}', 'iaco', $this->string(3));
        $this->createIndex('idx-airports-iata', '{{%airports}}', 'iata');
    }
}
