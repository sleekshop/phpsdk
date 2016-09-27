<?php

class ShopobjectsCtl
{

  function __construct()
  {

  }


/*
 * Derives the availability status as a label
 */
private function get_availability_label($qty=0,$qty_warning=0,$allow_override=0,$active=0)
{
	if($active==0 OR $allow_override==1) return("success");
	if($qty<$qty_warning AND $qty>0) return("warning");
	if($qty==0) return("danger");
	return("success");
}

private function get_shopobject_from_xml($so="", $type="product")
{
	$piecearray=array();
	$piecearray["id"]=(int)$so->id;
	$piecearray["class"]=(string)$so->class;
	$piecearray["name"]=(string)$so->name;
	$piecearray["permalink"]=(string)$so->seo->permalink;
	$piecearray["title"]=(string)$so->seo->title;
	$piecearray["description"]=(string)$so->seo->description;
	$piecearray["keywords"]=(string)$so->seo->keywords;
	if($type=="product")
	{
		$piecearray["taxclass"]=(string)$so->metadata->taxclass->attributes()->name;
		$piecearray["taxclass_calculation"]=(string)$so->metadata->taxclass->attributes()->calculation;
		$piecearray["taxclass_value"]=(float)$so->metadata->taxclass;
	    $piecearray["availability_quantity"]=(string)$so->availability->quantity;
	    $piecearray["availability_quantity_warning"]=(string)$so->availability->quantity_warning;
	    $piecearray["availability_allow_override"]=(string)$so->availability->allow_override;
	    $piecearray["availability_active"]=(string)$so->availability->active;
	    $piecearray["availability_label"]=self::get_availability_label($piecearray["availability_quantity"],$piecearray["availability_quantity_warning"],$piecearray["availability_allow_override"],$piecearray["availability_active"]);
	}
	$piecearray["creation_date"]=(string)$so->creation_date;
	$attributes=array();
	foreach($so->attributes->attribute as $attribute)
	{
		$attr=array();
		$attr["type"]=(string)$attribute->attributes()->type;
		$attr["id"]=(int)$attribute->attributes()->id;
		$attr["name"]=(string)$attribute->name;
		$attr["label"]=(string)$attribute->label;
		$attr["value"]=(string)$attribute->value;
		if($attr["type"]=="TXT") $attr["value"]=str_replace("\n","<br>",$attr["value"]);
		if((string)$attribute->attributes()->type=="IMG")
		{
			$width=intval((string)$attribute->width);
			$height=intval((string)$attribute->height);
			if($height!=0){$factor=PRODUCT_IMAGE_THUMB_HEIGHT/$height;}
			$width=intval($width*$factor);
			$height=intval($height*$factor);
			$attr["width"]=$width;
			$attr["height"]=$height;
		}
		if((string)$attribute->attributes()->type=="PRODUCTS")
		{
			$prods=$attribute->value;
			$prods_array=array();
			foreach($prods->product as $prod)
			{
				$piece=self::get_shopobject_from_xml($prod);
				$prods_array[]=$piece;
			}
			$attr["value"]=$prods_array;
		}
		if((string)$attribute->attributes()->type=="SELECT:CHAR")
		{
			foreach($attribute->select->option as $opt)
			{
				$attr["select"][(string)$opt->attributes()->id]=(string)$opt;
			}
		}
    if((string)$attribute->attributes()->type=="SELECT:FLOAT")
    {
      foreach($attribute->select->option as $opt)
      {
        $attr["select"][(float)$opt->attributes()->id]=(string)$opt;
      }
    }
    if((string)$attribute->attributes()->type=="HTML")
    {
      $attr["value"]=htmlspecialchars_decode($attr["value"]);
    }
		$attributes[(string)$attribute->name]=$attr;
	}
	$variations=array();
	foreach($so->variations->product as $var)
	{
		$variation=self::get_shopobject_from_xml($var);
		$variations[]=$variation;
	}
	$piecearray["attributes"]=$attributes;
	$piecearray["variations"]=$variations;
	return($piecearray);
}



private function get_products_from_xml($xml="")
{
	$result=array();
	foreach($xml->product as $so)
	{
     $result[(string)$so->name]=self::get_shopobject_from_xml($so, "product");
	}
	return($result);
}

private function get_contents_from_xml($xml="")
{
	$result=array();
	foreach($xml->content as $so)
	{
		$result[(string)$so->name]=self::get_shopobject_from_xml($so,"content");
		$result["byclass"][(string)$so->class][]=$result[(string)$so->name];
	}
	return($result);
}



/*
 * Delivers an array containing all categories with the parent defined by $id_parent
 */
public function GetShopobjects($id_category=0,$lang=DEFAULT_LANGUAGE,$order_column="",$order="ASC",$left_limit=0,$right_limit=0,$needed_attributes=array(),$country=DEFAULT_COUNTRY)
 {
  $sr=new SleekShopRequest();
  $xml=$sr->get_shopobjects_in_category($id_category,$lang,$order_column,$order,$left_limit,$right_limit,$needed_attributes,$country);
  $xml=new SimpleXMLElement($xml);
  $result=array();
  $result["id_category"]=(int)$xml->category->id;
  $result["name"]=(string)$xml->category->name;
  $result["permalink"]=(string)$xml->category->seo->permalink;
  $result["title"]=(string)$xml->category->seo->title;
  $result["description"]=(string)$xml->category->seo->description;
  $result["keywords"]=(string)$xml->category->seo->keywords;
  $attributes=array();
  foreach($xml->category->attributes->attribute as $attr)
  {
  	$attributes[(string)$attr->attributes()->name]=(string)$attr;
  }
  $result["attributes"]=$attributes;
  $result["products"]=self::get_products_from_xml($xml->products);
  $result["contents"]=self::get_contents_from_xml($xml->contents);
  return($result);
}


/*
 * Delivers an array containing all categories with the parent defined by $permalink
*/
public function SeoGetShopobjects($permalink,$lang=DEFAULT_LANGUAGE,$order_column="",$order="ASC",$left_limit=0,$right_limit=0,$needed_attributes=array(),$country=DEFAULT_COUNTRY)
{
	$sr=new SleekShopRequest();
	$xml=$sr->seo_get_shopobjects_in_category($permalink,$lang,$order_column,$order,$left_limit,$right_limit,$needed_attributes,$country);
	$xml=new SimpleXMLElement($xml);
	$result=array();
	$result["id_category"]=(int)$xml->category->id;
	$result["name"]=(string)$xml->category->name;
	$result["permalink"]=(string)$xml->category->seo->permalink;
	$result["title"]=(string)$xml->category->seo->title;
	$result["description"]=(string)$xml->category->seo->description;
	$result["keywords"]=(string)$xml->category->seo->keywords;
	$attributes=array();
	foreach($xml->category->attributes->attribute as $attr)
	{
		$attributes[(string)$attr->attributes()->name]=(string)$attr;
	}
	$result["attributes"]=$attributes;
	$result["products"]=self::get_products_from_xml($xml->products);
	$result["contents"]=self::get_contents_from_xml($xml->contents);
	return($result);
}





/*
 * Delivers the shopobject - details of a given shopobject determined by its id
 */
public function GetProductDetails($id_product=0,$lang=DEFAULT_LANGUAGE,$country=DEFAULT_COUNTRY)
{
	$sr=new SleekShopRequest();
	$xml=$sr->get_product_details($id_product,$lang,$country);
	$xml=new SimpleXMLElement($xml);
	$result=self::get_shopobject_from_xml($xml);
	return($result);
}


/*
 * Delivers the shopobject - details of a given shopobject determined by its id
*/
public function GetContentDetails($id_content=0,$lang=DEFAULT_LANGUAGE)
{
	$sr=new SleekShopRequest();
	$xml=$sr->get_content_details($id_content,$lang,"content");
	$xml=new SimpleXMLElement($xml);
	$result=self::get_shopobject_from_xml($xml,"content");
	return($result);
}



/*
 * Delivers Shopobject - Details given a permalink
 */
public function SeoGetProductDetails($permalink="")
{
	$sr=new SleekShopRequest();
	$xml=$sr->seo_get_product_details($permalink);
	$xml=new SimpleXMLElement($xml);
	$result=self::get_shopobject_from_xml($xml);
	return($result);
}


/*
 * Delivers Shopobject - Details given a permalink
*/
public function SeoGetContentDetails($permalink="")
{
	$sr=new SleekShopRequest();
	$xml=$sr->seo_get_content_details($permalink);
	$xml=new SimpleXMLElement($xml);
	$result=self::get_shopobject_from_xml($xml);
	return($result);
}


/*
 * Search
*/
public function SearchProducts($constraint=array(),$left_limit,$right_limit,$order_columns=array(),$order_type="ASC",$lang=DEFAULT_LANGUAGE,$needed_attributes=array())
{
	$sr=new SleekShopRequest();
	$xml=$sr->search_products($constraint,$left_limit,$right_limit,$order_columns,$order_type,$lang,$needed_attributes);
	$xml=new SimpleXMLElement($xml);
    $result["products"]=self::get_products_from_xml($xml);
    $result["count"]=(int)$xml->count;
    return($result);
}


}

?>
