<?php

namespace statikbe\carbontracker\records;

use craft\db\ActiveRecord;

class SiteStatisticsRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%carbontracker_stats}}';
    }
}
