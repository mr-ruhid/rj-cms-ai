<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function index()
    {
        return view('admin.support.index');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $body = "Name: {$validated['name']}\n";
            $body .= "Email: {$validated['email']}\n";
            $body .= "Subject: {$validated['subject']}\n\n";
            $body .= "Message:\n{$validated['message']}";

            Mail::raw($body, function ($message) use ($validated) {
                $message->to('rjnexus@hotmail.com')
                        ->subject('RJ CMS Support: ' . $validated['subject']);
            });

            return back()->with('success', __('Your support ticket has been submitted successfully. We will contact you soon.'));
        } catch (\Throwable $e) {
            return back()->with('error', __('Failed to send support ticket. Please try again later.'));
        }
    }

    public function faq()
    {
        return view('admin.support.faq');
    }

    public function chat()
    {
        return view('admin.support.chat');
    }
}