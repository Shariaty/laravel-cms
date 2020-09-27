<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Modules\Articles\BlogCategory;
use Modules\Magazines\MagazineCategory;
use Modules\News\NewsCat;
use Modules\Portal\Portal;
use Modules\Portal\PortalAlias;
use Modules\Portfolio\PortfolioCategory;
use Modules\Products\ProductCategory;
use Modules\Sale\SaleInvoice;
use Modules\Warehouse\PurchaseInvoice;
use Modules\Warehouse\WarehouseOutGo;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * If specified, this namespace is automatically applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = null;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });

        // Route Defines
        Route::model('newsCat', NewsCat::class);
        Route::model('articleCat', BlogCategory::class);
        Route::model('magazineCat', MagazineCategory::class);
        Route::model('productCat', ProductCategory::class);
        Route::model('purchaseInvoice', PurchaseInvoice::class);
        Route::model('saleInvoice', SaleInvoice::class);
        Route::model('wareHouse', WarehouseOutGo::class);
        Route::model('wareHouse', WarehouseOutGo::class);
        Route::model('portfolioCat', PortfolioCategory::class);
        Route::model('portal', PortalAlias::class);
        Route::model('portalTask', Portal::class);
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });
    }
}
