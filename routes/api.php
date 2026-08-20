<?php

use App\Http\Controllers\NlqController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NLQ API route
|--------------------------------------------------------------------------
|
| Merge this route into your application's routes/api.php file.
|
*/

Route::post('/nlq/ask', [NlqController::class, 'ask'])
    ->middleware(['auth:sanctum', 'throttle:20,1']);
