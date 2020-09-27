<!-- minification -->

<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <title> @if(!empty($title)) {{$settings->title->value}} | {{$title}} @else {{$settings->title->value}} | Management Area @endif</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="author" content="www.alashariaty.ir">

    <script src="{{asset('assets/plugins/pace/pace.min.js')}}"></script>
    <link href="{{asset('assets/plugins/pace/pace.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/plugins/simple-line-icons/simple-line-icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{asset('assets/plugins/bootstrap-toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/plugins/sweetalert2/dist/sweetalert2.css')}}" rel="stylesheet" type="text/css" />
    <!-- BEGIN THEME GLOBAL STYLES -->

    @yield('header')
    @yield('moduleHeader')

    <link href="{{asset('assets/admin/css/components.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{asset('assets/admin/css/plugins.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{asset('assets/admin/css/layout.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/admin/css/themes/default.min.css')}}" rel="stylesheet" type="text/css" id="style_color" />
    <link href="{{asset('assets/admin/css/custom.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/admin/css/custom.css')}}" rel="stylesheet" type="text/css" />

    <!--[if IE]>
    <link href="{{asset('assets/admin/custom/css/ie.css')}}" rel="stylesheet" type="text/css" />
    <![endif]-->

    <link rel="shortcut icon" href="{{asset('assets/images/icons/favicon.ico')}}" />

    <script>
        var Path                    = '{{ asset('') }}'+'administrator/';
        var PublicPath              = '{{ asset('') }}';
        var ApiPath                 = '{{ asset('') }}'+'api/';
        var _csrf_token             = '{{ csrf_token() }}';
    </script>

