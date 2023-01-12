<?php

//https://phppot.com/php/extracting-title-description-thumbnail-using-youtube-data-api/

function getInfoFromCurlInfo($videoId, $ytbId){
	
	$apikey = 'YouTube_Data_API_KEY';
	
    $googleApiUrl = 'https://www.googleapis.com/youtube/v3/videos?id=' . $videoId . '&key=' . $apikey . '&part=snippet,contentDetails,statistics';
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
		
	curl_close($ch);
		
	$data = json_decode($response);
		
	$value = json_decode(json_encode($data), true);
	
	
	$video_tags = (!empty($value['items'][0]['snippet']['tags'])) ? $value['items'][0]['snippet']['tags'] : false;
	
	//echo '<br/>';
	//echo '<pre>'; print_r($video_tags); 
	//echo '<br/>';
	//echo '<pre>'; print_r($value); exit;
	
	$pipe_separate_tags = "";
	
	if( !empty($video_tags) && count($video_tags) > 0 ){
		
		storeVideoTags( $videoId, $ytbId, $video_tags );
		
		$pipe_separate_tags = implode("|",$video_tags);
		
	}
	
	//echo '######################################';
	//echo '<br/>';
	//echo $defaultAudioLanguage = $value['items'][0]['snippet']['defaultAudioLanguage'];
	
	$return['title'] = (!empty($value['items'][0]['snippet']['title'])) ? $value['items'][0]['snippet']['title'] : '';
	$return['description'] = (!empty($value['items'][0]['snippet']['description'])) ? $value['items'][0]['snippet']['description'] : '';	
	$return['published_at'] =  (!empty($value['items'][0]['snippet']['publishedAt'])) ? $value['items'][0]['snippet']['publishedAt'] : '';
	$return['language'] =  (!empty($value['items'][0]['snippet']['defaultAudioLanguage'])) ? $value['items'][0]['snippet']['defaultAudioLanguage'] : '';
	
	
	$return['channel_id'] =  (!empty($value['items'][0]['snippet']['channelId'])) ? $value['items'][0]['snippet']['channelId'] : '';
	$return['channel_title'] =  (!empty($value['items'][0]['snippet']['channelTitle'])) ? $value['items'][0]['snippet']['channelTitle'] : '';
	
	$return['duration'] =  (!empty($value['items'][0]['contentDetails']['duration'])) ? $value['items'][0]['contentDetails']['duration'] : '';
	$return['duration_in_sec'] =  (!empty($return['duration'])) ? ISO8601ToSeconds($return['duration']) : '';
	
	
	$return['default_thumb'] = (!empty($value['items'][0]['snippet']['thumbnails']['default']['url'])) ? $value['items'][0]['snippet']['thumbnails']['default']['url'] : '';
	$return['medium_thumb'] = (!empty($value['items'][0]['snippet']['thumbnails']['medium']['url'])) ? $value['items'][0]['snippet']['thumbnails']['medium']['url'] : '';
	$return['high_thumb'] = (!empty($value['items'][0]['snippet']['thumbnails']['high']['url'])) ? $value['items'][0]['snippet']['thumbnails']['high']['url'] : '';
	$return['standard_thumb'] = (!empty($value['items'][0]['snippet']['thumbnails']['standard']['url'])) ? $value['items'][0]['snippet']['thumbnails']['standard']['url'] : '';
	$return['maxres_thumb'] = (!empty($value['items'][0]['snippet']['thumbnails']['maxres']['url'])) ? $value['items'][0]['snippet']['thumbnails']['maxres']['url'] : '';
	
	
	$return['definition'] =  (!empty($value['items'][0]['contentDetails']['definition'])) ? $value['items'][0]['contentDetails']['definition'] : '';
	$return['caption'] =  (!empty($value['items'][0]['contentDetails']['caption'])) ? $value['items'][0]['contentDetails']['caption'] : '';
	
	$return['view_count'] =  (!empty($value['items'][0]['statistics']['viewCount'])) ? $value['items'][0]['statistics']['viewCount'] : 0;
	$return['like_count'] =  (!empty($value['items'][0]['statistics']['likeCount'])) ? $value['items'][0]['statistics']['likeCount'] : 0;
	$return['dislike_count'] =  (!empty($value['items'][0]['statistics']['dislikeCount'])) ? $value['items'][0]['statistics']['dislikeCount'] : 0;
	$return['favorite_count'] =  (!empty($value['items'][0]['statistics']['favoriteCount'])) ? $value['items'][0]['statistics']['favoriteCount'] : 0;
	$return['comment_count'] =  (!empty($value['items'][0]['statistics']['commentCount'])) ? $value['items'][0]['statistics']['commentCount'] : 0;
	
	//echo '<br/>';
	//echo '<pre>'; print_r($return); exit;
	
	insertYTBFetchLog($videoId, $value);
	
	storeTextForSearch( $return['title'], $return['description'], $pipe_separate_tags, $videoId, $ytbId );
	
	//insertYTMediaInfo($media_info, $ytbId, $videoId);

	
	return $return;
}
