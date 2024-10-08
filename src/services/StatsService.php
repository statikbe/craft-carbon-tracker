<?php

namespace statikbe\carbontracker\services;

use craft\base\Component;
use craft\elements\Entry;
use craft\helpers\DateTimeHelper;
use statikbe\carbontracker\CarbonTracker;
use statikbe\carbontracker\models\SiteStatisticsModel;
use statikbe\carbontracker\records\SiteStatisticsRecord;

class StatsService extends Component
{
    public function getDataForEntry(Entry $entry): SiteStatisticsModel|bool
    {
        /** @var SiteStatisticsRecord|null $record */
        $record = SiteStatisticsRecord::find()->where(['entryId' => $entry->id])->andWhere(['siteId' => $entry->siteId])->orderBy('id DESC')->one();

        // INFO: if we don't find a record, we'll try to find one without the siteId
        if ($record === null) {
            /** @var SiteStatisticsRecord|null $record */
            $record = SiteStatisticsRecord::find()->where(['entryId' => $entry->id])->orderBy('id DESC')->one();
            if ($record === null) {
                return false;
            }
        }

        $model = new SiteStatisticsModel();
        $model->setAttributes($record->toArray());
        return $model;
    }

    public function upsertDataForEntry(Entry $entry): void
    {
        // Only update data once every 24 hours
        $date = DateTimeHelper::currentUTCDateTime()->modify('-1 day');
        $record = SiteStatisticsRecord::find()
            ->where(['=', 'entryId', $entry->id])
            ->andWhere(['=', 'siteId', $entry->siteId])
            ->andWhere(['>=', 'dateCreated', $date->format('c')])
            ->one();

        if ($record) {
            //We have a record from the past 24 hours, don't try and update now.
            \Craft::info("Skipping carbon update for {$entry->title}, we already have stats that are less than 24 hours old.", CarbonTracker::class);
            return;
        }

        $stats = CarbonTracker::getInstance()->api->getSite($entry);
        if ($stats) {
            $record = new SiteStatisticsRecord();
            $record->entryId = $stats->entryId;
            $record->siteId = $stats->siteId;
            $record->url = $stats->url;
            $record->green = $stats->green;
            $record->cleanerThan = $stats->cleanerThan;
            $record->rating = $stats->rating;
            $record->save();
        }
    }
}
