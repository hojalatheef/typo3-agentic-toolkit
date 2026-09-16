<?php

declare(strict_types=1);

namespace Acme\Contact\Controller;

use Acme\Contact\Domain\Model\Message;
use Acme\Contact\Service\MessageSender;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class ContactController extends ActionController
{
    public function __construct(private readonly MessageSender $messageSender) {}

    public function showAction(?Message $message = null): ResponseInterface
    {
        $this->view->assign('message', $message ?? new Message());
        return $this->htmlResponse();
    }

    public function submitAction(Message $message): ResponseInterface
    {
        $this->messageSender->send($message, (string)$this->settings['recipient']);
        $this->addFlashMessage('Thank you, we will get back to you.');
        return $this->redirect('show');
    }
}
