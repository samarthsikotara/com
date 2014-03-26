<?php
//echo "index2";
function str_ends_with($haystack, $needle) {
	return ( substr ($haystack, -strlen ($needle) ) === $needle) || $needle === '';
}
/* If the URL is too verbose (specifying index.php or page 1), then, of course
 * we just want the main page, which defaults to page 1 anyway. */
$url = parse_url ($_SERVER['REQUEST_URI']);
if (strpos($_SERVER['REQUEST_URI'],'index.php') !== false || ( isset ($_GET['page']) && $_GET['page'] == 1)){
	header("HTTP/1.1 301 Moved Permanently");
	$_SERVER['QUERY_STRING'] = str_replace('page=1','',$_SERVER['QUERY_STRING']);
	header ("Location: ./".($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
	exit;
}
elseif (str_ends_with($url['path'], '/page/1') || str_ends_with($url['path'], '/page/1/'))
{
	header("HTTP/1.1 301 Moved Permanently");
	header ("Location: ../".($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
	exit;
}

include_once('Smarty.class.php');
$main_smarty = new Smarty;
include_once('config.php');
include_once(mnminclude.'html1.php');
include_once(mnminclude.'link.php');
include_once(mnminclude.'interest.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'scribble.php');
include_once(mnminclude.'tags.php');
include_once(mnminclude.'search.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'searchscribble.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'news.php');

/*
if(!$current_user->authenticated && (!isset($_GET['sk']) || $_GET['sk']!='home')){
   header('Location: landing.php');
}
*/

include_once('global_variable.php');
$mobOpt=true;
include_once(mnminclude.'smartyvariables.php');
// module system hook

$main_smarty->assign('posttitle', 'Home');
$main_smarty->assign('og_posttitle', 'Home');
$main_smarty->assign('postImage', 'http://shaukk.com/shaukk_small_logo.png');
$main_smarty->assign('postUrl', 'http://shaukk.com/?sk=home');

$vars = '';
check_actions('index_top', $vars);
// find the name of the current category
if(isset($_REQUEST['category'])){
	$thecat = get_cached_category_data('category_safe_name', sanitize($_REQUEST['category'], 1));
	$main_smarty->assign('request_category_name', $thecat->category_name);
	$catID = $thecat->category_id;
	$thecat = $thecat->category_name;
	if (!$thecat)
	{
		header("Location: $my_pligg_base/404error.php");
//		$main_smarty->assign('tpl_center', '404error');
//		$main_smarty->display($the_template . '/pligg.tpl');
		die();
	}
}

// start a new search
$search=new Search();
$news = new News();
// check for some get/post
$sc_search=new Search_scribble();
?>
<script type="text/javascript">
    var pre_interest=new Array();
    var pre_interest_count=0;
    var pre_location_count=0;
    var pre_location=new Array();
    var news_interest_count=0;
    var news_interest=new Array();
</script>
<?php
if(isset($_REQUEST['interest'])){
    $search->interest=$_REQUEST['interest'];
    $interestFilter=true;
}
if(is_array($search->interest) && !count($search->interest)==0 && $interestFilter){
    foreach($search->interest as $interest){
        if(interestExists($interest)){
            ?>
            <script type="text/javascript">
                pre_interest[pre_interest_count]=new Array();
                pre_interest[pre_interest_count][0]=<?php echo $interest; ?>;
                pre_interest[pre_interest_count][1]="<?php echo getInterestName($interest) ?>";
                pre_interest_count++;
            </script>
        <?php
        }
    }
    $main_smarty->assign('interestFilter', $selectInterest) ;
}
if($current_user->authenticated && ($_REQUEST['data']!="plans" || in_array(0, $_REQUEST['interest']))){
    $i=0;
    foreach(interestedIn($current_user->user_id) as $interested){
        $search->interest[$i]=$interested->interest_id;
        $i++;
    }
    ?>
    <script type="text/javascript">
        pre_interest[pre_interest_count]=new Array();
        pre_interest[pre_interest_count][0]=0;
        pre_interest[pre_interest_count][1]="Your Interests";
        pre_interest_count++;
    </script>
    <?php
}
if(isset($_REQUEST['newsinterest'])){
    //$search->interest=$_REQUEST['interest'];
    $news->interest=$_REQUEST['newsinterest'];
    $newsinterestFilter=true;
}

if(is_array($news->interest) && !count($news->interest)==0 && $newsinterestFilter){
 foreach($news->interest as $interest){
        if(interestExists($interest)){
        ?>
        <script type="text/javascript">
            news_interest[news_interest_count]=new Array();
            news_interest[news_interest_count][0]=<?php echo $interest; ?>;
            news_interest[news_interest_count][1]="<?php echo getInterestName($interest) ?>";
            news_interest_count++;
            </script>
            <?php
            }
        }
        $main_smarty->assign('newsinterestFilter', $selectInterest) ;
}
if(isset($_REQUEST['location']) && is_array($_REQUEST['location'])){
    //print_r($_REQUEST['location']);
    $search->location=$_REQUEST['location'];
    foreach($search->location as $location){
        ?>
            <script type="text/javascript">
                pre_location[pre_location_count]=new Array();
                pre_location[pre_location_count][0]=new Array();
                pre_location[pre_location_count][1]=new Array();
                pre_location[pre_location_count][0][0]=<?php echo $location[0][0]; ?>;
                pre_location[pre_location_count][0][1]=<?php echo $location[0][1];?>;
                pre_location[pre_location_count][1][0]=<?php echo $location[1][0]; ?>;
                pre_location[pre_location_count][1][1]=<?php echo $location[1][1];  ?>;
                pre_location[pre_location_count][2]="<?php echo $location[2];  ?>";
                pre_location[pre_location_count][3]="<?php echo $location[3];  ?>";
                pre_location_count++;
            </script>
        <?php

    }
}elseif(isset($_REQUEST['landing_location']) && is_numeric($_REQUEST['landing_location'])){
    $loc_details=getMumbaiLocationDetBtId($_REQUEST['landing_location']);
    if(is_array($loc_details)){
        $loc_search[0][0][0]=$loc_details['lat1'];
        $loc_search[0][1][0]=$loc_details['lat2'];
        $loc_search[0][0][1]=$loc_details['lon1'];
        $loc_search[0][1][1]=$loc_details['lon2'];
        $loc_search[0][2]=$loc_details['location_id'];
        $loc_search[0][3]=$loc_details['location_name'];
        ?>
        <script type="text/javascript">
            pre_location[pre_location_count]=new Array();
            pre_location[pre_location_count][0]=new Array();
            pre_location[pre_location_count][1]=new Array();
            pre_location[pre_location_count][0][0]=<?php echo $loc_search[0][0][0]; ?>;
            pre_location[pre_location_count][0][1]=<?php echo $loc_search[0][0][1];?>;
            pre_location[pre_location_count][1][0]=<?php echo $loc_search[0][1][0]; ?>;
            pre_location[pre_location_count][1][1]=<?php echo $loc_search[0][1][1];  ?>;
            pre_location[pre_location_count][2]="<?php echo $loc_search[0][2];  ?>";
            pre_location[pre_location_count][3]="<?php echo $loc_search[0][3];  ?>";
            pre_location_count++;
        </script>
        <?php
        $search->location=$loc_search;

    }
}

if(isset($_REQUEST['interest_sc'])){
    $sc_search->interest=explode(",", sanitize($_REQUEST['interest_sc'], 3)); $interestFilter=true;
}
/*
else if($current_user->authenticated){
    $i=0;
    foreach(interestedIn($current_user->user_id) as $interested){
        $sc_search->interest[$i]=$interested->interest_id;
        $i++;
    }
}

 */

if(isset($_REQUEST['date'])){$search->date=sanitize($_REQUEST['date'], 3);}
if(isset($_REQUEST['time']) && is_array($_REQUEST['time'])){$search->time=$_REQUEST['time'];}
if(isset($_REQUEST['sc_date'])){$sc_search->date=sanitize($_REQUEST['sc_date'], 3);}
if(isset($_REQUEST['dateSort']) && $_REQUEST['dateSort']=='true'){$search->dateSort=true;$sc_search->dateSort=true;}
if(isset($_REQUEST['myPlans']) && $_REQUEST['myPlans']=="true"){
    if(!$current_user->authenticated) header('Location: login.php?return=index.php%3FmyPlans=true');
    $search->user= $current_user->user_id;
}
if(isset($_REQUEST['from'])){$search->newerthan = sanitize($_REQUEST['from'], 3);}
unset($_REQUEST['search']);
unset($_POST['search']);
unset($_GET['search']);
if(isset($_REQUEST['search'])){$search->searchTerm = sanitize($_REQUEST['search'], 3);}
if(isset($_REQUEST['search'])){$search->filterToStatus = "all";}
if(!isset($_REQUEST['search'])){$search->orderBy = "link_weight DESC, link_published_date DESC";}
if(isset($_REQUEST['tag'])){$search->searchTerm = sanitize($_REQUEST['search'], 3); $search->isTag = true;}
if(isset($thecat)){$search->category = $catID;}
if(isset($_REQUEST['tutorial']) && $_REQUEST['tutorial']=="yes"){
    $main_smarty->assign('tutorial', 'yes');
    if(isset($_REQUEST['tut_sc']) && $_REQUEST['tut_sc']=="yes"){
         $main_smarty->assign('tut', 'scribble');
    }else $main_smarty->assign('tut', 'plans');
}
if(isset($_REQUEST['tab']) &&( $_REQUEST['tab']=="news" || $_REQUEST['tab']=="scribble" || $_REQUEST['tab']=="bawraas")){
    $tab=$_REQUEST['tab'];
}else{
    $tab="";
}
$main_smarty->assign('tab', $tab);
// figure out what "page" of the results we're on
$search->offset = (get_current_page()-1)*$page_size;
$pl_offset = (get_current_page()-1)*$page_size;
// pagesize set in the admin panel
$search->pagesize =$page_size;
$news->offset = (get_current_page()-1)*$news->count;
$nw_offset = $news->offset;

// since this is index, we only want to view "published" stories
$search->filterToStatus = "published";
$sc_search->offset=(get_current_page()-1)*$page_size;
$sc_search->pagesize=$page_size;
$sc_search->orderBy="scribble_date DESC";

// this is for the tabs on the top that filter
if(isset($_GET['part'])){$search->setmek = $db->escape($_GET['part']);}
$search->do_setmek();

// do the search
$search->doSearch();
$sc_search->do_search();
$scribble_sql=$sc_search->sql;
$linksum_count = $search->countsql;
$linksum_sql = $search->sql;





if(isset($_REQUEST['category'])) {
	$category_data = get_cached_category_data('category_safe_name', sanitize($_REQUEST['category'], 1));
	$main_smarty->assign('meta_description', $category_data->category_desc);
	$main_smarty->assign('meta_keywords', $category_data->category_keywords);

	// breadcrumbs and page title for the category we're looking at
	$main_smarty->assign('title', ''.$main_smarty->get_config_vars('PLIGG_Visual_Published_News').' - ' . $thecat . '');
	$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Published_News');
	$navwhere['link1'] = getmyurl('root', '');
	$navwhere['text2'] = $thecat;
	$main_smarty->assign('navbar_where', $navwhere);


	$main_smarty->assign('page_header', $thecat . $main_smarty->get_config_vars('PLIGG_Visual_Published_News'));
	// pagename	
	define('pagename', 'published'); 
	$main_smarty->assign('pagename', pagename);
} else {
	// breadcrumbs and page title
	$navwhere['show'] = 'yes';
	$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Published_News');
	$navwhere['link1'] = getmyurl('root', '');
	$main_smarty->assign('navbar_where', $navwhere);
	$main_smarty->assign('posttitle', 'Home');
	$main_smarty->assign('page_header', $main_smarty->get_config_vars('PLIGG_Visual_Published_News'));
	// pagename	
	define('pagename', 'index'); 
	$main_smarty->assign('pagename', pagename);
}

//  make sure my_base_url is set
//if($my_base_url == ''){echo '<div style="text-align:center;"><span class=error>ERROR: my_base_url is not set. Please correct this using the <a href = "/admin/admin_config.php?page=Location%20Installed">admin panel</a>. Then refresh this page.</span></div>';}

// sidebar
$main_smarty = do_sidebar($main_smarty);
$sql = "SELECT user_login FROM " . table_users . " ORDER BY user_id DESC LIMIT 1";
$last_user = $db->get_var($sql);
$main_smarty->assign('last_user', $last_user);

// misc smarty
if(isset($from_text)){$main_smarty->assign('from_text', $from_text);}
if(isset($search->setmek)){$main_smarty->assign('setmeka', $search->setmek);}else{$main_smarty->assign('setmeka', '');}

$main_smarty->assign('URL_rss_page', getmyurl('rsspage', $category_data->category_safe_name, ''));

$fetch_scribble_summary=true;
$fetch_link_summary = true;
$fetch_news_summary=true;
include('./libs/link_summary.php'); // this is the code that show the links / stories
include('./libs/scribble_summary.php'); // this is the code that show the links / stories
include('./libs/news_summary.php'); // this is the code that show the links / stories
$pagination=getPaginationLinks('index');
if(isset($pagination['next'])){$main_smarty->assign('pagination_next', $pagination['next']);}
if(isset($pagination['previous'])){$main_smarty->assign('pagination_prev', $pagination['previous']);}
$locDetails=array();
if($current_user->authenticated){
    $loc = getLocationListByUser($current_user->user_id);
    $i=0;
    if(!count($loc)==0){
        foreach($loc as $location){
                $locDetails[$i]=getLocationDetails($location->location_id);
                $i++;
            }
    }
}
$locMum = getMumbaiLocationDet();


$main_smarty->assign('mylocations', $locDetails);
$main_smarty->assign('locations', $locMum);
$main_smarty->assign('mylocationsCount', count($locDetails));
if($search->date!=0){
$main_smarty->assign('dateFilter', '(+/- 3 days) '.date("d/m/Y", strtotime($search->date)));
}
if($search->user==$current_user->user_id && $current_user->authenticated){
    $main_smarty->assign('myPlans', 'true');
}


$i=0;
/*
if($current_user->authenticated)    $interests=interestedIn($current_user->user_id);
    else $interests=getInterestListByScribble(20);
  */
$interests=getInterestListByScribble(20);
    if(count($interests)!=0){
      foreach($interests as $item){
          $interest_list[$i]['id']=$item->interest_id;
          $interest_list[$i]['image']=getInterestImage($item->interest_id, '35');
          $interest_list[$i]['name']=$item->interest_name;
          $interest_list[$i]['interested']=true;
          //echo $check[$item->interest_id]."<br>";
          $i++;
      }
    }
$myGroup=myallgroup();
$myGroupCount=count($myGroup);
if(count($myGroup)>0){
    $i=0;
    foreach($myGroup as $result){
        $myGroup[$i]['image']=getGroupImage($result['group_id'], '100');
        $myGroup[$i]['image_small']=getGroupImage($result['group_id'], '50');
        $myGroup[$i]['image_large']=getGroupImage($result['group_id'], '250');
        $myGroup[$i]['url']=getmyurl('groups_url',$result['group_field6'] );
        $i++;
    }
}
$groupsSug=groupforuser(0,10);
//print_r($groupsSug);
$groupSugCount=count($groupsSug);
if(count($groupsSug)>0){
    $i=0;
    foreach($groupsSug as $result){
        $groupsSug[$i]['image']=getGroupImage($result['group_id'], '100');
        $groupsSug[$i]['image_small']=getGroupImage($result['group_id'], '50');
        $groupsSug[$i]['image_large']=getGroupImage($result['group_id'], '250');
        $groupsSug[$i]['url']=getmyurl('groups_url',$result['group_field6'] );
        $i++;
    }
}

/******* Added By Samarth**/

$sql="SELECT * FROM `shlinks` WHERE (link_field6 BETWEEN '2013-12-25 00:00:00' AND '2014-01-01 00:00:00') AND (link_field14 BETWEEN '2013-12-25 00:00:00' AND '2014-01-01 00:00:00') ORDER BY link_id ASC";

//$result=$db->get_results($sql, ARRAY_A);
$results=$db->get_results($sql, ARRAY_A);
$i=0;
$plans=array();
foreach($results as $plan){
	$plans[$i]['link_title']=$plan['link_title'];
	$plans[$i]['url']=getmyurl('events', $plan['link_title_url']);
	$i++;

}

$main_smarty->assign('result', $plans);
$link_summary_output='';
$link = new Link;
foreach($results as $plan){
    $link->id=$plan['link_id'];
    if($link->read()){
        $link_summary_output .= $link->print_summary('summary', true);
    }
}

$main_smarty->assign('link_summary_output', $link_summary_output);


/******/

$main_smarty->assign('mygroup',$myGroup);
$main_smarty->assign('mygroupcount',$myGroupCount);

$main_smarty->assign('allgroup',$groupsSug);

$main_smarty->assign('interest_list', $interest_list);
// show the template
    if(isset($_REQUEST['data']) && $_REQUEST['data']=='scribble'){
        //echo "saurav";
        echo $scribble_summary_output;
    }
    else if(isset($_REQUEST['data']) && $_REQUEST['data']=='plans'){
        echo $link_summary_output;
    }
    else if(isset($_REQUEST['data']) && $_REQUEST['data']=='news'){
        echo $news_summary_output;
    }
    else if(isset($_REQUEST['data']) && $_REQUEST['data']=='sp'){
        $array[0]=$link_summary_output;
        $array[1]=$scribble_summary_output;
        $array[2]=$news_summary_output;
        echo json_encode($array);
    }
    else{
        $main_smarty->assign('tpl_center', $the_template . '/index_new_year_center');
        $main_smarty->display($the_template . '/pligg.tpl');
    }
?>
<!--
<script type="text/javascript" >
    $(function(){
			$('select.slider').selectToUISlider({
				labels: 7
			});
			//fix color
			fixToolTipColor();
		});
</script>
-->
