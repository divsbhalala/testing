<?php 

ob_start();
session_start();

/* Set locale to Ireland */

//setlocale(LC_ALL, 'ie_IE');

$error_message = '';
$args_error = array();

if( !isset($_SESSION['username']) ){
	header("Location: adminlogin.php");
}

include "includes/header.php";
include "includes/commonfunctions.php";

$fbpageSQL = mysql_query("select c.fbteamnewspage, f.code from clubs_m_t c left join fbaccesscode f on f.id = c.fbteamnewspage where c.isActive = 'Active' limit 1 ");
        
$fbpages = mysql_fetch_assoc($fbpageSQL);

//Sports
$sportsitems = getSportslist();


//file uploads start
if(isset($_POST['submit_x'])){

//echo "testing";

//print_r($_FILES);

$on=$_FILES['f']['name'];
$ty=$_FILES['f']['type'];
$tp=$_FILES['f']['tmp_name'];
$dp="postimages/".$on;
	if($_FILES['f']['error']==0){

		$dp="postimages/".time().$on;

			move_uploaded_file($tp,$dp) or die("Error while uploading");	

			//echo"<h4>File $on uploaded Successfully</h4>";
	}
}

?>

<link rel="stylesheet" type="text/css" href="css/datepickr.css" />

<?php

//file uploads end

