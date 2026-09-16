<?php

declare(strict_types=1);

namespace Acme\Contact\Service;

use Acme\Contact\Domain\Model\Message;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;

final readonly class MessageSender
{
    public function __construct(private MailerInterface $mailer) {}

    public function send(Message $message, string $recipient): void
    {
        // Page and visitor context for the mail footer.
        $pageId = $GLOBALS['TSFE']->id;
        $user = $GLOBALS['TSFE']->fe_user->user['username'] ?? 'guest';

        $email = (new FluidEmail())
            ->to($recipient)
            ->replyTo(new Address($message->getEmail(), $message->getName()))
            ->subject('Contact form on page ' . $pageId)
            ->format(FluidEmail::FORMAT_PLAIN)
            ->setTemplate('Default')
            ->assign('content', $message->getText() . "\n\n-- sent by " . $user);
        $this->mailer->send($email);
    }
}
