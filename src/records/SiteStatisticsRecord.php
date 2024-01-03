<?php

namespace statikbe\carbontracker\records;

use craft\db\ActiveRecord;

/**
 * @property int $entryId entryId
 * @property string $url url
 * @property boolean $green green
 * @property float $cleanerThan cleanerThan
 * @property string $rating rating
 */
class SiteStatisticsRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%carbontracker_stats}}';
    }
}
