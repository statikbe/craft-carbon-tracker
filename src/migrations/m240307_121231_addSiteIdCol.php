<?php

namespace statikbe\carbontracker\migrations;

use Craft;
use craft\db\Migration;
use statikbe\carbontracker\records\SiteStatisticsRecord;

/**
 * m240307_121231_addSiteIdCol migration.
 */
class m240307_121231_addSiteIdCol extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->addColumn(
            SiteStatisticsRecord::tableName(),
            'siteId',
            $this->integer()->notNull()->after('entryId')
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m240307_121231_addSiteIdCol cannot be reverted.\n";
        return false;
    }
}
