<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class SenderMailPropertiesService to get email array for sender attributes
 * for sender emails
 */
class SenderMailPropertiesService
{
    use SignalTrait;

    /**
     * TypoScript configuration for cObject parsing
     *
     * @var array
     */
    protected array $configuration = [];

    public function __construct(protected array $settings)
    {
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
    }

    /**
     * Get sender email from form settings. If empty, take default from TypoScript or TYPO3 configuration
     *
     * @return string
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    public function getSenderEmail(): string
    {
        if ($this->settings['sender']['email'] !== '') {
            $senderEmail = $this->settings['sender']['email'];
        } else {
            $senderEmail = ConfigurationUtility::getDefaultMailFromAddress();
            $senderEmail = TypoScriptUtility::overwriteValueFromTypoScript(
                $senderEmail,
                $this->configuration['sender.']['default.'],
                'senderEmail'
            );
        }

        $signalArguments = [&$senderEmail, $this];
        $this->signalDispatch(self::class, __FUNCTION__, $signalArguments);
        return $senderEmail;
    }

    /**
     * Get sender name from form settings. If empty, take default from TypoScript or TYPO3 configuration.
     *
     * @return string
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    public function getSenderName(): string
    {
        if ($this->settings['sender']['name'] !== '') {
            $senderName = $this->settings['sender']['name'];
        } else {
            $senderName = ConfigurationUtility::getDefaultMailFromName();
            $senderName = TypoScriptUtility::overwriteValueFromTypoScript(
                $senderName,
                $this->configuration['sender.']['default.'],
                'senderName'
            );
        }

        $signalArguments = [&$senderName, $this];
        $this->signalDispatch(self::class, __FUNCTION__, $signalArguments);
        return $senderName;
    }
}
