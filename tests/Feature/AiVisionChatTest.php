<?php

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\User;
use App\Services\NineRouterService;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('AiMessage stores and casts image attachments properly', function () {
    $user = User::factory()->create();
    $conversation = AiConversation::create([
        'user_id' => $user->id,
        'title'   => 'Test Vision Conversation',
        'model'   => 'Spark',
    ]);

    $message = AiMessage::create([
        'ai_conversation_id' => $conversation->id,
        'role'               => 'user',
        'content'            => 'Lihat gambar ini',
        'images'             => [
            'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        ],
    ]);

    expect($message->fresh()->images)->toBeArray();
    expect(count($message->fresh()->images))->toBe(1);
    expect($message->fresh()->images[0])->toStartWith('data:image/png;base64');
});
