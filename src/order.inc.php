<?php

namespace Sleekshop;

class OrderCtl
{

  function __construct()
  {

  }

private static function get_order_details($xml="")
{
	$result=array();
	$result["id_user"]=(int)$xml->user->id_user;
	$result["username"]=(string)$xml->user->username;
	$result["order_id_payment_method"]=(int)$xml->order_payment_method->id;
	$result["order_payment_method"]=(string)$xml->order_payment_method->name;
	$result["order_id_delivery_method"]=(int)$xml->order_delivery_method->id;
	$result["order_delivery_method"]=(string)$xml->order_delivery_method->name;
	$result["order_id_payment_state"]=(int)$xml->order_payment_state->id;
	$result["order_payment_state"]=(string)$xml->order_payment_state->name;
	$result["order_id_delivery_state"]=(int)$xml->order_delivery_state->id;
	$result["order_delivery_state"]=(string)$xml->order_delivery_state->name;
	$result["id_order_state"]=(int)$xml->order_state->id;
	$result["order_state"]=(string)$xml->order_state->name;
	$result["id_order_type"]=(int)$xml->order_type->id;
	$result["order_type"]=(string)$xml->order_type->name;
	$result["order_number"]=(string)$xml->order_number;
	$result["username_creator"]=(string)$xml->username_creator;
	$result["creation_date"]=(string)$xml->creation_date;
	$result["username_modifier"]=(string)$xml->username_modifier;
	$result["modification_date"]=(string)$xml->modification_date;
	$result["delivery_companyname"]=(string)$xml->delivery_companyname;
	$result["delivery_department"]=(string)$xml->delivery_department;
	$result["delivery_salutation"]=(string)$xml->delivery_salutation;
	$result["delivery_firstname"]=(string)$xml->delivery_firstname;
	$result["delivery_lastname"]=(string)$xml->delivery_lastname;
	$result["delivery_street"]=(string)$xml->delivery_street;
	$result["delivery_number"]=(string)$xml->delivery_number;
	$result["delivery_zip"]=(string)$xml->delivery_zip;
	$result["delivery_state"]=(string)$xml->delivery_state;
	$result["delivery_city"]=(string)$xml->delivery_city;
	$result["delivery_country"]=(string)$xml->delivery_country;
	$result["invoice_companyname"]=(string)$xml->invoice_companyname;
	$result["invoice_department"]=(string)$xml->invoice_department;
	$result["invoice_salutation"]=(string)$xml->invoice_salutation;
	$result["invoice_firstname"]=(string)$xml->invoice_firstname;
	$result["invoice_lastname"]=(string)$xml->invoice_lastname;
	$result["invoice_street"]=(string)$xml->invoice_street;
	$result["invoice_number"]=(string)$xml->invoice_number;
	$result["invoice_zip"]=(string)$xml->invoice_zip;
	$result["invoice_state"]=(string)$xml->invoice_state;
	$result["invoice_city"]=(string)$xml->invoice_city;
	$result["invoice_country"]=(string)$xml->invoice_country;
	$result["note"]=(string)$xml->note;
	$result["email"]=(string)$xml->email;
	return($result);
}


/*
 * Set order details
 */
public static function SetOrderDetails($session="",$args=array())
 {
 	$sr=new SleekShopRequest();
 	$xml=$sr->set_order_details($session,$args);
 	$xml=new SimpleXMLElement($xml);
 	return(self::get_order_details($xml));
 }


 /*
  * Gets the order details
  */
 public static function GetOrderDetails($session="")
 {
 	$sr=new SleekShopRequest();
 	$xml=$sr->get_order_details($session,$args);
 	$xml=new SimpleXMLElement($xml);
 	return(self::get_order_details($xml));
 }


/*
 * Adds the delivery_costs to the order
 */
 public static function AddDeliveryCosts($session="",$delivery_costs=array())
 {
 	$sr=new SleekShopRequest();
 	$xml=$sr->add_delivery_costs($session,$delivery_costs);
 }


/*
 * Checks out the order
 */
 public static function Checkout($session="")
 {
 	$sr=new SleekShopRequest();
 	$xml=$sr->checkout($session);
 	$xml=new SimpleXMLElement($xml);
 	$result=array();
    $result["status"]=(string)$xml->status;
    $result["id_order"]=(int)$xml->id_order;
    $result["session"]=(string)$xml->session;
    $result["message"]=(string)$xml->message;
    $result["param"]=(string)$xml->param;
    return($result);
 }

 /*
  * Searching the orders
  */
  public static function SearchOrders($constraint=array(),$left_limit=0,$right_limit=0,$order_columns=array(),$order_type="ASC",$language=DEFAULT_LANGUAGE)
  {
  	$sr=new SleekShopRequest();
  	$xml=$sr->search_orders($constraint,$left_limit,$right_limit,$order_columns,$order_type,$language);
    $xml=new SimpleXMLElement($xml);
    $result=array();
    foreach($xml->order as $order)
    {
      $piece=array();
      $piece["id"]=(int)$order->id;
      $piece["order_payment_method"]=(string)$order->payment_method;
      $piece["order_delivery_method"]=(string)$order->delivery_method;
      $piece["order_payment_state"]=(string)$order->payment_state->name;
      $piece["order_delivery_state"]=(string)$order->delivery_state->name;
      $piece["order_state"]=(string)$order->order_state;
      $piece["order_number"]=(string)$order->order_number;
      $piece["creation_date"]=(string)$order->creation_date;
      $piece["delivery_companyname"]=(string)$order->delivery_companyname;
      $piece["delivery_department"]=(string)$order->delivery_department;
      $piece["delivery_salutation"]=(string)$order->order_delivery_salutation;
      $piece["delivery_firstname"]=(string)$order->order_delivery_firstname;
      $piece["delivery_lastname"]=(string)$order->order_delivery_lastname;
      $piece["delivery_street"]=(string)$order->order_delivery_street;
      $piece["delivery_number"]=(string)$order->order_delivery_number;
      $piece["delivery_zip"]=(string)$order->order_delivery_zip;
      $piece["delivery_state"]=(string)$order->order_delivery_state;
      $piece["delivery_city"]=(string)$order->order_delivery_city;
      $piece["delivery_country"]=(string)$order->order_delivery_country;
      $piece["invoice_companyname"]=(string)$order->order_invoice_companyname;
      $piece["invoice_department"]=(string)$order->order_invoice_department;
      $piece["invoice_salutation"]=(string)$order->order_invoice_salutation;
      $piece["invoice_firstname"]=(string)$order->order_invoice_firstname;
      $piece["invoice_lastname"]=(string)$order->order_invoice_lastname;
      $piece["invoice_street"]=(string)$order->order_invoice_street;
      $piece["invoice_number"]=(string)$order->order_invoice_number;
      $piece["invoice_zip"]=(string)$order->order_invoice_zip;
      $piece["invoice_state"]=(string)$order->order_invoice_state;
      $piece["invoice_city"]=(string)$order->order_invoice_city;
      $piece["invoice_country"]=(string)$order->order_invoice_country;
      $piece["note"]=(string)$order->order_note;
      $piece["email"]=(string)$order->order_email;
      $piece["cart"]=CartCtl::get_cart_array($order->cart);
      $result[]=$piece;
    }
  	return($result);
  }
}

?>
