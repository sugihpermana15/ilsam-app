<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'tel' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
            'inquiries' => ['nullable', 'string', 'max:150'],
            'textarea' => ['required', 'string', 'max:3000'],
        ];

        $messages = [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email format is not valid.',
            'textarea.required' => 'Project details is required.',
        ];

        // The template ships with vendor/ajax-form.js which posts via AJAX and
        // expects a plain-text response (not JSON and not a redirect).
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $html = '<ul class="mb-0">';
                foreach ($validator->errors()->all() as $error) {
                    $html .= '<li>' . e($error) . '</li>';
                }
                $html .= '</ul>';

                return response($html, 422);
            }

            $data = $validator->validated();

            Mail::to('market.ilsamindonesia@yahoo.com')->send(
                new ContactMessage(
                    $data,
                    $request->ip(),
                    (string) $request->userAgent(),
                )
            );

            return response('Thank you. Your message has been sent.', 200);
        }

        $data = $request->validate($rules, $messages);

        Mail::to('market.ilsamindonesia@yahoo.com')->send(
            new ContactMessage(
                $data,
                $request->ip(),
                (string) $request->userAgent(),
            )
        );

        return back()->with('success', 'Thank you. Your message has been sent.');
    }
}
