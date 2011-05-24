<?php

class Curl {

	/* VERY IMPORTANT
	** MULTI MODE: true,false
	** If you are having problems with curl_multi, change to false and it will work with 
	** non-multi curl. You will still be able to use $this->addRequest() and $this->perform() 
	** to execute multiple requests (however they won't be executed concurrently).
	** Sidenote: Also useful when you need to run requests slower.
	*/
	public $multiMode = true;

	// Max simultaneous requests, if there are more in the request pool it runs chunk()
	public $maxParallelRequests = 10;
	
	// Delay between requests in seconds, only works with $multiMode set to false.
	public $delayBetweenSingle = 0;
	
	/*
	** User-Agents
	*/
	public $useragentsFilePath = '';	// file (one per line)
	public $useragentsRotate = false;	// Auto rotation
	public $useragents = array();		// You can add directly to this array
	
	/*
	** Proxies
	*/
	public $proxiesFilePath = '';	// file (one per line, IP:PORT:USER:PASS)
	public $proxiesDelimiter = ',';	// delimiter for the file
	public $proxiesRotate = false;	// Auto rotation
	//public $proxiesMinSpeed = 60;	// Set minimum speed for the proxies in seconds, if response takes more they are removed
	//public $proxiesRetries = 0;	// Retry X times with a new proxy on error
	public $proxies = array();	// You can add directly to this array
	
	/*
	** Interface
	*/
	public $interfacesAutoDiscovery = false; // tries to autodiscover the available interfaces using `ifconfig`
	public $interfacesFilePath = '';	// file (one per line, 0.0.0.0)
	public $interfacesRotate = false;	// Auto rotation
	public $interfaces = array();		// You can add directly to this array

	/*
	** Cookies
	*/
	protected $handleCookies = true; // Handle cookies, set to false if you want to use COOKIEJAR or COOKIEFILE
	
	/*
	** Debug
	*/
	public $errors = array();	// Curl errors	


	/*
	** Request pool
	** request:	'url', 'options', 'callback', 'callbackArgs'
	**
	** 'url':		can be either a single URL or an array of URLs
	** 'options':		CURL options
	** 'callback':		func_name or array( obj, 'method_name')
	** 'callbackArgs':	array of arguments for the callback
	**
	** the callback receives a response data array (by reference, $this->data[$i]) as the first arg,  then anything you pass it with 'callbackArgs'
	** ex. function stripHtml(&$data) { $data['plainText'] = strip_tags($data['body']); }
	** -- the above will add ['plainText'] to $this->data;
	*/
	public $requests = array();
	
	/*
	** Response data associative arrays
	** response:	'body', 'head', 'headSize', 'cookies', 'httpCode', 'effectiveUrl'
	*/
	public $data = array();


	/* 
	** Internal variables
	*/

	// DEFAULT CURL OPTIONS 
	// Will override $curlOptions in addRequest()
	// DO NOT CHANGE 'CURLOPT_RETURNTRANSFER' and 'CURLOPT_HEADER' UNLESS YOU KNOW WHAT YOU ARE DOING
	public $defaultOptions = array(  
				CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true,
				CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 20);	
	
	protected $ch;		// Single curl handle
	protected $mh;		// Multi curl handle
	protected $chPool;	// Pool of handles for the multihandle
	protected $activeConnection;
	protected $mrc;
	
	protected $chunked = false;
	protected $chunks;
	
	protected $redirects = array();
	
	protected $tempHeaders = array();
	

/*
** Constructor and destructor
*/	
	public function __construct() {
	
		set_time_limit(0);
		
	// Default callbacks (IMPORTANT)
		$this->defaultOptions[CURLOPT_HEADERFUNCTION] = array($this, 'headerCallback');
		//$this->defaultOpts[CURLOPT_WRITEFUNCTION] = array($this, 'responseCallback');
		
	// Load proxies,useragents and interfaces
		if($this->proxiesRotate && $this->proxiesFilePath && is_readable($this->proxiesFilePath)) {
			$arr = file($this->proxiesFilePath);
			foreach(array_keys($arr) as $in) {
				$p = explode($this->proxiesDelimiter, trim($arr[$in]));
				
				$this->proxies[] = array(
						'ip'=> $p[0],
						'port'=> $p[1],
						'user'=> $p[2],
						'pass'=> $p[3]
						);

			}
		}
		
		if($this->useragentsRotate && $this->useragentsFilePath && is_readable($this->useragentsFilePath)) {
			$arr = file($this->useragentsFilePath);
			foreach(array_keys($arr) as $in) {
			
				$this->useragents[] = trim($arr[$in]);
			}
		}
		
		if($this->interfacesRotate && $this->interfacesAutoDiscovery) {
			$arr = $this->discoverInterfaces();
			$this->interfaces = $arr;
		
		} elseif($this->interfacesRotate && $this->interfacesFilePath && is_readable($this->interfacesFilePath)) {
			$arr = file($this->interfacesFilePath);
			foreach(array_keys($arr) as $in) {
			
				$this->interfaces[] = trim($arr[$in]);
			}
		}
	}
	
