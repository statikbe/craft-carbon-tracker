<?php

namespace statikbe\carbontracker;

use Craft;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\DefineHtmlEvent;
use craft\events\ModelEvent;
use craft\helpers\ElementHelper;
use craft\helpers\Queue;
use statikbe\carbontracker\jobs\CarbonStatsJob;
use statikbe\carbontracker\models\Settings;
use statikbe\carbontracker\services\ApiService;
use statikbe\carbontracker\services\StatsService;
use yii\base\Exception;
use yii\console\Application as ConsoleApplication;

/**
 * carbon-tracker plugin
 *
 * @method static CarbonTracker getInstance()
 * @method Settings getSettings()
 * @property ApiService $api
 * @property StatsService $stats
 * @author Statik.be <support@statik.be>
 * @copyright Statik.be
 * @license MIT
 */
class CarbonTracker extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    /**
     * @return non-empty-array<array>
     */
    public static function config(): array
    {
        return [
            'components' => [
                'api' => ApiService::class,
                'stats' => StatsService::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'statikbe\carbontracker\console\controllers';
        }

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('carbon-tracker/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        Event::on(Entry::class, Entry::EVENT_AFTER_SAVE,
            function (ModelEvent $event) {
                /** @var Entry $entry */
                $entry = $event->sender;
                if (!ElementHelper::isDraftOrRevision($entry) && $entry->getUrl()) {
                    Queue::push(new CarbonStatsJob([
                        'entryId' => $entry->id,
                        'title' => $entry->title,
                    ]), 2000, 0, 1);
                }
            });

        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_SIDEBAR_HTML,
            function (DefineHtmlEvent $event) {
                /** @var Entry $entry */
                $entry = $event->sender;
                try {
                    if (!ElementHelper::isDraftOrRevision($entry)) {
                        $stats = $this->stats->getDataForEntry($entry);
                        if ($stats) {
                            $data = Craft::$app->getView()->renderTemplate(
                                'carbon-tracker/_cp/_sidebar/_stats.twig',
                                [
                                    'entry' => $entry,
                                    'stats' => $stats,
                                    'settings' => $this->getSettings(),
                                ]
                            );
                            $event->html .= $data;
                        }
                    }
                } catch (Exception $e) {
                    Craft::error($e->getMessage(), __CLASS__);
                }
            }
        );
    }
}
