<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ChatsController extends Controller
{
    public function index(): View
    {
        Carbon::setLocale('ru');

        $currentUser = auth()->user();
        $canAccess = $currentUser && in_array($currentUser->role, ['freelancer', 'employer'], true);

        $myChats = collect();

        if ($canAccess && Schema::hasTable('chats') && Schema::hasTable('messages')) {
            $myChats = Chat::query()
                ->with([
                    'vacancy',
                    'employer',
                    'freelancer',
                    'messages' => static fn ($query) => $query->latest('created_at'),
                ])
                ->where('employer_user_id', $currentUser->id)
                ->orWhere('freelancer_user_id', $currentUser->id)
                ->get()
                ->map(function (Chat $chat) use ($currentUser): ?array {
                    $otherUser = $currentUser->role === 'freelancer' ? $chat->employer : $chat->freelancer;
                    $lastMessage = $chat->messages->first();

                    if (! $chat->vacancy || ! $otherUser) {
                        return null;
                    }

                    $lastActivityAt = $lastMessage?->created_at ?? $chat->created_at;
                    $isLastMessageMine = $lastMessage
                        ? (int) $lastMessage->sender_user_id === (int) $currentUser->id
                        : false;

                    return [
                        'id' => $chat->id,
                        'vacancy_title' => $chat->vacancy->title,
                        'other_user_name' => $otherUser->name,
                        'last_message_text' => $lastMessage?->text,
                        'last_message_at' => $lastMessage?->created_at,
                        'is_last_message_mine' => $isLastMessageMine,
                        'last_activity_at' => $lastActivityAt,
                    ];
                })
                ->filter()
                ->sortByDesc(static fn (array $chat): int => $chat['last_activity_at']?->getTimestamp() ?? 0)
                ->values();
        }

        return view('chats', [
            'currentUser' => $currentUser,
            'canAccess' => $canAccess,
            'myChats' => $myChats,
        ]);
    }
}
