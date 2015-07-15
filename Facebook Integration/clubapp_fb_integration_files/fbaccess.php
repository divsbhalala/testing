<?php
//Application Configurations

//arunvishwanadh
//$app_id        = "369220629922111";
//$app_secret    = "8c7807b4304f1c78ba34a711ffde1d63";

//ray
$app_id        = "1559197784350810";
$app_secret    = "9a5b66594f24adb8ac0e29572e56d201";

$site_url      = "http://www.myclubapp.club/admin/adminlogin.php";

try{
	include_once "fb/facebook.php"; 
}catch(Exception $e) {
	error_log($e);
}
// Create our application instance
$facebook = new Facebook(array(
		'appId'  => $app_id,
		'secret' => $app_secret,
	));

// Get User ID
$user = $facebook->getUser();
// We may or may not have this data based
// on whether the user is logged in.
// If we have a $user id here, it means we know
// the user is logged into
// Facebook, but we don’t know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
	//==================== Single query method ======================================
	try{
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	}catch(FacebookApiException $e) {
		error_log($e);
		$user = NULL;
	}
	//==================== Single query method ends =================================
}


if (!$user) {
	// Get login URL
	$loginUrl = $facebook->getLoginUrl(array(
			'scope'   => 'publish_stream, user_groups, user_likes, manage_pages',
			'redirect_uri' => $site_url,
		));
}
  

