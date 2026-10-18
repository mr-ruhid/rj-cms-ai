<?php

namespace RjPlugins\FormBuilder\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RjPlugins\FormBuilder\Models\Form;
use RjPlugins\FormBuilder\Models\FormSubmission;

class FormSubmitController extends Controller
{
    public function submit(Request $request, string $slug)
    {
        $form = Form::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $fields = $form->fields;

        $rules = [];
        foreach ($fields as $field) {
            $rule = [];
            if ($field->is_required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            switch ($field->type) {
                case 'email':
                    $rule[] = 'email';
                    break;
                case 'url':
                    $rule[] = 'url';
                    break;
                case 'number':
                    $rule[] = 'numeric';
                    break;
                case 'date':
                    $rule[] = 'date';
                    break;
                case 'time':
                    $rule[] = 'date_format:H:i';
                    break;
                case 'file':
                    $rule[] = 'file';
                    $rule[] = 'max:10240';
                    break;
            }

            $rules[$field->name] = implode('|', $rule);
        }

        $validated = $request->validate($rules);

        $data = $request->except(['_token', '_method']);

        FormSubmission::create([
            'form_id' => $form->id,
            'data' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'new',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Thank you! Your message has been sent.'),
            ]);
        }

        return back()->with('success', __('Thank you! Your message has been sent.'));
    }
}
