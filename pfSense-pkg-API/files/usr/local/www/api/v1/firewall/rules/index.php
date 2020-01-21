<?php

// IMPORTS--------------------------------------------------------------------------------------------------------------
require_once("config.inc");
require_once("auth.inc");
require_once("functions.inc");
require_once("api.inc");

// HEADERS--------------------------------------------------------------------------------------------------------------header('Content-Type: application/json');
header("Referer: no-referrer");

// VARIABLES------------------------------------------------------------------------------------------------------------
global $config, $api_resp, $client_params;
$read_only_action = true;    // Set whether this action requires read only access
$req_privs = array("page-all", "page-firewall-rules");    // Array of privs allowed
$http_method = $_SERVER['REQUEST_METHOD'];    // Save our HTTP method
$rule_array = array();    // Init our return array

// RUN TIME-------------------------------------------------------------------------------------------------------------
// Check that client is authenticated and authorized
if (api_authorized($req_privs, $read_only_action)) {
    // Check that our HTTP method is GET (READ)
    if ($http_method === 'GET') {
        // Check that we have a configuration
        if (!empty($config["filter"]["rule"])) {
            $rule_array = $config["filter"]["rule"];
        }
        if (isset($client_params['search'])) {
            $search = $client_params['search'];
            $rule_array = api_extended_search($rule_array, $search);
        }
        // Print our JSON response
        $api_resp = array("status" => "ok", "code" => 200, "return" => 0, "message" => "", "data" => $rule_array);
        http_response_code(200);
        echo json_encode($api_resp) . PHP_EOL;
        die();
    } else {
        $api_resp = array("status" => "bad request", "code" => 400, "return" => 2, "message" => "invalid http method");
        http_response_code(400);
        echo json_encode($api_resp) . PHP_EOL;
        die();
    }
} else {
    http_response_code(401);
    echo json_encode($api_resp) . PHP_EOL;
    die();
}