<?php

namespace statikbe\carbontracker\jobs;

use craft\elements\Entry;
use craft\queue\BaseJob;
use statikbe\carbontracker\CarbonTracker;

class CarbonStatsJob extends BaseJob
{
    public int $entryId;

    public string $title;

    /**
     * @inheritDoc
     */
    protected function defaultDescription(): string
    {
        return \Craft::t('app', 'Updating Carbon score for {title}', [
            'title' => $this->title,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function execute($queue): void
    {
        $entry = Entry::findOne(['id' => $this->entryId, 'status' => null]);
        CarbonTracker::getInstance()->stats->upsertDataForEntry($entry);
    }
}
