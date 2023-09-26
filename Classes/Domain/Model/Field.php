<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\BackendUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class Field
 */
class Field extends AbstractEntity
{
    final public const TABLE_NAME = 'tx_powermail_domain_model_field';
    final public const FIELD_TYPE_BASIC = 'basic';
    final public const FIELD_TYPE_ADVANCED = 'advanced';
    final public const FIELD_TYPE_EXTPORTABLE = 'exportable';

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * type
     *        Powermail field types are:
     *        "input", "textarea", "select", "check", "radio"
     *        "submit", "captcha", "reset", "text", "content"
     *        "html", "password", "file", "hidden", "date",
     *        "country", "location", "typoscript"
     *
     * @var string
     */
    protected string $type = '';

    /**
     * @var string
     */
    protected string $settings = '';

    /**
     * @var string
     */
    protected string $path = '';

    /**
     * @var int
     */
    protected int $contentElement = 0;

    /**
     * @var string
     */
    protected string $text = '';

    /**
     * @var string
     */
    protected string $prefillValue = '';

    /**
     * @var string
     */
    protected string $placeholder = '';

    /**
     * @var string
     */
    protected string $createFromTyposcript = '';

    /**
     * @var int
     */
    protected int $validation = 0;

    /**
     * @var string
     */
    protected string $validationConfiguration = '';

    /**
     * @var string
     */
    protected string $css = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var bool
     */
    protected bool $multiselect = false;

    /**
     * @var string
     */
    protected string $datepickerSettings = '';

    /**
     * @var string
     */
    protected string $feuserValue = '';

    /**
     * @var bool
     */
    protected bool $senderName = false;

    /**
     * @var bool
     */
    protected bool $senderEmail = false;

    /**
     * @var bool
     */
    protected bool $mandatory = false;

    /**
     * @var string
     */
    protected string $marker = '';

    /**
     * @var int
     */
    protected int $sorting = 0;

    /**
     * @var int
     */
    protected int $l10nParent = 0;

    /**
     * @var Page
     * This property can hold Page|int|null (depending on the context). "@var" must set to Page for property mapping.
     */
    protected $page = null;

    /**
     * @return string
     * @throws Exception
     */
    public function getTitle(): string
    {
        return TemplateUtility::fluidParseString($this->title);
    }

    /**
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Returns the type - must not be empty
     *
     * @return string $type
     */
    public function getType(): string
    {
        $type = $this->type;
        if (empty($type)) {
            $type = 'input';
            if ($this->isLocalized()) {
                $fieldRepository = GeneralUtility::makeInstance(FieldRepository::class);
                $originalType = $fieldRepository->getTypeFromUid($this->getUid());
                if (!empty($originalType)) {
                    $type = $originalType;
                }
            }
        }
        return $type;
    }

    /**
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Check if this field is of a basic field type
     * Basic field types are:
     *        "input", "textarea", "select", "check", "radio"
     *
     * @return bool
     */
    public function isBasicFieldType(): bool
    {
        $basicFieldTypes = [
            'input',
            'textarea',
            'select',
            'check',
            'radio',
        ];
        return in_array($this->getType(), $basicFieldTypes);
    }

    /**
     * Check if this field is of an advanced field type (includes also basic field types)
     * basicly used for export and frontend editing
     *
     * @return bool
     */
    public function isAdvancedFieldType(): bool
    {
        $advancedFieldTypes = [
            'hidden',
            'file',
            'location',
            'date',
            'country',
            'password',
        ];
        return $this->isBasicFieldType() || in_array($this->getType(), $advancedFieldTypes);
    }

    /**
     * @return bool
     * @throws DeprecatedException
     */
    public function isExportableFieldType(): bool
    {
        return $this->isAdvancedFieldType() || in_array($this->getType(), $this->getExportableTypesFromTypoScript());
    }

    /**
     * @return bool
     * @throws DeprecatedException
     */
    public function isTypeOf(string $type): bool
    {
        if ($type === self::FIELD_TYPE_BASIC) {
            return $this->isBasicFieldType();
        }
        if ($type === self::FIELD_TYPE_ADVANCED) {
            return $this->isAdvancedFieldType();
        }
        if ($type === self::FIELD_TYPE_EXTPORTABLE) {
            return $this->isExportableFieldType();
        }
        return false;
    }

    /**
     * @return string
     */
    public function getSettings(): string
    {
        return $this->settings;
    }

