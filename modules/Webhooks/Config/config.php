<?php

return [
    'name' => 'Webhooks',
    'webhook_by_admin'=>env('WEBHOOK_BY_ADMIN',''),
    'webhook_by_vendor'=>env('WEBHOOK_BY_VENDOR',''),
    'webhook_new_vendor'=>env('WEBHOOK_NEW_VENDOR',''),
    'webhook_status_by_admin'=>env('WEBHOOK_ORDER_STATUS_UPDATE',''),
];
