<?php
class Crawl {
	function getResult($url, $post_fields = array(), $referer)
	{
		$ch = curl_init();     
  		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0"); 
		if(!empty($post_fields)) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		$result = curl_exec($ch); 
		curl_close($ch);

		return $result;
	}

	function getContent($result, $regular_expressions) {
		if(is_array($regular_expressions)) {
			for($i = 0; $i < count($regular_expressions); $i++) {
				$regex_array = explode("(.*?)", $regular_expressions[$i]);
				$regex_array[0] = preg_quote($regex_array[0], "/");
				$regex_array[1] = preg_quote($regex_array[1], "/");
					
				$regular_expressions[$i] = $regex_array[0]."(.*?)".$regex_array[1];
				if($i == 0) {
					preg_match('/'.$regular_expressions[$i].'/is', $result, $content_cache);
				}else{
					preg_match('/'.$regular_expressions[$i].'/is', $content_cache[1], $content_cache);
				}
			}
		}else{
			$regex_array = explode("(.*?)", $regular_expressions);
			$regex_array[0] = preg_quote($regex_array[0], "/");
			$regex_array[1] = preg_quote($regex_array[1], "/");
			
			$regular_expressions = $regex_array[0]."(.*?)".$regex_array[1];
			
			preg_match('/'.$regular_expressions.'/is', $result, $content_cache);
		}
		
		if(!empty($content_cache)) {
			return $content_cache[1];
		}else{
			return "";
		}
	}
	
	function getListTitles($result, $regular_expressions) {
		if(is_array($regular_expressions)) {
			for($i = 0; $i < count($regular_expressions); $i++) {
				$regex_array = explode("(.*?)", $regular_expressions[$i]);
				$regex_array[0] = preg_quote($regex_array[0], "/");
				$regex_array[1] = preg_quote($regex_array[1], "/");
				
				$regular_expressions[$i] = $regex_array[0]."(.*?)".$regex_array[1];
			
				if($i == 0) {
					preg_match_all('/'.$regular_expressions[$i].'/is', $result, $title_cache);
				}else{
					for($j = 0; $j < count($title_cache[1]); $j++) {
						preg_match('/'.$regular_expressions[$i].'/is', $title_cache[1][$j], $title_title_cache);
						$title_cache[1][$j] = $title_title_cache[1];
					}
				}
			}
		}else{
			$regex_array = explode("(.*?)", $regular_expressions);
			$regex_array[0] = preg_quote($regex_array[0], "/");
			$regex_array[1] = preg_quote($regex_array[1], "/");
			
			$regular_expressions = $regex_array[0]."(.*?)".$regex_array[1];
			
			preg_match_all('/'.$regular_expressions.'/is', $result, $title_cache);
		}
		
		if(!empty($title_cache)) {
			return $title_cache[1];
		}else{
			return "";
		}
	}
	
	function regexReplace($result, $find_regexs, $replace_regexs) {
		if(is_array($find_regexs) && is_array($replace_regexs) && count($find_regexs) == count($replace_regexs)) {
			for($i = 0; $i < count($find_regexs); $i++) {
				$result = preg_replace('/'.$find_regexs[$i].'/is', $replace_regexs[$i], $result);
			}
		}else{
			$result = preg_replace('/'.$find_regexs.'/is', $replace_regexs, $result);
		}
		
		return $result;
	}
	
	function regexSplit($result, $split_regexs) {
		if(is_array($split_regexs)) {
			for($i = 0; $i < count($split_regexs); $i++) {
				if($i == 0) {
					$result = preg_split('/'.$split_regexs[$i].'/is', $result);
				}else{
					for($j = 0; $j < count($result); $j++) {
						$result[$j] = preg_split('/'.$split_regexs[$i].'/is', $result[$j]);
					}
				}	
			}
		}else{
			$result = preg_split('/'.$split_regexs.'/is', $result);
		}
		
		return $result;
	}
	
	function crawlMultiple($result, $crawl_multiple_array) {
		$retun_value = "";
		$check_crawl = 0;
		foreach($crawl_multiple_array as $key=>$value) {
			switch($key) {
				case 'content':
					if($check_crawl) {
						$return_value = $this->get_content($return_value, $value);
					}else{
						$return_value = $this->get_content($result, $value);
					}
					
					$check_crawl = 1;
					break;
				case 'titles':
					if($check_crawl) {
						$return_value = $this->get_list_titles($return_value, $value);
					}else{
						$return_value = $this->get_list_titles($result, $value);
					}
					$check_crawl = 1;
					break;
			}
		}
		return $return_value;
	}
	
	function downloadFile($www_url, $path, $referer) {
		$ch = curl_init($www_url);
		
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 
		curl_setopt($ch, CURLOPT_REFERER, $referer);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    	$data = curl_exec($ch);
 
    	curl_close($ch);
 		
    	file_put_contents($path, $data);
	}	
}
?>