if(isset($_POST['submit_x']))
{
	//echo 'teams='.$_POST['section'];
    if(empty($_POST['postname'])){

      $args_error[] = 'Please Enter Post Name'; 

    }else if(empty($_POST['postdesc'])){

      $args_error[] = 'Please Enter Description';   

    }else if(empty($_POST['postofdate'])){

      $args_error[] = 'Please Select Date';   

    }else if(empty($_POST['cat'])){

		//if($_SESSION['userType']=='Superadmin'){

      $args_error[] = 'Please Select Club';
   }else if(empty($_POST['section'])){
		if($_SESSION['userType']=='Clubmember' || $_SESSION['userType']=='Superadmin'){
    $args_error[] = 'Please Select Section';
		}
   }else if(empty($_POST['team'])){

		if($_SESSION['userType']=='Teammember' || $_SESSION['userType']=='Sectionmember'){

      $args_error[] = 'Please Select Team';
		}
   }


  if(empty($args_error)){

  /* Verify Email Address */

 /* $user_email = mysql_real_escape_string($_POST['email']);

  $SQLEmail = "SELECT email FROM `clubs_m_t` WHERE userEmail = '".$user_email."'";

  $SQLQueryEmail = mysql_query($SQLEmail);

  $count_email = mysql_num_rows($SQLQueryEmail);

  if($count_email < 1 ){	

   $sql="insert into section_m_t(sectionname,association_name,sectiondesc,sectionimage,club_id,createdDate,isActive)values('".$_POST['sectionname']."','".$_POST['association']."','".$_POST['sectiondesc']."','".$dp."','".$_POST['cat']."',now(),".$_POST['status'].")";

   $esql=mysql_query($sql);

   if($esql){

      header('location:sectionlist.php?msg=1');

    }else{ echo "error"; }

  }else{

    $post = $_POST;

    $error_message = 'Email Address has already been taken';

  }*/

  include "includes/fbaccess.php";

  if($_POST['status']=='1'){$status='Active';}else{$status='Inactive';}

  

  if($_SESSION['userType']!='Superadmin'){

  $getsection=mysql_query("select club_id from users_m_t where userId='".$_SESSION['userid']."'");

  $resection=mysql_fetch_array($getsection);

  //$secname=$resection['sectionname'];

  $sec_clubid=$resection['club_id'];

  }else{

	  $sec_clubid=$_POST['cat'];

	  }

  $getclubname=mysql_query("select clubName from clubs_m_t where clubId='".$sec_clubid."'");

  $reclub=mysql_fetch_array($getclubname);

  $clubname=$reclub['clubName'];

//echo "postteam=".$_POST['team']; 
//echo 'team='.$team=implode(',',$_POST['team']);exit;

 $sql="insert into posts_t(postTitle,fbPostId,fbpostlink,fbteamscheduletime,user_id,postDesc,postImage,postUrl,postDate,section_id,section_name,team,sport_id,club_id,club_name,createdby_usertype,createdby_user,createdDate,isActive)values('".addslashes($_POST['postname'])."', '". $fbpostId ."', '". $_POST['fbpostlink'] ."', '". date('Y-m-d h:i:s', strtotime($_POST['fbteamscheduletime'])) ."','".$_SESSION['userid']."','".addslashes($_POST['postdesc'])."','".$dp."','".$_POST['posturl']."','" . date('Y-m-d h:i:s', strtotime($_POST['postofdate'])) . "','".$_POST['section']."','".$secname."','".implode(',',$_POST['team'])."',".$_POST['sport_id'].",'".$sec_clubid."','".$clubname."','".$_SESSION['userType']."','".$_SESSION['username']."',now(),'".$status."')";
//echo $sql;
//exit;
 
 $esql=mysql_query($sql);
 $insertId = mysql_insert_id();
 
 // Subscription Email
 
 if(!empty($insertId)){
    
    $eSection = 'My Team News';
    $eTeam = '';
    $eTitle = $_POST['postname'];
    $eSubject = $eSection.': '.$eTeam.' '.$eTitle;
    $uName =  $_SESSION['username'];

    $bLogo = '';
    $bTitle = $_POST['postname'];
    $bImage = '<img src="http://www.myclubapp.club/admin/'.$dp.'" width="320" height="320"/>';
    $bStartdate =  date('d-m-y h:i:s', strtotime($_POST['postofdate']));
    $bDescription = addslashes($_POST['postdesc']);
    $bFblink = '<a href="https://www.facebook.com/818612878204744/posts/'.$fbpostId .'" alt="MRA Facebook Link" ><img src="http://www.myclubapp.club/images/FB_Share.png" width="100"/></a>';
    $bLoginlink = '<a href="http://www.myclubapp.club/mraweb.php" alt="MRA Web">Change Preferences</a>';
  
    /* Email Configuration */
    $follow = 1;
    $teamusersubscribe = implode(',',$_POST['team']);
    $teamsubscriberSQL = sprintf("SELECT ur.userEmail FROM  favourites tu LEFT JOIN team_m_t tm ON tm.teamId = tu.team_id LEFT JOIN users_m_t ur ON ur.userId = tu.user_id WHERE tu.team_id IN (%s) AND tu.follow = %d",$teamusersubscribe, $follow);
    $teamsubscriberQuery = mysql_query($teamsubscriberSQL) or die(mysql_error());

    while($teamsubscribers = mysql_fetch_array($teamsubscriberQuery)){ $teamsubscribedusers[] = $teamsubscribers['userEmail'];}
   
    $headers  = "From: My Rugby<ray@fcgeng.ie> \r\n";
    $headers .= "Reply-To: ray@fcgeng.ie \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    
    
    /* Club News email details */
    $tempCode = 'CN' ;
    $templayout = file_get_contents('../email-templates/postmessages.html'); 
    $tempSQL = "SELECT * FROM `email_template` WHERE template = '".$tempCode."' ORDER BY id DESC LIMIT 1";
    $tempQuery = mysql_query($tempSQL) or die(mysql_error());
    $tempResults = mysql_fetch_assoc($tempQuery);
    extract($tempResults);
   
    
    if(count($tempResults) > 0){
     $subject = $eSubject;
     $bContent = str_replace( array('[USER]','[LOGO]','[TITLE]','[DATE]', '[DESCRIPTION]','[FBLINK]', '[LOGINLINK]'), array($uName,$bLogo,$bTitle,$bStartdate, $bDescription, $bFblink, $bLoginlink), $content);                
     $body = str_replace('[CONTENT]', $bContent, $templayout);   
    }
    
   foreach( $teamsubscribedusers as $teamsubscribeduser ){
     $to = $teamsubscribeduser ;  
     if(!mail($to, $subject, $body, $headers)){
       die('Email sending failed');
     }
   }
  }
  

  if($esql){
	$tit = $_POST['postname'];
	  $desc = $_POST['postdesc'];
	  $dt_exp = explode(' ',$_POST['postofdate']);
	//for push notifications
	 if($_POST['notify']=='on' && date('d-m-Y')==$dt_exp[0]){
	 pushforTeam($tit,$desc,implode(',',$_POST['team']));
	 /*if(file_exists('pushnotes-functions.php')){
	  include('pushnotes-functions.php');
	  pushforTeam($tit,$desc);
	 }else{
		 die('File not found');
	 }*/
	 pushforTeamandroid($tit,$desc,implode(',',$_POST['team']));
	 }
      header('location:postlist.php?msg=1');

    }else{ echo "error ".mysql_error(); }

 }else{

   $post = $_POST;
   $clubs = getClublists($_POST['sport_id']);
   $error_message = join('<br/>',$args_error);  

 }



}

