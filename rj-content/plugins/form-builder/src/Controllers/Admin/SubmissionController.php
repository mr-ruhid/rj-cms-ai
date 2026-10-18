<?php

namespace RjPlugins\FormBuilder\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjPlugins\FormBuilder\Models\Form;
use RjPlugins\FormBuilder\Models\FormSubmission;

class SubmissionController extends Controller
{
    public function index(Form $form)
    {
        $submissions = $form->submissions()->orderByDesc('created_at')->paginate(20);

        return view('plugin-form-builder::admin.submissions.index', compact('form', 'submissions'));
    }

    public function show(Form $form, FormSubmission $submission)
    {
        if ($submission->form_id !== $form->id) {
            abort(404);
        }

        $submission->load('form');
        $form->load('fields');

        return view('plugin-form-builder::admin.submissions.show', compact('form', 'submission'));
    }

    public function destroy(Form $form, FormSubmission $submission)
    {
        if ($submission->form_id !== $form->id) {
            abort(404);
        }

        $submission->delete();

        return back()->with('success', __('Submission deleted.'));
    }
}
