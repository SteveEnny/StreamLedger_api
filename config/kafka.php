<?php



return [
    'brokers' => env('KAFKA_BROKERS', 'kafka:29092'),
    'default_topic' => env('KAFKA_DEFAULT_TOPIC', 'laravel_events'),
    'timeout' => env('KAFKA_TIMEOUT', 5000),
];