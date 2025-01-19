<?php

namespace Modules\Notifications\Notifications;

use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Notifications\Services\Templates\Template;
use Illuminate\Support\HtmlString;

abstract class Notification extends BaseNotification
{
    /** @var Template */
    protected $template;

    /** @var Attachment */
    protected $attachments = [];

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = $this->getTemplate();
    }

    abstract protected function getTemplate(): Template;

    abstract public function getTagsDict(): array;

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->template->getStatus()) {
            $mail = (new MailMessage)
                ->subject($this->template->getSubject())
                ->line(new HtmlString($this->template->getBody()));

            if ($this->template->getEmailForSendCopy()) {
                $mail->bcc($this->template->getEmailForSendCopy());
            }

            if ($this->attachments) {
                foreach ($this->attachments as $attachment) {
                    $mail->attach($attachment->getPath(), $attachment->getOptions());
                }
            }

            return $mail;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
