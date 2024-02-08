<?php

return [
    'name' => 'PayPal',
    'enabled'=>env('ENABLE_PAYPAL',false),
    'useVendor'=>env('VENDORS_OR_ADMIN_PAYPAL','admin')=="vendor",
    'useAdmin'=>env('VENDORS_OR_ADMIN_PAYPAL','admin')=="admin",
    'client_id'=>env('PAYPAL_CLIENT_ID',''),
    'secret'=>env('PAYPAL_SECRET',""),
    'mode'=>env('PAYPAL_MODE','sandbox')
];
