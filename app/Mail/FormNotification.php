<?php

namespace App\Mail;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Form $form;

    public FormSubmission $submission;

    public string $subjectLine;

    public string $mode;

    public function __construct(Form $form, FormSubmission $submission, string $subjectLine = '', string $mode = 'admin')
    {
        $this->form = $form;
        $this->submission = $submission;
        $this->subjectLine = $subjectLine !== '' ? $subjectLine : __('New form submission');
        $this->mode = $mode;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.form-notification',
            with: [
                'form' => $this->form,
                'submission' => $this->submission,
                'mode' => $this->mode,
                'fields' => $this->form->fields,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}