</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">
<input type="hidden" id="myIsCredit" class="myIsCredit" name="isCredit" value="1" />
<div class="page-wrapper">
    <!-- BEGIN HEADER -->
    <div class="page-header navbar navbar-fixed-top">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner ">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="{{route('admin.dashboard')}}">
                    <img src="{{asset('assets/admin/images/logo-horizental-light.png')}}" alt="logo" class="logo-default" /> </a>
                <div class="menu-toggler sidebar-toggler">
                    <span></span>
                </div>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                <span></span>
            </a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            @if(!empty($adminUser))
                                @if($adminUser->img)
                                <img class="img-circle" id="header-company-logo" src="{{asset('uploads/admins/profile-pictures/'.$adminUser->img)}}" />
                                @else
                                <img  class="img-circle" id="header-company-logo" src="{{ asset('assets/admin/images/profile-placeholder.jpg') }}" />
                                @endif
                                <span class="username username-hide-on-mobile">
                                    {{ ( $adminUser AND (!empty( $adminUser->name) )) ? $adminUser->name :$adminUser->email }}
                                </span>
                                <i class="fa fa-angle-down"></i>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li class="{{(strpos(URL::current(),url('employer/company')) !== false ) ? 'active': '' }}">
                                <a href="{{route('admin.profile')}}">
                                    <i class="icon-user"></i>Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{route('admin.lock')}}" id="confirmation-lock">
                                    <i class="icon-lock"></i> Lock Screen
                                </a>
                            </li>
                            <li>
                                <a href="{{route('admin.logout')}}" id="confirmation-logout">
                                    <i class="icon-key"></i> Log Out
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->

                    <li class="dropdown">
                        <a href="{{config('app.url')}}" title="View website" target="_blank" class="dropdown-toggle" data-hover="dropdown" data-close-others="true" aria-expanded="false">
                            <i class="fa fa-eye"></i>
                        </a>
                        <ul class="dropdown-menu">

                        </ul>
                    </li>

                    @can(config('permissions.PERMISSION_SETTINGS'))
                    <li class="dropdown">
                        <a href="{{route('admin.site.settings')}}" title="Settings" class="dropdown-toggle" data-hover="dropdown" data-close-others="true" aria-expanded="false">
                            <i class="fa fa-gear"></i>
                        </a>
                        <ul class="dropdown-menu">

                        </ul>
                    </li>
                    @endcan

                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END HEADER INNER -->
    </div>
    <!-- END HEADER -->
    <!-- BEGIN HEADER & CONTENT DIVIDER -->
    <div class="clearfix"> </div>
    <!-- END HEADER & CONTENT DIVIDER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <!-- BEGIN SIDEBAR -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <div class="page-sidebar navbar-collapse collapse">
                <!-- BEGIN SIDEBAR MENU -->
                <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 0px">
                    <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    <li class="sidebar-toggler-wrapper hide">
                        <div class="sidebar-toggler">
                            <span></span>
                        </div>
                    </li>
                    <!-- END SIDEBAR TOGGLER BUTTON -->

                    <li class="sidebar-search-wrapper text-center">
                        <div style="padding: 10px;" class="margin-top-15">
                            <div class="grow">
                            <a href="{{route('admin.profile')}}">
                                @if(getCurrentAdminUser()->img)
                                    <img class="img-thumbnail sidebar-logo" style="width: 120px;border-radius: 10px !important;" id="header-company-logo" src="{{asset('uploads/admins/profile-pictures/'.getCurrentAdminUser()->img)}}" />
                                @else
                                    <img class="img-thumbnail sidebar-logo" style="width: 120px;border-radius: 10px !important;box-shadow: 0px 0px 35px #eee;opacity: 0.4;" id="header-company-logo" src="{{ asset('assets/admin/images/profile-placeholder.jpg') }}" />
                                @endif
                            </a>
                            </div>
                            <h4 class="bold company_name" style="color: #eef4f7;">{{getCurrentAdminUser()->name}}</h4>
                        </div>
                    </li>

                    <li class="nav-item {{(strpos(URL::current(),url('administrator/dashboard')) !== false ) ? 'active open': '' }}">
                        <a href="{{route('admin.dashboard')}}" class="nav-link nav-toggle">
                            <i class="icon-home"></i>
                            <span class="title">Dashboard</span>
                            <span class="selected"></span>
                        </a>
                    </li>

                    @can(config('permissions.PERMISSION_ADMIN_USERS'))
                    <li class="nav-item {{ (strpos(URL::current(),url('administrator/users')) !== false ) || (strpos(URL::current(),url('administrator/roles')) !== false ) ? 'active open': '' }}">
                        <a href="javascript:;" class="nav-link nav-toggle ">
                            <i class="fa icon-users"></i>
                            <span class="title">Admin Users</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item {{(strpos( URL::current(),url('administrator/users')) !== false ) ? 'active open': '' }}">
                                <a href="{{route('admin.users')}}" class="nav-link nav-toggle">
                                    <i class="fa fa-user"></i>
                                    <span class="title">Admin Users</span>
                                    <span class="selected"></span>
                                </a>
                            </li>

                            <li class="nav-item {{(strpos(URL::current(),url('administrator/roles')) !== false ) ? 'active open': '' }}">
                                <a href="{{route('roles.index')}}" class="nav-link nav-toggle">
                                    <i class="fa fa-user-secret"></i>
                                    <span class="title">Roles</span>
                                    <span class="selected"></span>
                                </a>
                            </li>

                        </ul>
                    </li>
                    @endcan


                    @can(config('permissions.PERMISSION_PUBLIC_USERS'))
                    <li class="nav-item {{(strpos(URL::current(),url('administrator/publicUsers')) !== false ) ? 'active open': '' }}">
                        <a href="{{route('admin.publicUsers')}}" class="nav-link nav-toggle">
                            <i class="fa fa-user"></i>
                            <span class="title">Public Users</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endcan



                    @can(config('permissions.PERMISSION_FILE_MANAGER'))
                    <li class="nav-item {{(strpos(URL::current(),url('administrator/mediaManager')) !== false ) ? 'active open': '' }}">
                        <a href="{{route('admin.mediaManager')}}" class="nav-link nav-toggle">
                            <i class="fa fa-file-archive-o"></i>
                            <span class="title">File Manager</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endcan

                    @can(config('permissions.PERMISSION_STATIC_PAGES'))
                    <li class="nav-item {{(strpos(URL::current(),url('administrator/pages/list')) !== false ) ? 'active open': '' }}">
                            <a href="{{route('admin.pages.list')}}" class="nav-link ">
                                <i class="fa fa-columns"></i>
                                <span class="title">Static Pages</span>
                            </a>
                        </li>
                    @endcan

                    @if( hasModule('Skill') )
                        @can(config('permissions.PERMISSION_SKILLS'))
                        <li class="nav-item {{(strpos(URL::current(),url('administrator/skills')) !== false ) ? 'active open': '' }}">
                            <a href="javascript:;" class="nav-link nav-toggle ">
                                <i class="fa fa-bolt"></i>
                                <span class="title">Skills</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item start {{(strpos(URL::current(),url('administrator/skills/categories')) !== false ) ? 'active open': '' }} ">
                                    <a href="{{route('admin.skills.categories')}}" class="nav-link ">
                                        <i class="fa fa-folder"></i>
                                        <span class="title">Categories</span>
                                    </a>
                                </li>
                                <li class="nav-item start {{(strpos(URL::current(),url('administrator/skills/list')) !== false ) ? 'active open': '' }} ">
                                    <a href="{{ route('admin.skills.list') }}" class="nav-link ">
                                        <i class="fa fa-bolt"></i>
                                        <span class="title">Skills</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                    @endif


                    @if( hasModule('SlideShow') )
                        @can(config('permissions.PERMISSION_SLIDESHOW'))
                            <li class="nav-item {{(strpos(URL::current(),url('administrator/slide-show/list')) !== false ) ? 'active open': '' }} ">
                                <a href="{{ route('admin.slide.list') }}" class="nav-link ">
                                    <i class="fa fa-file-image-o"></i>
                                    <span class="title">Slide Show</span>
                                </a>
                            </li>
                        @endif
                    @endif


                    @if( hasModule('Project') )
                        @can(config('permissions.PERMISSION_PROJECTS'))
                            <li class="nav-item {{(strpos(URL::current(),url('administrator/projects')) !== false ) ? 'active open': '' }}">
                                <a href="javascript:;" class="nav-link nav-toggle ">
                                    <i class="fa fa-briefcase"></i>
                                    <span class="title">Projects</span>
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item start {{(strpos(URL::current(),url('administrator/projects/categories')) !== false ) ? 'active open': '' }} ">
                                        <a href="{{route('admin.projects.categories')}}" class="nav-link ">
                                            <i class="fa fa-folder"></i>
                                            <span class="title">Categories</span>
                                        </a>
                                    </li>
                                    <li class="nav-item start {{(strpos(URL::current(),url('administrator/projects/list')) !== false ) ? 'active open': '' }} ">
                                        <a href="{{ route('admin.projects.list') }}" class="nav-link ">
                                            <i class="fa fa-briefcase"></i>
                                            <span class="title">Projects</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if( hasModule('Stores') )
                        @can(config('permissions.PERMISSION_STORES'))
                            <li class="nav-item {{(strpos(URL::current(),url('administrator/stores/list')) !== false ) ? 'active open': '' }}">
                                <a href="{{route('admin.stores.list')}}" class="nav-link ">
                                    <i class="icon icon-basket-loaded"></i>
                                    <span class="title">Stores</span>
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if( hasModule('Products') )
                        @can(config('permissions.PERMISSION_PRODUCTS'))
                            <li class="nav-item {{ (strpos(URL::current(),url('administrator/products/list')) !== false ) || (strpos(URL::current(),url('administrator/products/categories')) !== false ) ? 'active open': '' }}">
                                <a href="javascript:;" class="nav-link nav-toggle ">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="title">Products Section</span>
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item {{(strpos( URL::current(),url('administrator/products/list')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.products.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span class="title">Products</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                    @can(config('permissions.PERMISSION_PRODUCT_CATEGORIES'))
                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/products/categories')) !== false ) ? 'active': '' }}">
                                        <a href="{{route('admin.products.categories')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-folder-open-o"></i>
                                            <span class="title">Product Categories</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>
                                    @endcan

                                </ul>
                            </li>
                        @endcan
                    @endif


                    @if( hasModule('Portfolio') )
                        @can(config('permissions.PERMISSION_PORTFOLIO'))
                            <li class="nav-item {{ (strpos(URL::current(),url('administrator/portfolio/list')) !== false ) || (strpos(URL::current(),url('administrator/portfolio/categories')) !== false ) ? 'active open': '' || (strpos(URL::current(),url('administrator/portfolio/designers')) !== false ) ? 'active open': '' }}">
                                <a href="javascript:;" class="nav-link nav-toggle ">
                                    <i class="fa fa-pencil-square"></i>
                                    <span class="title">Portfolio Section</span>
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item {{(strpos( URL::current(),url('administrator/portfolio/list')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.portfolio.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-image"></i>
                                            <span class="title">Portfolio</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                    @can(config('permissions.PERMISSION_PORTFOLIO_CATEGORIES'))
                                        <li class="nav-item {{(strpos(URL::current(),url('administrator/portfolio/categories')) !== false ) ? 'active': '' }}">
                                            <a href="{{route('admin.portfolio.categories')}}" class="nav-link nav-toggle">
                                                <i class="fa fa-folder-open-o"></i>
                                                <span class="title">Portfolio Categories</span>
                                                <span class="selected"></span>
                                            </a>
                                        </li>
                                    @endcan

                                    {{--<li class="nav-item {{(strpos(URL::current(),url('administrator/portfolio/designers')) !== false ) ? 'active': '' }}">--}}
                                        {{--<a href="{{route('admin.portfolio.designers')}}" class="nav-link nav-toggle">--}}
                                            {{--<i class="fa fa-user-circle"></i>--}}
                                            {{--<span class="title">Portfolio Designers</span>--}}
                                            {{--<span class="selected"></span>--}}
                                        {{--</a>--}}
                                    {{--</li>--}}

                                </ul>
                            </li>
                        @endcan
                    @endif

                    @if( hasModule('Khadamat') )
                        @can(config('permissions.PERMISSION_SERVICES'))
                            <li class="nav-item {{(strpos( URL::current(),url('administrator/services/list')) !== false ) ? 'active open': '' }}">
                                <a href="{{route('admin.services.list')}}" class="nav-link nav-toggle">
                                    <i class="fa fa-gears"></i>
                                    <span class="title">Services</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        @endcan
                    @endif


                    @if( hasModule('News') )
                        @can(config('permissions.PERMISSION_NEWS'))
                        <li class="nav-item {{ (strpos(URL::current(),url('administrator/news/list')) !== false ) || (strpos(URL::current(),url('administrator/news/categories')) !== false ) ? 'active open': '' }}">
                        <a href="javascript:;" class="nav-link nav-toggle ">
                            <i class="fa fa-newspaper-o"></i>
                            <span class="title">News Section</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item {{(strpos( URL::current(),url('administrator/news/list')) !== false ) ? 'active open': '' }}">
                                <a href="{{route('admin.news.list')}}" class="nav-link nav-toggle">
                                    <i class="fa fa-newspaper-o"></i>
                                    <span class="title">News</span>
                                    <span class="selected"></span>
                                </a>
                            </li>

                            <li class="nav-item {{(strpos(URL::current(),url('administrator/news/categories')) !== false ) ? 'active open': '' }}">
                                <a href="{{route('admin.news.categories')}}" class="nav-link nav-toggle">
                                    <i class="fa fa-folder-open-o"></i>
                                    <span class="title">News Categories</span>
                                    <span class="selected"></span>
                                </a>
                            </li>

                        </ul>
                    </li>
                        @endcan
                    @endif

                    @if( hasModule('Articles') )
                        @can(config('permissions.PERMISSION_BLOG'))
                        <li class="nav-item {{ (strpos(URL::current(),url('administrator/articles/list')) !== false ) || (strpos(URL::current(),url('administrator/articles/categories')) !== false ) ? 'active open': '' }}">
                            <a href="javascript:;" class="nav-link nav-toggle ">
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                <span class="title">Blog Section</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item {{(strpos( URL::current(),url('administrator/articles/list')) !== false ) ? 'active open': '' }}">
                                    <a href="{{route('admin.articles.list')}}" class="nav-link nav-toggle">
                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                        <span class="title">Articles</span>
                                        <span class="selected"></span>
                                    </a>
                                </li>

                                <li class="nav-item {{(strpos(URL::current(),url('administrator/articles/categories')) !== false ) ? 'active open': '' }}">
                                    <a href="{{route('admin.articles.categories')}}" class="nav-link nav-toggle">
                                        <i class="fa fa-folder-open-o"></i>
                                        <span class="title">Articles Categories</span>
                                        <span class="selected"></span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        @endcan
                    @endif

                    @if( hasModule('Events') )
                        @can(config('permissions.PERMISSION_EVENTS'))
                            <li class="nav-item {{(strpos( URL::current(),url('administrator/events/list')) !== false ) ? 'active open': '' }}">
                                <a href="{{route('admin.events.list')}}" class="nav-link nav-toggle">
                                    <i class="fa fa-bell-o" aria-hidden="true"></i>
                                    <span class="title">Events</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if( hasModule('Magazines') )
                        @can(config('permissions.PERMISSION_MAGAZINES'))
                            <li class="nav-item {{ (strpos(URL::current(),url('administrator/magazines/list')) !== false ) || (strpos(URL::current(),url('administrator/magazines/categories')) !== false ) ? 'active open': '' }}">
                                <a href="javascript:;" class="nav-link nav-toggle ">
                                    <i class="icon-book-open icons"></i>
                                    <span class="title">Magazines Section</span>
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item {{(strpos( URL::current(),url('administrator/magazines/list')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.magazines.list')}}" class="nav-link nav-toggle">
                                            <i class="icon-book-open icons"></i>
                                            <span class="title">Magazines</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                    @can(config('permissions.PERMISSION_MAGAZINE_CATEGORIES'))
                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/magazines/categories')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.magazines.categories')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-folder-open-o"></i>
                                            <span class="title">Magazine Categories</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>
                                    @endcan

                                </ul>
                            </li>
                        @endcan
                    @endif


                    @if( hasModule('Sale') )
                        @can(config('permissions.PERMISSION_SALE'))
                            <li class="nav-item {{ (strpos(URL::current(),url('administrator/sale')) !== false ) || (strpos(URL::current(),url('administrator/orders')) !== false ) || (strpos(URL::current(),url('administrator/payments')) !== false ) ? 'active open': '' || (strpos(URL::current(),url('administrator/priceList')) !== false ) ? 'active open': ''}}">
                                <a href="javascript:;" class="nav-link nav-toggle ">
                                    <i class="fa fa-shopping-bag"></i>
                                    <span class="title">Sale Section</span>
                                    <span class="arrow"></span>
                                </a>

                                <ul class="sub-menu">

                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/orders')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.order.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-shopping-bag"></i>
                                            <span class="title">Orders</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/sale')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.sale.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-tag"></i>
                                            <span class="title">Invoices</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/payments')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.payments.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-credit-card-alt"></i>
                                            <span class="title">Payments</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/priceList')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.priceList.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-money"></i>
                                            <span class="title">Price List</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                        @endcan
                    @endif

                    @if( hasModule('Warehouse') )
                        @can(config('permissions.PERMISSION_WAREHOUSE'))

                            <li class="nav-item  {{(strpos(URL::current(),url('administrator/warehouse')) !== false ) ? 'active open': '' }}">
                                <a href="javascript:;" class="nav-link nav-toggle ">
                                    <i class="fa fa-truck"></i>
                                    <span class="title">Warehouse</span>
                                    <span class="arrow"></span>
                                </a>

                                <ul class="sub-menu">
                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/warehouse/list')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.warehouse.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-arrow-right"></i>
                                            <span class="title">Warehouse Entry</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/warehouse/outgo/list')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.warehouse.outgo.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-arrow-left"></i>
                                            <span class="title">Warehouse Outgo</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/warehouse/inventory/list')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.warehouse.inventory.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-cubes"></i>
                                            <span class="title">Inventory list</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>

                                </ul>
                            </li>


                        @endcan
                    @endif


                @if( hasModule('Contact') )
                    @can(config('permissions.PERMISSION_CONTACTS'))
                        <li class="nav-item {{(strpos(URL::current(),url('administrator/contacts')) !== false ) ? 'active open': '' }}">
                            <a href="{{route('admin.contacts.list')}}" class="nav-link nav-toggle">
                                <i class="fa fa-envelope-o"></i>
                                <span class="title">Messages @if(isset($messageContactCounter) && $messageContactCounter > 0 ) <span class="badge badge-danger pull-right pulsate" id="message-counter" data-counter="{{$messageContactCounter}}">{{$messageContactCounter}}</span> @endif </span>
                                <span class="selected"></span>
                            </a>
                        </li>
                    @endcan
                @endif

                @if( hasModule('Comments') )
                    @can(config('permissions.PERMISSION_COMMENTS'))
                        <li class="nav-item {{(strpos(URL::current(),url('administrator/comments')) !== false ) ? 'active open': '' }}">
                            <a href="{{route('admin.comments.list')}}" class="nav-link nav-toggle">
                                <i class="fa fa-comments"></i>
                                <span class="title">Comments</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                    @endcan
                @endif

                @if( hasModule('Portal') )
                    @can(config('permissions.PERMISSION_PORTAL_UPDATER'))
                        <li class="nav-item  {{(strpos(URL::current(),url('administrator/portal')) !== false ) ? 'active open': '' }}">
                                <a href="javascript:;" class="nav-link nav-toggle ">
                                    <i class="fa fa-magic"></i>
                                    <span class="title">Portal.ir</span>
                                    <span class="arrow"></span>
                                </a>

                                <ul class="sub-menu">
                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/portal/list')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.portalUpdater.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-list-alt"></i>
                                            <span class="title">Aliases</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{(strpos(URL::current(),url('administrator/portal/portalList')) !== false ) ? 'active open': '' }}">
                                        <a href="{{route('admin.portal.list')}}" class="nav-link nav-toggle">
                                            <i class="fa fa-play"></i>
                                            <span class="title">Tasks</span>
                                            <span class="selected"></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                    @endcan
                @endif


                </ul>
                <!-- END SIDEBAR MENU -->
            </div>
            <!-- END SIDEBAR -->
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->
            <div class="page-content" style="min-height: 550px !important;">
                <!-- BEGIN PAGE HEADER-->

                <!-- BEGIN PAGE BAR -->
                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li>
                            <a href="{{route('admin.dashboard')}}">Home</a>
                        </li>
                        @if(isset($title))
                        <li>
                            <i class="fa fa-circle"></i>
                            <span>{{ $title }}</span>
                        </li>
                        @endif
                    </ul>
                    <div class="page-toolbar">
                        <div id="dashboard-report-range-qa" class="pull-right btn btn-sm" data-placement="bottom">
                            <span class="btn btn-danger btn-xs disabled" style="margin-top: 3px;">
                                <i class="icon-calendar"></i>&nbsp;
                                <span>{{ $miladiDate  }}</span>
                            </span>
                            <span class="btn btn-success btn-xs disabled" style="margin-top: 3px;">
                                <i class="icon-calendar"></i>&nbsp;
                                <span style="font-family: Tahoma, Helvetica, Arial">{{ $jalaliDate }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 10px;"></div>
