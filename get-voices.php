<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_GET['lang'])){
  $currentUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo 'Invalid request. Enter your request following by '.$currentUrl.'?lang=iso_code_here';
  die;
}
// Load Config Files
$key_file = __DIR__ . "/lib/_service_account.json";
$voices_file = __DIR__ . "/voices.json";
$voices_list = __DIR__ . "/voices-list.json";

// (A) LOAD TTS LIBRARY
require "vendor/autoload.php";
use Google\ApiCore\ApiException;
use Google\Cloud\TextToSpeech\V1\ListVoicesResponse;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
$textToSpeechClient = new TextToSpeechClient(["credentials" => $key_file]); // CHANGE TO YOUR OWN!

// (B) SAVE ENTIRE LIST TO FILE
try {
  $response = $textToSpeechClient->listVoices();
  file_put_contents($voices_file, $response->serializeToJsonString());
} catch (ApiException $ex) { print_r($ex); }
unset($response);

// (C) FILTER ENGLISH ONLY
$all = json_decode(file_get_contents($voices_file), 1);
$lang = [];
foreach ($all["voices"] as $v) { if (substr($v["name"], 0, 2) == $_GET['lang']) {
  $lang[] = [
    "code" => $v["languageCodes"][0],
    "name" => $v["name"],
    "gender" => $v["ssmlGender"]
  ];
}}

// (D) SAVE FILTERED LIST

$inp = file_get_contents($voices_list);
$tempArray = json_decode($inp);
if(!is_null($tempArray) && count($tempArray)>0){  
  $merged = array_merge($tempArray,$lang);
  $jsonData = json_encode($merged); 
} else { 
  $jsonData = json_encode($lang);
}

file_put_contents($voices_list, $jsonData);
echo "Success.";
