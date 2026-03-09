<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function show(string $chatId): View
    {
        Carbon::setLocale('ru');

        $currentUser = auth()->user();
        $canAccess = $currentUser && in_array($currentUser->role, ['freelancer', 'employer'], true);

        if (! $canAccess) {
            return view('chat', [
                'currentUser' => $currentUser,
                'canAccess' => false,
                'chat' => null,
                'otherUser' => null,
                'vacancy' => null,
                'chatMessages' => collect(),
                'notFound' => false,
            ]);
        }

        if (! Schema::hasTable('chats') || ! Schema::hasTable('messages')) {
            return view('chat', [
                'currentUser' => $currentUser,
                'canAccess' => true,
                'chat' => null,
                'otherUser' => null,
                'vacancy' => null,
                'chatMessages' => collect(),
                'notFound' => true,
            ]);
        }

        $chat = Chat::query()
            ->with(['vacancy', 'employer', 'freelancer', 'messages.sender'])
            ->find($chatId);

        if (! $chat || ! $this->isParticipant($chat, (int) $currentUser->id)) {
            return view('chat', [
                'currentUser' => $currentUser,
                'canAccess' => true,
                'chat' => null,
                'otherUser' => null,
                'vacancy' => null,
                'chatMessages' => collect(),
                'notFound' => true,
            ]);
        }

        $otherUser = $currentUser->role === 'freelancer' ? $chat->employer : $chat->freelancer;
        $chatMessages = $chat->messages->sortBy('created_at')->values();

        return view('chat', [
            'currentUser' => $currentUser,
            'canAccess' => true,
            'chat' => $chat,
            'otherUser' => $otherUser,
            'vacancy' => $chat->vacancy,
            'chatMessages' => $chatMessages,
            'notFound' => false,
        ]);
    }

    public function store(Request $request, string $chatId): RedirectResponse
    {
        $currentUser = auth()->user();

        if (! $currentUser || ! in_array($currentUser->role, ['freelancer', 'employer'], true)) {
            abort(403);
        }

        if (! Schema::hasTable('chats') || ! Schema::hasTable('messages')) {
            return redirect()
                ->route('chat.show', ['chat' => $chatId])
                ->with('chat_error', 'Чат недоступен: таблицы сообщений пока не созданы.');
        }

        $chat = Chat::find($chatId);

        if (! $chat || ! $this->isParticipant($chat, (int) $currentUser->id)) {
            abort(404);
        }

        $data = $request->validate([
            'text' => ['required', 'string', 'max:5000'],
        ]);

        Message::create([
            'chat_id' => $chat->id,
            'sender_user_id' => $currentUser->id,
            'text' => trim((string) $data['text']),
        ]);

        return redirect()->route('chat.show', ['chat' => $chat->id]);
    }

    private function isParticipant(Chat $chat, int $userId): bool
    {
        return (int) $chat->employer_user_id === $userId || (int) $chat->freelancer_user_id === $userId;
    }
}
