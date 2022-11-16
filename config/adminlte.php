<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Dream Aero',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Dream</b> Aero',
    'logo_img' => 'img/dreamaero-admin-logo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Dream Aero',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => ['lg' => true],
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => true,
    'sidebar_collapse_auto_size' => 1090,
    'sidebar_collapse_remember' => true,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => true,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => '/',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        /*[
            'type'         => 'navbar-search',
            'text'         => 'search',
            'topnav_right' => true,
        ],*/
        [
            'type'         => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
        	'key'		  => 'calendar',
            'text'        => 'Календарь',
            'url'         => '/',
            'icon'        => 'far fa-fw fa-calendar-alt',
            'label'       => '',
            'label_color' => '',
        ],
		[
			'key'		  => 'deal',
			'text'        => 'Сделки',
			'url'         => '/deal',
			'icon'        => 'fas fa-handshake',
			'can'		  => 'is_admin',
			'label'       => '',
			'label_color' => '',
		],
		[
			'key'         => 'certificate',
			'text' 		  => 'Сертификаты',
			'url'  		  => '/certificate',
			'can'  		  => 'is_admin',
			'icon' 		  => 'far fa-fw fa-file-alt',
			'label'       => '',
			'label_color' => '',
		],
		[
			'key'         => 'contractor',
			'text'        => 'Контрагенты',
			'url'         => '/contractor',
			'icon'        => 'far fa-fw fa-address-book',
			'can'		  => 'is_admin',
			'label'       => '',
			'label_color' => '',
		],
		[
			'key'		  => 'schedule',
			'text'        => 'График работы',
			'url'         => '/schedule',
			'icon'        => 'far fa-fw fa-clock',
			'label'       => '',
			'label_color' => '',
		],
		[
			'text'    => 'Ценообразование',
			'icon'    => 'fas fa-fw fa-dollar-sign',
			'can'	  => 'is_superadmin',
			'submenu' => [
				[
					'text' => 'Цены',
					'url'  => '/pricing',
					'icon' => 'fas fa-hand-holding-usd',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Промокоды',
					'url'  => '/promocode',
					'icon' => 'fas fa-fw fa-tag',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Акции',
					'url'  => '/promo',
					'icon'    => 'fas fa-fw fa-exclamation',
				],
				[
					'text' => 'Варианты скидок',
					'url'  => '/discount',
					'icon' => 'fas fa-fw fa-percentage',
					'can'  => 'is_superadmin',
				],
			],
		],
		[
			'text'    => 'Справочники',
			'icon'    => 'fas fa-fw fa-book',
			'can'	  => 'is_superadmin',
			'submenu' => [
				[
					'text' => 'Города',
					'url'  => '/city',
					'icon' => 'far fa-fw fa-building',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Локации',
					'url'  => '/location',
					'icon' => 'fas fa-fw fa-map-marker-alt',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Юр.лица',
					'url'  => '/legal_entity',
					'icon' => 'far fa-fw fa-id-card',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Авиатренажеры',
					'url'  => '/flight_simulator',
					'icon' => 'fas fa-fw fa-plane',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Типы продуктов',
					'url'  => '/product_type',
					'icon' => 'fas fa-fw fa-list',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Продукты',
					'url'  => '/product',
					'icon' => 'fas fa-fw fa-grip-horizontal',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Статусы',
					'url'  => '/status',
					'icon' => 'fas fa-fw fa-project-diagram',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Способы оплаты',
					'url'  => '/payment_method',
					'icon' => 'fab fa-fw fa-cc-visa',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Пользователи',
					'url'  => '/user',
					'icon' => 'fas fa-fw fa-users',
					'can'  => 'is_superadmin',
				],
			],
		],
		[
			'text'        => 'Уведомления',
			'url'         => '/notification',
			'icon'        => 'fas fa-fw fa-bell',
			'can'	  	  => 'is_superadmin',
			'label'       => '',
			'label_color' => '',
		],
		[
			'text'        => 'Лог операций',
			'url'         => '/log',
			'icon'        => 'fas fa-fw fa-history',
			'can'	  => 'is_superadmin',
			'label'       => '',
			'label_color' => '',
		],
        [
            'text'    => 'Аналитика',
            'icon'    => 'far fa-fw fa-file-excel',
			//'can'	  => 'is_admin',
            'submenu' => [
                [
                    'text' => 'NPS',
                    'url'  => '/report/nps',
					'can'  => 'is_superadmin',
					'icon' => 'far fa-circle nav-icon',
                ],
				[
					'text' => 'Показания платформы',
					'url'  => '/report/platform',
					'can'  => 'is_superadmin',
					'icon' => 'far fa-circle nav-icon',
				],
				[
					'text' => 'Журнал учета налёта',
					'url'  => '/report/flight_log',
					//'can'  => 'is_superadmin',
					'icon' => 'far fa-circle nav-icon',
				],
				[
					'text' => 'Личные продажи',
					'url'  => '/report/personal-selling',
					'can'  => 'is_admin',
					'icon' => 'far fa-circle nav-icon',
				],
				[
					'text' => 'Спонтанные / Повторные',
					'url'  => '/report/unexpected-repeated',
					'can'  => 'is_superadmin',
					'icon' => 'far fa-circle nav-icon',
				],
				[
					'text' => 'Аэрофлот: списание',
					'url'  => '/report/aeroflot/write-off',
					'can'  => 'is_superadmin',
					'icon' => 'far fa-circle nav-icon',
				],
				[
					'text' => 'Аэрофлот: начисление',
					'url'  => '/report/aeroflot/accrual',
					'can'  => 'is_superadmin',
					'icon' => 'far fa-circle nav-icon',
				],
				[
					'text' => 'Самостоятельные оплаты',
					'url'  => '/report/contractor-self-made-payed-deals',
					'can'  => 'is_superadmin',
					'icon' => 'far fa-circle nav-icon',
				],
            ],
        ],
		[
			'text'        => 'Wiki',
			'url'         => '/wiki',
			'icon'        => 'fas fa-fw fa-info',
			'can'		  => 'is_admin',
			'label'       => '',
			'label_color' => '',
		],
		[
			'text'    => 'Контент',
			'icon'    => 'fas fa-sitemap',
			'can'	  => 'is_superadmin',
			'submenu' => [
				[
					'text' => 'Новости',
					'url'  => '/content/news',
					'icon' => 'far fa-circle nav-icon',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Галерея',
					'url'  => '/content/gallery',
					'icon' => 'far fa-circle nav-icon',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Отзывы',
					'url'  => '/content/reviews',
					'icon' => 'far fa-circle nav-icon',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Гости',
					'url'  => '/content/guests',
					'icon' => 'far fa-circle nav-icon',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Страницы',
					'url'  => '/content/pages',
					'icon' => 'far fa-circle nav-icon',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Промобоксы',
					'url'  => '/content/promoboxes',
					'icon' => 'far fa-circle nav-icon',
					'can'  => 'is_superadmin',
				],
				[
					'text' => 'Wiki',
					'url'  => '/content/wiki',
					'icon' => 'far fa-circle nav-icon',
					'can'  => 'is_superadmin',
				],
			],
		],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
		'Fullcalendar' => [
			'active' => false,
			'files' => [
				[
					'type' => 'js',
					'asset' => true,
					'location' => 'vendor/fullcalendar/main.js',
				],
				[
					'type' => 'js',
					'asset' => true,
					'location' => 'vendor/fullcalendar/locales-all.min.js',
				],
				[
					'type' => 'js',
					'asset' => true,
					'location' => 'vendor/fullcalendar/locales/ru.js',
				],
				[
					'type' => 'css',
					'asset' => true,
					'location' => 'vendor/fullcalendar/main.min.css',
				],
			],
		],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