if(isset($_REQUEST['bid']))



{

	$fsql=mysql_query('select * from posts_t where postId='.$_REQUEST['bid']);

	$f=mysql_fetch_assoc($fsql);
        
         if(!empty($f['fbPostId'])){           
          $fbPostid = explode('_',$f['fbPostId']);
          $f['fbPostId'] = 'https://www.facebook.com/'.$fbPostid[0].'/posts/'.$fbPostid[1];
        } 
        
         $clubs = getClublists($f['sport_id']);

	

}

if(isset($_POST['update_x']))

{

//file uploads



	if($_FILES && $_FILES['f']['error']==0 && !empty($_FILES['f']['name'])){



$on=$_FILES['f']['name'];

$ty=$_FILES['f']['type'];

$tp=$_FILES['f']['tmp_name'];

$dp="postimages/".time().$on;



			move_uploaded_file($tp,$dp) or die("Error while uploading");

$imgqry="select postImage from posts_t where postId=".$_REQUEST['bid'];

$imgre=mysql_query($imgqry);

$imgres=mysql_fetch_assoc($imgre);

$imgname=$imgres['postImage'];

@unlink($imgname);	

			

$esql=mysql_query("update posts_t set postImage='".$dp."' where postId=".$_REQUEST['bid']);

			

			//echo"<h4>File $on uploaded Successfully</h4>";

		

	}

//file uploads

	

//$esql=mysql_query("update clubs_m_t set firstname='".$_POST['firstname']."', lastname='".$_POST['lastname']."', userEmail='".$_POST['email']."', phone='".$_POST['phone']."', IsActive=".$_POST['status']." where clubId=".$_REQUEST['bid']);





if($_POST['status']=='1'){$status='Active';}else{$status='Inactive';}



  if($_SESSION['userType']!='Superadmin'){

  $getsection=mysql_query("select club_id from users_m_t where userId='".$_SESSION['userid']."'");

  $resection=mysql_fetch_array($getsection);

  //$secname=$resection['sectionname'];

  $sec_clubid=$resection['club_id'];

  }else{

	  $sec_clubid=$_POST['cat'];

	  }

  $getclubname=mysql_query("select clubName from clubs_m_t where clubId='".$sec_clubid."'");

  $reclub=mysql_fetch_array($getclubname);

  $clubname=$reclub['clubName'];

  

$esql=mysql_query("update posts_t set postTitle='".addslashes($_POST['postname'])."',user_id='".$_SESSION['userid']."',postDesc='".addslashes($_POST['postdesc'])."' ,postUrl='".$_POST['posturl']."',postDate='". date('Y-m-d', strtotime($_POST['postofdate']))."', section_id='".$_POST['section']."',section_name='".$secname."',team='".implode(',',$_POST['team'])."', sport_id = ".$_POST['sport_id'].", club_id='".$sec_clubid."',club_name='".$clubname."',createdby_usertype='".$_SESSION['userType']."',createdby_user='".$_SESSION['username']."',createdDate=now(),isActive='".$status."' where postId=".$_REQUEST['bid']);





	 if($esql)

	  {
		  $currentpage = $_GET['currentpage'];
		   if($_GET['limit']!=""){
		 $limit = $_GET['limit'];
		 }else{
		 $limit = 10;	 
		 }
		 $club=$_GET['club'];
		 $section=$_GET['section'];
		 $team=$_GET['team'];
		 $startdate=$_GET['startdate'];
		 $enddate=$_GET['enddate'];
				
$querystring = "club=".$club."&section=".$section."&team=".$team."&startdate=".$startdate."&enddate=".$enddate."&srch="."Search"."&currentpage=".$currentpage."&limit=".$limit;

		 header('location:postlist.php?msg=3&'.$querystring);
	
	   }

	   else

	   {

		echo "error".mysql_error();

	    }

    

	

	

}

