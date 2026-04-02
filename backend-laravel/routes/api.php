<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController,
    ActivityController,
    ContactController,
    EditorImageUploadController,
    NewsController,
    OlympiadController,
    SubjectController,
    SubscriptionController,
    TeamController,
    TelegramWebhookController,
    TestResultController};
use App\Http\Controllers\Api\Admin\{
    BookController,
    ReviewController,
    WishlistController,
    SubjectController as AdminSubjectController,
    TopicController as AdminTopicController,
    NewsController as AdminNewsController,
    RoleController as AdminRoleController,
    UserController as AdminUserController,
    OlympiadController as AdminOlympiadController,
    TeamMemberController as AdminTeamMemberController
};

/*
|--------------------------------------------------------------------------
| Public Routes (Ochiq yo'nalishlar)
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => response()->json(["message" => "Abutolib API Base URL"]));

// Auth & OTP
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('revoke-and-login', [AuthController::class, 'revokeAndLogin']);

    Route::post('otp/email/send',   [AuthController::class, 'sendEmailOtp']);
    Route::post('otp/email/verify', [AuthController::class, 'verifyEmailOtp']);
    Route::post('otp/telegram/send',   [AuthController::class, 'sendTelegramOtp']);
    Route::post('otp/telegram/verify', [AuthController::class, 'verifyTelegramOtp']);
});

// News & Content
Route::get('news',            [NewsController::class, 'index']);
Route::get('news/categories', [NewsController::class, 'categories']);
Route::get('news/{slug}',     [NewsController::class, 'show']);
Route::get('team',            [TeamController::class, 'index']);
Route::post('contact',        [ContactController::class, 'store']);

// Subjects & Education
Route::get('subjects',                   [SubjectController::class, 'index']);
Route::get('subjects/{slug}',            [SubjectController::class, 'show']);
Route::get('topics/{topicId}/questions', [SubjectController::class, 'questions']);
Route::get('subjects/file-download/{id}',[SubjectController::class, 'downloadFile']);

// Books & Reviews (Public parts)
Route::apiResource('books', BookController::class)->only(['index', 'show']);
Route::get('books/trending/all',  [BookController::class, 'trending']);
Route::get('books/top-rated/all', [BookController::class, 'topRated']);
Route::apiResource('books.reviews', ReviewController::class)->only(['index', 'show'])->shallow();

// Other Public
Route::get('olympiads/today', [OlympiadController::class, 'today']);
Route::post('telegram/webhook', [TelegramWebhookController::class, 'handle']);


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Faqat tizimga kirganlar uchun)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Plans
    Route::get('/plans', [SubscriptionController::class, 'index']);
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/my-subscription', [SubscriptionController::class, 'currentSubscription']);

    // Image upload
    Route::post('/upload/image', [EditorImageUploadController::class, 'upload']);

    // User Profile & Account
    Route::prefix('auth')->group(function () {
        Route::get('me',                [AuthController::class, 'me']);
        Route::post('logout',           [AuthController::class, 'logout']);
        Route::put('profile',           [AuthController::class, 'updateProfile']);
        Route::put('password',          [AuthController::class, 'updatePassword']);
        Route::get('sessions',          [AuthController::class, 'sessions']);
        Route::delete('sessions/{id}',  [AuthController::class, 'revokeSession']);
        Route::delete('sessions',       [AuthController::class, 'revokeAllSessions']);
    });

    // Books Interaction
    Route::get('books/{book}/download',       [BookController::class, 'download']);
    Route::post('books/{book}/reviews/user',  [ReviewController::class, 'userReview']);
    Route::post('reviews/{review}/helpful',   [ReviewController::class, 'markHelpful']);
    Route::post('books/{book}/reviews',       [ReviewController::class, 'store']);

    // Wishlist
    Route::apiResource('wishlists', WishlistController::class)->except(['update', 'show']);
    Route::get('wishlists/{book}/check', [WishlistController::class, 'check']);
    Route::get('wishlists/count',        [WishlistController::class, 'count']);

    // User Progress & Activity
    Route::get('user-activity/years', [ActivityController::class, 'getYears']);
    Route::get('user-activity',       [ActivityController::class, 'getHeatmapData']);

    Route::prefix('test')->group(function () {
        Route::post('save',            [TestResultController::class, 'save']);
        Route::get('results',          [TestResultController::class, 'index']);
        Route::get('results/{id}',     [TestResultController::class, 'show']);
        Route::get('resume/{topicId}', [TestResultController::class, 'resume']);
        Route::get('stats',            [TestResultController::class, 'stats']);
    });

    // Olympiads (User Side)
    Route::prefix('olympiads')->group(function () {
        Route::get('/',                     [OlympiadController::class, 'index']);
        Route::get('{olympiad}',            [OlympiadController::class, 'show']);
        Route::get('{olympiad}/questions',  [OlympiadController::class, 'questions']);
        Route::post('{olympiad}/start',     [OlympiadController::class, 'start']);
        Route::post('{olympiad}/submit',    [OlympiadController::class, 'submit']);
        Route::get('{olympiad}/leaderboard',[OlympiadController::class, 'leaderboard']);
        Route::get('{olympiad}/my-result',  [OlympiadController::class, 'myResult']);

        Route::post('{olympiad}/register',         [OlympiadController::class, 'register']);
        Route::delete('{olympiad}/unregister',     [OlympiadController::class, 'unregister']);
        Route::get('{olympiad}/my-registration',   [OlympiadController::class, 'myRegistration']);
        Route::get('{olympiad}/participants',      [OlympiadController::class, 'participants']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Admin paneli uchun)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        // Resources
        Route::apiResource('subjects',     AdminSubjectController::class);
        Route::apiResource('topics',       AdminTopicController::class);
        Route::apiResource('news',         AdminNewsController::class);
        Route::apiResource('olympiads',    AdminOlympiadController::class);
        Route::apiResource('team-members', AdminTeamMemberController::class);
        Route::apiResource('books',        BookController::class)->except(['index', 'show']);

        // User Management
        Route::apiResource('users', AdminUserController::class);
        Route::patch('users/{user}/toggle-active', [AdminUserController::class, 'toggleActive']);
        Route::put('users/{user}/roles/sync',      [AdminUserController::class, 'syncRoles']);
        Route::post('users/{user}/roles/assign',    [AdminUserController::class, 'assignRole']);
        Route::delete('users/{user}/roles/remove', [AdminUserController::class, 'removeRole']);

        // Roles & Permissions
        Route::get('permissions',   [AdminRoleController::class, 'permissions']);
        Route::apiResource('roles', AdminRoleController::class);

        // Specialized Admin Actions
        Route::post('olympiads/{olympiad}/import-questions', [AdminOlympiadController::class, 'importQuestions']);
    });
});

// Payment
Route::post('/payments/callback/{provider}', [SubscriptionController::class, 'handleWebhook']);
