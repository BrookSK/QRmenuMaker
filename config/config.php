<?php

return [
    'version' => '3.4.1',
    'env'=>[
        [
            'name'=>'Setup',
            'slug'=>'setup',
            'icon'=>'ni ni-settings',
            'fields'=>[
                ['separator'=>'System', 'title'=>'Project name', 'key'=>'APP_NAME', 'value'=>'Site name'],
                ['title'=>'Link to your site', 'key'=>'APP_URL', 'value'=>'http://localhost'],
                ['title'=>'Subdomains', 'key'=>'IGNORE_SUBDOMAINS', 'value'=>'www,127', 'help'=>'Subdomain your app works in. ex if your subdomain is app.yourdomain.com, here you should have www,app '],
                ['title'=>'App debugging', 'key'=>'APP_DEBUG', 'value'=>'true', 'ftype'=>'bool', 'help'=>'Enable if you experience error 500'],
                ['title'=>'Wildcard domain', 'help'=>'If you have followed the procedure to enable wildcard domain, select this so you can have shopname.yourdomain.com', 'key'=>'WILDCARD_DOMAIN_READY', 'value'=>'false', 'ftype'=>'bool'],
                ['title'=>'Enable guest log', 'key'=>'ENABLE_GUEST_LOG', 'value'=>'true', 'ftype'=>'bool', 'onlyin'=>'qrsaas'],
                ['title'=>'Hide project branding on menu page', 'key'=>'HIDE_PROJECT_BRANDING', 'value'=>'true', 'ftype'=>'bool', 'onlyin'=>'qrsaas'],
               
                ['title'=>'Front end template', 'key'=>'FRONT_END_TEMPLATE', 'value'=>'defaulttemplate', 'ftype'=>'select', 'data'=>[]],
                ['title'=>'Disable the landing page', 'help'=>'When landing page is disabled, the project will start from the login page. In this case it is best to have the system in subdomain', 'key'=>'DISABLE_LANDING', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'qrsaas'],
                ['title'=>'Hide register on login page when landing disabled', 'key'=>'DISABLE_LANDING_AND_HIDE_REGISTER', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'qrsaas'],

                ['separator'=>'Ordering and items', 'title'=>'Completely disable ordering', 'key'=>'QRSAAS_DISABLE_ODERING', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'qrsaas', 'help'=>'If this is selected, then cart, and orders will not be shown'],
                ['title'=>'Directly approve order', 'help'=>'When selected admin does not have to approve order', 'key'=>'APP_ORDER_APPROVE_DIRECTLY', 'value'=>'true', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['title'=>'Assign orders to drivers automatically', 'key'=>'ALLOW_AUTOMATED_ASSIGN_TO_DRIVER', 'value'=>'true', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['title'=>'Allow vendor to do their own delivery', 'key'=>'APP_ALLOW_SELF_DELIVER', 'value'=>'true', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['title'=>'Time for order to be prepared', 'help'=>'Average time order is prepared, so users can not order before vendor or shop is closing', 'key'=>'TIME_TO_PREPARE_ORDER_IN_MINUTES', 'value'=>0, 'type'=>'hidden', 'onlyin'=>'ft'],
                ['title'=>'Search radius for vendors', 'help'=>'Maximum distance that vendors are shown to user', 'key'=>'LOCATION_SEARCH_RADIUS', 'value'=>50, 'type'=>'number', 'onlyin'=>'ft'],
                ['title'=>'Search radius for drivers', 'help'=>'When you have automatic assign to driver, this is a way to show the system for the maximum range to look for driver', 'key'=>'DRIVER_SEARCH_RADIUS', 'value'=>15, 'type'=>'number', 'onlyin'=>'ft'],
                ['title'=>'Disable continues orders', 'help'=>'If enabled, orders done on same table will be merged, until order is not closed/finished by vendor', 'key'=>'DISABLE_CONTINIUS_ORDERING', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'qrsaas'],
                ['title'=>'Enable pickup , system wide', 'key'=>'ENABLE_PICKUP', 'value'=>'true', 'ftype'=>'bool'],
                ['title'=>'Hide cash on delivery, system wide', 'key'=>'HIDE_COD', 'value'=>'false', 'ftype'=>'bool'],
                ['title'=>'Delivery / time intervals in minutes', 'help'=>'Separate the time slots into N Minutes. ex 09:00-09-15 , 09:15-09:30 - value is 15 ', 'key'=>'DELIVERY_INTERVAL_IN_MINUTES', 'value'=>30, 'type'=>'number'],
                ['title'=>'Default payment type', 'key'=>'DEFAULT_PAYMENT', 'value'=>'cod', 'ftype'=>'select', 'data'=>['cod'=>'Cash on Delivery', 'stripe'=>'Stripe Card processing']],
                ['title'=>'Is your project multi city', 'help'=>'When selected, the front page will display list of cities', 'key'=>'MULTI_CITY', 'value'=>'true', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['title'=>'Single mode - run this site for one vendor only', 'key'=>'SINGLE_MODE', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['title'=>'The id of the vendor for single mode', 'help'=>'If you have single mode selected, than this vendor id will be show', 'key'=>'SINGLE_MODE_ID', 'value'=>'1', 'type'=>'number', 'onlyin'=>'ft'],
                ['title'=>'Enable import via CSV for vendor items', 'key'=>'ENABLE_IMPORT_CSV', 'value'=>'false', 'ftype'=>'bool'],
                ['title'=>'Send order email notification on vendor email ( Can be configured per vendor via apps )', 'key'=>'ENABLE_SEND_ORDER_MAIL_TO_VENDOR', 'value'=>'false', 'ftype'=>'bool'],  
                
                
                ['separator'=>'Delivery costs', 'title'=>'Enable cost per distance', 'key'=>'ENABLE_COST_PER_DISTANCE', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['title'=>'Cost per kilometer', 'key'=>'COST_PER_KILOMETER', 'value'=>'1', 'onlyin'=>'ft'],
                ['title'=>'Enable cost based on range', 'help'=>'If you have enable cost based on range, the delivery cost will be calculated based on what range the distance for delivery is in', 'key'=>'ENABLE_COST_IN_RANGE', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['help'=>'Range in kilometers ex from 0km - 5km will be 0-5', 'title'=>'First range', 'key'=>'RANGE_ONE', 'value'=>'0-5', 'onlyin'=>'ft'],
                ['title'=>'Second range', 'key'=>'RANGE_TWO', 'value'=>'5-7', 'onlyin'=>'ft'],
                ['title'=>'Third range', 'key'=>'RANGE_THREE', 'value'=>'7-10', 'onlyin'=>'ft'],
                ['title'=>'Fourth range', 'key'=>'RANGE_FOUR', 'value'=>'10-15', 'onlyin'=>'ft'],
                ['title'=>'Fifth range', 'key'=>'RANGE_FIVE', 'value'=>'15-20', 'onlyin'=>'ft'],

                ['title'=>'Price for first range', 'key'=>'RANGE_ONE_PRICE', 'value'=>'5', 'onlyin'=>'ft'],
                ['title'=>'Price for second range', 'key'=>'RANGE_TWO_PRICE', 'value'=>'6', 'onlyin'=>'ft'],
                ['title'=>'Price for third range', 'key'=>'RANGE_THREE_PRICE', 'value'=>'8', 'onlyin'=>'ft'],
                ['title'=>'Price for fourth range', 'key'=>'RANGE_FOUR_PRICE', 'value'=>'10', 'onlyin'=>'ft'],
                ['title'=>'Price for fifth range', 'key'=>'RANGE_FIVE_PRICE', 'value'=>'15', 'onlyin'=>'ft'],

                ['title'=>'Driver percent from the order', 'help'=>'From 0-100. Based on your business type, this value determines how much driver will make from the delivery fee. This value can be change on driver level also', 'key'=>'DRIVER_PERCENT_FROM_DELIVERY_FEE', 'value'=>'100', 'onlyin'=>'ft'],

                ['title'=>'Demo vendor slug', 'separator'=>'Other settings', 'help'=>'Enter the domain - slug of your demo vendor that will show on the landing page', 'key'=>'demo_restaurant_slug', 'value'=>'leukapizza', 'onlyin'=>'qrsaas'],
                ['title'=>'Vendor entity name', 'help'=>'Ex. Company, Restaurant, Shop, Business etc', 'key'=>'VENDOR_ENTITY_NAME', 'value'=>'Restaurant'],
                ['title'=>'Vendor entity name in plural', 'help'=>'Ex. Companies, Restaurants, Shops, Businesses etc', 'key'=>'VENDOR_ENTITY_NAME_PLURAL', 'value'=>'Restaurants'],
                ['title'=>'Url route for vendor', 'help'=>'If you want to change the link the vendor is open in. ex yourdomain.com/shop/shopname. shop - should be the value here', 'key'=>'URL_ROUTE', 'value'=>'restaurant'],
                ['title'=>'Url route for vendor in plural', 'help'=>'If you want to change the link the vendor management is open in. ex yourdomain.com/shops. shops - should be the value here', 'key'=>'URL_ROUTE_PLURAL', 'value'=>'restaurants'],
                ['title'=>'Apps download code', 'help'=>'If you have extended license, or some specific product, we will send you App download code. Send us ticket.', 'key'=>'EXTENDED_LICENSE_DOWNLOAD_CODE', 'value'=>''], 
                ['title'=>'Print templates images', 'help'=>'Links to images representing the images for the templates. You can use remote images', 'key'=>'templates', 'value'=>'/impactfront/img/menu_template_1.jpg,/impactfront/img/menu_template_2.jpg', 'onlyin'=>'qrsaas'],
                ['title'=>'Print templates zip', 'help'=>'Link to .zip representing the template for download. You can use remote file', 'key'=>'linkToTemplates', 'value'=>'/impactfront/img/templates.zip', 'onlyin'=>'qrsaas'],

                ['title'=>'Chars in menu list', 'key'=>'CHARS_IN_MENU_LIST', 'value'=>'40', 'type'=>"number", 'help'=>'Controls to how many chars the menu description should be trimmed'],
                ['title'=>'Enable multi language menus', 'help'=>'When enabled, vendors can add language version to the menu', 'key'=>'ENABLE_MILTILANGUAGE_MENUS', 'value'=>'false', 'ftype'=>'bool'],

                ['title'=>'Enable change log in update screen', 'key'=>'ENABLE_CHANGELOG_IN_UPDATE', 'value'=>'true', 'ftype'=>'bool'],

                ['title'=>'Position for the register driver link', 'key'=>'DRIVER_LINK_REGISTER_POSITION', 'value'=>'footer', 'data'=>['footer'=>'Footer', 'navbar'=>'Navigation bar', 'dontshow'=>'Hidden'], 'ftype'=>'select', 'onlyin'=>'ft'],
                ['title'=>'Position for the register vendor link', 'key'=>'RESTAURANT_LINK_REGISTER_POSITION', 'value'=>'footer', 'data'=>['footer'=>'Footer', 'navbar'=>'Navigation bar', 'dontshow'=>'Hidden'], 'ftype'=>'select', 'onlyin'=>'ft'],

                ['title'=>'Title of driver link', 'key'=>'DRIVER_LINK_REGISTER_TITLE', 'value'=>'Wanna drive for us?', 'onlyin'=>'ft'],
                ['title'=>'Title for the register vendor link', 'key'=>'RESTAURANT_LINK_REGISTER_TITLE', 'value'=>'Add your vendor', 'onlyin'=>'ft'],

                ['title'=>'Mobile app secret', 'key'=>'APP_SECRET', 'value'=>'APP_SECRET'],
                ['title'=>'Unit','ftype'=>'select', 'key'=>'UNIT', 'value'=>'K','data'=>['k'=>'Kilometers', 'Mi'=>'Milles'] ],
                ['title'=>'App environment', 'key'=>'APP_ENV', 'value'=>'local', 'ftype'=>'select', 'data'=>['local'=>'Local', 'prodcution'=>'Production']],
                ['title'=>'Debug app level', 'type'=>'hidden', 'key'=>'APP_LOG_LEVEL', 'value'=>'debug', 'data'=>['debug'=>'Debug', 'error'=>'Error']],
                ['separator'=>'Links', 'title'=>'Link to terms and services', 'key'=>'LINK_TO_TS', 'value'=>"/blog/terms-and-conditions"],
                [ 'title'=>'Link to privacy policy', 'key'=>'LINK_TO_PR', 'value'=>"/blog/how-it-works"],
            
                ['separator'=>"Custom fields on order", 'title'=>'Label on the custom fields', 'key'=>'LABEL_ON_CUSTOM_FIELDS', 'value'=>"Customer Info"]
                
            
            ],
        ],

        [
            'name'=>'Finances',
            'slug'=>'finances',
            'icon'=>'ni ni-money-coins',
            'fields'=>[
                ['separator'=>'General', 'title'=>'Tool used for subscriptions', 'key'=>'SUBSCRIPTION_PROCESSOR', 'value'=>'Stripe', 'ftype'=>'select', 'data'=>[]],
                ['title'=>'Enable Pricing','key'=>'ENABLE_PRICING', 'value'=>'true', 'ftype'=>'bool'],
                ['title'=>'The free plan ID','title'=>'', 'key'=>'FREE_PRICING_ID', 'value'=>'1'],
                ['title'=>'Force users to use paid plan','title'=>'', 'key'=>'FORCE_USERS_TO_PAY', 'value'=>'false','ftype'=>'bool'],

                
                ['title'=>'Enable Finance dashboard for owner', 'help'=>'More advance, finance related reports for owner', 'key'=>'ENABLE_FINANCES_OWNER', 'value'=>'true', 'ftype'=>'bool'],
                ['title'=>'Enable Finance dashboard for admin', 'key'=>'ENABLE_FINANCES_ADMIN', 'help'=>'More advance, finance related reports for admin', 'value'=>'true', 'ftype'=>'bool'],


                ['separator'=>'Stripe', 'title'=>'Enable stripe for payments when ordering', 'key'=>'ENABLE_STRIPE', 'value'=>'true', 'ftype'=>'bool'],
                ['title'=>'Stripe API key', 'key'=>'STRIPE_KEY', 'value'=>'pk_test_XXXXXXXXXXXXXX'],
                ['title'=>'Stripe API Secret', 'key'=>'STRIPE_SECRET', 'value'=>'sk_test_XXXXXXXXXXXXXXX'],
                ['title'=>'Enable Stripe connect', 'help'=>'If enabled, vendors will be able to connect, and money to be send directly to them', 'key'=>'ENABLE_STRIPE_CONNECT', 'value'=>'true', 'ftype'=>'bool'],
                ["title"=> "System will use", "key" => "VENDORS_OR_ADMIN_STRIPE", "ftype" => "select", "onlyin"=>"qrsaas", "value"=>"admin","data"=>[ "admin"=>"Admin defined Stripe", "vendor"=>"Vendor defined Stripe"]],

                ['separator'=>'Local bank transfer', 'title'=>'Local bank transfer explanation', 'key'=>'LOCAL_TRANSFER_INFO', 'value'=>'Wire us the plan amout on the following bank accoun. And inform us about the wire.'],
                ['title'=>'Bank Account', 'key'=>'LOCAL_TRANSFER_ACCOUNT', 'value'=>'IBAN: 12112121212121'],
                
                ],
        ],
        [],
        [
            'name'=>'Apps & Plugins',
            'slug'=>'plugins',
            'icon'=>'ni ni-spaceship',
            'fields'=>[

                
                ['separator'=>'WhatsApp ordering', 'title'=>'Enable WhatsApp order submit', 'help'=>'When activated, if owner has entered his whatsapp phone  a send to whatsapp order will be shown on order completed page. Order will be sent to owner whatsapp phone', 'key'=>'WHATSAPP_ORDERING_ENABLED', 'value'=>'true', 'ftype'=>'bool', 'onlyin'=>'qrsaas'],
                ['separator'=>'WhatsApp ordering','title'=>'Enable WhatsApp order submit', 'help'=>'When activated, a send to whatsapp order will be shown on order completed page. Order will be sent to admin whatsapp phone', 'key'=>'WHATSAPP_ORDERING_ENABLED', 'value'=>'true', 'ftype'=>'bool', 'onlyin'=>'ft'],


                ['separator'=>'Google plugins', 'title'=>'Recaptcha site key', 'help'=>"Make empty if you can't make submition on register screen", 'key'=>'RECAPTCHA_SITE_KEY', 'value'=>''],
                ['title'=>'Recaptcha secret', 'help'=>"Make empty if you can't make submition on register screen", 'key'=>'RECAPTCHA_SECRET_KEY', 'value'=>''],
                ['title'=>'Google maps api key', 'key'=>'GOOGLE_MAPS_API_KEY', 'value'=>''],
                ['title'=>'Enable location search', 'key'=>'ENABLE_LOCATION_SEARCH', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['title'=>'Google analytics key', 'key'=>'GOOGLE_ANALYTICS', 'value'=>''],
                ['separator'=>'Login services', 'title'=>'Google client id for sign in', 'key'=>'GOOGLE_CLIENT_ID', 'value'=>'', 'onlyin'=>'ft'],
                ['title'=>'Google client secret for sign in', 'key'=>'GOOGLE_CLIENT_SECRET', 'value'=>'', 'onlyin'=>'ft'],
                ['title'=>'Google redirect link for sign in', 'key'=>'GOOGLE_REDIRECT', 'value'=>'', 'onlyin'=>'ft'],
                ['title'=>'Facebook client id', 'key'=>'FACEBOOK_CLIENT_ID', 'value'=>'', 'onlyin'=>'ft'],
                ['title'=>'Facebook client secret', 'key'=>'FACEBOOK_CLIENT_SECRET', 'value'=>'', 'onlyin'=>'ft'],
                ['title'=>'Facebook redirec', 'key'=>'FACEBOOK_REDIRECT', 'value'=>'', 'onlyin'=>'ft'],
                ['separator'=>'Notifications', 'title'=>'Onesignal App id', 'key'=>'ONESIGNAL_APP_ID', 'value'=>''],
                ['title'=>'Onesignal rest api key', 'key'=>'ONESIGNAL_REST_API_KEY', 'value'=>''],
                ['title'=>'Twillo Account SID', 'key'=>'TWILIO_ACCOUNT_SID', 'value'=>'SID', 'onlyin'=>'ft'],
                ['title'=>'Twillo Account auth token', 'key'=>'TWILIO_AUTH_TOKEN', 'value'=>'TOKEN', 'onlyin'=>'ft'],
                ['title'=>'Twillo from number', 'key'=>'TWILIO_FROM', 'value'=>'NUMBER', 'onlyin'=>'ft'],
                ['title'=>'System should send sms notifications', 'key'=>'SEND_SMS_NOTIFICATIONS', 'value'=>'false', 'ftype'=>'bool', 'onlyin'=>'ft'],
                ['separator'=>'Pusher live notifications', 'title'=>'Pusher app id', 'help'=>'Pusher is used for notification for call waiter and new orders available', 'key'=>'PUSHER_APP_ID', 'value'=>''],
                ['title'=>'Pusher app key', 'key'=>'PUSHER_APP_KEY', 'value'=>''],
                ['title'=>'Pusher app secret', 'key'=>'PUSHER_APP_SECRET', 'value'=>''],
                ['title'=>'Pusher app cluster', 'key'=>'PUSHER_APP_CLUSTER', 'value'=>'eu'],
                ['title'=>'Broadcast Driver', 'key'=>'BROADCAST_DRIVER', 'value'=>'log', 'ftype'=>'select', 'data'=>['log'=>'Log', 'pusher'=>'Pusher']],

                ['separator'=>'Cookies','title'=>'Cookie Consent', 'key'=>'ENABLE_DEFAULT_COOKIE_CONSENT', 'value'=>'true', 'ftype'=>'bool', 'help'=>'Cookie consent popup - you can import other via js'],

                

                ['separator'=>'Share this', 'title'=>'Share this property id', 'help'=>'You can find this number in Share this import link', 'key'=>'SHARE_THIS_PROPERTY', 'value'=>''],
                ['separator'=>'Futy', 'title'=>'Futy key', 'key'=>'FUTY_KEY', 'value'=>''],

            ],
        ],
        [
            'name'=>'SMTP',
            'slug'=>'smtp',
            'icon'=>'ni ni-email-83',
            'fields'=>[
                ['title'=>'Mail driver', 'key'=>'MAIL_MAILER', 'value'=>'smtp', 'ftype'=>'select', 'data'=>['smtp'=>'SMTP', 'sendmail'=>'PHP Sendmail - best of port 465']],
                ['title'=>'Host', 'key'=>'MAIL_HOST', 'value'=>'smtp.mailtrap.io', 'hint'=>'Your SMTP send server'],
                ['title'=>'Port', 'key'=>'MAIL_PORT', 'value'=>'2525', 'help'=>'Common ports are 26, 465, 587'],
                ['title'=>'Encryption', 'key'=>'MAIL_ENCRYPTION', 'value'=>'', 'ftype'=>'select', 'data'=>['null'=>'Null - best for port 26', ''=>'None - best for port 587', 'ssl'=>'SSL - best for port 465','tls'=>"TLS",'starttls'=>"STARTTLS"]],

                ['title'=>'Username', 'key'=>'MAIL_USERNAME', 'value'=>'802fc656dd8029'],
                ['title'=>'Password', 'key'=>'MAIL_PASSWORD', 'value'=>'bbcf39d313eac6'],
                ['title'=>'From address', 'key'=>'MAIL_FROM_ADDRESS', 'value'=>'bd5d577b7c-be3ae1@inbox.mailtrap.io'],
                ['title'=>'From Name', 'key'=>'MAIL_FROM_NAME', 'value'=>'Your Site'],

                ['title'=>'', 'key'=>'DB_CONNECTION', 'value'=>'mysql', 'data'=>['mysql'=>'MySql'], 'type'=>'hidden'],
                ['title'=>'', 'key'=>'DB_HOST', 'value'=>'127.0.0.1', 'hint'=>'Your SMTP send server', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'DB_PORT', 'value'=>'3306', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'DB_DATABASE', 'value'=>'laravel', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'DB_USERNAME', 'value'=>'laravel', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'DB_PASSWORD', 'value'=>'laravel', 'type'=>'hidden'],

                ['title'=>'', 'key'=>'CACHE_DRIVER', 'value'=>'file', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'SESSION_DRIVER', 'value'=>'file', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'QUEUE_DRIVER', 'value'=>'sync', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'REDIS_HOST', 'value'=>'127.0.0.1', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'REDIS_PASSWORD', 'value'=>'null', 'type'=>'hidden'],
                ['title'=>'', 'key'=>'REDIS_PORT', 'value'=>'6379', 'type'=>'hidden'],

            ],
        ],
    ],
];