?>

 <!--main_box -->

    

	

	<script type="text/javascript">



function fnsection(sid)

{

	var sid=sid;

	$.post("getsectionsofclub.php",{sid:sid}, function(response){

	document.getElementById('section').innerHTML=response;

	})

	

}

function fnteam(tid)

{

	var tid=tid;
//alert(tid);
	$.post("getteamnewsofsection.php",{tid:tid}, function(response){

	document.getElementById('team').innerHTML=response;

	})

	

}



 function Form1_Validator(Form143)

{

f=document.Form1;

na=f.cname.value;



nam =/^[a-z A-Z]+$/;

	

if (Form143.username.value == "")

{

 alert("You must enter Category Name.");

 Form143.cname.focus();

 return false;

}





 if(!nam.test(na)){

 

  alert("You must enter valid category Name.");



 Form143.cname.focus();



 return false;



  }

}

	</script>

    

    

    

 	<div id="main_box">

	  	<?php include "includes/leftpanel.php";?>

            <div class="error-message"><?php echo $error_message; ?></div>

        <div class="right">

        <h1 class="title_1"><?php if(isset($_REQUEST['bid'])){ echo 'Update';} else {echo 'New';}?> My Team News Post<?php if(isset($_REQUEST['bid'])){ echo ' : '.stripslashes($f['postTitle']);}?></h1>

  <form name="Form1" id="myform" action="" method="post"  enctype="multipart/form-data" onsubmit="return Form1_Validator(this)" >

                <table width="200" border="0" class="table_bg">

    <tr>

    <td>Post Title</td>

	<td><textarea rows="2" cols="31" name="postname" id="postname" maxlength="44"><?php if(isset($post['postname'])){echo $post['postname'];} if(isset($_REQUEST['bid'])){ echo stripslashes($f['postTitle']);} ?></textarea>

    </td>

    </tr>

  <!--tr>

    <td>External URL</td>

    <td><input type="text" name="posturl" <?php if(isset($_REQUEST['bid'])){?>  <?php } ?> id="posturl" value="<?php //if(isset($post['posturl'])){echo $post['posturl'];} //if(isset($_REQUEST['bid'])){ echo $f['postUrl'];}?>"></td>

  </tr-->

  <?php if($f['postImage']!="postimages/" && isset($_REQUEST['bid'])){?> 

  <tr><td></td><td><img src="<?php echo $f['postImage'];?>" width="100" height="100" ></td></tr>

  <? } ?>

  <?php if($f['postImage']=="postimages/" && $f['team']!=""){

	$teamimg="select teamimage from team_m_t where teamId=".$f['team'];

	$teamimgre=mysql_query($teamimg);

	$teamimgres=mysql_fetch_assoc($teamimgre);  

  ?> 

  <tr><td></td><td><img src="<?php echo $teamimgres['teamimage'];?>" width="100" height="100" ></td></tr>

  <? } ?>

  <tr>

  <td>Image</td>

  <td><input type="file" name="f" /></td>

  </tr>

  <tr><td></td>

  <td style="color:#ff0000">Please Upload the Image size: 600 X 450 </td>

  </tr>

  <tr>

    <td> Description</td>

    <td><textarea rows="6" cols="31" name="postdesc" id="postdesc" ><?php if(isset($post['postdesc'])){echo $post['postdesc'];} if(isset($_REQUEST['bid'])){ echo stripslashes($f['postDesc']);} ?></textarea>

    </td>

  </tr>

  <tr>

    <td>Date</td>

    <td><input type="text" name="postofdate" <?php if(isset($_REQUEST['bid'])){?> <?php } ?> id="postofdate" value="<?php if(isset($post['postofdate'])){echo $post['postofdate'];} if(isset($_REQUEST['bid'])){ echo date('d-m-Y h:i:s',strtotime($f['postDate']));}?>"></td>

  </tr>

 

 <?php if($_SESSION['userType']=='Superadmin'){?>
      <tr>
     <td>Sport</td> 
     <td>
         <select name="sport_id" id="admin-sport">
             <option value="">---- Select Sport -----</option>
             <?php foreach($sportsitems as $sportsitem):?>
                <option <?php if($_POST['sport_id'] == $sportsitem['id']){ echo 'selected="selected"'; } if( $f['sport_id'] == $sportsitem['id'] ){ echo 'selected="selected"'; }?> value="<?=$sportsitem['id'];?>"><?=$sportsitem['name'];?></option>                
             <?php endforeach; ?>
         </select>
     </td>
    </tr>
  <?php } ?>

   <?php if($_SESSION['userType']=='Superadmin'){?>

    <tr>

     <td class="pad_top">Club</td>
      <td class="pad_top">
         <select id="club" name="cat">
              <?php                 
                $options = '<option value="0">Select Club</option>'; 
                if(isset($_REQUEST['bid']) || isset($_POST['sport_id'])){
                   foreach( $clubs as $club){
                     if($club['clubId'] == $f['club_id'] || $club['clubId'] == $_POST['club_id']){ $selected = 'selected = "selected"'; }else{ $selected = '';}                
                     $options .= '<option value="'.$club['clubId'].'" '.$selected.'>'.$club['clubName'].'</option>';  
                   }  
                }
                echo $options;
              ?>    
          </select>  
      </td>
    </tr>

   <? } ?> 

	<?php //echo 'session='.$_SESSION['userType']; 
