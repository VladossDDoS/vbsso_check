<?php

include(ABSPATH . 'wp-content/plugins/vbsso_check/log4php/src/main/php/Logger.php');

Logger::configure(plugin_dir_path(__FILE__) . 'config.xml');


function vbsso_check_add_scripts()
{
    wp_enqueue_script('vbsso-check-main-script', plugins_url() . '/vbsso_check/js/main.js', array('jquery'));
    wp_localize_script('vbsso-check-main-script', 'rest_object', array(
        'api_nonce' => wp_create_nonce('wp_rest'),
        'api_url' => site_url('/wp-json/rest/v1/')
    ));

    wp_enqueue_script('google-captcha', 'https://www.google.com/recaptcha/api.js?onload=reCaptchaCallback&render=explicit');
}

function return_platform_suffix($platform)
{
    $config_data = file_get_contents(plugins_url() . "/vbsso_check/includes/config.json");
    $config_data = json_decode($config_data, true);
    return $config_data[$platform];
}

function isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

function vbsso_check_curl_request($url, $platform)
{
    $log = Logger::getLogger('myLogger');

    $urlTo = $url . return_platform_suffix($platform);
    $curl = curl_init();


    $log->info('curl request here to ' . $urlTo);

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $urlTo
    ]);

    $resp = curl_exec($curl);

    if (!curl_errno($curl)) {
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
            if (isJson($resp)) {
                $json = json_decode($resp, true);
                if (!isset($json['version']) || !isset($json['product'])) {
                    $resp = ' vBSSO is installed but probably corrupted we are unable to verify it.';
                } else {
                    $resp = "vBSSO installed, version - " . $json['version'] . " for php " . $json['php'];
                }
            }
        } elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
            $resp = 'HTTP code = ' . curl_getinfo($curl, CURLINFO_HTTP_CODE) . ", vBSSO is not installed there and unable to verify the following url. ";
        }
    } elseif (curl_error($curl)) {
        $log->error('Something wrong with CURL');
        $log->error('Responded with nothing');
    }

    if (empty($resp)){
        $resp = 'Error, something is wrong';
    }

    curl_close($curl);

    return $resp;
}

function vbsso_check_rest_endpoint()
{
    $namespace = 'rest/v1';

    register_rest_route($namespace, '/vbsso-check/', array(
        'methods' => 'GET',
        'callback' => 'vbsso_check_rest_handler',
        'args' => array(
            'url' => array('required' => true),
            'platform' => array('required' => true)
        )
    ));
}

function vbsso_check_rest_handler($request)
{
    $log = Logger::getLogger('myLogger');
    $log->info('Have request here to rest api.');

    $response = vbsso_check_curl_request($request['url'], $request['platform']);
    $log->info('Responded with ' . $response);
    return new WP_REST_Response($response, 200);
}

add_action('rest_api_init', 'vbsso_check_rest_endpoint');
add_action('wp_enqueue_scripts', 'vbsso_check_add_scripts');