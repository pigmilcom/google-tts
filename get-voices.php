<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_GET['lang'])){
  $currentUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo 'Invalid request. Enter your request following by '.$currentUrl.'?lang=iso_code_here';
  die;
}
// (A) LOAD TTS LIBRARY
require "vendor/autoload.php";
use Google\ApiCore\ApiException;
use Google\Cloud\TextToSpeech\V1\ListVoicesResponse;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
$key_file = __DIR__ . "/lib/_service_account.json";
$textToSpeechClient = new TextToSpeechClient(["credentials" => $key_file]); // CHANGE TO YOUR OWN!

// (B) SAVE ENTIRE LIST TO FILE
try {
  $response = $textToSpeechClient->listVoices();
  file_put_contents("voices.json", $response->serializeToJsonString());
} catch (ApiException $ex) { print_r($ex); }
unset($response);

// (C) FILTER ENGLISH ONLY
$all = json_decode(file_get_contents("voices.json"), 1);
$en = [];
foreach ($all["voices"] as $v) { if (substr($v["name"], 0, 2) == $_GET['lang']) {
  $en[] = [
    "code" => $v["languageCodes"][0],
    "name" => $v["name"],
    "gender" => $v["ssmlGender"]
  ];
}}

// (D) SAVE FILTERED LIST
file_put_contents("voices-list.json", json_encode($en));
echo "Success.";