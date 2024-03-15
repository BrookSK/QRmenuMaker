<?php

return [
    'name' => 'Stripelinks',
    'useVendor'=>env('VENDORS_OR_ADMIN_STRIPE','admin')=="vendor",
    'useAdmin'=>env('VENDORS_OR_ADMIN_STRIPE','admin')=="admin",
    'enabled'=>env('STRIPE_LINKS_ENABLED',false),
];
