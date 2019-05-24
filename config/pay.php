<?php

return [
    'alipay' => [
        'app_id'         => '',
        'ali_public_key' => '',
        'private_key'    => '',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => 'wxb45b00527a12124b',
        'mch_id'      => '1531728311',
        'key'         => 'd75a49ad6b70a0396fd1be07fc741480',
        'cert_client' => app_path( '/config/wxpay/apiclient_cert.pem') ,
        'cert_key'    => app_path( '/config/wxpay/apiclient_key.pem')  ,
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
