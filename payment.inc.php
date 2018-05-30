<?php

class PaymentCtl
{

  function __construct()
  {

  }



public static function GetPaymentMethods()
{
	$sr=new SleekShopRequest();
	$xml=$sr->get_payment_methods();
	$xml=new SimpleXMLElement($xml);
	$result=array();
	foreach($xml->payment_method as $method)
	{
		$piecearray=array();
		$piecearray["id"]=(int)$method->id;
		$piecearray["name"]=(string)$method->name;
		foreach($method->attributes->attribute as $attr)
		{
			$piecearray["attributes"][(string)$attr->attributes()->name]=(string)$attr;
		}
		$result[(string)$method->name]=$piecearray;
	}
	return($result);
}

/*
 * Initiates the Payment
 */
 public static function DoPayment($id_order=0,$args=array())
 {
 	$sr=new SleekShopRequest();
 	$xml=$sr->do_payment($id_order,$args);
 	$xml=new SimpleXMLElement($xml);
 	$result=array();
 	$result["method"]=(string)$xml->method;
 	$result["status"]=(string)$xml->status;
 	$result["redirect"]=html_entity_decode((string)($xml->redirect));
 	return($result);
 }

}

?>
