<?php

return [
    'alipay' => [
        'app_id'         => '2019041063866292',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAoiCmfBOUigQFmBNBbHW16muIYvNeRkv3R8VM+dW/XzIL/Qrk/2qWdAqitgQO7LSlFGmyQ5LjRSvNSJACOc2C6txHSAZVsNuMfrRHjzMOuqHjBrz1YIJA374gDMU8O9dXQtFZJK1opv031UrrCEpm3p6BIQVJCiyj41m/lh2YNfpPKovAF3NoC2wmYCMuPHvG0gG6OzwKonqoGKiHzvUsa7oGro1hd7YmqHvcj59WFRmorxiDKkjWpZ6vkRwpxlIHR/n38fY33FgIIJgTS6HTtZ0TGRV+Eq1VcX6LakdEAuvOvaNUNuRFJTfnbkUt3Ozc8Dutux5y/8hIEHfQn/fYXwIDAQAB',
        'private_key' => 'MIIEpQIBAAKCAQEA2w9oo5nbO/mjFXkDcMdMXuVN/vH1qOVeYs/h6qWa87QlJR7rfsKX4vHIY3JgkUn+3cPOTHVeydI5xoxTc0Ynyu1chWe2S83ZW3Se5LH+3zF7+AjEbuNJi/KHLtKJHDPGxkxY+gchKFNqnX/7X0tRneP/KpMBHnCbAJaECmSbpG7zSR5wXbv0DC8RJOWkxYQuDZZeaHCCi8vi3DgjvqHFpd9XwFUomy0WGkZZ4GV7bZkbERuGyila3CO4rg2Vu+H4amKQUhWzAZLLiLUxRX+QQrqJjCJWZvIzUSSL+7ZvbS2M+LQ9KyBnMMv4nvkJuTzzC6/TFSIoIRWZ6AFT19iHxwIDAQABAoIBAQCLNPSLGqfOKiqv55H/JjdrCo8RF1PEe9YKNw7a3VhlzDHasBZ5HFIHHxz3zWR+j6ZN6TN9NQ0CdfzPJreVRpzdAQ/NmwWmkG6zBp4+uLo9vaWJDpE94mQHchzj7VjCOkj0FOpqIQO6K6BR7FWizitUieHKfVa1sFtf+UZ5kEu0Jrvzf1sjDgMcOjBCYHEhhL8wZWJBIZUazIrwMRmd9LFsF/yUjHQT0VsgHCZ4hDL+JWmTIju2cpV7TUbcxQkB35Z44kxB7iXyYVQqxMQsea980SiQHeRGUZuqS/FmWthwpUJOSsYitZlpCsYt9ddKDn4sLihB3kC1Q5QCngj7EBlJAoGBAP2+pU5ZpCgcucCJRc2Mkitb2cN/lemKaA6izdIT5JI+B/El+i+kGq4p65ENf5DiXalPb8Lp5u0x2QcguhL4oHqgawMeWUNyejzYqxpCqK+PgZ5CWKEoL/NBGS5kHjGSI3BNpaLRcStU8YnaWgAIP0R/1OI9F1DRfod8EegslAM9AoGBAN0B2BerEQP8zSUb5MNGp1iNpqHQxwlq6OrMHfUNHknt30Ccn5SUEsmXgwMqhjMcsKMCH1V/gWQ6/DPouzRJKZPqNXsRPLdZ/bwuJk+HxestbloNv5NTQ/yhunZCAodpnmTcM3aY071SJjRFBhOrHagWgkTgx7ULfjTAOKUa5RdTAoGAOzYKow7d9JJHcjtWM5Hy5fVICouyMs4eXBP3jWcg9lNNTSN8jzNvGmcj1jRdKTT5gDHONaCZR45TQ4uKgDTOVe8rKY4ibE/BYIN64eTeMiF9sK1wuyKClfQB6UZpmU2twV4NIcJX8zGeL3npeIp0IQrnZWe2EiDgssLr9s64N00CgYEAxylpRfL384L2j9FyNvPNzMRh/IK7SuZYHXdyK6n1uKOvQ3eha3CA+cAa5ViF9TuOhmdcO10bCmsJafH5+eVQnaY7Kbtf3s8vFEz2Y0c0eXnIb0jicm5f3yMTeIyF4OeyQKwbiG2yqnKEUSg+kKzsndQi8tUpwyInSnxKz6bL+lcCgYEA5trnRM4bCD+1JxZHHAaN0WPdYwGdzkyPQ2s7bSW0qSgzwzpSTVlOHOlu7fn+iA7kmPua8DHfKnrQSb3JHCX7zyIW4bkDpTaIQtzQoaHMgfy7f9BCQlz/9W4k+vjIM8Voc5ldln0kJg/wlvdaLCTqYb3Rbb+xhmx7zXRzUGGHeDQ=',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
       // 'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ],

    'wechat' => [
        'app_id'      => 'wxb45b00527a12124b',
        'mch_id'      => '1531728311',
        'key'         => 'd75a49ad6b70a0396fd1be07fc741480',
        'cert_client' => config_path( 'wxpay/apiclient_cert.pem') ,
        'cert_key'    => config_path( 'wxpay/apiclient_key.pem')  ,
        'notify' => 'hjt.lxrs.net',
        'secret'             => 'd5a353466a0daf036a54f2e59010fd4b',
        // 'http' => [
        //     //'cert' => '/usr/local/php/cacert-2019-05-15.pem'

        // ],
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
