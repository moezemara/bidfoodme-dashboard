<?php
namespace Src\User;

class Home {

  private $server;
  private $post;
  private $config;
  private $mustache;
  private $username;
  private $template;

  public function __construct($server, $post, $config, $mustache, $username){
    $this->server = $server;
    $this->post = $post;
    $this->config = $config;
    $this->mustache = $mustache;
    $this->username = $username;
    $this->template = file_get_contents("pages/home.hbs");
  }

  public function processRequest(){
    if (!isset($this->post['page'])){return $this->P_home();}
    switch($this->post['page']){
      case 'fetch':
        $response = $this->P_fetch();
        break;
      
      case 'extract':
        $response = $this->P_extract();
        break;

      case 'predict':
        $response = $this->P_predict();
        break;
      
      case 'factor':
        $response = $this->P_factor();
        break;
      
      case 'markcustomer':
        $response = $this->P_markcustomer();
        break;
      
      case 'finalize':
        $response = $this->P_final();
        break;

      case 'factorpush':
        $this->postdata();
        $response = $this->P_predict();
        break;

      case 'markcustomerpush':
        $this->postdata();
        $response = $this->P_predict();
        break;

      case 'search':
        if (!isset($this->post['uuid'])){return $this->P_home();}
        $response = $this->P_search();
        break;
      
      default:
        $response = $this->P_home();
        break;
    }
    return $response;
  }

  private function P_fetch(){
    $fetcheddata = $this->fetchtablecontent();
    $fetcheddata = $this->fixattachment($fetcheddata);
    $tablecontent = file_get_contents("pages/tables/fetch.hbs");
    $tablecontent = $this->mustache->render($tablecontent, array('tablename' => 'fetch table','fetcheddata' => json_encode($fetcheddata)));
    return $this->mustache->render($this->template, array('username' => $this->username, 'pagename' => 'Fetch table', 'pagecontent' => $tablecontent));
  }

  private function P_extract(){
    $fetcheddata = $this->fetchtablecontent();
    $tablecontent = file_get_contents("pages/tables/extract.hbs");
    $tablecontent = $this->mustache->render($tablecontent, array('tablename' => 'extract table','fetcheddata' => json_encode($fetcheddata)));
    return $this->mustache->render($this->template, array('username' => $this->username, 'pagename' => 'Extract table', 'pagecontent' => $tablecontent));
  }

  private function P_predict(){
    $fetcheddata = $this->fetchtablecontent();
    $tablecontent = file_get_contents("pages/tables/predict.hbs");
    $tablecontent = $this->mustache->render($tablecontent, array('tablename' => 'predict table', 'api_url'=> $this->config['API'], 'fetcheddata' => json_encode($fetcheddata)));
    return $this->mustache->render($this->template, array('username' => $this->username, 'pagename' => 'Predict table', 'pagecontent' => $tablecontent));
  }

  private function P_final(){
    $fetcheddata = $this->fetchtablecontent();
    $tablecontent = file_get_contents("pages/tables/final.hbs");
    $tablecontent = $this->mustache->render($tablecontent, array('tablename' => 'final table','fetcheddata' => json_encode($fetcheddata)));
    return $this->mustache->render($this->template, array('username' => $this->username, 'pagename' => 'Final table', 'pagecontent' => $tablecontent));
  }

  private function P_factor(){
    $fetcheddata = $this->fetchtablecontent();
    $tablecontent = file_get_contents("pages/tables/factor.hbs");
    $tablecontent = $this->mustache->render($tablecontent, array('tablename' => 'factor table','fetcheddata' => json_encode($fetcheddata)));
    return $this->mustache->render($this->template, array('username' => $this->username, 'pagename' => 'Factor table', 'pagecontent' => $tablecontent));
  }

  private function P_markcustomer(){
    $fetcheddata = $this->fetchtablecontent();
    $tablecontent = file_get_contents("pages/tables/markcustomer.hbs");
    $tablecontent = $this->mustache->render($tablecontent, array('tablename' => 'Marked customers table', 'api_url'=> $this->config['API'], 'fetcheddata' => json_encode($fetcheddata)));
    return $this->mustache->render($this->template, array('username' => $this->username, 'pagename' => 'Marked customers table', 'pagecontent' => $tablecontent));
  }