	public function __destruct() {

	}

/*
** Single HTTP methods
*/
	public function get($url, $curlOpts = '', $callback='', $callbackArgs='') {

		$curlOpts[CURLOPT_HTTPGET] = true;
		
		$a[] = $this->addRequest($url,$curlOpts,$callback, $callbackArgs);
		$this->perform($a);

		return end($this->data);
	}
	
	public function post($url, $vars, $curlOpts='', $callback='', $callbackArgs='') {
		$curlOpts[CURLOPT_POST] = true;
		if(!is_array($vars[0])) {
			foreach(array_keys($vars) as $v) {
				$curlOpts[CURLOPT_POSTFIELDS] = $this->prepareVars($vars);
				$a[] = $this->addRequest($url,$curlOpts,$callback,$callbackArgs);
			}
		} else {
			$curlOpts[CURLOPT_POSTFIELDS] = $this->prepareVars($vars);
			$a[] = $this->addRequest($url,$curlOpts,$callback,$callbackArgs);
		}

		$this->perform($a);
		
		return end($this->data);
	}

/*
** Multiple HTTP methods
*/
	public function addRequest($url, $curlOptions = '',$callback='', $callbackArgs='', $metaData='') {

			//$p = parse_url($url);
					
			$r = array( 
				'url'		=> $url, 
				'options'	=> $o,
				);
				
			if( is_callable($callback)) {
				$r['callback']	= $callback;
				$r['callbackArgs'] = $callbackArgs;
			}
			
			if(is_array($metaData))
				$r['metaData'] = $metaData;
				
			$request = $r;

		return $request;
	}

	public function addArray($urls, $curlOptions = '',$callback='', $callbackArgs='') {
			if(!is_array($curlOptions[0])) {
					$curlOptions = array($curlOptions);
			}
			if(is_array($url)) {
				foreach(array_keys($url) as $i){
					//$p = parse_url($url[$i]);
					
					$o = (is_array($curlOptions[$i])) ? $curlOptions[$i] : $curlOptions[0];
					
					$r = array( 
						'url'		=> $url[$i], 
						'options'	=> $o,
						);
					if( is_callable($callback)) {
						$r['callback']	= $callback;
						$r['callbackArgs'] = $callbackArgs;
					}
						
					$requests[] = $r;
				}
			}
			return $requests;
	}
	
	public function perform($requests) {
		$k = count($requests);
		$this->requests = $requests;
		if($k > 0 ) {
			//If there's only one request drop down to single mode
			if($k == 1) {
				$this->multiMode = false;
			}
			// Chunk requests if they are more than allowed, not already chunked and $this->multiMode is true
			if($this->multiMode && !$this->chunked && $this->maxParallelRequests < count($this->requests)) {
				$this->chunk( $maxParallelRequests );
			}
			
			$this->chPool = array();
			
			// if not chunked perform() once 
			if(!$this->chunked) {
				$this->makeHandles($this->requests);
				$this->performMulti();
			}
			// else perform() for each chunk
			else 
			{
				foreach(array_keys($this->chunks) as $i) {
			// assign a chunk to be perform()ed
					$requests = $this->chunks[$i];
			// initialise the single handles, stored in $this->chPool
					$this->makeHandles($requests);
			// perform requests in the pool		
					$this->performMulti();
			// cleanup
					$this->perforMultiCleanup();
				}
			}
			$this->requests = array();
		}
	}
/*
** General methods
*/
	public function chunk($chunkSize) {
		if($chunkSize > 0) {
			$k = count($this->requests);
			if($k > $chunkSize) {
				$this->chunked = true;
				$this->chunks = array_chunk($this->requests, $chunkSize, true);
			}
		}
	}

