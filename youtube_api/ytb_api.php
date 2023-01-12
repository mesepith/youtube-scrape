<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include('ytb_mysql.php');
	include('ytb_curl.php');
	include('ytb_function.php');
	
	//$media_info = getInfoFromCurlInfo('n3eFM3uUMzQ', 15);
	//echo '<pre>'; print_r($media_info);
	//exit;
	
	function fetchDataFromYtApi($start_from){
				
		$yt_data = fetchYTBData( $start_from );
		//echo '<pre>'; print_r($yt_data); exit;
		
		if( count($yt_data) > 0 ){			
			
			$media_info = array();
			
			foreach( $yt_data AS $key=>$value ){
				
				$media_info = getInfoFromCurlInfo($value['vid_id'],  $value['id']);
				
				/*
				 *If SEO url is already present then no need to put new seo url
				 */  
				if( empty($value['seo_url']) ){
				
					/*Make SEO URL: Decode below url with base_convert$media_info['seo_url'], 10, 36) https://stackoverflow.com/questions/959957/php-short-hash-like-url-shortening-websites*/
					//$media_info['seo_url'] = intval($value['vid_id'], 36);
					$int_seo_url = intval($value['vid_id'], 36);
					
					/*If SEO url is zero or negative*/
					if( $int_seo_url < 1){
						
						$newmd5 = md5($value['id']);
						$int_seo_url = intval($newmd5, 36);
						
					}
					
					$media_info['seo_url'] = $int_seo_url . $value['id'];
				}else{
					
					echo ' <br/> seo_url is already present of '.$value['vid_id'].' with ytb_id: '.$value['id'].'<br/>';
					
				}
				insertYTMediaInfo($media_info, $value['id'], $value['vid_id']);
			}
			
			
			$start_from = $start_from+10;
			fetchDataFromYtApi($start_from);
			
		}else{
			
			echo '<br/>No Data From Database</br>';
			
		}	
		
		
	}
	$start_from = 0;
	fetchDataFromYtApi($start_from);

?>
