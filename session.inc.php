<?php

class SessionCtl
{

  function __construct()
  {

  }

/*
 * Delivers a valid session and returns it
 */
public static function GetSession()
 {
  $sr=new SleekShopRequest();
  $xml=$sr->get_new_session();
  $xml=new SimpleXMLElement($xml);
  $code=(string)$xml->code;
  return($code);
}


}

?>
