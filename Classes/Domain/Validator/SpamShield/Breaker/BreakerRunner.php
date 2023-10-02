<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Exception\ClassDoesNotExistException;
use In2code\Powermail\Exception\ConfigurationIsMissingException;
use In2code\Powermail\Exception\InterfaceNotImplementedException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class BreakerRunner
 */
class BreakerRunner
{
    /**
     * @var string
     */
    protected string $interface = BreakerInterface::class;

    /**
     * @param Mail $mail
     */
    public function __construct(protected ?Mail $mail, protected array $settings, protected array $flexForm)
    {
    }

    /**
     * @return bool
     * @throws ClassDoesNotExistException
     * @throws ConfigurationIsMissingException
     * @throws Exception
     * @throws InterfaceNotImplementedException
     */
    public function isSpamCheckDisabledByAnyBreaker(): bool
    {
        foreach ($this->getBreaker() as $breaker) {
            if (!isset($breaker['class'])) {
                throw new ConfigurationIsMissingException(
                    'Setup ...spamshield.disable.NO.class not given in TypoScript',
                    1_516_024_297_083
                );
            }
            if (!class_exists($breaker['class'])) {
                throw new ClassDoesNotExistException(
                    'Class ' . $breaker['class'] . ' does not exists - check if file was loaded with autoloader',
                    1_516_024_305_363
                );
            }
            if (!is_subclass_of($breaker['class'], $this->interface)) {
                throw new InterfaceNotImplementedException(
                    'Breaker method does not implement ' . $this->interface,
                    1_516_024_315_548
                );
            }
            /** @var AbstractBreaker $breakerInstance */
            $breakerInstance = GeneralUtility::makeInstance(
                $breaker['class'],
                $this->mail,
                $this->settings,
                $this->flexForm,
                $breaker['configuration'] ?? []
            );
            $breakerInstance->initialize();
            if ($breakerInstance->isDisabled() === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    protected function getBreaker(): array
    {
        $breakerConfiguration = [];
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $settings = $configurationService->getTypoScriptSettings();
        if (!empty($settings['spamshield']['_disable'])) {
            $breakerConfiguration = $settings['spamshield']['_disable'];
        }
        return $breakerConfiguration;
    }
}