	public function prepareVars( $vars ) {
		if( is_array($vars) && !empty($vars)) {
			$str = '';
			foreach( array_keys($vars) as $k)
				$str .= urlencode($k).'='.urlencode($vars[$k]).'&';

			$str = substr($str, 0, -1);
			return $str;
		} else { 
			return false;
		}
	}
	
	public function discoverInterfaces() {
		
		$a = shell_exec( 'ifconfig -a' );
		//$a .= 'lala addr:127.123.123.123 addr:122.234.354.34 lala addr:10.10.10.10 addr:100.12.12.12 ';
		preg_match_all( '@addr:(?=([0-9.]+))(?!(?:10\.|127\.|172\.[1-3][6-9_0]\.|192\.|169\.254\.[0-9.]+))@', $a, $m);
		return $m[1];
	}	
	
/******
** Internal methods
******/
	protected function setDefaultOptions(&$ch) {
	
		// Rotate properties
		if( $this->useragentsRotate && !empty($this->useragents) ) {
		
			$options[CURLOPT_USERAGENT] = $this->rotateProperty('useragents');
		}
		
		if( $this->proxiesRotate && !empty($this->proxies) ) {
		
			$proxy = $this->rotateProperty('proxies');
			
			$options[CURLOPT_PROXYPORT] = $proxy['port'];
			$options[CURLOPT_PROXY] = $proxy['ip'];
			if( isset($proxy['user']) ) {
				$options[CURLOPT_PROXYUSERPWD] = $proxy['user'].':'.(isset($proxy['pass']))?$proxy['user']:'';
			}
		}
		
		if( $this->interfacesRotate && !empty($this->interfaces) ) {
			
			$interface = $this->rotateProperty('interfaces');
			$options[CURLOPT_INTERFACE] = $interface;
		}
		
		$this->setOptions($ch, $options);
		$this->setOptions($ch, $this->defaultOptions);
	}
		
	
	protected function setOptions(&$ch, $curlOptions) {
		if(is_array($curlOptions) && !empty($curlOptions))
			curl_setopt_array($ch, $curlOptions);
	}

	protected function makeHandles($requests) {
		foreach(array_keys($requests) as $i) {
			
			// Create handle into $this->chPool
			if( $this->chPool[$i] = curl_init( $requests[$i]['url'] )) {

				// Apply default options to all requests
				$this->setDefaultOptions($this->chPool[$i]);

				// Apply individual options if any
				if(isset($requests[$i]['options']))
					$this->setOptions($this->chPool[$i], $requests[$i]['options']);
				
				if(isset($requests[$i]['metaData']))
					$this->data[$i] = $requests[$i]['metaData'];
			}
		}
	}
	
	protected function addHandle(&$ch) {
	
		curl_multi_add_handle($this->mh, $ch);
	}
	
