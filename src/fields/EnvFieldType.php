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

namespace justenough\envfield\fields;

use Craft;
use craft\base\Element;

use craft\base\ElementInterface;
use craft\fields\PlainText as PlainTextField;
use craft\helpers\App;
use craft\helpers\Html;
use craft\web\twig\variables\Cp as CpVariable;
use justenough\envfield\EnvField;
use justenough\envfield\models\Settings;


use yii\db\Schema;

/**
 * @author    Just Enough Consulting
 * @package   EnvField
 * @since     1.0.0
 */

class EnvFieldType extends PlainTextField
{
    // Public Properties
    // =========================================================================
    public string $warning = 'Take care when selecting values to avoid leaking potentially sensitive data';

    public string $tip = 'Can be set to an environment variable';

    public bool $enableAutosuggest = true;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'envfield/settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate('envfield/input', [
            'id' => Html::id($this->handle),
            'name' => $this->handle,
            'value' => $value,
            'field' => $this,
            'suggestions' => $this->enableAutosuggest ? $this->getPermittedSuggestions() : [],
            'orientation' => $this->getOrientation($element),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return Craft::$app->request->isCpRequest ? $value : App::parseEnv($value);
    }

    public function getElementValidationRules(): array
    {
        return array_merge(
            ['validateValue'],
            parent::getElementValidationRules()
        );
    }

    public function getAllSuggestions(): array
    {
        $cpVar = new CpVariable();

        return $cpVar->getEnvSuggestions(false);
    }

    public function getPermittedSuggestions(): array
    {
        $allSuggestions = $this->getAllSuggestions();

        $allSuggestions[0]['data'] = $this->filterSuggestions($allSuggestions[0]['data']);

        return $allSuggestions;
    }

    /**
     * @inheritdoc
     */
    public function validateValue(Element $element): void
    {
        $value = $element->getFieldValue($this->handle);

        $validates = ! in_array($value, $this->getAllEnvValues()) || in_array($value, $this->getValidEnvValues());

        if (! $validates) {
            $element->addError($this->handle, 'The environment variable provided is not permitted here');
        }
    }

    // Protected Methods
    // =========================================================================

    protected function filterSuggestions(array $suggestions): array
    {
        return array_values(array_filter($suggestions, [$this, 'shouldAllowVarName']));
    }

    protected function shouldAllowVarName(array $option): bool
    {
        ['name' => $name ] = $option;

        /**
         * @var Settings
         */
        $settings = EnvField::getInstance()->getSettings();

        if ($settings->excludePattern) {
            return ! preg_match($settings->excludePattern, $name);
        } elseif ($settings->includePattern) {
            return preg_match($settings->includePattern, $name);
        } else {
            return true;
        }
    }

    protected function getAllEnvValues(): array
    {
        $suggestions = $this->getAllSuggestions();

        return array_map(fn ($value) => $value['name'], $suggestions[0]['data']);
    }

    protected function getValidEnvValues(): array
    {
        $suggestions = $this->getPermittedSuggestions();

        return array_map(fn ($value) => $value['name'], $suggestions[0]['data']);
    }


    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Environment Variable-Aware Plain Text';
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }
}
