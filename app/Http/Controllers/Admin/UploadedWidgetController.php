<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UploadedWidget;
use App\Services\WidgetUploader;
use Illuminate\Http\Request;

class UploadedWidgetController extends Controller
{
    protected WidgetUploader $uploader;

    public function __construct(WidgetUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function index(Request $request)
    {
        $this->uploader->discover();

        $query = UploadedWidget::with('uploader')->orderBy('name');

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $widgets = $query->get();

        $counts = [
            'all' => UploadedWidget::count(),
            'active' => UploadedWidget::where('status', 'active')->count(),
            'inactive' => UploadedWidget::where('status', 'inactive')->count(),
            'error' => UploadedWidget::where('status', 'error')->count(),
        ];

        $categories = \App\Widgets\WidgetRegistry::categories();

        return view('admin.widgets.index', compact('widgets', 'counts', 'categories'));
    }

    public function show(UploadedWidget $widget)
    {
        $widget->load('uploader');

        $metadata = $widget->metadata();
        $schema = $widget->schema();
        $requires = $widget->requires();

        return view('admin.widgets.show', compact('widget', 'metadata', 'schema', 'requires'));
    }

    public function activate(Request $request, UploadedWidget $widget)
    {
        $result = $this->uploader->activate($widget);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function deactivate(Request $request, UploadedWidget $widget)
    {
        $result = $this->uploader->deactivate($widget);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function destroy(Request $request, UploadedWidget $widget)
    {
        if ($widget->isActive()) {
            $message = __('Deactivate the widget before deleting.');

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $result = $this->uploader->delete($widget);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()
                ->route('admin.widgets.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:zip|max:20480',
        ]);

        $result = $this->uploader->installFromZip($validated['file']);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()
                ->route('admin.widgets.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function preview(UploadedWidget $widget)
    {
        $data = [
            'id' => $widget->slug,
            'name' => $widget->name,
            'category' => $widget->category,
            'icon' => $widget->icon,
            'schema' => $widget->schema(),
            'defaults' => $widget->defaults(),
            'source' => 'uploaded',
        ];

        return response()->json([
            'success' => true,
            'widget' => $data,
        ]);
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'slugs' => 'required|array',
            'slugs.*' => 'string|exists:rj_widgets,slug',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        $result = $this->uploader->bulkAction($validated['slugs'], $validated['action']);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }
}