//if ($user) {

	// Proceed knowing you have a logged in user who has a valid session.

	//========= Batch requests over the Facebook Graph API using the PHP-SDK ========
	// Save your method calls into an array
	$queries = array(
		array('method' => 'GET', 'relative_url' => '/'.$user),
		array('method' => 'GET', 'relative_url' => '/'.$user.'/groups?limit=5000'),
		array('method' => 'GET', 'relative_url' => '/'.$user.'/accounts?limit=5000')                
	);

	// POST your queries to the batch endpoint on the graph.
	try{
		$batchResponse = $facebook->api('?batch='.json_encode($queries), 'POST');
	}catch(Exception $o) {
		error_log($o);
	}       

	//Return values are indexed in order of the original array, content is in ['body'] as a JSON
	//string. Decode for use as a PHP array.
	$user_info  = json_decode($batchResponse[0]['body'], TRUE);
	$groups   = json_decode($batchResponse[1]['body'], TRUE);
	$pages   = json_decode($batchResponse[2]['body'], TRUE);
	//========= Batch requests over the Facebook Graph API using the PHP-SDK ends =====

	if (isset($_POST['submit_x']) && isset($_POST['ids'])) {
            
		$list_ids = array();
		$group_new_list = array();
		if (isset($groups['data'])) {
			foreach ($groups['data'] as $group) {
				$group_new_list[$group['id']] = $group['name'];
			}
		}

		$page_new_list = array();
		if (isset($pages['data'])) {
			foreach ($pages['data'] as $page) {
				$page_new_list[$page['id']] = $page['name'];
			}
		}                
               
                
                // Special Offers Page 
		if ($_POST['offerTitle'] || $_POST['offerName'] || $_POST['offerDesc']) {
                    
			$body = array();
                        if(isset($_POST['access_token'])) $body['access_token'] = $_POST['access_token'];
			if (isset($_POST['offerTitle'])) $body['message'] = $_POST['offerTitle'];
			if (isset($_POST['offerName'])) $body['name'] = $_POST['offerName'];
			if (isset($_POST['offerTitle'])) $body['caption'] = $_POST['offerTitle'];
			if (isset($_POST['offerDesc'])) $body['description'] = $_POST['offerDesc']; 
                        if (isset($_POST['fbpostlink'])) $body['link'] = $_POST['fbpostlink']; 
//                        if(isset($_POST['fbofferscheduletime'])){
//                           $body['published'] = 'false';  
//                           $body['scheduled_publish_time'] = strtotime($_POST['fbofferscheduletime']); 
//                        }
			$body['picture'] = 'http://'.$_SERVER['SERVER_NAME'].'/admin/'.$dp;               
                                           
                } 
                
                // Club News Page 
		if ($_POST['newsname'] || $_POST['newsdesc']) {
                    
			$body = array();
                        if(isset($_POST['access_token'])) $body['access_token'] = $_POST['access_token'];
			if (isset($_POST['newsname'])) $body['message'] = $_POST['newsname'];
			if (isset($_POST['newsname'])) $body['name'] = $_POST['newsname'];
			if (isset($_POST['newsname'])) $body['caption'] = $_POST['newsname'];
			if (isset($_POST['newsdesc'])) $body['description'] = $_POST['newsdesc'];                        
//                        if(isset($_POST['fbclubscheduletime'])){
//                           $body['scheduled_publish_time'] = strtotime($_POST['fbclubscheduletime']); 
//                        }
			$body['picture'] = 'http://'.$_SERVER['SERVER_NAME'].'/admin/'.$dp;
                        if (isset($_POST['fbpostlink'])) $body['link'] = $_POST['fbpostlink'];                      
                }
                
                 // My Team News Page
                if ($_POST['postname'] || $_POST['postdesc']) {
                    
			$body = array();
                        if(isset($_POST['access_token'])) $body['access_token'] = $_POST['access_token'];
			if (isset($_POST['postname'])) $body['message'] = $_POST['postname'];
			if (isset($_POST['postname'])) $body['name'] = $_POST['postname'];
			if (isset($_POST['postname'])) $body['caption'] = $_POST['postname'];
			if (isset($_POST['postdesc'])) $body['description'] = $_POST['postdesc'];                        
//                        if(isset($_POST['fbteamscheduletime'])){                           
//                          $body['scheduled_publish_time'] = strtotime($_POST['fbteamscheduletime']); 
//                         }
			$body['picture'] = 'http://'.$_SERVER['SERVER_NAME'].'/admin/'.$dp;
                        if (isset($_POST['fbpostlink'])) $body['link'] = $_POST['fbpostlink'];                      
                }
                
                //Squad messages
                if ($_POST['squadnews'] || $_POST['squadnewsdesc']) {
                    
			$body = array();
                        if(isset($_POST['access_token'])) $body['access_token'] = $_POST['access_token'];
			if (isset($_POST['squadnews'])) $body['message'] = $_POST['squadnews'];
			if (isset($_POST['squadnews'])) $body['name'] = $_POST['squadnews'];
			if (isset($_POST['subtitle'])) $body['caption'] = $_POST['subtitle'];
			if (isset($_POST['squadnewsdesc'])) $body['description'] = $_POST['squadnewsdesc'];                        
//                        if(isset($_POST['fbsquadscheduletime'])){                           
//                          $body['scheduled_publish_time'] = strtotime($_POST['fbsquadscheduletime']);                     
//                        }
			if(!empty($dp)){ $body['picture'] = 'http://'.$_SERVER['SERVER_NAME'].'/admin/'.$dp; } else{ $body['picture'] = ''; }
                        if (isset($_POST['fbpostlink'])) $body['link'] = $_POST['fbpostlink'];                      
                }
                
                          
			$batchPost=array();

			$i=1;
			$flag=1;                        
                        
			foreach ($_POST['ids'] as $id) {
                            
			      $batchPost[] = array('method' => 'POST', 'relative_url' => "/$id/feed", 'body' => http_build_query($body));
                                
                                
				//if ($i++ == 50) {
                                
                                 //posts message on page statues 
                                
//                                $post_url = '/'.$id.'/photos';
//                                
//                                $msg_body = array(
//                                 'source'=>'@'.realpath($dp),   
//                                 'message' => $_POST['offerDesc'],                                
//                                );
                                
//                                if(isset($_POST['offerStartDate'])){
//                                    if($_POST['offerStartDate'] > date('Y-m-d',time())){
//                                        $msg_body['published'] = 'false';
//                                        $msg_body['scheduled_publish_time'] = strtotime($_POST['offerStartDate']);
//                                    }
//                                 }
                                
                                //echo '<pre>'; print_r($msg_body); exit; 
                                    
                                   
					try{
						$multiPostResponse = $facebook->api('?batch='.urlencode(json_encode($batchPost)), 'POST');  
                                               // $multiPostResponse = $facebook->api($post_url, 'post', $msg_body );
                                                // print_r($multiPostResponse); exit();
						if (is_array($multiPostResponse)) {
							foreach ($multiPostResponse as $singleResponse) {
								$temp = json_decode($singleResponse['body'], true);
								if (isset($temp['id'])) {
									$splitId = explode("_", $temp['id']);
									if (!empty($splitId[1])) $list_ids[] = $splitId[0];
                                                                        //print_r($temp);
                                                                        $fbpostId = $temp['id']; 
								}elseif (isset($temp['error'])) {
									print_r($temp['error'], true);
								}
							}
						}
					}catch(FacebookApiException $e) {
						print_r($e);
					}

					$flag=0;
					unset($batchPost);
					$i=1;
				//}

			}
                        
                        
                        
			if (isset($batchPost) && count($batchPost) > 0 ) {
				try{
					//$multiPostResponse = $facebook->api($post_url, 'post', $msg_body );
                                        $multiPostResponse = $facebook->api('?batch='.urlencode(json_encode($batchPost)), 'POST');  
					if (is_array($multiPostResponse)) {
						foreach ($multiPostResponse as $singleResponse) {
							$temp = json_decode($singleResponse['body'], true);
							if (isset($temp['id'])) {
								$splitId = explode("_", $temp['id']);
								if (!empty($splitId[1])) $list_ids[] = $splitId[0];
							}elseif (isset($temp['error'])) {
								error_log(print_r($temp['error'], true));
							}
						}
					}
				}catch(FacebookApiException $e) {
					error_log($e);
				}
				$flag=0;
			}
		
		
	}
        
        if (isset($multiPostResponse)) {

		$failed = array_diff($_POST['ids'], $list_ids);
		if (!empty($list_ids)) {
			//echo "<div class='message success' ><b>Successfully posted to:</b><br>";
			$temp = array();
			foreach ($list_ids as $list_id) {
				if (array_key_exists($list_id, $group_new_list)) {
					$temp[] = "<a href='http://www.facebook.com/$list_id' >" . $group_new_list[$list_id] . "</a>";
				}else {
					$temp[] = "<a href='http://www.facebook.com/$list_id' >" .  $page_new_list[$list_id] . "</a>";
				}
			}
			//echo implode(", ", $temp);
			//echo "</div>";
		}
		if (!empty($failed)) {
			//echo "<div class='message error' ><b>Unsuccessfull to:</b><br>";
			$temp = array();
			foreach ($failed as $list_id) {
				if (array_key_exists($list_id, $group_new_list)) {
					$temp[] = "<a href='http://www.facebook.com/$list_id' >" . $group_new_list[$list_id] . "</a>";
				}else {
					$temp[] = "<a href='http://www.facebook.com/$list_id' >" .  $page_new_list[$list_id] . "</a>";
				}
			}

			//echo implode(", ", $temp);
			//echo "</div>";
		}
	}        
        
//}
?>