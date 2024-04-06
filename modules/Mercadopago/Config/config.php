<?php

return [
    'name' => 'Mercadopago',
    'enabled'=>env('ENABLE_MERCADOPAGO',false),
    'useVendor'=>env('VENDORS_OR_ADMIN_MERCADOPAGO','admin')=="vendor",
    'useAdmin'=>env('VENDORS_OR_ADMIN_MERCADOPAGO','admin')=="admin",
    'access_token'=>env('MERCADOPAGO_ACCESS_TOKEN','')
];
