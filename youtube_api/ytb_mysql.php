<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ketosir";
global $conn;
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}


//print_r($conn); exit;

function fetchYTBData( $start_from ){
	global $conn;
	$sql = "SELECT id, vid_id, seo_url FROM ytb limit ".$start_from.",10 ";
	$result = $conn->query($sql);
	
	//return $result;
	$json = [];
	if ($result->num_rows > 0) {
		//return $result->fetch_row();
		// output data of each row
		while($row = $result->fetch_assoc()) {
			//echo "id: " . $row["id"]. " , vid_id: " . $row["vid_id"]. "<br>";
			 $json[] = $row;
		}
	} else {
		echo "0 results";
	}
	
	return $json;
	
	$conn->close();

}

function insertYTMediaInfo( $media_info, $id, $vid_id){
	global $conn;
	
	$valueSets = array();
	foreach($media_info as $key => $value) {
	   $valueSets[] = $key . " = '" . addslashes($value) . "'";
	}
	
	$sql = "UPDATE ytb SET ".join(",",$valueSets)." , api_updation_count=2 WHERE id=" . $id . " AND vid_id='" . $vid_id . "' limit 1";

	if (mysqli_query($conn, $sql)) {
		echo "Record updated successfully in ytb table with id= " . $id . " AND vid_id= " . $vid_id . "<br/>";
	} else {
		echo "Error updating record in ytb table : " . mysqli_error($conn) . " with id=" . $id . "<br/>";
	}
	
	echo '<br/><br/>';
	echo '##############################';
	echo '<br/><br/>';
		
}

function insertYTBFetchLog( $videoId, $value ){
	
	global $conn;
	
	$json_dataz = json_encode($value);
	
	$data = addslashes($json_dataz);
	
	//echo '<pre>'; print_r($value); 
	$sql = "INSERT INTO ytb_fetch_log (vid_id, data)
	VALUES ('$videoId', '$data')";

	if (mysqli_query($conn, $sql)) {
		echo "New record created successfully in ytb_fetch_log with vid_id= " . $videoId . "<br/>";
	} else {
		echo "Error insert in ytb_fetch_log table : " . $sql . "<br>" . mysqli_error($conn) . " with vid_id=" . $videoId . "<br/>";
	}
	
}

/*
 *Store Tags start
 **/
function storeVideoTags( $videoId, $ytbId, $video_tags ){
	 
	 global $conn;
	 
	 if(is_array($video_tags)){
		
		foreach( $video_tags AS $tag){
			
			$sql = "INSERT INTO ytb_tags (tag)
			VALUES ('$tag')";
			
			if (mysqli_query($conn, $sql)) {
				
				$last_insert_id = $conn->insert_id;
				
				echo "New Tag ".$tag."  created successfully in ytb_tags with vid_id= " . $videoId . " ,ytbId : " . $ytbId . " ,last_insert_id : " .$last_insert_id;	
				
				tagId_map_with_vidId_and_ytbId( $videoId, $ytbId, $last_insert_id, $tag );
				
				echo " <br/>";
									
			} else {	
							
				echo "Error insert in ytb_tags table : " . $sql . "<br>" . mysqli_error($conn) . " with vid_id=" . $videoId . " ,Error No is: ".mysqli_errno($conn);	
				echo " <br/>";
				
				if( mysqli_errno($conn) == '1062'){
					
					$tag_id = findTagIdByTagName($tag);
					
					echo ', Already present tag id: ' . $tag_id . " <br/>";;
					
					tagId_map_with_vidId_and_ytbId( $videoId, $ytbId, $tag_id, $tag );
					
					echo " <br/>";
				}
				
				
			}
			echo " <br/>";
		}
		
	}
	 
}
 
 /*
  * find TagId By Tag Name
  */
  
function findTagIdByTagName( $tag ){
	  
	global $conn;
	$sql = "SELECT id FROM ytb_tags where tag = '".$tag."' limit 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		
		$arr_result = $result->fetch_assoc();
		
		return $arr_result['id'];
		
	}
}

/*
 *Tag id map with video id and ytb table id
 */  

function tagId_map_with_vidId_and_ytbId( $videoId, $ytbId, $tag_id, $tag ){
	
	global $conn;
	$sql = "INSERT INTO ytb_tagid_map_with_vidid (tag_id, vid_id, ytb_id, tag)
	VALUES ('$tag_id', '$videoId', '$ytbId', '$tag')";
	
	if (mysqli_query($conn, $sql)) {
				
		echo "Mapped successfully in ytb_tagid_map_with_vidid with vid_id= " . $videoId . " ,ytbId : " . $ytbId . " ,tag_id : " .$tag_id;	
	}else{
		
		echo "Error insert in ytb_tagid_map_with_vidid table : " . $sql . "<br>" . mysqli_error($conn) . " with vid_id=" . $videoId . " ,ytbId : " . $ytbId . " ,tag_id : " .$tag_id . " ,Error No is: ".mysqli_errno($conn);	
		
	}
	
}

/*
 * store title, description, pipe_separate_tags For Search
 **/
 
function storeTextForSearch( $title, $description, $pipe_separate_tags, $videoId, $ytbId ){
	
	global $conn;
	$sql = "INSERT INTO ytb_txt_search (ytb_id, vid_id, title, description, tags)
	VALUES ('$ytbId', '$videoId', '$title', '$description', '$pipe_separate_tags')";
	
	if (mysqli_query($conn, $sql)) {
				
		echo "Insert successfully in ytb_txt_search with vid_id= " . $videoId . " ,ytbId : " . $ytbId;	
	}else{
		
		echo "Error insert in ytb_txt_search table : " . $sql . "<br>" . mysqli_error($conn) . " with vid_id=" . $videoId . " ,ytbId : " . $ytbId . " ,Error No is: ".mysqli_errno($conn);	
		
	}
	echo '<br/><br/>';
	
}
 
?>
