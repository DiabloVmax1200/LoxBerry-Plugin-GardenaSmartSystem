<?php

    error_reporting(-1);
	ini_set('display_errors','On');
    include("data.inc.php");
    include("gardena.class.inc.php");
    include("functions.inc.php");
    
    $gardena = new gardena($user, $pw);
    $mower = $gardena -> getFirstDeviceOfCategory($gardena::CATEGORY_MOWER);
    $gateway = $gardena -> getFirstDeviceOfCategory($gardena::CATEGORY_GATEWAY);
    
//echo var_dump($gardena);

foreach($gardena -> locations as $location)
{
	echo "Location:" . $location -> name . "<br>";
	echo "authorized_at:" . $location -> authorized_at . "<br>";
	echo "address:" . $location -> geo_position -> address . "<br>";
	echo "latitude:" . $location -> geo_position -> latitude . "<br>";
	echo "longitude:" . $location -> geo_position -> longitude . "<br>";
}
foreach($gardena -> devices as $locationId => $devices)
{   
	//Erstellung von SendeDaten im Format
	//[DeviceCategory].[DeviceName].[DataCategorie].[DataName]:[DataValue] (optional:[ = DataValueString])
	$dataToSend = "";
	
	$DeviceCategory ="";
	$DeviceName ="";
	$DataCategorie = "";
	$DataName ="";
	$DataValue ="";
	$DataValueString ="";
	
	//Liste alle Geräte
	foreach($devices as $device)
	{
		$DeviceCategory = $device -> category;
		$DeviceName = $device -> name;
		echo "<b>Device Category:" . $device -> category . "</b><br>";
		echo "Device Name:" . $device -> name . "<br>";
		echo "configuration_synchronized:". $device -> configuration_synchronized . "<br>";
	
		//Liste alle Kategorien
		foreach ($device -> abilities as $ability)
		{
			$DataCategorie = $ability -> name;
			echo "<b>Categorie: </b>". $ability -> name . "</b><br>";
			
			//Liste alle Eigenschaften
			foreach($ability -> properties as $property)
		    {
		    	$DataValueString = "";
		    	$DataName = $property -> name;
		    	echo $property -> name . ":  ";
		    	if(is_string($property -> value)){
		    		echo "Datatyp String - Value: ". $property -> value;
		    		$DataValueString = $property -> value;
		    	}
		    	elseif(is_int($property -> value)){
		    		echo "Datatyp Int - Value: ". $property -> value;
		    		$DataValue = $property -> value;
		    	}
		    	elseif(is_bool($property -> value)){
		    			echo "Datatyp Bool - ";
		    		if ($property -> value){
		    			echo "Value: ". "1";
		    			$DataValue = 1;
		    		}
		    		else{
		    			echo "Value: ". "0";
		    			$DataValue = 0;
		    		}	    			
		    	}
		    	else{
		    		echo "Error: DataType Value ". $DataName . " unknown.";
					$DataValue = 255;
		    	}
		    	
		    	if (array_key_exists('unit', $property)){
		    	echo $property -> unit . "<br>";
		    	}
		    	else echo "<br>";
		    	if (array_key_exists('timestamp', $property)){
		    	echo "timestamp:" . $property -> timestamp . "<br>";
		    	}
            	if (sizeof($property -> supported_values) > 0)
            	{
	  				echo "--->Possible Values: " . var_export($property -> supported_values, true) . "<br>";
	        		$valPos = 0;
	        		if(is_bool($property -> value)){
			    		if ($property -> value){
			    			$valPos = array_search ("true", $property -> supported_values);
			    		}
			    		else{
			    			$valPos = array_search ("false", $property -> supported_values);
			    		}
	        		}
	        		else{
	        			$valPos = array_search ($property -> value , $property -> supported_values);
	        		}
	        		if ($valPos === False){
	        			echo "Error: Data Value not found!<br>";
	        			$DataValue = 255;
	        		}
	        		else {
	        			echo "Data Value at Position: " . $valPos . "<br>";
	        			$DataValue = $valPos;
	        		}
            	}
				
				//Bulid String for Transfer
	            $dataToSend = $DeviceCategory . "." . $DeviceName . "." . $DataCategorie . "." . $DataName . ":" . $DataValue;
	            if ($DataValueString) $dataToSend = $dataToSend . "[" . $DataValueString ."]";
	            echo "&nbsp Data to send: " . $dataToSend . "<br>";
            	//TODO transfer Data
            	
		    }
			echo "<br>";
		}
	} 
}            


	if(!empty($_GET["action"])) //action hat einen Wert
	{
		if ($_GET["action"] === "INFO")
		{

		}
		else if ($_GET["action"] === "PARK_UNTIL_FURTHER_NOTICE")
		{
			$gardena -> sendCommand($mower, $gardena -> CMD_MOWER_PARK_UNTIL_FURTHER_NOTICE);
		}
		else if ($_GET["action"] === "PARK_UNTIL_NEXT_TIMER")
		{
			$gardena -> sendCommand($mower, $gardena -> CMD_MOWER_PARK_UNTIL_NEXT_TIMER);
		}
		else if ($_GET["action"] === "RESUME_SCHEDUL")
		{
			$gardena -> sendCommand($mower, $gardena -> CMD_MOWER_RESUME_SCHEDUL);
		}
		else if ($_GET["action"] === "START")
		{		
			if(!empty($_GET["duration"]))
			{
				if(ctype_digit($_GET["duration"]))
				{
					$CMD_MOWER_START_XXHOURS = array("name" => "start_override_timer", "parameters" => array("duration" => $_GET["duration"]));
					echo "START for:";
					echo var_dump($CMD_MOWER_START_XXHOURS);
					echo "<br>";
					$gardena -> sendCommand($mower, $CMD_MOWER_START_XXHOURS);
				}
				else
				{
					echo "<br>ERROR: Parameter duration is not a Number";
				}			
			}
			else
			{
				echo "START for 6h<br>";
				$gardena -> sendCommand($mower, $gardena -> CMD_MOWER_START_06HOURS);		
			}	
		}
		else
		{
			echo "<br>ERROR: Parameter action has not a valid value";
		}
	}
	else
	{
		echo "Possible Param: <br><b>action</b><br>Values: INFO, PARK_UNTIL_FURTHER_NOTICE, PARK_UNTIL_NEXT_TIMER, RESUME_SCHEDUL, START<br>";
		echo "<br><b>[duration]</b><br>Value:Duration in minutes, only for Param action START. Without the Param duration the mower will start for 6h";
	}
?>