
<?php
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    return response()->json([
        'token' => $request->user()->createToken('api-token')->plainTextToken
    ]);
});
route::get('/', function (Request $request) {
    return response()->json(['message' => 'Logged out successfully']);
});