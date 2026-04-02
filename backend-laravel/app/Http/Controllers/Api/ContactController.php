<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        $contact = Contact::create($validated);

        return response()->json([
            'message' => 'Xabaringiz muvaffaqiyatli yuborildi. Tez orada javob beramiz!',
            'data'    => $contact,
        ], 201);
    }

    public function index(Request $request)
    {
        $contacts = Contact::orderByDesc('created_at')
            ->paginate(20);

        return response()->json($contacts);
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied',
        ]);

        $contact->update(['status' => $request->status]);

        return response()->json(['message' => 'Status yangilandi.', 'data' => $contact]);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['message' => 'O\'chirildi.']);
    }
}