if($f['club_id']==""){?>

    <tr id="section-row">

      <td>Section</td><td>
	  <!--<select><option value="">Select Section</option></select>-->
	  
	  <select name="section" id="csection">

<?php if($_SESSION['userType']=='Superadmin' || $_SESSION['userType']=='Clubmember'){?>
         	<option value="">Select Section</option>
			<? } ?>
<?php if($_SESSION['userType']=='Clubmember'){?>
            <?php 
			//if($_SESSION['userType']=='Clubmember'){ 
			$sectionsql=mysql_query("select * from section_m_t where club_id='".$sec_clubid."' and isActive='Active' order by sectionId desc");
			/*} else if($_SESSION['userType']=='Sectionmember'){ 
			$sectionsql=mysql_query("select * from section_m_t where sectionId='6' and isActive='Active' order by sectionId desc");
			}*/
            $countsection=mysql_num_rows($sectionsql);

			if($countsection!=""){

			while($resection=mysql_fetch_array($sectionsql)){?>

            

            <option value="<?php echo $resection['sectionId']?>" <?php  if(isset($post['section']) && $post['section'] == $resection['sectionId']){echo 'selected="selected"';} if(isset($_REQUEST['bid']) && $f['section_id'] == $resection['sectionId']){ echo 'selected="selected"';}?>><?php echo $resection['sectionname'];?></option>

      <?	} 

		}  ?>      

           </select> 
	  <? }  ?>
	  <!--login with section memeber -->
	  <?php if($_SESSION['userType']=='Sectionmember' || $_SESSION['userType']=='Teammember') {
		//echo "select * from section_m_t where sectionId='".$sec_sectionid."' and isActive='Active' order by sectionId desc";?>
            <?php $sectionsql=mysql_query("select * from section_m_t where sectionId='".$sec_sectionid."' and isActive='Active' order by sectionId desc");

            $countsection=mysql_num_rows($sectionsql);

			if($countsection!=""){

			while($resection=mysql_fetch_array($sectionsql)){?>

            

            <option value="<?php echo $resection['sectionId']?>" <?php  if(isset($post['section']) && $post['section'] == $resection['sectionId']){echo 'selected="selected"';} if(isset($_REQUEST['bid']) && $f['section_id'] == $resection['sectionId']){ echo 'selected="selected"';}?>><?php echo $resection['sectionname'];?></option>

      <?	} 

		}  ?>      

           </select> 
	  <? }  ?>
	  
	  
	  
	  
	  
	  
	  
</td></tr>

 <? } ?> 

 <?php if($f['club_id']!=""){?>  

     <tr>

       <td id="section-new" style="display:none;">Section</td><td>

     <div id="sectiondiv">

     <tr id="section-old" class="section-old">

     <td class="pad_top" >Section</td>

     <td class="pad_top">        

         <select name="section" id="csection">
		<?php if($_SESSION['userType']=='Superadmin'){?>
         	<option value="">Select Section</option>
		<? } ?>
            <?php 
			if($_SESSION['userType']=='Superadmin' || $_SESSION['userType']=='Clubmember'){
			$sectionsql=mysql_query("select * from section_m_t where club_id='".$f['club_id']."' and isActive='Active' order by sectionId desc");
			}else{
			$sectionsql=mysql_query("select * from section_m_t where sectionId='".$sec_sectionid."' and isActive='Active' order by sectionId desc");
			}

            $countsection=mysql_num_rows($sectionsql);

			if($countsection!=""){

			while($resection=mysql_fetch_array($sectionsql)){?>

            

            <option value="<?php echo $resection['sectionId']?>" <?php  if(isset($post['section']) && $post['section'] == $resection['sectionId']){echo 'selected="selected"';} if(isset($_REQUEST['bid']) && $f['section_id'] == $resection['sectionId']){ echo 'selected="selected"';}?>><?php echo $resection['sectionname'];?></option>

      <?	} 

		}  ?>      

           </select>  

         </td>

         

    </tr>

  </div></td></tr>

  <? } ?> 

	<?php if($f['club_id']==""){ //echo "testing";?>

    <tr id="team-row">

      <td>Team</td><td>
	  <select name="team[]" id="team" multiple="multiple">
	  <?php if($_SESSION['userType']=='Superadmin'){?>
	  <option value="">Select Team</option>
	  <? } ?>
	     <!--login with section memeber or Team Member -->
	  <?php if($_SESSION['userType']=='Sectionmember' || $_SESSION['userType']=='Teammember') {
		//echo "select * from team_m_t where section_id='".$sec_sectionid."' and isActive='Active' order by sectionId desc";?>
            <?php 
			
			if($_SESSION['userType']=='Sectionmember'){
				$teamsql=mysql_query("select * from team_m_t where section_id='".$sec_sectionid."' and isActive='Active' order by teamId desc");
			}else{
				$teamsql=mysql_query("select * from team_m_t where teamId='".$sec_teamid."' and isActive='Active' order by teamId desc");
			}
            $countteam=mysql_num_rows($teamsql);
			$team=explode(',',$f['team']);
			if($countteam!=""){

			while($reteam=mysql_fetch_array($teamsql)){?>

            

            <option value="<?php echo $reteam['teamId']?>" <?php  if(isset($post['team']) && $post['team'] == $reteam['teamId']){echo 'selected="selected"';} foreach($team as $tea){if(isset($_REQUEST['bid']) && $tea == $reteam['teamId']){ echo 'selected="selected"';}}?>><?php echo $reteam['teamname'];?></option>

      <?	} 

		}  ?>      

           </select> 
	  <? }  ?>
	  
	 
	  
	 </td></tr>

 <? } ?> 

 <?php if($f['club_id']!=""){?>  

     <tr>

       <td id="team-new" style="display:none;">Team</td><td>     

     <tr id="team-old" class="team-old">

     <td class="pad_top" >Team</td>

     <td class="pad_top">        


         <select name="team[]" id="team" multiple="multiple">
			<?php if($_SESSION['userType']=='Superadmin' || $_SESSION['userType']=='Clubmember'){?>	
         	<option value="">Select Team</option>
			<? } ?>
            <?php 
			if($_SESSION['userType']=='Superadmin' || $_SESSION['userType']=='Clubmember'){
			$teamsql=mysql_query("select * from team_m_t where club_id='".$f['club_id']."' and isActive='Active' and section_id='".$f['section_id']."' order by teamId desc");
			}else if($_SESSION['userType']=='Sectionmember'){
			$teamsql=mysql_query("select * from team_m_t where section_id='".$sec_sectionid."' and isActive='Active' and section_id='".$f['section_id']."' order by teamId desc");
			}else{
			$teamsql=mysql_query("select * from team_m_t where teamId='".$sec_teamid."' and isActive='Active' order by teamId desc");	
			}
            $countteam=mysql_num_rows($teamsql);
			$team=explode(',',$f['team']);
			if($countteam!=""){

			while($ret=mysql_fetch_array($teamsql)){
				
			?>
		
            

            <option value="<?php echo $ret['teamId']?>" <?php  if(isset($post['team']) && $post['team'] == $ret['teamId']){echo 'selected="selected"';}foreach($team as $tea){ if(isset($_REQUEST['bid']) && $tea == $ret['teamId']){ echo 'selected="selected"';}}?>><?php echo $ret['teamname'];?></option>
			
      <?	} 

		}  ?>      

           </select>  

         </td>

         

    </tr>

  </td></tr>

  <? } ?>    

  <tr>

   <? //} ?>
      
      
   <tr>

       <td>FB Post Schedule DateTime</td>

       <td><input type="text" name="fbteamscheduletime"  readonly="readonly" style="width:70%"  <?php if(isset($_REQUEST['bid'])){?>  <?php } ?> value="<?php if(isset($post['fbteamscheduletime'])){echo $post['fbteamscheduletime'];} if(isset($f['fbteamscheduletime'])){ echo date('d-m-Y h:i:s', strtotime($f['fbteamscheduletime'])) ;}?>"></td>

   </tr>
    
   <tr>

    <td>FB Post Link</td>

    <td><input type="text" name="fbpostlink"  <?php  if(isset($f['fbpostlink'])) { echo 'readonly="readonly"'; } ?> style="width:70%" <?php if(isset($_REQUEST['bid'])){?>  <?php } ?> id="fbPostId" value="<?php if(isset($post['fbpostlink'])){echo $post['fbpostlink'];} if(isset($f['fbpostlink'])){ echo $f['fbpostlink'];}?>"></td>

  </tr>

  <tr>

    <td>Autocaptured FB URL</td>

    <td><input type="text" name="fbPostId" readonly="readonly" style="width:70%" <?php if(isset($_REQUEST['bid'])){?>  <?php } ?> id="fbPostId" value="<?php if(isset($post['fbPostId'])){echo $post['fbPostId'];} if(isset($f['fbPostId'])){ echo $f['fbPostId'];}?>"></td>

  </tr>

  <tr>

    <td>Status</td>

    <td><select name="status" id="status" 

	<?php //if( $_SESSION['userType'] == 'User' || $_SESSION['userType'] == 'Teammember' && $_REQUEST['action']=='edit'){?><!--disabled--> <?php //}?>

     <option value="1" <?php if(isset($post['status']) && $post['status'] == 'Active'){echo "selected='selected'";} if(isset($_REQUEST['bid'])){  if($f['isActive']== 'Active') {echo "selected='selected'";} }?>>Active</option>

    <option value="0" <?php if(isset($post['status']) && $post['status'] == 'Inactive'){echo "selected='selected'";} if(isset($_REQUEST['bid'])){  if($f['isActive']== 'Inactive') {echo "selected='selected'";} }?>>Inactive</option>

    </select></td>

  </tr>

 
