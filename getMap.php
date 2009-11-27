<?php
if(isset($_POST["location"])){
	$result = array("message"=>"Could not find: ".$_POST["location"]);
	// country limited to GB query
	if($response = @file_get_contents('http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22'.$_POST["location"].'%22%20and%20country.code%20%3D%20%22GB%22&format=json&callback=')){
	// no country limit query
	//if($response = @file_get_contents('http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22'.$_POST["location"].'%22&format=json&callback=')){
		$result["message"] = "Found: ".$_POST["location"];
		$result["data"] = json_decode($response);
		if($result["data"]->query->results){
			$result["isSingle"] = (count($result["data"]->query->results->place)<=1);
			$result["count"] = count($result["data"]->query->results->place);

			if($result["isSingle"]){
				$titles = array();
				foreach($result["data"]->query->results->place as $key=>$value){
					if(strpos($key,"admin")!==false){
						$titles[] = $value->content;
					}
					$title = implode(",",array_reverse($titles,true));
				}
				$result["data"]->query->results->place->desc = $title;
			}else{
				
				foreach($result["data"]->query->results->place as $placeId => $place){
					$titles = array();
					foreach($place as $key => $value){
						if(strpos($key,"locality")!==false){
							$titles[] = $value->content;
						}	
						$title = implode(",",array_reverse($titles,true));
					}					
					$result["data"]->query->results->place[$placeId]->desc = $title;
				}
			}
		}
	}
	echo json_encode($result);
	exit;
}
?>
