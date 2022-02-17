<?php

declare(strict_types=1);
/**
 * Env Field plugin for Craft CMS 3.x
 *
 * A custom plain text field, but with environment variable support
 *
 * @link      https://justenough.co
 * @copyright Copyright (c) 2022 Just Enough Consulting
 */

namespace justenough\envfield;

use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use justenough\envfield\fields\EnvFieldType;
use justenough\envfield\models\Settings;

use yii\base\Event;

/**
 * Class EnvField
 *
 * @author    Just Enough Consulting
 * @package   EnvField
 * @since     1.0.0
 *
 */
class EnvField extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var EnvField
     */
    public static $plugin;

    public $hasCpSettings = false;
    public $hasCpSection = false;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = EnvFieldType::class;
            }
        );
    }

    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * Copy example config to project's config folder
     */
    protected function afterInstall(): void
    {
        $configSource = __DIR__ . DIRECTORY_SEPARATOR . 'config.example.php';
        $configTarget = \Craft::$app->getConfig()->configDir . DIRECTORY_SEPARATOR . 'envfile.php';

        if (! file_exists($configTarget)) {
            copy($configSource, $configTarget);
        }
    }
}
