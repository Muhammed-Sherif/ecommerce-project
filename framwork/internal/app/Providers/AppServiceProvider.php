<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind accounts\domains\contracts\Iuser to accounts\infrastructure\repositries\IuserImp
        $this->app->bind(\accounts\domains\contracts\Iuser::class, \accounts\infrastructure\repositries\IuserImp::class);

        // Bind accounts\application\queries\checkUserCrediants to itself
        $this->app->bind(\accounts\application\queries\checkUserCrediants::class, function ($app) {
            return new \accounts\application\queries\checkUserCrediants($app->make(\accounts\domains\contracts\Iuser::class));
        });

        $this->app->bind(\accounts\domains\contracts\ISanctumToken::class, \framwork\shared\SanctumTokenImp::class);

        // Products
        $this->app->bind(\products\domains\contracts\IProductRepository::class, \products\infrastructure\repositories\ProductRepository::class);
        $this->app->bind(\products\domains\contracts\IInventoryCommandsGetway::class, \products\infrastructure\getway\InventoryCommnadsGetway::class);
        $this->app->bind(\products\domains\contracts\IImageUploadService::class, \products\infrastructure\services\ImageUploadService::class);

        // Orders
        $this->app->bind(\orders\domains\contracts\IOrderRepository::class, \orders\infrastructure\repositories\OrderRepository::class);

        // Cart
        $this->app->bind(\cart\domains\contracts\ICartRepository::class, \cart\infrastructure\repositories\CartRepository::class);
        $this->app->bind(\cart\domains\contracts\ICartApi::class, \cart\shared\CartApi::class);
        $this->app->bind(\cart\domains\contracts\IInventoryQueriesGetway::class, \cart\infrastructure\getway\InventoryQueriesGetway::class);

        // Comments
        $this->app->bind(\comments\domains\contracts\ICommentRepository::class, \comments\infrastructure\repositories\CommentRepository::class);

        // Coupons
        $this->app->bind(\Coupons\domains\contracts\ICouponRepository::class, \Coupons\infrastructure\repositories\CouponRepository::class);
        $this->app->bind(\Coupons\domains\contracts\IProductGateway::class, \Coupons\infrastructure\getway\ProductGateway::class);
        $this->app->bind(\Coupons\domains\contracts\IUserGateway::class, \Coupons\infrastructure\getway\UserGateway::class);
        $this->app->bind(\Coupons\domains\contracts\ICartGateway::class, \Coupons\infrastructure\getway\CartGateway::class);
        $this->app->bind(\Coupons\domains\contracts\ICouponApi::class, \Coupons\shared\CouponApi::class);
        $this->app->bind(\Coupons\domains\contracts\ICouponValidationApi::class, \Coupons\shared\CouponValidationApi::class);

        // Reviews
        $this->app->bind(\reviews\domains\contracts\IReviewRepository::class, \reviews\infrastructure\repositories\ReviewRepository::class);
        $this->app->bind(\reviews\domains\contracts\IOrdersQueriesGetway::class, \reviews\infrastructure\getway\OrdersQueriesGetway::class);
        // Orders
        $this->app->bind(\orders\domains\contracts\IOrderRepository::class, \orders\infrastructure\repositories\OrderRepository::class);
        $this->app->bind(\orders\domains\contracts\ICouponGateway::class, \orders\infrastructure\getway\CouponGateway::class);
        $this->app->bind(\orders\domains\contracts\ICouponValidationGateway::class, \orders\infrastructure\getway\CouponValidationGateway::class);
        $this->app->bind(\orders\domains\contracts\ICartGateway::class, \orders\infrastructure\getway\CartGateway::class);
        $this->app->bind(\orders\domains\contracts\IPaymentGateway::class, \orders\infrastructure\getway\PaymentGateway::class);
        $this->app->bind(\orders\internal\IOrdersQueriesApi::class, \orders\shared\OrdersQueriesApiImp::class);

        // Shipping address
        $this->app->bind(\accounts\domains\contracts\IShippingAddressRepository::class, \accounts\infrastructure\repositries\ShippingAddressRepository::class);

        // Payments
        $this->app->bind(\payments\shared\IPaymentApi::class, \payments\shared\PaymentApi::class);

        // Inventory
        $this->app->bind(\inventory\domains\contracts\IInventoryRepository::class, \inventory\infrastructure\repositories\InventoryRepository::class);
        $this->app->bind(\inventory\domains\contracts\IStockMovementRepository::class, \inventory\infrastructure\repositories\StockMovementRepository::class);
        $this->app->bind(\inventory\shared\IInventoryCommandsGetway::class, \inventory\internal\InventoryCommandsApiImp::class);
        $this->app->bind(\inventory\shared\IInventoryQueriesGetway::class, \inventory\internal\InventoryQueriesApiImp::class);
        $this->app->bind(\inventory\shared\IInventoryQueriesGetway::class, \inventory\internal\InventoryQueriesApiImp::class);

        // Shipments
        $this->app->bind(\shipments\domains\contracts\Ishipment::class, \shipments\infrastructure\repositories\ShipmentRepository::class);

        // Event Bus
        $this->app->singleton(\shared\IEventBus::class, \shared\EventBus::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
