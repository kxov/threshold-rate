<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class NotificationSender
{
    public function __construct(
        private readonly string $recipientEmail,
        private readonly string $recipientPhone,
        private readonly NotifierInterface $notifier
    )
    {
    }

    public function sendNotification(string $message): void
    {
        $notification = (new Notification('Rate info', ['email']))
            ->content($message);

        $this->notifier->send($notification, new Recipient($this->recipientEmail, $this->recipientPhone));
    }
}
