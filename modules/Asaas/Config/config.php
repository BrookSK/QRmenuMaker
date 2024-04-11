<?php

return [
    'name' => 'Asaas',
    'enabled'=>env('ENABLE_ASAAS',false),
    'useVendor'=>env('VENDORS_OR_ADMIN_ASAAS','admin')=="vendor",
    'useAdmin'=>env('VENDORS_OR_ADMIN_ASAAS','admin')=="admin",
    'access_token'=>env('ASAAS_ACCESS_TOKEN','')
];
