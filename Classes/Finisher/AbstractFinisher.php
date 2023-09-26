<?php

declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class AbstractFinisher
 */
abstract class AbstractFinisher implements FinisherInterface
{
    /**
     * @var Mail
     */
    protected Mail $mail;

    /**
     * Extension settings
     *
     * @var array
     */
    protected array $settings = [];

    /**
     * Finisher service configuration
     *
     * @var array
     */
    protected array $configuration = [];

    /**
     * Was form finally submitted?
     *
     * @var bool
     */
    protected bool $formSubmitted = false;

    /**
     * Controller actionName - usually "createAction" or "confirmationAction"
     *
     * @var string
     */
    protected string $actionMethodName = '';

    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        bool $formSubmitted,
        string $actionMethodName,
        protected ContentObjectRenderer $contentObject
    ) {
        $this->setMail($mail);
        $this->setConfiguration($configuration);
        $this->setSettings($settings);
        $this->setFormSubmitted($formSubmitted);
        $this->setActionMethodName($actionMethodName);
    }

    /**
     * @return Mail
     */
    public function getMail(): Mail
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     * @return FinisherInterface
     */
    public function setMail(Mail $mail): FinisherInterface
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return FinisherInterface
     */
    public function setSettings(array $settings): FinisherInterface
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @return FinisherInterface
     */
    public function setConfiguration(array $configuration): FinisherInterface
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Form is not marked as submitted in case of optin usage
     *
     * @return bool
     */
    public function isFormSubmitted(): bool
    {
        return $this->formSubmitted;
    }

    /**
     * @param bool $formSubmitted
     * @return FinisherInterface
     */
    public function setFormSubmitted(bool $formSubmitted): FinisherInterface
    {
        $this->formSubmitted = $formSubmitted;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionMethodName(): string
    {
        return $this->actionMethodName;
    }

    /**
     * @param string $actionMethodName
     * @return FinisherInterface
     */
    public function setActionMethodName(string $actionMethodName): FinisherInterface
    {
        $this->actionMethodName = $actionMethodName;
        return $this;
    }

    /**
     * @return void
     */
    public function initializeFinisher(): void
    {
    }
}
