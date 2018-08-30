<?
    class SavCo_FunctionsGen extends SavCo
    {
        public function __construct($db)
        {
            parent::__construct();
            $this->db = Zend_Registry::get('db2');
        }

        public static function is_decimal($val)
        {
            return is_numeric($val) && floor($val) != $val;
        }


        public static function Bubblesort($array=array(),$property=null){
                if (!$length = count($array)) {
                    return $array;
                }
                for ($outer = 0; $outer < $length; $outer++) {
                    for ($inner = 0; $inner < $length; $inner++) {
                        if ($array[$outer][$property] > $array[$inner][$property]) {
                            $tmp = $array[$outer];
                            $array[$outer] = $array[$inner];
                            $array[$inner] = $tmp;
                        }
                    }
                }
            return $array;
        }

        public static function SumOfArray($array){
            $total=0;
            foreach($array as $item){
               $total=$total+$item;
            }

            return $total;
        }

		public function escapeString($string){
			$string2 = $string;
			$string2 = str_replace("&", "&amp;", $string2);
			$string2 = str_replace("<", "&lt;", $string2);
			$string2 = str_replace(">", "&gt;", $string2);
			$string2 = str_replace("'", "&apos;", $string2);
			$string2 = str_replace("\"", "&quot;", $string2);

			return $string2;
		}

		public static function FormatPhone($ph){
			$onlynums = preg_replace('/[^0-9]/','',$ph);
        	if (strlen($onlynums)==10) { $areacode = substr($onlynums, 0,3);
        	      $exch = substr($onlynums,3,3);
         	     $num = substr($onlynums,6,4);
              $ph = "(".$areacode.") " . $exch . "-" . $num;        
          }
		  return $ph;
     	}

        public static function PhoneFormatStrip($ph){
            return  preg_replace("/[^0-9,.]/","",$ph);
        }

        public static function FormatDateTimeFromTimeStamp($timeStamp, $hasTime=false){
            $date=$hasTime?date('M d, Y h:i:s a',$timeStamp):date('M d, Y',$timeStamp);

            return $date ;
        }

        public static function SecondsToLog($seconds_in){
		    $seconds_remaining=$seconds_in;
            $hours=$minutes=$seconds=0;

		    if($seconds_remaining>60*60){
               $hours=floor($seconds_remaining/(60*60));
               $seconds_remaining=$seconds_remaining-($hours*(60*60));
            }

            if($seconds_remaining>60){
                $minutes=floor($seconds_remaining/(60));
                $seconds_remaining=$seconds_remaining-($minutes*(60));
            }


            if($seconds_remaining){
                $seconds=$seconds_remaining;
            }

            return ($hours?$hours>9?$hours.':':'0'.$hours.':':'00:').($minutes?$minutes>9?$minutes.':':'0'.$minutes.':':'00:').($seconds?$seconds>9?$seconds:'0'.$seconds:'00');
        }

        public static function LogToSeconds($logFromattedTime_in){
            $logFromattedTimeReversedArr=array_reverse(explode(':',$logFromattedTime_in));
            $seconds_out=0;

            if(count($logFromattedTimeReversedArr)>-1){//SECONDS
                $seconds_out=(int)$logFromattedTimeReversedArr[0];
            }

            if(count($logFromattedTimeReversedArr)>0){//MINUTES
                $seconds_out=$seconds_out+((int)$logFromattedTimeReversedArr[1]*(60));
            }

            if(count($logFromattedTimeReversedArr)>1){//HOURS
                $seconds_out=$seconds_out+((int)$logFromattedTimeReversedArr[2]*(60*60));
            }

            if(count($logFromattedTimeReversedArr)>2){//DAYS
                $seconds_out=$seconds_out+($logFromattedTimeReversedArr[3]*(60*60*24));
            }

            return $seconds_out;
        }

        public static function get_client_ip() {
            $ipaddress = '';
            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('HTTP_X_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            else if(getenv('HTTP_X_FORWARDED'))
                $ipaddress = getenv('HTTP_X_FORWARDED');
            else if(getenv('HTTP_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
            else if(getenv('HTTP_FORWARDED'))
                $ipaddress = getenv('HTTP_FORWARDED');
            else if(getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = 'UNKNOWN';
            return $ipaddress;
        }

     	function replaceHtml($text){
			return ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
                     "<a href=\"\\0\">\\0</a>", $text);
		}
	
	
		static public function createRandomPassword($length=6) {
	    	$chars = "23456789abcdefghijklmnopqrstuvwxyz";
    		srand((double)microtime()*1000000);
    		$i = 0;
    		$pass = null ;

    		while ($i <= $length) {
       		 	$num = rand() % 35;
       		 	$tmp = substr($chars, $num, 1);
       		 	$pass = $pass . $tmp;
      	  		$i++;
    		}
            
    		return $pass;
		}
	
		function generateRandID(){  //looks like a possibility of duplicates
      		return md5(generateRandStr(16));
   		}
   
		function generateRandomness($length=6,$level=2){
   			list($usec, $sec) = explode(' ', microtime());
  			 srand((float) $sec + ((float) $usec * 100000));

   			$validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
  			$validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
   			$validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

   			$password  = "";
   			$counter   = 0;

   			while ($counter < $length) {
     			$actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);

     			// All character must be different
     			if (!strstr($password, $actChar)) {
       	 			$password .= $actChar;
        			$counter++;
     			}
   			}

   			return $password;
		}

  		/**
    	* generateRandStr - Generates a string made up of randomized
    	* letters (lower and upper case) and digits, the length
    	* is a specified parameter.
    	*/
   		function generateRandStr($length){
      		$randstr = "";
      		for($i=0; $i<$length; $i++){
        		$randnum = mt_rand(0,61);
				if($randnum < 10){
            		$randstr .= chr($randnum+48);
         		}else if($randnum < 36){
            		$randstr .= chr($randnum+55);
         		}else{
            		$randstr .= chr($randnum+61);
         		}
      		}
      		return $randstr;
   		}


		function encrypt($string, $key) {
			$result = '';
			for($i=0; $i<strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key))-1, 1);
				$char = chr(ord($char)+ord($keychar));
				$result.=$char;
			}
			return base64_encode($result);
		}

	function decrypt($string, $key) {
			$result = '';
			$string = base64_decode($string);

			for($i=0; $i<strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key))-1, 1);
				$char = chr(ord($char)-ord($keychar));
				$result.=$char;
			}
			return $result;
	} 

	public static function distanceOfTimeInWords($fromTime, $toTime = 0, $showLessThanAMinute = false) {
	    $distanceInSeconds = round(abs($toTime - $fromTime));
	    $distanceInMinutes = round($distanceInSeconds / 60);
       
        if ( $distanceInMinutes <= 1 ) {
            if ( !$showLessThanAMinute ) {
                return ($distanceInMinutes == 0) ? 'less than a minute' : '1 minute';
            } else {
                if ( $distanceInSeconds < 5 ) {
                    return 'less than 5 seconds';
                }
                if ( $distanceInSeconds < 10 ) {
                    return 'less than 10 seconds';
                }
                if ( $distanceInSeconds < 20 ) {
                    return 'less than 20 seconds';
                }
                if ( $distanceInSeconds < 40 ) {
                    return 'half a minute';
                }
                if ( $distanceInSeconds < 60 ) {
                    return 'less than a minute';
                }
               
                return '1 minute';
            }
        }
        if ( $distanceInMinutes < 45 ) {
            return $distanceInMinutes . ' minutes';
        }
        if ( $distanceInMinutes < 90 ) {
            return '1 hour';
        }
        if ( $distanceInMinutes < 1440 ) {
            return round(floatval($distanceInMinutes) / 60.0) . ' hours';
        }
        if ( $distanceInMinutes < 2880 ) {
            return '1 day';
        }
        if ( $distanceInMinutes < 43200 ) {
            return  round(floatval($distanceInMinutes) / 1440) . ' days';
        }
        if ( $distanceInMinutes < 86400 ) {
            return '1 month';
        }
        if ( $distanceInMinutes < 525600 ) {
            return round(floatval($distanceInMinutes) / 43200) . ' months';
        }
        if ( $distanceInMinutes < 1051199 ) {
            return '1 year';
        }
       
        return 'over ' . round(floatval($distanceInMinutes) / 525600) . ' years';
	}	

	public static function GetUnique_filename()
  	{
		//NO extenions
  		// explode the IP of the remote client into four parts
  		$ipbits = explode(".", $_SERVER["REMOTE_ADDR"]);
  		// Get both seconds and microseconds parts of the time
  		list($usec, $sec) = explode(" ",microtime());

  		// Fudge the time we just got to create two 16 bit words
  		$usec = (integer) ($usec * 65536);
  		$sec = ((integer) $sec) & 0xFFFF;

  		// Fun bit - convert the remote client's IP into a 32 bit
  		// hex number then tag on the time.
  		// Result of this operation looks like this xxxxxxxx-xxxx-xxxx
  		$uid = sprintf("%08x-%04x-%04x",($ipbits[0] << 24)
         | ($ipbits[1] << 16)
         | ($ipbits[2] << 8)
         | $ipbits[3], $sec, $usec);

  		// Tag on the extension and return the filename
  		return $uid;
  }


  	public static function GetDistance($lat1, $lng1, $lat2, $lng2, $miles = true)
	{	
		$pi80 = M_PI / 180;
		$lat1 *= $pi80;
		$lng1 *= $pi80;
		$lat2 *= $pi80;
		$lng2 *= $pi80;
 
		$r = 6372.797; // mean radius of Earth in km
		$dlat = $lat2 - $lat1;
		$dlng = $lng2 - $lng1;
		$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$km = $r * $c;
 
		$distance=($miles ? ($km * 0.621371192) : $km);
		return number_format($distance,2,'.','');
	}

        static public function RestGETURL($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $contents = curl_exec ($ch);
            curl_close ($ch);
            return $contents;
        }


        static public function RestPOSTURL_old($url,$body=null){
            $ch = curl_init();
            $fieldsCount=1;
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_POST,true);

            if($body)curl_setopt($ch,CURLOPT_POSTFIELDS,$body);


            $result = curl_exec($ch);

            // Check for errors and display the error message
            if($errno = curl_errno($ch)) {
                $error_message = curl_strerror($errno);
                echo "cURL error ({$errno}):\n {$error_message}";
            }

            curl_close($ch);
            return $result;
        }


        static public function RestPOSTURL($url,$body=null){
            $ch = curl_init();
            $fieldsCount=1;
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch,CURLOPT_POST,true);

            if($body)curl_setopt($ch,CURLOPT_POSTFIELDS,$body);


            $result = curl_exec($ch);

            // Check for errors and display the error message
            if($errno = curl_errno($ch)) {
                $error_message = curl_strerror($errno);
                echo "cURL error ({$errno}):\n {$error_message}";
            }

            curl_close($ch);
            return $result;
        }


    static public function GetDirections($lat1,$lon1,$lat2,$lon2,$hasSensor=true){
	    //http://code.google.com/apis/maps/documentation/directions/
	    //origin (required) — The address or textual latitude/longitude value from which you wish to calculate directions. *
	    //destination (required) — The address or textual latitude/longitude value from which you wish to calculate directions.*
	    //mode (optional, defaults to driving) — specifies what mode of transport to use when calculating directions. Valid values are specified in Travel Modes.
	    // waypoints (optional) specifies an array of waypoints. Waypoints alter a route by routing it through the specified location(s). A waypoint is specified as either a latitude/longitude coordinate or as an address which will be geocoded. (For more information on waypoints, see Using Waypoints in Routes below.)
	    //alternatives (optional), if set to true, specifies that the Directions service may provide more than one route alternative in the response. Note that providing route alternatives may increase the response time from the server.
	    //avoid (optional) indicates that the calculated route(s) should avoid the indicated features. Currently, this parameter supports the following two arguments:
	          //o tolls indicates that the calculated route should avoid toll roads/bridges.
	          //o highways indicates that the calculated route should avoid highways.
	    //  units (optional) — specifies what unit system to use when displaying results. Valid values are specified in Unit Systems below.
	    //region (optional) — The region code, specified as a ccTLD ("top-level domain") two-character value. (For more information see Region Biasing below.)
	   //language (optional) — The language in which to return results. See the supported list of domain languages. Note that we often update supported languages so this list may not be exhaustive. If language is not supplied, the Directions service will attempt to use the native language of the browser wherever possible. See Region Biasing for more information.
	   //sensor (required) — Indicates whether or not the directions request comes from a device with a location sensor. This value must be either true or false.
    	$_googleURL="http://maps.googleapis.com/maps/api/directions/json";
		$_origin=preg_replace( '/\s+/', '',sprintf('%s,%s',$lat1,$lon1));
    	$_desitiniation=preg_replace( '/\s+/', '',sprintf('%s,%s',$lat2,$lon2));
    	$_hasSensor=$hasSensor?'true':'false';
    	
    	$url=sprintf("%s?origin=%s&destination=%s&sensor=%s",
    				$_googleURL,
    				$_origin,
    				$_desitiniation,
    				$_hasSensor);
    					
    	$_directions=SavCo_FunctionsGen::restGETURL($url);
    	return $_directions;
    }
     static public function Format_bytes($a_bytes)
        {
            if ($a_bytes < 1024) {
                return $a_bytes .' B';
            } elseif ($a_bytes < 1048576) {
                return round($a_bytes / 1024, 2) .' KiB';
            } elseif ($a_bytes < 1073741824) {
                return round($a_bytes / 1048576, 2) . ' MiB';
            } elseif ($a_bytes < 1099511627776) {
                return round($a_bytes / 1073741824, 2) . ' GiB';
            } elseif ($a_bytes < 1125899906842624) {
                return round($a_bytes / 1099511627776, 2) .' TiB';
            } elseif ($a_bytes < 1152921504606846976) {
                return round($a_bytes / 1125899906842624, 2) .' PiB';
            } elseif ($a_bytes < 1180591620717411303424) {
                return round($a_bytes / 1152921504606846976, 2) .' EiB';
            } elseif ($a_bytes < 1208925819614629174706176) {
                return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
            } else {
                return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
            }
        }


        public static function array2XML($arr,$root) {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><{$root}></{$root}>");
            $f = create_function('$f,$c,$a','
        foreach($a as $v) {
            if(isset($v["@text"])) {
                $ch = $c->addChild($v["@tag"],$v["@text"]);
            } else {
                $ch = $c->addChild($v["@tag"]);
                if(isset($v["@items"])) {
                    $f($f,$ch,$v["@items"]);
                }
            }
            if(isset($v["@attr"])) {
                foreach($v["@attr"] as $attr => $val) {
                    $ch->addAttribute($attr,$val);
                }
            }
        }');
            $f($f,$xml,$arr);
            return $xml->asXML();
        }

        /* make a URL small */
        public static function GenerateBitlyUrl($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
        {
            //create the URL
            $bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;

            //get the url
            //could also use cURL here
            $response = file_get_contents($bitly);

            //parse depending on desired format
            if(strtolower($format) == 'json')
            {
                $json = @json_decode($response,true);
                return $json['results'][$url]['shortUrl'];
            }
            else //xml
            {
                $xml = simplexml_load_string($response);
                return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
            }
        }

        public static function ListFiles($dir,$recurseLevel=0,$includeDir=true) {
            $recurseLevel++;
            //Customize to store directories as well
            //Needs to compare what is in the to
            if($dh = opendir($dir)) {

                $files = Array();
                $inner_files = Array();

                while($file = readdir($dh)) {
                    if($file != "." && $file != ".." && $file[0] != '.') {
                        if(is_dir($dir . "/" . $file)) {
                            if((strcmp($file,"data")==0 && $recurseLevel==1)||(strcmp($file,"tmp")==0 && $recurseLevel==2)){ //make certain it is not the data directory on the first level
                                  null; //don't process
                            }else{
                                if($includeDir){
                                    //Get Directory Info
                                    $thisFile=$dir . "/" . $file;
                                    $theFile['fullPath']=$thisFile;
                                    $theFile['name']=sprintf("%s/",basename($thisFile));
                                    $theFile['type']="1";
                                    $theFile['size']=SavCo_FunctionsGen::Format_bytes(filesize($thisFile));
                                    $theFile['time']=filemtime($thisFile);
                                    $theFile['level']=$recurseLevel;
                                    array_push($files, $theFile);
                                }

                                $inner_files = SavCo_FunctionsGen::ListFiles($dir . "/" . $file,$recurseLevel);
                                if(is_array($inner_files)) $files = array_merge($files, $inner_files);
                            }
                        } else {
                            //Push specifically the filename,fileType,fileSize,fileTime,
                            $thisFile=$dir . "/" . $file;
                            $theFile['fullPath']=$thisFile;
                            $theFile['name']=basename($thisFile);
                            $theFile['type']="0";
                            $theFile['size']=SavCo_FunctionsGen::Format_bytes(filesize($thisFile));
                            $theFile['time']=filemtime($thisFile);
                            $theFile['level']=$recurseLevel;
                            array_push($files, $theFile);
                        }
                    }
                }

                closedir($dh);
                return $files;
            }
        }

        function LineDiff($old, $new){
            $maxlen=0;

            foreach($old as $oindex => $ovalue){
                $nkeys = array_keys($new, $ovalue);
                foreach($nkeys as $nindex){
                    $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                        $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                    if($matrix[$oindex][$nindex] > $maxlen){
                        $maxlen = $matrix[$oindex][$nindex];
                        $omax = $oindex + 1 - $maxlen;
                        $nmax = $nindex + 1 - $maxlen;
                    }
                }
            }
            if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
            return array_merge(
                SavCo_FunctionsGen::LineDiff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
                array_slice($new, $nmax, $maxlen),
                SavCo_FunctionsGen::LineDiff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
        }

        public static function ZipPathsofFilesDB($dir,$dbName='',$pathOfZip){
            //Get Paths of Files
            $paths=array();
            $paths=SavCo_FunctionsGen::ListFiles($dir,0,false);

            //Pull Database Schema at the dir level

            //Add Database to the paths

            //Zip Files
            if(SavCo_FunctionsGen::CreateZip($paths,$pathOfZip,true)){
                //Delete the DB Schema that was created if it was created

            }
            return true;
        }



        /* creates a compressed zip file */
        public static function CreateZip($files = array(),$destination = '',$overwrite = false) {
            //if the zip file already exists and overwrite is false, return false
            if(file_exists($destination) && !$overwrite) { return false; }
            //vars
            $valid_files = array();
            //if files were passed in...
            if(is_array($files)) {
                //cycle through each file
                foreach($files as $file) {
                    //make sure the file exists
                    if(file_exists($file)) {
                        $valid_files[] = $file;
                    }
                }
            }
            //if we have good files...
            if(count($valid_files)) {
                //create the archive
                $zip = new ZipArchive();
                if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                    return false;
                }
                //add the files
                foreach($valid_files as $file) {
                    $zip->addFile($file,$file);
                }
                //debug
                //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

                //close the zip -- done!
                $zip->close();

                //check to make sure the file exists
                return file_exists($destination);
            }
            else
            {
                return false;
            }
        }

        public static function Hex2rgb($hex) {
            $hex = str_replace("#", "", $hex);

            if(strlen($hex) == 3) {
                $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            } else {
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            }
            $rgb = array($r/255, $g/255, $b/255);
            //return implode(",", $rgb); // returns the rgb values separated by commas
            return $rgb; // returns an array with the rgb values
        }

            public static function GetImageFromURL($url){

                return file_get_contents($url);
            }

        /**
         * Get either a Gravatar URL or complete image tag for a specified email address.
         *
         * @param string $email The email address
         * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
         * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
         * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
         * @param boole $img True to return a complete IMG tag False for just the URL
         * @param array $atts Optional, additional key/value attributes to include in the IMG tag
         * @return String containing either just a URL or a complete image tag
         * @source https://gravatar.com/site/implement/images/php/
         */
        function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
            $url = 'https://www.gravatar.com/avatar/';
            $url .= md5( strtolower( trim( $email ) ) );
            $url .= "?s=$s&d=$d&r=$r";
            if ( $img ) {
                $url = '<img src="' . $url . '"';
                foreach ( $atts as $key => $val )
                    $url .= ' ' . $key . '="' . $val . '"';
                $url .= ' />';
            }
            return $url;
        }
        
        function tr_date_format($date){
            
            return date('F d, Y \a\t H:i A',$date);
        }


        public static function Parseurl($url) {
            $result = parse_url($url);
            return $result['host'];
        }
    }