<?php

namespace App\Services;

use App\Mail\FormNotification;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FormService
{
    public function submit(Form $form, array $data, Request $request): FormSubmission
    {
        $settings = $form->settings();

        $payload = [
            'form_id' => $form->id,
            'data' => $data,
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'referrer' => (string) $request->headers->get('referer', ''),
            'user_id' => auth()->id(),
        ];

        if (!($settings['store_submissions'] ?? true)) {
            $payload['notes'] = __('Submissions storage disabled.');
        }

        $submission = FormSubmission::create($payload);

        $this->sendAdminNotification($form, $submission);
        $this->sendUserNotification($form, $submission);
        $this->triggerWebhook($form, $submission);

        return $submission;
    }

    public function validateData(Form $form, array $input): array
    {
        $rules = [];
        $attributes = [];

        foreach ($form->fields as $field) {
            if ($field->type === 'hidden') {
                continue;
            }

            $rules[$field->name] = $field->validationRules();
            $attributes[$field->name] = $field->label;
        }

        if (($form->settings()['enable_captcha'] ?? false)) {
            $rules['rj_captcha'] = ['required', 'string', 'in:' . $this->captchaAnswer()];
            $attributes['rj_captcha'] = __('Captcha');
        }

        return validator($input, $rules, [], $attributes)->validate();
    }

    protected function sendAdminNotification(Form $form, FormSubmission $submission): void
    {
        $config = $form->notifications();
        $email = trim((string) ($config['admin_email'] ?? ''));

        if ($email === '') {
            return;
        }

        $subject = $config['admin_subject'] ?? __('New form submission');

        try {
            Mail::to($email)->send(new FormNotification($form, $submission, $subject, 'admin'));
        } catch (\Throwable $e) {
            logger()->error('Form admin notification failed: ' . $e->getMessage());
        }
    }

    protected function sendUserNotification(Form $form, FormSubmission $submission): void
    {
        $config = $form->notifications();
        $field = trim((string) ($config['user_email_field'] ?? ''));

        if ($field === '') {
            return;
        }

        $userEmail = $submission->getField($field);

        if (!$userEmail || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $subject = $config['user_subject'] ?? __('Thank you for your message');

        try {
            Mail::to($userEmail)->send(new FormNotification($form, $submission, $subject, 'user'));
        } catch (\Throwable $e) {
            logger()->error('Form user notification failed: ' . $e->getMessage());
        }
    }

    protected function triggerWebhook(Form $form, FormSubmission $submission): void
    {
        $config = $form->actions();
        $url = trim((string) ($config['webhook_url'] ?? ''));

        if ($url === '') {
            return;
        }

        $method = strtoupper((string) ($config['webhook_method'] ?? 'POST'));

        if (!in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $method = 'POST';
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 10]);

            $client->request($method, $url, [
                'json' => [
                    'form' => [
                        'id' => $form->id,
                        'name' => $form->name,
                        'slug' => $form->slug,
                    ],
                    'submission' => [
                        'id' => $submission->id,
                        'data' => $submission->data,
                        'created_at' => $submission->created_at?->toIso8601String(),
                    ],
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'RJ-CMS-Form/1.0',
                ],
            ]);
        } catch (\Throwable $e) {
            logger()->error('Form webhook failed: ' . $e->getMessage());
        }
    }

    protected function captchaAnswer(): string
    {
        return (string) session('rj_captcha_answer', '');
    }

    public function generateCaptcha(): array
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);

        session(['rj_captcha_answer' => (string) ($a + $b)]);

        return [
            'question' => $a . ' + ' . $b . ' = ?',
        ];
    }

    public function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = \Illuminate\Support\Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Form::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}