  private function P_search(){
    $fetcheddata = $this->fetchtablecontent();
    $fetcheddata->emails = $this->fixattachment($fetcheddata->emails);
    $tablecontent_fetch = file_get_contents("pages/tables/fetch.hbs");
    $tablecontent_extract = file_get_contents("pages/tables/extract.hbs");
    $tablecontent_predict = file_get_contents("pages/tables/predict.hbs");
    $tablecontent_final = file_get_contents("pages/tables/final.hbs");

    $tablecontent_fetch = $this->mustache->render($tablecontent_fetch, array('tablename' => 'fetch table','fetcheddata' => json_encode($fetcheddata->emails)));
    $tablecontent_extract = $this->mustache->render($tablecontent_extract, array('tablename' => 'extract table','fetcheddata' => json_encode($fetcheddata->extracts)));
    $tablecontent_predict = $this->mustache->render($tablecontent_predict, array('tablename' => 'predict table','fetcheddata' => json_encode($fetcheddata->predictions)));
    $tablecontent_final = $this->mustache->render($tablecontent_final, array('tablename' => 'final table','fetcheddata' => json_encode($fetcheddata->final)));

    $pagecontent = $tablecontent_fetch.$tablecontent_extract.$tablecontent_predict.$tablecontent_final;
    return $this->mustache->render($this->template, array('username' => $this->username, 'pagename' => 'search: '.$this->post['uuid'], 'pagecontent' => $pagecontent));
  }

  private function fixattachment($fetcheddata){
    $new_fetcheddata = array();
    foreach ($fetcheddata as $item) {
      if (json_decode($item->attachments) == []){
        $item->attachments = 'empty';
        array_push($new_fetcheddata, $item);
        continue;
      }
      $attachment = json_decode($item->attachments)[0];
      $attachment = $this->config['API'].$attachment;
      $item->attachments = $attachment;
      array_push($new_fetcheddata, $item);
    }
    return $new_fetcheddata;
  }

  private function fetchtablecontent(){
    if ($this->post['page'] == 'search'){
      $url = $this->config['API'].'/'.$this->post['page'].'?uuid='.$this->post['uuid'];
    }elseif($this->post['page'] == 'factorpush' || $this->post['page'] == 'markcustomerpush'){
      $url = $this->config['API'].'/predict';
    }
    else{
      $url = $this->config['API'].'/'.$this->post['page'];
    }
    
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET' );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 25);
    
    $results = curl_exec($curl);
    curl_close($curl);

    if (!$results){
      return 0;
    }

    $response = json_decode($results);

    if ($response->success == 0){
      return 0;
    }

    $message = $response->message;

    return $message;
  }

  private function postdata(){
    if ($this->post['page'] == 'factorpush'){
      $url = $this->config['API'].'/factor';
    }else if($this->post['page'] == 'markcustomerpush'){
      $url = $this->config['API'].'/markcustomer';
    }else{
      $url = $this->config['API'].'/'.$this->post['page'];
    }
    
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/x-www-form-urlencoded',
      'Accept: */*',
      'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36'
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->post));
    curl_setopt($curl, CURLOPT_TIMEOUT, 25);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $results = curl_exec($curl);
    return True;
  }
  
  private function P_home(){
    $this->post['page'] = 'stats';
    $fetcheddata = $this->fetchtablecontent();
    $cardcontent = file_get_contents("pages/cards/stats.hbs");
    $settingscontent = file_get_contents("pages/cards/settings.hbs");
    $pagecontent = $cardcontent."\n".$settingscontent;
    $pagecontent = $this->mustache->render($pagecontent, array('api_url' => $this->config['API'], 'fetcheddata' => json_encode($fetcheddata)));
    return $this->mustache->render($this->template, array('username' => $this->username, 'pagename' => 'Welcome', 'pagecontent' => $pagecontent));
  }
}