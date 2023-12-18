<?php

namespace statikbe\carbontracker\models;

use Craft;
use craft\base\Model;

/**
 * carbon-tracker settings
 */
class SiteStatisticsModel extends Model
{
    public int $entryId;
    public string $url = "https://";
    public bool $green = false;
    public int $bytes = 0;
    public float $cleanerThan = 0.0;
    public string $rating = "F";
    public $dateUpdated;

    public function rules(): array
    {
        return [
            [['entryId','url', 'green', 'bytes', 'cleanerThan', 'rating', 'dateUpdated'], 'required'],
            [['entryId','url', 'green', 'bytes', 'cleanerThan', 'rating', 'dateUpdated'], 'safe'],
        ];
    }

}
