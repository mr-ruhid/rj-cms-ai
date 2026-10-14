<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function index(Form $form, Request $request)
    {
        $query = $form->submissions()->with('user');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where('data', 'like', '%' . $search . '%');
        }

        $submissions = $query->paginate(25)->withQueryString();

        $counts = [
            'all' => $form->submissions()->count(),
            'new' => $form->submissions()->where('status', 'new')->count(),
            'read' => $form->submissions()->where('status', 'read')->count(),
            'replied' => $form->submissions()->where('status', 'replied')->count(),
            'spam' => $form->submissions()->where('status', 'spam')->count(),
            'archived' => $form->submissions()->where('status', 'archived')->count(),
        ];

        return view('admin.forms.submissions', compact('form', 'submissions', 'counts'));
    }

    public function show(Form $form, FormSubmission $submission)
    {
        if ($submission->form_id !== $form->id) {
            abort(404);
        }

        if ($submission->status === 'new') {
            $submission->update(['status' => 'read']);
        }

        $submission->load('user');

        return view('admin.forms.submission', compact('form', 'submission'));
    }

    public function update(Request $request, Form $form, FormSubmission $submission)
    {
        if ($submission->form_id !== $form->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:new,read,replied,spam,archived',
            'notes' => 'nullable|string',
        ]);

        $submission->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Submission updated.'));
    }

    public function destroy(Form $form, FormSubmission $submission)
    {
        if ($submission->form_id !== $form->id) {
            abort(404);
        }

        $submission->delete();

        return redirect()
            ->route('admin.forms.submissions.index', $form)
            ->with('success', __('Submission deleted.'));
    }

    public function bulk(Request $request, Form $form)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'action' => 'required|in:mark_read,mark_spam,mark_archived,delete',
        ]);

        $query = $form->submissions()->whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'mark_read':
                $query->update(['status' => 'read']);
                break;
            case 'mark_spam':
                $query->update(['status' => 'spam']);
                break;
            case 'mark_archived':
                $query->update(['status' => 'archived']);
                break;
            case 'delete':
                $query->delete();
                break;
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Action applied.'));
    }

    public function export(Form $form)
    {
        $filename = 'form-' . $form->slug . '-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($form) {
            $out = fopen('php://output', 'w');

            $columns = ['ID', 'Date', 'Status'];

            foreach ($form->fields as $field) {
                $columns[] = $field->label;
            }

            fputcsv($out, $columns);

            $form->submissions()->orderBy('created_at')->chunk(500, function ($submissions) use ($out, $form) {
                foreach ($submissions as $sub) {
                    $row = [
                        $sub->id,
                        $sub->created_at?->format('Y-m-d H:i:s'),
                        $sub->status,
                    ];

                    foreach ($form->fields as $field) {
                        $value = $sub->getField($field->name);
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $row[] = (string) $value;
                    }

                    fputcsv($out, $row);
                }
            });

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}