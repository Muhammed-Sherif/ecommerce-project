<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use orders\api\controllers\CheckoutController;

use accounts\api\controllers\AuthController;
use products\api\controllers\ProductController;
use cart\api\controllers\CartController;
use cms\api\controllers\SettingController;
use comments\api\controllers\CommentController;
// use referment\api\controllers\RefermentController;
use Coupons\api\controllers\CouponController;
use reviews\api\controllers\ReviewController;
use orders\api\controllers\OrderController;
use accounts\api\controllers\ProfileController;
use payments\api\controllers\PaymentController;
use inventory\infrastructure\controllers\InventoryController;

// CMS / Settings - Public for reading, Protected for writing
Route::get('/settings', [SettingController::class, 'index']);

// Public routes
Route::post('/auth/register', function (Request $request, AuthController $controller) {
    return response()->json($controller->register($request->all(), app(\accounts\application\commands\CreateUserHandler::class)));
});

Route::post('/auth/login', function (Request $request, AuthController $controller) {
    return response()->json($controller->login($request->all()));
});

// Products - Public
Route::get('/products', function (ProductController $controller) {
    return response()->json($controller->index(app(\products\application\queries\GetAllProductsHandler::class)));
});

Route::get('/products/{id}', function ($id, ProductController $controller) {
    return response()->json($controller->show($id, app(\products\application\queries\GetProductHandler::class)));
});

// Comments - Public (read-only)
Route::get('/comments', function (CommentController $controller) {
    return response()->json($controller->index(app(\comments\application\queries\GetAllCommentsHandler::class)));
});

Route::get('/comments/{id}', function ($id, CommentController $controller) {
    return response()->json($controller->show($id, app(\comments\application\queries\GetCommentHandler::class)));
});

Route::get('/products/{productId}/comments', function ($productId, CommentController $controller) {
    return response()->json($controller->byProduct($productId, app(\comments\application\queries\GetCommentsByProductHandler::class)));
});

// Reviews - Public (read-only)
Route::get('/products/{productId}/reviews', function ($productId, ReviewController $controller) {
    return response()->json($controller->byProduct($productId, app(\reviews\application\queries\GetProductReviewsHandler::class)));
});

// Referments - Public (read-only)
// Route::get('/referments', function (RefermentController $controller) {
//     return response()->json($controller->index(app(\referment\application\queries\GetAllRefermentsHandler::class)));
// });

// // Route::get('/referments/{id}', function ($id, RefermentController $controller) {
    //return response()->json($controller->show($id, app(\referment\application\queries\GetRefermentHandler::class)));
//});

// Coupons - Public (read-only)
Route::get('/coupons', function (CouponController $controller) {
    return response()->json($controller->index(app(\Coupons\application\queries\GetAllCouponsHandler::class)));
});

Route::get('/coupons/{id}', function ($id, CouponController $controller) {
    return response()->json($controller->show($id, app(\Coupons\application\queries\GetCouponHandler::class)));
});

Route::get('/coupons/code/{code}', function ($code, CouponController $controller) {
    return response()->json($controller->byCode($code, app(\Coupons\application\queries\GetCouponByCode::class)));
});

Route::get('/coupons/validate/{code}', function ($code, CouponController $controller) {
    return response()->json($controller->checkValidityByCode($code, app(\Coupons\application\queries\CheckValidityOfCouponByCodeHandler::class)));
});

// Payment Webhooks - Public
Route::post('/payments/{gateway}/webhook', function ($gateway, Request $request, PaymentController $controller) {
    \Log::info('Payment Webhook: ' . json_encode($request->all()));
    return $controller->webhook($gateway, $request);
});