<tr>

    <td align="left">Send Push Notification</td>

	<td align="left"><input type="checkbox" name="notify" ></td>

    </tr>  
   <tr>

    <td></td>

   <td ><input type="image" src=" <?php if(isset($_REQUEST['bid'])){ echo 'images/btn_update.png';}else { echo 'images/btn_submit.png';}?>" name="<?php if(isset($_REQUEST['bid'])){ echo 'update';}else { echo 'submit';}?>" id="submit"/></td>

  </tr>

 <tr><td><input type="hidden" value="<?php echo $fbpages['fbteamnewspage']; ?>" name="ids[]"/></td></tr>
 <tr><td><input type="hidden" value="<?php echo $fbpages['code']; ?>" name="access_token"/></td></tr>
</table>



                </form>

       			

        

        </div>									

    </div>

 	

    <!--/main_box -->

<?php include "includes/footer.php";?>

<script type="text/javascript" src="js/datepickr.min.js"></script>

<script type="text/javascript">			

new datepickr('postofdate', {

				'dateFormat': 'Y-m-d'

			});	

function checklength(obj){

	

	var str = obj.value;

	var nstr = str.substring(0,500);

	if( parseInt(str.length) > 499 ){

		alert("You have reached maximum limit");

		obj.value = nstr;

		obj.focus();

		return false;

	}

}

</script>

