<?php
	
	//
	// Functions
	// ----------------------------------------
	
	
	
	// Connecting
	// ---------------------------------------- //
	function mysqlConnect(){
		
		$test = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		
		if($test){
			if(mysql_select_db( DB_NAME )){ return 1; }
			else { return 0; }
		}
		else { return 0; }
		
	}
	

	// Querys
	// ---------------------------------------- //
	function getQuerySingle($q){
		
		$result = @mysql_query($q);
		
		if($result){
			$row = mysql_fetch_assoc($result);
			return $row;
		}
		else { return 0; }
		
	}		

	function getQueryMany($q){
		
		$result = @mysql_query($q);
		
		if($result){
			$return_array = array();
			while($row = mysql_fetch_assoc($result)){ array_push($return_array, $row); }
			return($return_array);
		}
		else { return 0; }
		
	}


	// Array to String
	// ---------------------------------------- //
		
	function arrayToString($a, $key){
		$return_str = '';
		for($i=0; $i < sizeof($a); $i++){
			$return_str .= ''.$a[$i][$key].'';
			if( $i < (sizeof($a) -1) ){ $return_str .= ', '; }
		}
		return $return_str;
	}


	// Clean Input
	// ---------------------------------------- //
	
	include('class.input_filter.php');
	
	function cleanInput($str){
		$filter = new InputFilter();
		$clean_str = $filter->process($str);
		return $clean_str;
	}


	// Validate Email
	// ---------------------------------------- //
	
	/*
	/* Note: Not supported by all version of PHP, uncomment to include
	/*
	
	include('is_email.php');
	
	function validateEmail($email){
    	return ( is_email($email) )? true : false;	
	}
	
	*/
	
	
	// Validate URL
	// ---------------------------------------- //
	
	function validateUrl($url){

        if($url==NULL) return false;
		
		if(eregi(' ', $url)==true) return false;
		
        $protocol = '(http://|https://)';
        $allowed = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';

        $regex = "^". $protocol . // must include the protocol
                         '(' . $allowed . '{1,63}\.)+'. // 1 or several sub domains with a max of 63 chars
                         '[a-z]' . '{2,6}'; // followed by a TLD
        if(eregi($regex, $url)==true) return true;
        else return false;

	}

	// Printing Date
	// ---------------------------------------- //

	function printDate($date){
		$date = strtotime($date);
		return date('l, M. j, Y', $date);
	}


	// Printing Time
	// ---------------------------------------- //

	function printTime($date){
		$date = strtotime($date);
		return date('h:i a', $date);
	}
	

	// String Truncate
	// ---------------------------------------- //

	function strTruncateByWords($str, $max_words){
		$str_array = explode(' ',$str);
		if( ( count($str_array) > $max_words ) && ( $max_words > 0) ){
			$str = implode(' ',array_slice($str_array, 0, $max_words))."...";
		}
		return $str;
	}

	function strTruncateByChars($str, $length=36) { 
		$x = substr($str, 0, $length); 
		if (strlen($str) > $length){ $x .= ' ...'; }
		return $x; 
	}


	// Checks if a string contains only alpha characters.
	// ---------------------------------------- //
	
	function is_alpha($someString) {
		return (preg_match("/[A-Z\s_]/i", $someString) > 0) ? true : false;
	}
	
	
	// Get Microtime.
	// ---------------------------------------- //
	
	function microtime_float(){
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
	

	// Paginate
	// ---------------------------------------- //
	
	function paginate($results, $pass_vars = '', $page = 0, $limit = 20, $targetpage = 'index.php'){
		
		// if $results is a string, perform the query, 
		// otherwise it will be treated as an array
		$results = ( is_string($results) )? getQueryMany($results) : $results;
			
		$total_row_count = sizeof($results);
				
		$vars = '?'.$pass_vars.'';
		$stages = 3;
		
		$start = ($page)? (($page - 1) * $limit) : 0;						
		
		// Initial page num setup
		if ($page == 0){ $page = 1; }
		$prev = $page - 1;	
		$next = $page + 1;							
		$lastpage = ceil( $total_row_count/$limit );		
		$lastpagem1 = $lastpage - 1;						
	
		$paginate = '';

		if($lastpage > 1){	
			
			$paginate .= '<div class="paginate">';
			
			// Previous
			if ($page > 1){
				$paginate.= "<a href='".$targetpage."".$vars."&page=".$prev."'>previous</a>";
			}
			else{
				$paginate.= "<span class='disabled'>previous</span>";	
			}
			
			
			// Pages	
			if ($lastpage < 7 + ($stages * 2)){	// Not enough pages to breaking it up	
				for ($counter = 1; $counter <= $lastpage; $counter++){
					if ($counter == $page){
						$paginate.= "<span class='current'>".$counter."</span>";
					}else{
						$paginate.= "<a href='".$targetpage."".$vars."&page=".$counter."'>".$counter."</a>";
					}					
				}
			}
			elseif($lastpage > 5 + ($stages * 2)){	// Enough pages to hide a few?
				// Beginning only hide later pages
				if($page < 1 + ($stages * 2)){
					for ($counter = 1; $counter < 4 + ($stages * 2); $counter++){
						if ($counter == $page){
							$paginate.= "<span class='current'>".$counter."</span>";
						}else{
							$paginate.= "<a href='".$targetpage."".$vars."&page=".$counter."'>".$counter."</a>";
						}					
					}
					$paginate.= "<span>...</span>";
					$paginate.= "<a href='".$targetpage."".$vars."&page=".$lastpagem1."'>".$lastpagem1."</a>";
					$paginate.= "<a href='".$targetpage."".$vars."&page=".$lastpage."'>".$lastpage."</a>";		
				}
				// Middle hide some front and some back
				elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)){
					$paginate.= "<a href='".$targetpage."".$vars."&page=1'>1</a>";
					$paginate.= "<a href='".$targetpage."".$vars."&page=2'>2</a>";
					$paginate.= "<span>...</span>";
					for ($counter = $page - $stages; $counter <= $page + $stages; $counter++){
						if ($counter == $page){
							$paginate.= "<span class='current'>".$counter."</span>";
						}else{
							$paginate.= "<a href='".$targetpage."".$vars."&page=".$counter."'>".$counter."</a>";}					
					}
					$paginate.= "<span>...</span>";
					$paginate.= "<a href='".$targetpage."".$vars."&page=".$lastpagem1."'>".$lastpagem1."</a>";
					$paginate.= "<a href='".$targetpage."".$vars."&page=".$lastpage."'>".$lastpage."</a>";		
				}
				// End only hide early pages
				else{
					$paginate.= "<a href='".$targetpage."".$vars."&page=1'>1</a>";
					$paginate.= "<a href='".$targetpage."".$vars."&page=2'>2</a>";
					$paginate.= "<span>...</span>";
					for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page){
							$paginate.= "<span class='current'>".$counter."</span>";
						}else{
							$paginate.= "<a href='".$targetpage."".$vars."&page=".$counter."'>".$counter."</a>";}					
					}
				}
			}
						
			// Next
			if ($page < $counter - 1){ 
				$paginate.= "<a href='".$targetpage."".$vars."&page=".$next."'>next</a>";
			}else{
				$paginate.= "<span class='disabled'>next</span>";
				}
				
			
			$paginate.= '			
			</div>
			';
		}
	 	echo $paginate;
	}
	
	
?>