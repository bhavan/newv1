<?php
ini_set('error_reporting',1);
ini_set('display_errors',1);

/* HIDE iadbanner -- YOGI */
include("class.paggination.php");
include("connection.php");



/* No need */
//$query = "select * from jos_phocagallery_categories where id<>2 and published=1 and approved=1 order by id desc";
//$rec   = mysql_query($query) or die(mysql_error());
//$param = db_fetch("select * from `jos_phocagallery_categories` where id != 2 and `published` = 1 and `approved` = 1 order by ordering asc", true, true);

$CatId = isset($_GET['id']) ? $_GET['id']:0;

if($CatId>0){
	$select_query = "select title,filename from jos_phocagallery where  catid={$CatId} and published=1 and approved=1 order by id desc";
}else{
	$select_query = "select title,filename from jos_phocagallery where catid<>2 and published=1 and approved=1 order by id desc";
}

if(isset($_GET['id']) && $_GET['id'] != 0){

	$rec_no			=	mysql_query($select_query);
	$mydb			=	new pagination(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	$mydb->connection();
	$num_records	=	mysql_num_rows($rec_no);
	$num_rec		=	30;

	$mydb->set_qry($select_query);
	$mydb->set_record_per_sheet($num_rec);

	$num_pages	=	$mydb->num_pages();
	
	if (isset($_REQUEST['start']))
		$recno=$_REQUEST['start'];
	else
		$recno=0;

	$rec			=	$mydb->execute_query($recno);
	$current_page	=	$mydb->current_page();
	$start_page=$mydb->start_page();
	$end_page=$mydb->end_page();
	$photoindent	=	$recno-1;
	$i = 0;
	while($row	=	mysql_fetch_array($rec)) {
		//		$j++;
		//$row['image_href_url']		=	"album.php?start=".($photoindent+$j)."&backstart=".(int)$_REQUEST[start]."&id=".$CatId;
		$filename = $row['filename'];
		$userfolder = '';
		$tmp_arr = explode('/', $row['filename']);
		if(count($tmp_arr) > 1){
			$userfolder = $tmp_arr[0].'/';
			$filename = $tmp_arr[1];
		}
		
		$data[$i]['name']		= $row['title'];
		$data[$i]['thumb']		= "http://".$_SERVER['SERVER_NAME']."/partner/".$_SESSION['partner_folder_name']."/images/phocagallery/".$userfolder."thumbs/phoca_thumb_m_".$filename;
		$data[$i]['picture']	= "http://".$_SERVER['SERVER_NAME']."/partner/".$_SESSION['partner_folder_name']."/images/phocagallery/".$userfolder."thumbs/phoca_thumb_l_".$filename;
		++$i;
	} 

	$response = array(
    	'data' => $data,
    	'meta' => array(
        'total' => $num_records,
        'limit' => $num_records,
        'offset' => 0
    	)
	);

	//echo "<pre>"; print_r($data);
	header('Content-type: application/json');
	echo json_encode($response);
	
	
	
	/* Individual photo listing */
	/*
	while($row	=	mysql_fetch_array($rec)) {
		$file=explode('/',$row['filename']);
		echo $j++;
		if (count($file)>=2){?>
			<a href="photos_view.php?start=<?=$photoindent+$j?>&backstart=<?=(int)$_REQUEST[start]?>&id=<?=$CatId ?>"><img src="/partner/<?=$_SESSION['partner_folder_name']?>/images/phocagallery/<?=$file[0]?>/thumbs/phoca_thumb_s_<?=$file[1]?>" width="55" border="0" /></a>
		<? }else{?>
			<a href="photos_view.php?start=<?=$photoindent+$j?>&backstart=<?=(int)$_REQUEST[start]?>&id=<?=$CatId ?>"><img src="/partner/<?=$_SESSION['partner_folder_name']?>/images/phocagallery/thumbs/phoca_thumb_s_<?=$row['filename']?>" width="55" border="0"/></a>
		<? } 
	}
	*/
	
	/* Photos listing on base of category ends here */
	
}else{

	/* Code for category listing */  
	//$query	=	"select * from jos_phocagallery_categories where id<>2 and published=1 and approved=1 order by ordering";
	$query	=	"select id,title as name from jos_phocagallery_categories where id<>2 and published=1 and approved=1 order by ordering";
	$rec	=	mysql_query($query) or die(mysql_error());
	//$num_records = mysql_num_rows($rec);	
	$catData = array();
	while($r	=	mysql_fetch_assoc($rec)){
		$catData[] = $r;
	}
		//echo "<pre>";
		//print_r($param);
		//echo "</pre>";
		//header('Content-type: application/json');
		//echo json_encode($param);
		
	$num_records = 0;		
	foreach($catData as $k => $v){
		
		$query1 = "select id, filename from `jos_phocagallery` where `published` = 1 and `approved` = 1 and `catid` = ".$v['id'] ." ORDER BY ordering"; 
		$rec1	=	mysql_query($query1) or die(mysql_error());
		$totalCatPhotos = mysql_num_rows($rec1);

		if($totalCatPhotos > 0){

			$data[$num_records]['id'] = intval($v['id']);
			$data[$num_records]['name']		= $v['name'];

			$v['photos'] = array();
	
			while($r1	=	mysql_fetch_assoc($rec1)){
				$v['photos'][] = $r1;
			}
					
			$id = rand(0, (count($v['photos']) - 1));
			$tmp_arr = explode('/', $v['photos'][$id]['filename']);
			$userfolder = '';
			$filename = $v['photos'][$id]['filename'];
		
			if(count($tmp_arr) > 1){
				$userfolder = $tmp_arr[0].'/';
				$filename = $tmp_arr[1];
			}
			unset($tmp_arr);
			
			if(trim($userfolder) == '' && trim($filename) == ''){
				$data[$num_records]['thumb'] = '';
			}else{
				$data[$num_records]['thumb'] = 'http://'.$_SERVER['SERVER_NAME'].'/partner/'.$_SESSION['partner_folder_name'].'/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_m_'.$filename;
			}
			$data[$num_records]['num_photos'] = mysql_num_rows($rec1);
			++$num_records;
		}			
	}
	/* Jason code for galleries file (Phoca gallery Category listing) */
	
	//echo "<pre>"; print_r($data);

	$response = array(
    	'data' => $data,
    	'meta' => array(
        'total' => $num_records,
        'limit' => $num_records,
        'offset' => 0
    	)
	);
	
	//echo "<pre>";
	//print_r($response);
	header('Content-type: application/json');
	echo json_encode($response);
}
?>