<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * File Model for single uploaded files
 */
class File
{
    use SignalTrait;

    /**
     * New, cleaned and unique filename
     *
     * @var string
     */
    protected string $newName = '';

    /**
     * Is there a problem with this file?
     *
     * @var bool
     */
    protected bool $valid = true;

    /**
     * Like "image/png"
     *
     * @var string
     */
    protected string $type = '';

    /**
     * Filesize
     *
     * @var int
     */
    protected int $size = 0;

    /**
     * Uploadfolder for this file
     *
     * @var string
     */
    protected string $uploadFolder = 'uploads/tx_powermail/';

    /**
     * Already uploaded to uploadfolder?
     *
     * @var bool
     */
    protected bool $uploaded = false;

    /**
     * File must be renamed?
     *
     * @var bool
     */
    protected bool $renamed = false;

    /**
     * Related field
     *
     * @var Field|null
     */
    protected ?Field $field = null;

    /**
     * @param string $temporaryName
     */
    public function __construct(protected string $marker, protected string $originalName, protected ?string $temporaryName)
    {
    }

    /**
     * @return string
     */
    public function getMarker(): string
    {
        return $this->marker;
    }

    /**
     * @return File
     */
    public function setMarker(string $marker): File
    {
        $this->marker = $marker;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @return File
     * @noinspection PhpUnused
     */
    public function setOriginalName(string $originalName): File
    {
        $this->originalName = $originalName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemporaryName(): string
    {
        return $this->temporaryName;
    }

    /**
     * @return File
     * @noinspection PhpUnused
     */
    public function setTemporaryName(string $temporaryName): File
    {
        $this->temporaryName = $temporaryName;
        return $this;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }

    /**
     * @return File
     */
    public function setNewName(string $newName): File
    {
        $this->newName = $newName;
        return $this;
    }

    /**
     * Set a new name and set renamed to true
     *
     * @return File
     */
    public function renameName(string $newName): File
    {
        $this->newName = $newName;
        $this->setRenamed(true);
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return File
     */
    public function setValid(bool $valid): File
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return File
     */
    public function setType(string $type): File
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return File
     */
    public function setSize(int $size): File
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploadFolder(): string
    {
        return $this->uploadFolder;
    }

    /**
     * @return File
     */
    public function setUploadFolder(string $uploadFolder): File
    {
        $this->uploadFolder = StringUtility::addTrailingSlash($uploadFolder);
        return $this;
    }

    /**
     * @return bool
     */
    public function isUploaded(): bool
    {
        return $this->uploaded;
    }

    /**
     * @return File
     */
    public function setUploaded(bool $uploaded): File
    {
        $this->uploaded = $uploaded;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRenamed(): bool
    {
        return $this->renamed;
    }

    /**
     * @return File
     */
    public function setRenamed(bool $renamed): File
    {
        $this->renamed = $renamed;
        return $this;
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @return File
     */
    public function setField(Field $field): File
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return bool
     */
    public function validFile(): bool
    {
        return $this->getSize() > 0 && $this->getOriginalName();
    }

    /**
     * Check if file is existing on the server
     *
     * @return bool
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    public function isFileExisting(): bool
    {
        return $this->isUploaded() && file_exists($this->getNewPathAndFilename(true));
    }

    /**
     * @return string
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    public function getNewPathAndFilename(bool $absolute = false): string
    {
        $pathAndFilename = $this->getUploadFolder() . $this->getNewName();
        if ($absolute === true) {
            $pathAndFilename = GeneralUtility::getFileAbsFileName($pathAndFilename);
        }
        $this->signalDispatch(self::class, __FUNCTION__, [$pathAndFilename, $this]);
        return $pathAndFilename;
    }
}
