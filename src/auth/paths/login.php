<?php

namespace Src\Auth\Paths;

class Login {
  public function pagecontent(){
    return file_get_contents("pages/login.hbs");
  }
}