// Protected routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\InitializeTenancyByUser::class])->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
    Route::post('/auth/logout', function (AuthController $controller) {
        return response()->json($controller->logout());
    });
    
    Route::get('/auth/me', function (AuthController $controller) {
        return response()->json($controller->getCurrentUser());
    });
    
    Route::get('/users', function (AuthController $controller) {
        return response()->json($controller->getAll());
    });
    
    Route::get('/users/{id}', function ($id, AuthController $controller) {
        return response()->json($controller->getById($id));
    });
    
    Route::put('/users/{id}', function ($id, Request $request, AuthController $controller) {
        return response()->json($controller->update($id, $request->all()));
    });
    
    Route::delete('/users/{id}', function ($id, AuthController $controller) {
        return response()->json($controller->delete($id));
    });

    // Products - Protected (Admin only in real app, but for now just protected)
    Route::post('/products', function (Request $request, ProductController $controller) {
        return response()->json($controller->store($request->all(), app(\products\application\commands\CreateProductHandler::class)));
    });

    Route::put('/products/{id}', function ($id, Request $request, ProductController $controller) {
        return response()->json($controller->update($id, $request->all(), app(\products\application\commands\UpdateProductHandler::class)));
    });

    Route::delete('/products/{id}', function ($id, ProductController $controller) {
        return response()->json($controller->destroy($id, app(\products\application\commands\DeleteProductHandler::class)));
    });

    // Cart
    Route::get('/cart', function (Request $request, CartController $controller) {
        return response()->json($controller->index($request->user()->id, app(\cart\application\queries\GetCartHandler::class)));
    });

    Route::post('/cart', function (Request $request, CartController $controller) {
        return response()->json($controller->store($request->user()->id, $request->all(), app(\cart\application\commands\AddToCartHandler::class)));
    });

    Route::put('/cart', function (Request $request, CartController $controller) {
        return response()->json($controller->update($request->user()->id, $request->all(), app(\cart\application\commands\UpdateCartItemHandler::class)));
    });

    Route::delete('/cart', function (Request $request, CartController $controller) {
        return response()->json($controller->destroy($request->user()->id, $request->all(), app(\cart\application\commands\RemoveCartItemHandler::class)));
    });

    Route::delete('/cart/clear', function (Request $request, CartController $controller) {
        return response()->json($controller->clear($request->user()->id, app(\cart\application\commands\ClearCartHandler::class)));
    });

    // Comments - Protected (write)
    Route::post('/comments', function (Request $request, CommentController $controller) {
        $payload = array_merge($request->all(), ['user_id' => $request->user()->id]);
        return response()->json($controller->store($payload, app(\comments\application\commands\CreateCommentHandler::class)));
    });

    Route::put('/comments/{id}', function ($id, Request $request, CommentController $controller) {
        return response()->json($controller->update($id, $request->all(), app(\comments\application\commands\UpdateCommentHandler::class)));
    });

    Route::delete('/comments/{id}', function ($id, CommentController $controller) {
        return response()->json($controller->destroy($id, app(\comments\application\commands\DeleteCommentHandler::class)));
    });

    // Reviews - Protected (write)
    Route::post('/reviews', function (Request $request, ReviewController $controller) {
        $payload = array_merge($request->all(), [
            'user_id' => $request->user()->id,
            'user_name' => $request->user()->name ?? 'Anonymous'
        ]);
        return response()->json($controller->store($payload, app(\reviews\application\commands\CreateReviewHandler::class)));
    });

    Route::put('/reviews/{id}/approve', function ($id, ReviewController $controller) {
        return response()->json($controller->approve($id, app(\reviews\application\commands\ApproveReviewHandler::class)));
    });

    Route::delete('/reviews/{id}', function ($id, ReviewController $controller) {
        return response()->json($controller->destroy($id, app(\reviews\application\commands\DeleteReviewHandler::class)));
    });

    // Profile - Shipping
    Route::get('/profile/shipping', function (Request $request, ProfileController $controller) {
        return response()->json($controller->getShipping($request->user()->id, app(\accounts\application\queries\GetShippingAddressHandler::class)));
    });

    Route::put('/profile/shipping', function (Request $request, ProfileController $controller) {
        return response()->json($controller->updateShipping($request->user()->id, $request->all(), app(\accounts\application\commands\UpsertShippingAddressHandler::class)));
    });

    // // Referments - Protected (read own)
    // Route::get('/referments/me', function (Request $request, RefermentController $controller) {
    //     return response()->json($controller->byUser($request->user()->id, app(\referment\application\queries\GetRefermentsByUserHandler::class)));
    // });

    // // Referments - Protected (write)
    // Route::post('/referments', function (Request $request, RefermentController $controller) {
    //     $payload = array_merge($request->all(), ['user_id' => $request->user()->id]);
    //     return response()->json($controller->store($payload, app(\referment\application\commands\CreateRefermentHandler::class)));
    // });

    // Route::put('/referments/{id}', function ($id, Request $request, RefermentController $controller) {
    //     return response()->json($controller->update($id, $request->all(), app(\referment\application\commands\UpdateRefermentHandler::class)));
    // });

    // Route::delete('/referments/{id}', function ($id, RefermentController $controller) {
    //     return response()->json($controller->destroy($id, app(\referment\application\commands\DeleteRefermentHandler::class)));
    // });

    // Coupons - Protected (write)
    Route::post('/coupons', function (Request $request, CouponController $controller) {
        return response()->json($controller->store($request->all(), app(\Coupons\application\commands\CreateCouponHandler::class)));
    });

    Route::put('/coupons/{id}', function ($id, Request $request, CouponController $controller) {
        return response()->json($controller->update($id, $request->all(), app(\Coupons\application\commands\UpdateCouponHandler::class)));
    });

    Route::delete('/coupons/{id}', function ($id, CouponController $controller) {
        return response()->json($controller->destroy($id, app(\Coupons\application\commands\DeleteCouponHandler::class)));
    });

    // Orders
    Route::get('/orders', function (Request $request, OrderController $controller) {
        return $controller->index($request);
    });

    Route::get('/orders/{id}', function (Request $request, $id, OrderController $controller) {
        return $controller->show($request, $id);
    });

    Route::post('/orders', function (Request $request, OrderController $controller) {
        return $controller->create($request);
    });

    Route::put('/orders/{id}/status', function (Request $request, $id, OrderController $controller) {
        return $controller->updateStatus($request, $id);
    });

    Route::post('/orders/{id}/cancel', function (Request $request, $id, OrderController $controller) {
        return $controller->cancel($request, $id);
    });

    // CMS Settings Update
    Route::post('/settings', [SettingController::class, 'update']);

    // Inventory
    Route::get('/inventory/product/{productId}', function (Request $request, $productId, InventoryController $controller) {
        return $controller->getByProduct($request, $productId);
    });

    Route::post('/inventory/adjust', function (Request $request, InventoryController $controller) {
        return $controller->adjust($request);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
