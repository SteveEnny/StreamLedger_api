<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Services\KafkaProducerService;
use Illuminate\Support\Facades\Route;



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);


Route::group(['middleware' => 'auth:sanctum'], function() {
    /// Wallet Route
    Route::get('wallet', [WalletController::class, 'wallet']);


    /// Transaction Routes
    Route::resource('transaction', TransactionController::class)->only(['index', 'store']);

    Route::get('transaction/export', [TransactionController::class, 'export']);
});

// Route::get('/test-kafka-service', function (KafkaProducerService $kafka) {
//     // Test simple message
//     $kafka->sendMessage('test_topic', 'Hello from service!');

//     // Test JSON message
//     $kafka->sendJson('events', ['test' => 'data', 'time' => now()]);

//     // Test user event
//     $kafka->sendUserEvent(1, 'test_action', ['key' => 'value']);

//     // Test notification
//     $kafka->sendNotification('info', 'System started', ['version' => '1.0']);

//     return 'All messages sent!';
// });

// Route::get('kafka-test', function () {
//        try {
//            $conf = new \RdKafka\Conf();
//            $conf->set('metadata.broker.list', 'kafka:29092');

//            $producer = new \RdKafka\Producer($conf);
//            $topic = $producer->newTopic('laravel_test');

//            $message = json_encode([
//                'test' => 'Hello from Laravel!',
//                'timestamp' => now()
//            ]);

//            $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
//            $producer->poll(0);
//            $result = $producer->flush(1000);

//            return response()->json([
//                'status' => $result === RD_KAFKA_RESP_ERR_NO_ERROR ? 'Success' : 'Failed',
//                'message' => 'Message sent to Kafka'
//            ]);
//        } catch (Exception $e) {
//            return response()->json(['error' => $e->getMessage()], 500);
//        }
//    });