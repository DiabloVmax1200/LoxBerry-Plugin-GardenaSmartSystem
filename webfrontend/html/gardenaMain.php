#!/usr/bin/php
<?php
include("header.inc.php");

$miniserverIP = "";
$log = Null;
$gardenaCfg = Null;
$gardena = Null;
$msArray = Null;
$msID = 0;

// Creates a log object, automatically assigned to your plugin, with the group name "GardenaLog"
$log = LBLog::newLog( [ "name" => "GardenaLog", "package" => $lbpplugindir, "logdir" => $lbplogdir, "loglevel" => 6] );
// After log object is created, logging is started with LOGSTART
// A start timestamp and other information is added automatically
LOGSTART("GardenaMain script started");

$gardenaCfg = new Config_Lite("$lbpconfigdir/gardena.cfg",LOCK_EX,INI_SCANNER_RAW);

if ($gardenaCfg == Null){
	LOGCRIT("Unable to read config file, terminating");
	LOGEND("Processing terminated");
	exit;
}
else {
	LOGOK("Reading config file successfull");
}

if ($gardenaCfg->get("GARDENA","ENABLED")){
	LOGOK("Plugin is enabled");
} else{
	LOGOK("Plugin is disabled");
	LOGEND("Processing terminated");
	exit;
}



//Neues Gardena Objekt anlegen. Username und Passwort werden aus cfg Datei gelesen.
//Umgang mit ini file: https://www.loxwiki.eu/display/LOXBERRY/Writing+ini+files+in+PHP
$gardena = new gardena($gardenaCfg->get("GARDENA","USERNAME"), $gardenaCfg->get("GARDENA","PASSWORD"), $log);

$msArray = LBSystem::get_miniservers();
if (!is_array($msArray)){
	LOGCRIT("No Miniserver configured, terminating");
   	LOGEND("Processing terminated");
    exit;
}
else{
	LOGDEB("Miniservers configured.");
}

$msID = $gardenaCfg->get("GARDENA","MINISERVER");

if ($msID < 1){
	LOGCRIT("No Miniserver in Gardena Config File configured, terminating");
    LOGEND("Processing terminated");
    exit;
}
if (count($msArray) < $msID){
	LOGCRIT("In Loxberry configured Miniservers and the selected miniserver in Gardena Config File do not match, terminating");
    LOGEND("Processing terminated");
    exit;
}
$miniserverIP = $msArray[$msID]['IPAddress'];

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
	
	//List of all Devices
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
		    	$DataValue ="";
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
            	//Tansfer Data
            	sendUDP($dataToSend, $miniserverIP, $gardenaCfg->get("GARDENA","UDPPORT"));
            	//Wait 0.25Sec for next loop - this is to limit the stress of the miniserver
            	usleep(250000);
		    }
			echo "<br>";
		}
	} 
}            
?>