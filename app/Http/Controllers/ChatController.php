<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewChatMessageNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function show(string $chatId): View
    {
        Carbon::setLocale('ru');

        $currentUser = auth()->user();

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

        if (! $chat) {
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

        $this->authorize('view', $chat);

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

    public function store(StoreMessageRequest $request, string $chatId): RedirectResponse
    {
        if (! Schema::hasTable('chats') || ! Schema::hasTable('messages')) {
            return redirect()
                ->route('chat.show', ['chat' => $chatId])
                ->with('chat_error', 'Чат недоступен: таблицы сообщений пока не созданы.');
        }

        $chat = Chat::query()->find($chatId);

        if (! $chat) {
            abort(404);
        }

        $this->authorize('sendMessage', $chat);

        $senderId = (int) auth()->id();
        $message = Message::query()->create([
            'chat_id' => $chat->id,
            'sender_user_id' => $senderId,
            'text' => $request->validated('text'),
        ]);

        $recipientId = $senderId === (int) $chat->employer_user_id
            ? (int) $chat->freelancer_user_id
            : (int) $chat->employer_user_id;

        $recipient = User::query()->find($recipientId);

        if ($recipient && ! $recipient->is_blocked) {
            $recipient->notify(new NewChatMessageNotification(
                senderName: (string) auth()->user()?->name,
                preview: Str::limit((string) $message->text, 120),
                chatUrl: route('chat.show', ['chat' => $chat->id]),
            ));
        }

        return redirect()->route('chat.show', ['chat' => $chat->id]);
    }

    public function messages(Request $request, string $chatId): JsonResponse
    {
        if (! Schema::hasTable('chats') || ! Schema::hasTable('messages')) {
            return response()->json(['messages' => []]);
        }

        $chat = Chat::query()->find($chatId);

        if (! $chat) {
            abort(404);
        }

        $this->authorize('view', $chat);

        $afterId = max(0, (int) $request->query('after', 0));

        $messages = Message::query()
            ->where('chat_id', $chat->id)
            ->when($afterId > 0, static fn ($query) => $query->where('id', '>', $afterId))
            ->orderBy('id')
            ->get(['id', 'sender_user_id', 'text', 'created_at'])
            ->map(static fn (Message $message): array => [
                'id' => (int) $message->id,
                'sender_user_id' => (int) $message->sender_user_id,
                'text' => (string) $message->text,
                'time' => $message->created_at?->format('H:i') ?? '',
            ])
            ->values();

        return response()->json(['messages' => $messages]);
    }
}