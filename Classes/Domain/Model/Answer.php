<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Answer
 */
class Answer extends AbstractEntity
{
    final public const TABLE_NAME = 'tx_powermail_domain_model_answer';
    final public const VALUE_TYPE_TEXT = 0;
    final public const VALUE_TYPE_ARRAY = 1;
    final public const VALUE_TYPE_DATE = 2;
    final public const VALUE_TYPE_UPLOAD = 3;

    /**
     * @var string
     */
    protected $value = '';

    /**
     * valueType
     *      0 => text
     *      1 => array
     *      2 => date
     *      3 => upload
     *
     * @var int
     */
    protected $valueType = null;

    /**
     * @var Mail
     */
    protected $mail = null;

    /**
     * @var Field
     */
    protected $field = null;

    /**
     * All mails and answers should be stored with sys_language_uid=-1 to get those values from persisted objects
     * in fe requests in every language (e.g. for optin mails, etc...)
     *
     * @var int|null
     */
    protected ?int $_languageUid = -1;

    /**
     * @return mixed $value
     */
    public function getValue()
    {
        $value = $this->value;

        // if serialized, change to array
        if (ArrayUtility::isJsonArray((string)$this->value)) {
            // only if type multivalue or upload
            if ($this->getValueType() === self::VALUE_TYPE_ARRAY || $this->getValueType() === self::VALUE_TYPE_UPLOAD) {
                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            }
        }

        if ($this->isTypeDateForTimestamp($value)) {
            $value = date(
                LocalizationUtility::translate('datepicker_format_' . $this->getField()->getDatepickerSettings()),
                (int)$value
            );
        }

        if ($this->isTypeMultiple($value)) {
            $value = (empty($value) ? [] : [(string)$value]);
        }

        return $value;
    }

    /**
     * Sets the value
     *
     * @return Answer
     */
    public function setValue(mixed $value): Answer
    {
        $value = $this->convertToJson($value);
        $value = $this->convertToTimestamp($value);
        $this->value = $value;
        return $this;
    }

    /**
     * Returns value and enforces a string
     *        An array will be returned as commaseparated string
     *
     * @return string
     */
    public function getStringValue(string $glue = ', '): string
    {
        $value = $this->getValue();
        if (is_array($value)) {
            $value = implode($glue, $value);
        }
        return (string)$value;
    }

    /**
     * Returns raw value - could be
     *        - Same as getValue()
     *        - Timestamp (Date fields) instead of human readable date
     *        - JSON string for multiple fields instead of array
     *
     * @return string|int
     */
    public function getRawValue()
    {
        return $this->value;
    }

    /**
     * @return Answer
     */
    public function setValueType(int $valueType): Answer
    {
        $this->valueType = (int)$valueType;
        return $this;
    }

    /**
     * @return int
     */
    public function getValueType(): int
    {
        if ($this->valueType === null) {
            if ($this->getField() !== null) {
                $field = $this->getField();
                $this->setValueType($field->dataTypeFromFieldType($field->getType()));
            } else {
                $this->setValue(0);
            }
        }
        return $this->valueType;
    }

    /**
     * @return Mail $mail
     */
    public function getMail(): ?Mail
    {
        return $this->mail;
    }

    /**
     * @return Answer
     */
    public function setMail(Mail $mail): Answer
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return Field $field
     */
    public function getField(): ?Field
    {
        return $this->field;
    }

    /**
     * @return Answer
     */
    public function setField(Field $field): Answer
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @param string|array $value
     * @return bool
     */
    protected function isTypeDateForTimestamp($value): bool
    {
        return $this->getValueType() === self::VALUE_TYPE_DATE && is_numeric($value) && $this->getField() !== null;
    }

    /**
     * @param string|array $value
     * @return bool
     */
    protected function isTypeDateForDate($value): bool
    {
        if (is_object($this->getField()) || is_string($this->getField())) {
            return !empty($value) && method_exists($this->getField(), 'getType')
            && $this->getValueType() === self::VALUE_TYPE_DATE && !is_numeric($value);
        }
        return false;
    }

    /**
     * If multitext or upload force array
     *
     * @param string|array $value
     * @return bool
     */
    protected function isTypeMultiple($value): bool
    {
        return ($this->getValueType() === self::VALUE_TYPE_ARRAY || $this->getValueType() === self::VALUE_TYPE_UPLOAD)
            && !is_array($value);
    }

    /**
     * If array, encode to JSON string
     *
     * @param string|array $value
     * @return string
     */
    protected function convertToJson($value): string
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_THROW_ON_ERROR);
        }
        return (string)$value;
    }

    /**
     * Convert string to timestamp for date fields (datepicker)
     *
     * @return int|string
     */
    protected function convertToTimestamp(string $value)
    {
        if ($this->isTypeDateForDate($value)) {
            if (empty($this->translateFormat)) {
                $format = LocalizationUtility::translate(
                    'datepicker_format_' . $this->getField()->getDatepickerSettings()
                );
            } else {
                $format = $this->translateFormat;
            }
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                if ($this->getField()->getDatepickerSettings() === 'date') {
                    $date->setTime(0, 0, 0);
                }
                $value = $date->getTimestamp();
            } else {
                try {
                    // fallback html5 date field - always Y-m-d H:i
                    $date = new \DateTime($value);
                } catch (\Exception) {
                    // clean value if string could not be converted
                    $value = '';
                }
                if ($date) {
                    if ($this->getField()->getDatepickerSettings() === 'date') {
                        $date->setTime(0, 0, 0);
                    }
                    $value = $date->getTimestamp();
                }
            }
        }
        return $value;
    }
}
