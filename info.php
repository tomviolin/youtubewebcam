<?php

$path = 'ZendGdata-1.12.17/library' . PATH_SEPARATOR . 'ZendFramework-1.12.17/library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');


$developerKey="AI39si5vb1iT5QoLLdyhik5mAbZ55ps70Visdvmataqa2lKur3kVvBC8JCX_AWkADpUAhNSkp2MhA6_l7ZIXfyPcg-xVtZ2Kng";
$applicationId = 'Webcam Video Uploader - v1';
$clientId = '';


// authenticate
$authenticationURL= 'https://www.google.com/accounts/ClientLogin';
$httpClient = 
  Zend_Gdata_ClientLogin::getHttpClient(
//              $username = 'UWMFreshwaterWebcam@gmail.com',
              $username = 'freshwaterwebcams@gmail.com',
              $password = 'Webcams56access',
              $service = 'youtube',
              $client = null,
              $source = 'UWMSFSWebcams', // a short string identifying your application
              $loginToken = null,
              $loginCaptcha = null,
              $authenticationURL);

$yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
