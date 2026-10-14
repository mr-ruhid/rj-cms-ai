<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Services\FormService;
use Illuminate\Http\Request;

class FormSubmitController extends Controller
{
    protected FormService $service;

    public function __construct(FormService $service)
    {
        $this->service = $service;
    }

    public function submit(Request $request, string $slug)
    {
        $form = Form::with('fields')->active()->where('slug', $slug)->first();

        if (!$form) {
            return $this->respond($request, false, __('Form not found.'), 404);
        }

        try {
            $validated = $this->service->validateData($form, $request->all());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->respond($request, false, __('Please check the form and try again.'), 422, $e->errors());
        }

        $this->service->submit($form, $validated, $request);

        session()->forget('rj_captcha_answer');

        $settings = $form->settings();
        $message = $settings['success_message'] ?? __('Thank you! Your submission has been received.');
        $redirect = trim((string) ($settings['redirect_url'] ?? ''));

        return $this->respond($request, true, $message, 200, [], $redirect);
    }

    protected function respond(Request $request, bool $success, string $message, int $status = 200, array $errors = [], string $redirect = '')
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
                'errors' => $errors,
                'redirect' => $redirect,
            ], $status);
        }

        return back()
            ->with($success ? 'success' : 'error', $message)
            ->withErrors($errors);
    }
}