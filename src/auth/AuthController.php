<?php
namespace Src\Auth;

class AuthController {
  private $config;
  private $server;
  private $session;

  public function __construct($config, $server, $session){
    $this->config = $config;
    $this->server = $server;
    $this->session = $session;
  }

  public function processRequest(){
    switch ($this->server['REQUEST_METHOD']) {
      case 'POST':
      case 'GET':
        $response = $this->process();
        break;

      default:
        $response = $this->notFoundResponse();
        break;
    }
    return $response;
  }

  private function process(){
    if(!isset($this->session['Authorization'])){
      return 0;
    }

    if (preg_match('/Bearer\s(\S+)/', $this->session['Authorization'], $matches)){

      if (!isset($matches[1])){ //no token matched
        return 0;
      }

      $token = $matches[1];

      $verify_response = $this->verify($token);

      if ($verify_response['success'] == 0){
        return 0;
      }else{
        if(!isset($verify_response['message']->username)){
          return 0;
        }
        return $verify_response['message']->username;
      }
      
    }else{
      return 0;
    }
    
  }

  private function verify($token){
    $url = $this->config['API_VERIFY'].$token;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET' );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 25);
    
    $results = curl_exec($curl);
    curl_close($curl);

    if (!$results){
      $response = array('success' => 0, 'message' => 'Connection time out');
      return $response;
    }

    $response = array('success' => 1, 'message' => json_decode($results)->message);
    return $response;
  }

  private function notFoundResponse(){
    return 0;
  }
}