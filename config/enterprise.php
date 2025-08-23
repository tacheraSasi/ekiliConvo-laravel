<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enterprise Features Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for enterprise-grade features in ekiliConvo
    |
    */

    'recording' => [
        'enabled' => env('ENTERPRISE_RECORDING_ENABLED', true),
        'storage_driver' => env('RECORDING_STORAGE_DRIVER', 'local'),
        'storage_path' => env('RECORDING_STORAGE_PATH', 'recordings'),
        'base_url' => env('RECORDING_BASE_URL', config('app.url') . '/recordings'),
        'max_duration' => 14400, // 4 hours in seconds
    ],

    'file_sharing' => [
        'enabled' => true,
        'max_file_size' => env('ENTERPRISE_MAX_FILE_SIZE', 52428800), // 50MB in bytes
        'allowed_types' => [
            'image/*',
            'video/*', 
            'audio/*',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/zip',
            'application/x-rar-compressed'
        ],
        'chunk_size' => 30000, // RTM message size consideration
    ],

    'connectivity' => [
        'turn_server' => [
            'url' => env('ENTERPRISE_TURN_SERVER_URL', 'stun:stun.agora.io:3478'),
            'username' => env('ENTERPRISE_TURN_SERVER_USERNAME', ''),
            'password' => env('ENTERPRISE_TURN_SERVER_PASSWORD', ''),
        ],
        'quality_monitoring' => [
            'enabled' => true,
            'check_interval' => 5000, // milliseconds
            'poor_quality_threshold' => 4,
        ],
        'reconnection' => [
            'max_attempts' => 5,
            'base_delay' => 2000, // milliseconds
        ],
    ],

    'security' => [
        'room_password' => [
            'min_length' => env('ROOM_PASSWORD_MIN_LENGTH', 4),
            'max_length' => env('ROOM_PASSWORD_MAX_LENGTH', 50),
            'require_complexity' => false,
        ],
        'audit_logs' => [
            'enabled' => true,
            'retention_days' => env('AUDIT_LOG_RETENTION_DAYS', 90),
        ],
        'role_permissions' => [
            'host' => [
                'mute_participants',
                'unmute_participants', 
                'remove_participants',
                'lock_room',
                'unlock_room',
                'set_password',
                'remove_password',
                'start_recording',
                'stop_recording',
                'view_audit_logs',
            ],
            'participant' => [
                'join_stream',
                'send_message',
                'share_file',
                'raise_hand',
                'share_screen',
                'send_reactions',
            ],
        ],
    ],

    'ui' => [
        'grid_layout' => [
            'max_visible_participants' => 16,
            'auto_switch_layout' => true,
            'layout_switch_threshold' => 4,
        ],
        'active_speaker' => [
            'enabled' => true,
            'volume_threshold' => 10,
            'highlight_duration' => 2000, // milliseconds
        ],
        'reactions' => [
            'enabled' => true,
            'available_reactions' => ['👍', '👏', '❤️', '😂', '🎉'],
            'animation_duration' => 2000, // milliseconds
        ],
    ],

    'agora' => [
        'app_id' => env('AGORA_APP_ID'),
        'app_certificate' => env('AGORA_APP_CERTIFICATE'),
        'customer_id' => env('AGORA_CUSTOMER_ID'),
        'customer_secret' => env('AGORA_CUSTOMER_SECRET'),
        'cloud_recording' => [
            'region' => 'CN',
            'mode' => 'mix',
            'transcoding_config' => [
                'width' => 1920,
                'height' => 1080,
                'fps' => 30,
                'bitrate' => 2000,
            ],
        ],
    ],
];