    /**
     * @return void
     */
    public function setSettings(string $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Modify settings for select, radio and checkboxes
     *        option1 =>
     *            label => Red Shoes
     *            value => red
     *            selected => 1
     *
     * @return array
     * @throws Exception
     */
    public function getModifiedSettings(): array
    {
        return $this->optionArray($this->getSettings(), $this->getCreateFromTyposcript());
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return int
     */
    public function getContentElement(): int
    {
        return $this->contentElement;
    }

    /**
     * @return void
     */
    public function setContentElement(int $contentElement): void
    {
        $this->contentElement = (int)$contentElement;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return void
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getPrefillValue(): string
    {
        return $this->prefillValue;
    }

    /**
     * @return void
     */
    public function setPrefillValue(string $prefillValue): void
    {
        $this->prefillValue = $prefillValue;
    }

    /**
     * @return void
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @return void
     */
    public function setCreateFromTyposcript(string $createFromTyposcript): void
    {
        $this->createFromTyposcript = $createFromTyposcript;
    }

    /**
     * @return string
     */
    public function getCreateFromTyposcript(): string
    {
        return $this->createFromTyposcript;
    }

    /**
     * @return int
     */
    public function getValidation(): int
    {
        return $this->validation;
    }

    /**
     * @return void
     */
    public function setValidation(int $validation): void
    {
        $this->validation = $validation;
    }

    /**
     * @return void
     */
    public function setValidationConfiguration(string $validationConfiguration): void
    {
        $this->validationConfiguration = $validationConfiguration;
    }

    /**
     * @return string
     */
    public function getValidationConfiguration(): string
    {
        return $this->validationConfiguration;
    }

    /**
     * @return string
     */
    public function getCss(): string
    {
        return $this->css;
    }

    /**
     * @return void
     */
    public function setCss(string $css): void
    {
        $this->css = $css;
    }

    /**
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return void
     */
    public function setMultiselect(bool $multiselect): void
    {
        $this->multiselect = $multiselect;
    }

    /**
     * @return bool
     */
    public function isMultiselect(): bool
    {
        return $this->multiselect;
    }

    /**
     * @return string
     */
    public function getMultiselectForField(): string
    {
        $value = $this->isMultiselect();
        if ($value) {
            $value = 'multiple';
        } else {
            $value = '';
        }
        return $value;
    }

    /**
     * @return void
     */
    public function setDatepickerSettings(string $datepickerSettings): void
    {
        $this->datepickerSettings = $datepickerSettings;
    }

    /**
     * @return string
     */
    public function getDatepickerSettings(): string
    {
        $datepickerSettings = $this->datepickerSettings;
        if (empty($datepickerSettings)) {
            $datepickerSettings = 'date';
        }
        return $datepickerSettings;
    }

    /**
     * Rewrite datetime to datetime-local (Chrome support)
     *
     * @return string
     */
    public function getDatepickerSettingsOptimized(): string
    {
        $settings = $this->getDatepickerSettings();
        if ($settings === 'datetime') {
            $settings = 'datetime-local';
        }
        return $settings;
    }

    /**
     * @return string
     */
    public function getFeuserValue(): string
    {
        return $this->feuserValue;
    }

    /**
     * @return void
     */
    public function setFeuserValue(string $feuserValue): void
    {
        $this->feuserValue = $feuserValue;
    }

    /**
     * @return bool
     */
    public function isSenderEmail(): bool
    {
        return $this->senderEmail;
    }

    /**
     * @return void
     */
    public function setSenderEmail(bool $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return bool
     */
    public function isSenderName(): bool
    {
        return $this->senderName;
    }

    /**
     * @return void
     */
    public function setSenderName(bool $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * @return bool
     */
    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * @return void
     */
    public function setMandatory(bool $mandatory): void
    {
        $this->mandatory = $mandatory;
    }

    /**
     * @return string $marker
     */
    public function getMarker(): string
    {
        $marker = $this->marker;
        if ($this->isLocalized()) {
            $fieldRepository = GeneralUtility::makeInstance(FieldRepository::class);
            $marker = $fieldRepository->getMarkerFromUid($this->getUid());
        }
        if (empty($marker)) {
            $marker = 'uid' . $this->getUid();
        }
        return $marker;
    }

    /**
     * Returns the marker, even if empty
     *
     * @return string $marker
     */
    public function getMarkerOriginal(): string
    {
        return $this->marker;
    }

    /**
     * @return void
     */
    public function setMarker(string $marker): void
    {
        $this->marker = $marker;
    }

    /**
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * @return void
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * @return void
     */
    public function setPage(Page $page): void
    {
        $this->page = $page;
    }

    /**
     * @return Page|null
     */
    public function getPage(): ?Page
    {
        return $this->page;
    }

    /**
     * Create an options array (Needed for fieldsettings: select, radio, check)
     *        option1 =>
     *            label => Red Shoes
     *            value => red
     *            selected => 1
     *
     * @param string $string Options from the Textarea
     * @param string $typoScriptObjectPath Path to TypoScript like lib.blabla
     * @return array Options Array
     * @throws Exception
     */
    protected function optionArray(string $string, string $typoScriptObjectPath, bool $parse = true): array
    {
        if (empty($string)) {
            $string = TypoScriptUtility::parseTypoScriptFromTypoScriptPath($typoScriptObjectPath);
        }
        if (empty($string)) {
            $string = 'Error, no options to show';
        }
        return $this->buildOptions($string, $parse);
    }

    /**
     * @param string $string Options from the Textarea
     * @return array
     * @throws Exception
     */
    protected function buildOptions(string $string, bool $parse): array
    {
        $options = [];
        $string = str_replace('[\n]', PHP_EOL, $string);
        $settingsField = GeneralUtility::trimExplode(PHP_EOL, $string, true);
        foreach ($settingsField as $line) {
            $settings = GeneralUtility::trimExplode('|', $line, false);
            $value = ($settings[1] ?? $settings[0]);
            $label = ($parse ? TemplateUtility::fluidParseString($settings[0]) : $settings[0]);
            $options[] = [
                'label' => $label,
                'value' => $value,
                'selected' => !empty($settings[2]) && $settings[2] === '*' ? 1 : 0,
            ];
        }
        return $options;
    }

    /**
     * Return expected value type from fieldtype
     *
     * @return int
     * @throws DeprecatedException
     */
    public function dataTypeFromFieldType(string $fieldType): int
    {
        $dataType = 0;
        static $types = null;
        if (is_null($types)) {
            $types = [
                'captcha' => Answer::VALUE_TYPE_TEXT,
                'check' => Answer::VALUE_TYPE_ARRAY,
                'content' => Answer::VALUE_TYPE_TEXT,
                'date' => Answer::VALUE_TYPE_DATE,
                'file' => Answer::VALUE_TYPE_UPLOAD,
                'hidden' => Answer::VALUE_TYPE_TEXT,
                'html' => Answer::VALUE_TYPE_TEXT,
                'input' => Answer::VALUE_TYPE_TEXT,
                'location' => Answer::VALUE_TYPE_TEXT,
                'password' => Answer::VALUE_TYPE_TEXT,
                'radio' => Answer::VALUE_TYPE_TEXT,
                'reset' => Answer::VALUE_TYPE_TEXT,
                'select' => Answer::VALUE_TYPE_TEXT,
                'submit' => Answer::VALUE_TYPE_TEXT,
                'text' => Answer::VALUE_TYPE_TEXT,
                'textarea' => Answer::VALUE_TYPE_TEXT,
                'typoscript' => Answer::VALUE_TYPE_TEXT,
            ];
            $types = $this->extendTypeArrayWithTypoScriptTypes($types);
        }

        // change select fieldtype to array if multiple checked
        if ($fieldType === 'select') {
            $types['select'] = $this->isMultiselect() ? 1 : 0;
        }

        if (array_key_exists($fieldType, $types)) {
            $dataType = $types[$fieldType];
        }
        return $dataType;
    }

    /**
     * @return bool
     */
    public function isLocalized(): bool
    {
        return $this->_getProperty('_languageUid') > 0 &&
            $this->_getProperty('l10nParent') > 0;
    }

    /**
     * Extend dataType with TSConfig
     *
     *      Example Page TSConfig:
     *          tx_powermail.flexForm.type.addFieldOptions.new = New Field
     *          tx_powermail.flexForm.type.addFieldOptions.new.dataType = 0
     *
     * @param array $types
     * @return array
     * @throws DeprecatedException
     */
    protected function extendTypeArrayWithTypoScriptTypes(array $types): array
    {
        $typoScript = BackendUtility::getPagesTSconfig(FrontendUtility::getCurrentPageIdentifier());
        if (!empty($typoScript['tx_powermail.']['flexForm.'])) {
            $configuration = $typoScript['tx_powermail.']['flexForm.'];
            if (isset($configuration['type.']['addFieldOptions.'])) {
                foreach ((array)$configuration['type.']['addFieldOptions.'] as $fieldTypeName => $fieldType) {
                    if (!empty($fieldType['dataType'])) {
                        $fieldTypeName = substr($fieldTypeName, 0, -1);
                        $types[$fieldTypeName] = (int)$fieldType['dataType'];
                    }
                }
            }
        }
        return $types;
    }

    /**
     * Extend exportable field type with types from TSConfig
     *
     *      Example Page TSConfig:
     *          tx_powermail.flexForm.type.addFieldOptions.new = New Field
     *          tx_powermail.flexForm.type.addFieldOptions.new.export = 1
     *
     * @return array ['new', 'myownfield']
     * @throws DeprecatedException
     */
    protected function getExportableTypesFromTypoScript(): array
    {
        $types = [];
        $typoScript = BackendUtility::getPagesTSconfig($this->getPid());
        if (ArrayUtility::isValidPath($typoScript, 'tx_powermail./flexForm.')) {
            $configuration = $typoScript['tx_powermail.']['flexForm.'];
            $configuration['type.'] ??= [];
            foreach ((array)($configuration['type.']['addFieldOptions.'] ?? []) as $fieldTypeName => $fieldType) {
                if (!empty($fieldType['export'])) {
                    if ($fieldType['export'] === '1') {
                        $fieldTypeName = rtrim($fieldTypeName, '.');
                        $types[] = $fieldTypeName;
                    }
                }
            }
        }
        return $types;
    }
}
