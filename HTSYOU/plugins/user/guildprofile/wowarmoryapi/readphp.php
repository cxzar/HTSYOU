<?php
$xml = simplexml_load_file("http://www.wowhead.com/item=75110&xml");

//echo $xml->getName() . "<br />";

foreach($xml->item as $child)
  {
  	echo $child->icon->attributes();
	echo $child->inventorySlot ->attributes();
	break;
  }
?> 