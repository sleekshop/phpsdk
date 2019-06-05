<?php

namespace Sleekshop;

class CartCtl
{

  function __construct()
  {

  }


 public static function get_cart_array($xml="")
 {
 	$result=array();
 	$result["sum"]=(float)$xml->sum;
 	$result["last_inserted_id"]=(int)$xml->last_inserted_element_id;
  $result["sum_count"]=0;
 	$contents=array();
 	foreach($xml->contents->element as $element)
 	{
 		$piece=array();
 		$piece["type"]=(string)$element->attributes()->type;
 		$piece["id"]=(int)$element->id;
 		$piece["id_product"]=(int)$element->id_product;
 		$piece["quantity"]=(float)$element->quantity;
    $result["sum_count"]+=$piece["quantity"];
 		$piece["price"]=(float)$element->price;
 		$piece["sum_price"]=(float)$element->sum_price;
 		$piece["name"]=(string)$element->name;
 		$piece["description"]=(string)$element->description;
 		$attributes=array();
 		foreach($element->attributes->attribute as $attr)
 		{
 			$attributes[(string)$attr->attributes()->name]=(string)$attr;
 		}
 		$piece["attributes"]=$attributes;
    $subs=array();
if(isset($element->contents))
{
 foreach($element->contents->element as $sub)
  {
   $sub_piece=array();
   $sub_piece["type"]=(string)$sub->attributes()->type;
   $sub_piece["id"]=(int)$sub->id;
   $sub_piece["id_product"]=(int)$sub->id_product;
   $sub_piece["quantity"]=(float)$sub->quantity;
   $sub_piece["price"]=(float)$sub->price;
   $sub_piece["sum_price"]=(float)$sub->sum_price;
   $sub_piece["name"]=(string)$sub->name;
   $sub_piece["description"]=(string)$sub->description;
   $subs[]=$sub_piece;
  }
$piece["contents"]=$subs;
}
 		$contents[]=$piece;
 	}
 	$delivery_costs=array();
 	$delivery_costs["sum"]=(float)$xml->delivery_costs->sum;
 	foreach($xml->delivery_costs->positions->position as $pos)
 	 {
 	  $piece=array();
 	  $piece["name"]=(string)$pos->name;
 	  $piece["price"]=(float)$pos->price;
 	  $piece["tax"]=(float)$pos->tax;
 	  $delivery_costs[(string)$pos->name]=$piece;
 	 }
 	$result["contents"]=$contents;
 	$result["delivery_costs"]=$delivery_costs;
 	return($result);
 }



/*
 * Adds an element to the cart
 */
public static function Add($session="",$id_product=0,$quantity=0,$price_field="",$name_field="",$description_field="",$language=DEFAULT_LANGUAGE,$element_type="PRODUCT_GR",$id_parent_element=0,$attributes=array())
 {
  $sr=new SleekShopRequest();
  $xml=$sr->add_to_cart($session,$id_product,$quantity,$price_field,$name_field,$description_field,$language,$element_type,$id_parent_element,$attributes);
  $xml=new SimpleXMLElement($xml);
  $cart=self::get_cart_array($xml);
  setcookie("cart",serialize($cart));
  return($cart);
 }


 /*
  * Deletes an element from the cart
  */
 public static function Del($session="",$id_element=0)
 {
 	$sr=new SleekShopRequest();
 	$xml=$sr->sub_from_cart($session,$id_element);
 	$xml=new SimpleXMLElement($xml);
 	$cart=self::get_cart_array($xml);
 	setcookie("cart",serialize($cart));
 	return($cart);
 }


 /*
  * Returns the current cart
  */
 public static function Get($session="")
 {
 	if($_COOKIE["cart"]=="" OR 1)
 	{
 	$sr=new SleekShopRequest();
 	$xml=$sr->get_cart($session);
 	$xml=new SimpleXMLElement($xml);
 	$cart=self::get_cart_array($xml);
 	setcookie("cart",serialize($cart));
 	}
 	$cart=unserialize(stripslashes($_COOKIE["cart"]));
 	return($cart);
 }

/*
 * Gets a new cart from the server
 */
public static function Refresh($session="")
{
	$sr=new SleekShopRequest();
	$xml=$sr->get_cart($session);
	$xml=new SimpleXMLElement($xml);
	$cart=self::get_cart_array($xml);
	setcookie("cart",serialize($cart));
	return($cart);
}

}

?>
