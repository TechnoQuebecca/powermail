<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

use In2code\Powermail\Domain\Model\Mail;

/**
 * Class AbstractBreaker
 */
abstract class AbstractBreaker implements BreakerInterface
{
    /**
     * @var Mail
     */
    protected $mail = null;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $flexForm = [];

    public function __construct(Mail $mail, array $settings, array $flexForm, array $configuration = [])
    {
        $this->setMail($mail);
        $this->setSettings($settings);
        $this->setFlexForm($flexForm);
        $this->setConfiguration($configuration);
    }

    /**
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * @return Mail
     */
    public function getMail(): Mail
    {
        return $this->mail;
    }

    /**
     * @return AbstractBreaker
     */
    public function setMail(Mail $mail): BreakerInterface
    {
        $this->mail = $mail;
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
     * @return AbstractBreaker
     */
    public function setConfiguration(array $configuration): BreakerInterface
    {
        $this->configuration = $configuration;
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
     * @return AbstractBreaker
     */
    public function setSettings(array $settings): BreakerInterface
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return array
     */
    public function getFlexForm(): array
    {
        return $this->flexForm;
    }

    /**
     * @return AbstractBreaker
     */
    public function setFlexForm(array $flexForm): BreakerInterface
    {
        $this->flexForm = $flexForm;
        return $this;
    }
}
