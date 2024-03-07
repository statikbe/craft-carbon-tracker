<?php

namespace statikbe\carbontracker\migrations;

use Craft;
use craft\db\Migration;
use statikbe\carbontracker\records\SiteStatisticsRecord;

class Install extends Migration
{
    public function safeUp(): bool
    {
        $this->createTable(
            SiteStatisticsRecord::tableName(),
            [
                'id' => $this->primaryKey(),
                'entryId' => $this->integer()->notNull(),
                'siteId' => $this->integer()->notNull(),
                'url' => $this->string()->notNull(),
                'green' => $this->boolean()->defaultValue(0),
                'bytes' => $this->integer(),
                'cleanerThan' => $this->float()->defaultValue(0.0),
                'rating' => $this->string(2),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'dateDeleted' => $this->dateTime()->null(),
                'uid' => $this->uid(),
            ]
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(),
            SiteStatisticsRecord::tableName(),
            'entryId',
            '{{%elements}}',
            'id',
            'CASCADE'
        );

        Craft::$app->getDb()->schema->refresh();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists(SiteStatisticsRecord::tableName());
        return true;
    }
}
