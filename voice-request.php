<?php
// (A) LOAD TTS LIBRARY
require "vendor/autoload.php";
use Google\ApiCore\ApiException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechResponse;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

// (B) TEXT INPUT
$input = new SynthesisInput();
$input->setText($_POST["txt"]);
// $input->setSsml("<speak>" . $_POST["txt"] . "</speak>");

// (C) VOICE SETTING
$v = explode("@", $_POST["voice"]);
$voice = new VoiceSelectionParams();
$voice->setLanguageCode($v[0]);
$voice->setName($v[1]);
$audioConfig = new AudioConfig();
$audioConfig->setAudioEncoding(AudioEncoding::MP3);
$audioConfig->setSpeakingRate($_POST["rate"]); // 0.25 to 4.0
$audioConfig->setPitch($_POST["pitch"]); // -20 to 20
$audioConfig->setVolumeGainDb($_POST["gain"]); // -96 to 16

// (D) TEXT TO SPEECH 
$key_file = __DIR__ . "/lib/_service_account.json";
$textToSpeechClient = new TextToSpeechClient(["credentials" => $key_file]); // CHANGE TO YOUR OWN!
$res = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);
file_put_contents("demo.mp3", $res->getAudioContent());

header("Location: index.php?play");
exit();
?>