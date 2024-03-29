<?php

namespace statikbe\carbontracker\models;

use craft\base\Model;

/**
 * carbon-tracker settings
 */
class SiteStatisticsModel extends Model
{
    public int $entryId;
    public int $siteId;
    public string $url = "https://";
    public bool $green = false;
    public int $bytes = 0;
    public float $cleanerThan = 0.0;
    public string $rating = "F";
    public \DateTime $dateUpdated;

    /**
     * @return non-empty-array<array>
     */
    public function rules(): array
    {
        return [
            [['entryId', 'siteId', 'url', 'green', 'bytes', 'cleanerThan', 'rating', 'dateUpdated'], 'required'],
            [['entryId', 'siteId', 'url', 'green', 'bytes', 'cleanerThan', 'rating', 'dateUpdated'], 'safe'],
        ];
    }
}
