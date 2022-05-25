<?php

require 'vendor/autoload.php';

global $AfService;

use AfService\AfService;

$AfService = new AfService(parse_ini_file(__DIR__ . '/config.ini'));

// $AfService->authPoints([
//     'transaction' => [
//         'id' => 123456,
//         'pan' => 101735686,
//         'dateTime' => date('Ymdhis'),
//     ],
//     'payment' => [
//         [
//             'payMeans' => 'C',
//             'amount' => 100,
//         ]
//     ],
//     'amount' => 100,
//     'currency' => 643, // RUB
//     'returnUrl' => 'https://ya.ru',
//     'transactionDate' => date('Ymdhis'),
// ]);

// $AfService->getOrderInfo(['orderId' => 234, 'transactionDate' => date('Ymdhis')]);

// $AfService->registerOrder([
//     'orderId' => 234,
//     'amount' => 1,
//     'currency' => 643, // RUB
//     'returnUrl' => 'https://ya.ru',
//     'transactionDate' => date('Ymdhis'),
// ]);