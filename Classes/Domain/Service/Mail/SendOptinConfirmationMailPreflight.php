<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\HashUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class SendOptinConfirmationMailPreflight
 */
class SendOptinConfirmationMailPreflight
{
    /**
     * @var SendMailService
     */
    protected SendMailService $sendMailService;

    /**
     * @var MailRepository
     */
    protected MailRepository $mailRepository;

    public function __construct(protected array $settings, protected array $conf)
    {
        $this->sendMailService = GeneralUtility::makeInstance(SendMailService::class);
        $this->mailRepository = GeneralUtility::makeInstance(MailRepository::class);
    }

    /**
     * @return void
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    public function sendOptinConfirmationMail(Mail $mail): void
    {
        $email = [
            'template' => 'Mail/OptinMail',
            'receiverEmail' => $this->mailRepository->getSenderMailFromArguments($mail),
            'receiverName' => $this->mailRepository->getSenderNameFromArguments(
                $mail,
                [$this->conf['sender.']['default.'], 'senderName']
            ),
            'senderEmail' => $this->settings['sender']['email'],
            'senderName' => $this->settings['sender']['name'],
            'replyToEmail' => $this->settings['sender']['email'],
            'replyToName' => $this->settings['sender']['name'],
            'subject' => ObjectUtility::getContentObject()->cObjGetSingle(
                $this->conf['optin.']['subject'],
                $this->conf['optin.']['subject.']
            ),
            'rteBody' => '',
            'format' => $this->settings['sender']['mailformat'],
            'variables' => [
                'hash' => HashUtility::getHash($mail),
                'hashDisclaimer' => HashUtility::getHash($mail, 'disclaimer'),
                'mail' => $mail,
                'L' => FrontendUtility::getSysLanguageUid(),
            ],
        ];
        $this->sendMailService->sendMail($email, $mail, $this->settings, 'optin');
    }
}
