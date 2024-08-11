<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    public function getMessageCount(): JsonResponse
    {
        $messageCount = ContactMessage::count();
        return response()->json(['count' => $messageCount]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        ContactMessage::create($validatedData);

        return response()->json(['message' => 'Contact message received successfully!'], 200);
    }

    public function index()
    {
        $messages = ContactMessage::all();
        return response()->json($messages);
    }

    public function destroy($id)
    {
        $message = ContactMessage::find($id);
        
        if ($message) {
            $message->delete();
            return Response::json(['message' => 'Message deleted successfully!'], 200);
        } else {
            return Response::json(['error' => 'Message not found'], 404);
        }
    }
}
