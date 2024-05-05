<?php
if (!defined('ABSPATH')) exit;

$scope = "create";
$text = esc_html__("Create New Test", "convert-pro");
$test = (object) [
    'name' => '',
    'variations' =>  [
        (object) [
            'id' => 'null',
            'name' => 'v1',
            'percentage' => '50'
        ],
        (object) [
            'id' => null,
            'name' => 'v2',
            'percentage' => '50'
        ]
    ]
];


include(__DIR__ . "/form-view.php");
