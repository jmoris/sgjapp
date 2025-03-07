<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentoRecibido extends Notification implements ShouldQueue
{
    use Queueable;

    protected $rut_emisor;
    protected $tipo_doc;
    protected $folio;

    /**
     * Create a new notification instance.
     */
    public function __construct($rut_emisor, $tipo_doc, $folio)
    {
        $this->rut_emisor = $rut_emisor;
        $this->tipo_doc = $tipo_doc;
        $this->folio = $folio;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'rut_emisor' => $this->rut_emisor,
            'tipo_doc' => $this->tipo_doc,
            'folio' => $this->folio
        ];
    }
}
