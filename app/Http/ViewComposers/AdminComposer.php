<?php

namespace App\Http\ViewComposers;

use App\Facade\Facades\OilSettings;
use App\User;
use Carbon\Carbon;
use Illuminate\View\View;
use Modules\Contact\Contact;
use Modules\Sale\SaleInvoice;
use Modules\Stores\Store;
use Opilo\Farsi\JalaliDate;


class AdminComposer
{
    protected $adminUser;
    protected $jalaliDate;
    protected $miladiDate;
    protected $settings;
    protected $totalStores;
    protected $publicUsers;
    protected $messageContactCounter;
    protected $commentsCounter;
    protected $ordersCounter;

    public function __construct()
    {
        $this->adminUser = getCurrentAdminUser();
        $this->jalaliDate = JalaliDate::fromDateTime( Carbon::now() )->format('D ، d M y');
        $this->miladiDate = Carbon::now()->format('Y-M-d');
        $this->settings = OilSettings::get(['site', 'social', 'map', 'credit']);
        $this->publicUsers = User::count();

        if(hasModule('Contact')){
            $this->messageContactCounter = Contact::unread()->count();
        }

        if(hasModule('Stores')){
            $this->totalStores = Store::count();
        }

        if(hasModule('Sale')){
            $this->ordersCounter = (new SaleInvoice)->where('type' , SITE_ORDER)->where('status' , ORDER_WAIT_TO_CONFIRM)->count();
        }

        if(hasModule('Comments')){
            $this->commentsCounter = \Modules\Comments\Comment::notApproved()->count();
        }
    }

    public function compose(View $view)
    {
        $view->with('adminUser'  , $this->adminUser);
        $view->with('jalaliDate' , $this->jalaliDate);
        $view->with('miladiDate' , $this->miladiDate);
        $view->with('settings'   , $this->settings);
        $view->with('totalStores'   , $this->totalStores);
        $view->with('publicUsers'   , $this->publicUsers);

        if(hasModule('Contact')){
            $view->with('messageContactCounter'   , $this->messageContactCounter);
        }

        if(hasModule('Comments')){
            $view->with('commentsCounter'   , $this->commentsCounter);
        }

        if(hasModule('Sale')){
            $view->with('ordersCounter'   , $this->ordersCounter);
        }
    }
}
