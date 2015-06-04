<?php
	if($_SERVER["REQUEST_METHOD"]== "GET")

{
		$zip = $_GET["location"];
		$t = $_GET["type"];
		$unit = $_GET["tempUnit"];
		if($t=="City")
		{
				$zip = urlencode($zip);
				$type = 7;
				$type = urlencode($type);
				$query = '$and(' . ".q('$zip')" . ",.type($type));";
				$api = 'http://where.yahooapis.com/v1/places';
				$url = $api.$query;
				$a= $url."start=0;count=5?appid=R8FTvNnV34Eyzdt9uZZGbRXbYbPnEI.M1zF.XkwIOAQUKo.tCCyLOL610dShRUeS0wTn4KHezLXC9UlsUsA2iA--";
				$file = "weather.xml";
				$data = @file_get_contents($a);
				$data1="";
				@file_put_contents($file, $data);
				$xml = simplexml_load_file($file);
				$woeid = array();
				
				foreach($xml->place as $place)
				{
					$woeid['i'] = $place->woeid;
					
					if($unit=="f")
					{
						$data1= @file_get_contents("http://weather.yahooapis.com/forecastrss?w={$woeid['i']}&u=f");
						@file_put_contents($file, $data1);
						if($xml = simplexml_load_file($file))
						{
							$error = strpos(strtolower($xml->channel->description), 'error');
						}
						if(!$error)
						{
							$description = $xml->channel->item->description;
					$imgpattern = '/src="(.*?)"/i'; 
					preg_match($imgpattern, $description, $matches);
					$imgdesc  = '/href="(.*?)"/i';
					$weather['icon_url']= $matches[1];
					preg_match($imgdesc, $description, $matches);
					$weather['icon_desc']= $matches[1];
					$weather['temp'] = $xml->channel->item->children('yweather', TRUE)->condition->attributes()->temp;  
					$weather['units'] = $xml->channel->children('yweather', TRUE)->units->attributes()->temperature;
					$weather['conditions'] = $xml->channel->item->children('yweather', TRUE)->condition->attributes()->text;
					$weather['city'] = $xml->channel->children('yweather', TRUE)->location->attributes()->city;
					$weather['region'] = $xml->channel->children('yweather', TRUE)->location->attributes()->region;
					$weather['country'] = $xml->channel->children('yweather', TRUE)->location->attributes()->country;
					$weather['lat'] = $xml->channel->item->children('geo', TRUE)->lat;
					$weather['long'] = $xml->channel->item->children('geo', TRUE)->long;
					$weather['link_details'] = $xml->channel->link;
					
					$dom = new DOMDocument("1.0");
							$root = $dom->createElement("weather");
							$dom->appendChild($root);
							
							$feed = $dom->createElement("feed");
							$root->appendChild($feed);
							$text1 = $dom->createTextNode("http://weather.yahooapis.com/forecastrss?w={$woeid['i']}&u=f");
							
							$feed->appendChild($text1);
							
							$link = $dom->createElement("link");
							$root->appendChild($link);
							$text2 = $dom->createTextNode($weather['link_details']);
							$link->appendChild($text2);
							
							$location = $dom->createElement("location");
							$root->appendChild($location);
							$city = $dom->createAttribute("city");
							$location->appendChild($city);// create attribute value node
							$cityValue = $dom->createTextNode($weather['city']);
							$city->appendChild($cityValue);
							$region= $dom->createAttribute("region");
							$location->appendChild($region);
							$regionValue = $dom->createTextNode($weather['region']);
							$region->appendChild($regionValue);
							$country = $dom->createAttribute("country");
							$location->appendChild($country);
							$countryValue = $dom->createTextNode($weather['country']);
							$country->appendChild($countryValue);
							
							$units = $dom->createElement("units");
							$root->appendChild($units);
							$temperature = $dom->createAttribute("temperature");
							$units->appendChild($temperature);
							$tempValue = $dom->createTextNode($weather['units']);
							$temperature->appendChild($tempValue);
							
							$condition = $dom->createElement("condition");
							$root->appendChild($condition);
							$text = $dom->createAttribute("text");
							$condition->appendChild($text);
							$textValue = $dom->createTextNode($weather['conditions']);
							$text->appendChild($textValue);
							$temp = $dom->createAttribute("temp");
							$condition->appendChild($temp);
							$tempValue = $dom->createTextNode($weather['temp']);
							$temp->appendChild($tempValue);
							
							$img = $dom->createElement("img");
							$root->appendChild($img);
							$text3 = $dom->createTextNode($weather['icon_url']);
							$img->appendChild($text3); 
							
							foreach($xml->channel->item->children('yweather', TRUE)->forecast as $forecast)
							{
							$weather['day'] = $forecast->attributes()->day;
							//echo $weather['day'];
							$weather['low'] = $forecast->attributes()->low;
							//echo $weather['low'];
							$weather['high'] =$forecast->attributes()->high;
							$weather['text'] =$forecast->attributes()->text;
				
							$f = $dom->createElement("forecast");
							$root->appendChild($f);
							$day = $dom->createAttribute("day");
							$f->appendChild($day);
							$dayValue = $dom->createTextNode($weather['day']);
							$day->appendChild($dayValue);
							$low = $dom->createAttribute("low");
							$f->appendChild($low);
							$lowValue = $dom->createTextNode($weather['low']);
							$low->appendChild($lowValue);
							$high = $dom->createAttribute("high");
							$f->appendChild($high);
							$highValue = $dom->createTextNode($weather['high']);
							$high->appendChild($highValue);
							$text = $dom->createAttribute("text");
							$f->appendChild($text);
							$textValue = $dom->createTextNode($weather['text']);
							$text->appendChild($textValue);
							}
							$dom->save("order.xml");
							echo $dom->saveXML();
							break;
						}	
					}
					if($unit=="c")
					{
						$data1= @file_get_contents("http://weather.yahooapis.com/forecastrss?w={$woeid['i']}&u=c");
						@file_put_contents($file, $data1);
						if($xml = simplexml_load_file($file))
						{
							$error = strpos(strtolower($xml->channel->description), 'error');//server response but no weather data for woeid
						}
						else
						{
							$error = TRUE;//no response from weather server
						}
						if(!$error)
						{
							$description = $xml->channel->item->description;
					$imgpattern = '/src="(.*?)"/i'; 
					preg_match($imgpattern, $description, $matches);
					$imgdesc  = '/href="(.*?)"/i';
					$weather['icon_url']= $matches[1];
					preg_match($imgdesc, $description, $matches);
					$weather['icon_desc']= $matches[1];
					$weather['temp'] = $xml->channel->item->children('yweather', TRUE)->condition->attributes()->temp;  
					$weather['units'] = $xml->channel->children('yweather', TRUE)->units->attributes()->temperature;
					$weather['conditions'] = $xml->channel->item->children('yweather', TRUE)->condition->attributes()->text;
					$weather['city'] = $xml->channel->children('yweather', TRUE)->location->attributes()->city;
					$weather['region'] = $xml->channel->children('yweather', TRUE)->location->attributes()->region;
					$weather['country'] = $xml->channel->children('yweather', TRUE)->location->attributes()->country;
					$weather['lat'] = $xml->channel->item->children('geo', TRUE)->lat;
					$weather['long'] = $xml->channel->item->children('geo', TRUE)->long;
					$weather['link_details'] = $xml->channel->link;
					
					$dom = new DOMDocument("1.0");
							$root = $dom->createElement("weather");
							$dom->appendChild($root);
							
							$feed = $dom->createElement("feed");
							$root->appendChild($feed);
							$text1 = $dom->createTextNode("http://weather.yahooapis.com/forecastrss?w={$woeid['i']}&u=c");
							
							$feed->appendChild($text1);
							
							$link = $dom->createElement("link");
							$root->appendChild($link);
							$text2 = $dom->createTextNode($weather['link_details']);
							$link->appendChild($text2);
							
							$location = $dom->createElement("location");
							$root->appendChild($location);
							$city = $dom->createAttribute("city");
							$location->appendChild($city);// create attribute value node
							$cityValue = $dom->createTextNode($weather['city']);
							$city->appendChild($cityValue);
							$region= $dom->createAttribute("region");
							$location->appendChild($region);
							$regionValue = $dom->createTextNode($weather['region']);
							$region->appendChild($regionValue);
							$country = $dom->createAttribute("country");
							$location->appendChild($country);
							$countryValue = $dom->createTextNode($weather['country']);
							$country->appendChild($countryValue);
							
							$units = $dom->createElement("units");
							$root->appendChild($units);
							$temperature = $dom->createAttribute("temperature");
							$units->appendChild($temperature);
							$tempValue = $dom->createTextNode($weather['units']);
							$temperature->appendChild($tempValue);
							
							$condition = $dom->createElement("condition");
							$root->appendChild($condition);
							$text = $dom->createAttribute("text");
							$condition->appendChild($text);
							$textValue = $dom->createTextNode($weather['conditions']);
							$text->appendChild($textValue);
							$temp = $dom->createAttribute("temp");
							$condition->appendChild($temp);
							$tempValue = $dom->createTextNode($weather['temp']);
							$temp->appendChild($tempValue);
							
							$img = $dom->createElement("img");
							$root->appendChild($img);
							$text3 = $dom->createTextNode($weather['icon_url']);
							$img->appendChild($text3); 
							
							foreach($xml->channel->item->children('yweather', TRUE)->forecast as $forecast)
							{
							$weather['day'] = $forecast->attributes()->day;
							//echo $weather['day'];
							$weather['low'] = $forecast->attributes()->low;
							//echo $weather['low'];
							$weather['high'] =$forecast->attributes()->high;
							$weather['text'] =$forecast->attributes()->text;
				
							$f = $dom->createElement("forecast");
							$root->appendChild($f);
							$day = $dom->createAttribute("day");
							$f->appendChild($day);
							$dayValue = $dom->createTextNode($weather['day']);
							$day->appendChild($dayValue);
							$low = $dom->createAttribute("low");
							$f->appendChild($low);
							$lowValue = $dom->createTextNode($weather['low']);
							$low->appendChild($lowValue);
							$high = $dom->createAttribute("high");
							$f->appendChild($high);
							$highValue = $dom->createTextNode($weather['high']);
							$high->appendChild($highValue);
							$text = $dom->createAttribute("text");
							$f->appendChild($text);
							$textValue = $dom->createTextNode($weather['text']);
							$text->appendChild($textValue);
							}
							$dom->save("order.xml");
							echo $dom->saveXML();
							break;
							
							
						}
					}	
				}
			}
			
		
		elseif($t=="Zip")
		{
			
				$file = "weather.xml";
				$data = @file_get_contents("http://where.yahooapis.com/v1/concordance/usps/{$zip}?appid=R8FTvNnV34Eyzdt9uZZGbRXbYbPnEI.M1zF.XkwIOAQUKo.tCCyLOL610dShRUeS0wTn4KHezLXC9UlsUsA2iA--");
				
				@file_put_contents($file, $data);
				$xml = simplexml_load_file($file);
				$woeid['w'] = $xml->woeid;
				if($unit=="f")
				{
					$data1 = @file_get_contents("http://weather.yahooapis.com/forecastrss?w={$woeid['w']}&u=f");
				}
				else
				{
					$data1 = @file_get_contents("http://weather.yahooapis.com/forecastrss?w={$woeid['w']}&u=c");
				}
				@file_put_contents($file, $data1);
				if($xml = simplexml_load_file($file))
				{
					$error = strpos(strtolower($xml->channel->description), 'error');//server response but no weather data for woeid
				}
				else
				{
					$error = TRUE;//no response from weather server
				}
				if(!$error)
				{
					$description = $xml->channel->item->description;
					$imgpattern = '/src="(.*?)"/i'; 
					preg_match($imgpattern, $description, $matches);
					$imgdesc  = '/href="(.*?)"/i';
					$weather['icon_url']= $matches[1];
					preg_match($imgdesc, $description, $matches);
					$weather['icon_desc']= $matches[1];
					$weather['temp'] = $xml->channel->item->children('yweather', TRUE)->condition->attributes()->temp;  
					$weather['units'] = $xml->channel->children('yweather', TRUE)->units->attributes()->temperature;
					$weather['conditions'] = $xml->channel->item->children('yweather', TRUE)->condition->attributes()->text;
					$weather['city'] = $xml->channel->children('yweather', TRUE)->location->attributes()->city;
					$weather['region'] = $xml->channel->children('yweather', TRUE)->location->attributes()->region;
					$weather['country'] = $xml->channel->children('yweather', TRUE)->location->attributes()->country;
					$weather['lat'] = $xml->channel->item->children('geo', TRUE)->lat;
					$weather['long'] = $xml->channel->item->children('geo', TRUE)->long;
					$weather['link_details'] = $xml->channel->link;
					
					$dom = new DOMDocument("1.0");
							$root = $dom->createElement("weather");
							$dom->appendChild($root);
							
							$feed = $dom->createElement("feed");
							$root->appendChild($feed);
							$text1 = $dom->createTextNode("http://weather.yahooapis.com/forecastrss?w={$woeid['w']}&u=f");
							
							$feed->appendChild($text1);
							
							$link = $dom->createElement("link");
							$root->appendChild($link);
							$text2 = $dom->createTextNode($weather['link_details']);
							$link->appendChild($text2);
							
							$location = $dom->createElement("location");
							$root->appendChild($location);
							$city = $dom->createAttribute("city");
							$location->appendChild($city);// create attribute value node
							$cityValue = $dom->createTextNode($weather['city']);
							$city->appendChild($cityValue);
							$region= $dom->createAttribute("region");
							$location->appendChild($region);
							$regionValue = $dom->createTextNode($weather['region']);
							$region->appendChild($regionValue);
							$country = $dom->createAttribute("country");
							$location->appendChild($country);
							$countryValue = $dom->createTextNode($weather['country']);
							$country->appendChild($countryValue);
							
							$units = $dom->createElement("units");
							$root->appendChild($units);
							$temperature = $dom->createAttribute("temperature");
							$units->appendChild($temperature);
							$tempValue = $dom->createTextNode($weather['units']);
							$temperature->appendChild($tempValue);
							
							$condition = $dom->createElement("condition");
							$root->appendChild($condition);
							$text = $dom->createAttribute("text");
							$condition->appendChild($text);
							$textValue = $dom->createTextNode($weather['conditions']);
							$text->appendChild($textValue);
							$temp = $dom->createAttribute("temp");
							$condition->appendChild($temp);
							$tempValue = $dom->createTextNode($weather['temp']);
							$temp->appendChild($tempValue);
							
							$img = $dom->createElement("img");
							$root->appendChild($img);
							$text3 = $dom->createTextNode($weather['icon_url']);
							$img->appendChild($text3); 
							
							foreach($xml->channel->item->children('yweather', TRUE)->forecast as $forecast)
							{
							$weather['day'] = $forecast->attributes()->day;
							//echo $weather['day'];
							$weather['low'] = $forecast->attributes()->low;
							//echo $weather['low'];
							$weather['high'] =$forecast->attributes()->high;
							$weather['text'] =$forecast->attributes()->text;
				
							$f = $dom->createElement("forecast");
							$root->appendChild($f);
							$day = $dom->createAttribute("day");
							$f->appendChild($day);
							$dayValue = $dom->createTextNode($weather['day']);
							$day->appendChild($dayValue);
							$low = $dom->createAttribute("low");
							$f->appendChild($low);
							$lowValue = $dom->createTextNode($weather['low']);
							$low->appendChild($lowValue);
							$high = $dom->createAttribute("high");
							$f->appendChild($high);
							$highValue = $dom->createTextNode($weather['high']);
							$high->appendChild($highValue);
							$text = $dom->createAttribute("text");
							$f->appendChild($text);
							$textValue = $dom->createTextNode($weather['text']);
							$text->appendChild($textValue);
							}
							$dom->save("order.xml");
							echo $dom->saveXML();
							
							
					
				}
			}
		
}			
?>
	