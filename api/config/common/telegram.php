<?php

declare(strict_types=1);

return [
    'config' => [
        'telegramBot' => [
            'token' => getenv('TELEGRAM_BOT_TOKEN'),
            'chatId' => getenv('TELEGRAM_CHAT_ID'),
        ]
    ]
];