	protected function removeHandle(&$ch) {
	
		curl_multi_remove_handle($this->mh, $ch);
	}
	protected function performMulti() {
		
	// Mode is single curl
		if($this->multiMode === false) 
		{
			foreach(array_keys($this->chPool) as $i ) {
			
				if($this->delayBetweenSingle > 0)
					sleep($this->delayBetweenSingle);
					
				$response = curl_exec($this->chPool[$i]);
				
				if ( ($err = curl_error($this->chPool[$i])) == 0 ) {
					
					$this->parseResponse($i, $response);
				} else {
				
					$ern = curl_errno($this->chPool[$i]);
					$this->errors[$i]= array( 'num' => $ern, 'msg' => $err );
				}
				curl_close($this->chPool[$i]);
			}
	// Mode is multi curl	
		} else {
		// initialise multihandle
		$this->mh = curl_multi_init();
		array_walk($this->chPool, array($this, 'addHandle'));
		// Start performing the requests
		do { $this->mrc = curl_multi_exec($this->mh, $this->activeConnection); }
		while ($this->mrc == CURLM_CALL_MULTI_PERFORM);

		while ($this->activeConnection && $this->mrc == CURLM_OK) {
			// Wait for network
			if (curl_multi_select($this->mh) != -1) {
				// pull in any new data, or at least handle timeouts
				do {$this->mrc = curl_multi_exec($this->mh, $this->activeConnection); }
				while ($this->mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		
		if ($this->mrc != CURLM_OK) {
			// echo "Curl multi read error $this->mrc\n"
		}

		foreach(array_keys($this->chPool) as $i ) {

			if ( ($err = curl_error($this->chPool[$i])) == '' ) {
			// Retrieve data
				$response = curl_multi_getcontent($this->chPool[$i]);
				$this->parseResponse($i,$response);
		 	} else {
		 	
				$ern = curl_errno($this->chPool[$i]);
				$this->errors[$i]= array( 'num' => $ern, 'msg' => $err );
			}
		}
		}
	}
	
	protected function performMultiCleanup() {
	
		foreach(array_keys($this->chPool) as $i) {
			curl_multi_remove_handle($this->mh, $this->chPool[$i]);
			curl_close($this->chPool[$i]);
		}
		curl_multi_close($this->mh);
	}
	
	protected function headerCallback($ch, $head) {
		// finds the key for our $ch which is a resource
		// the third paramater 'true', makes it check the type of needle
		$i = array_search($ch, $this->chPool, true);

		$len = strlen($head);

		$this->data[$i]['headSize'] += $len;
		

		if( $this->headerFilter($i, $head) ) {

        		return $len;
        	} else {
        		return -1; // stops the request during reading the header
        	}
    	}

    	protected function headerFilter($i, $head) {
    		$abort=false;
    		$head = trim($head);
 		if( !empty($head)) { 
 		
			$this->data[$i]['head'][] = $head; 
			
        		$p = strpos($head,':');
        		$type = strtolower( substr($head,0,$p) );
        		$content = trim(substr($head,$p+1));
        		
        		
        		switch($type) {
        		// Cookies
        			case 'set-cookie':
        				if( $this->handleCookies) {
        					$cookie =  $this->parseCookie($content);
						$this->data[$i]['cookies'][] = $cookie;
					}
					break;
        		// Redirects
        			case 'location':
        				$this->redirect[$i][]= $content; 
        				break;
        		}
        	} else {
        	
        	}
        	
        	// Set $abort to true to stop the request while reading the header
    		if($abort) {
    			return false;
    		} else {
    			return true;
    		}
    	}
    	
    	//
    	// This receives chunks of the whole http response (including headers). You must return the length of each chunk or an error will happen.
    	// You can do stuff here if you know what you are doing, otherwise leave it alone.
    	//
	//protected function responseCallback($ch, $response) {
	//	return strlen($response);
	//}
	
	protected function parseResponse($i, $resp) {
	
		$this->data[$i]['body'] = trim(substr($resp,$this->data[$i]['headSize']-1));
		
		if(isset($this->requests[$i]['callback'])) {
			$args = array(&$this->data[$i]);
			if(is_array($this->requests[$i]['callbackArgs'])) {
				array_unshift($this->requests[$i]['callbackArgs'], &$this->data[$i]);
				$args = $this->requests[$i]['callbackArgs'];
			}
			call_user_func_array($this->requests[$i]['callback'], $args );
		}
	}
	
	protected function parseCookie($str) {
		if( strpos($str, ';') === false) {
			$cdata = explode('=',$str);
			$parts['name'] = trim($cdata[0]);
			$parts['value']= trim($cdata[1]);
		} else {
			$cookiesplit = explode( ';', $str );
			$parts = array();

			foreach( $cookiesplit as $data ) {
				$cdata = explode( '=', $data );
				$cdata[0] = trim( $cdata[0] );
				switch($cdata[0]) {
					case 'expires':
						$cdata[1] = strtotime( $cdata[1] );
						break;
					case 'secure':
						$cdata[1] = true;
						break;
				}
				if( in_array( $cdata[0], array( 'domain', 'expires', 'path', 'secure', 'comment' ) ) ) {
					$parts[trim($cdata[0])] = $cdata[1];
				}
				else {
					$parts['name'] = $cdata[0];
					$parts['value']= $cdata[1];
				}
			}
		}
		
		if( !empty($parts['name']) ) {
			return $parts['name'].'='.$parts['value'];
		} else {
			return false;
		}
		
	}
	
	protected function rxCallback() {}
	
	
	protected function rotateProperty($property) {
	
		$p = $this->$property[array_rand($this->$property)];
		
		return $p;
	}
}


?>