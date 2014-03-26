<?php
//echo "index2";


include_once('Smarty.class.php');
$main_smarty = new Smarty;
include('config.php');

include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'interest.php');
include(mnminclude.'interest_member.php');
include(mnminclude.'scribble.php');
include(mnminclude.'tags.php');
include(mnminclude.'search.php');
include(mnminclude.'searchscribble.php');
include(mnminclude.'smartyvariables.php');


include('global_variable.php');
// module system hook

die();

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
// check for some get/post
$sc_search=new Search_scribble();
if(isset($_REQUEST['interest'])){
    $search->interest=explode(",", sanitize($_REQUEST['interest'], 3)); $interestFilter=true;
}
else if($current_user->authenticated){
    $i=0;
    foreach(interestedIn($current_user->user_id) as $interested){
        $search->interest[$i]=$interested->interest_id;
        $i++;
    }
}
if(isset($_REQUEST['interest_sc'])){
    $sc_search->interest=explode(",", sanitize($_REQUEST['interest'], 3)); $interestFilter=true;
}
else if($current_user->authenticated){
    $i=0;
    foreach(interestedIn($current_user->user_id) as $interested){
        $sc_search->interest[$i]=$interested->interest_id;
        $i++;
    }
}

if(isset($_REQUEST['date'])){$search->date=sanitize($_REQUEST['date'], 3);}
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
if(!isset($_REQUEST['search'])){$search->orderBy = "link_published_date DESC, link_date DESC";}
if(isset($_REQUEST['tag'])){$search->searchTerm = sanitize($_REQUEST['search'], 3); $search->isTag = true;}
if(isset($thecat)){$search->category = $catID;}

// figure out what "page" of the results we're on
$search->offset = (get_current_page()-1)*$page_size;
// pagesize set in the admin panel
$search->pagesize = $page_size;

// since this is index, we only want to view "published" stories
$search->filterToStatus = "published";
$sc_search->offset=(get_current_page()-1)*$page_size;
$sc_search->pagesize=$page_size;

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
	$main_smarty->assign('pretitle', $thecat );
	$main_smarty->assign('posttitle', $main_smarty->get_config_vars('PLIGG_Visual_Published_News'));
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
	$main_smarty->assign('posttitle', $main_smarty->get_config_vars('PLIGG_Visual_Home_Title'));
	$main_smarty->assign('page_header', $main_smarty->get_config_vars('PLIGG_Visual_Published_News'));
	// pagename	
	define('pagename', 'index'); 
	$main_smarty->assign('pagename', pagename);
}

//  make sure my_base_url is set
if($my_base_url == ''){echo '<div style="text-align:center;"><span class=error>ERROR: my_base_url is not set. Please correct this using the <a href = "/admin/admin_config.php?page=Location%20Installed">admin panel</a>. Then refresh this page.</span></div>';}

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
include('./libs/link_summary.php'); // this is the code that show the links / stories
include('./libs/scribble_summary.php'); // this is the code that show the links / stories
$main_smarty->assign('link_pagination', do_pages($rows, $page_size, "published", true));

if(is_array($search->interest) && !count($search->interest)==0 && $interestFilter){
    foreach($search->interest as $interest){
        if(interestExists($interest)){
            $selectInterest.=getInterestName($interest)." | ";
        }
    }
$main_smarty->assign('interestFilter', $selectInterest) ;
}

if($search->date!=0){
$main_smarty->assign('dateFilter', '(+/- 3 days) '.date("d/m/Y", strtotime($search->date)));
}
if($search->user==$current_user->user_id && $current_user->authenticated){
    $main_smarty->assign('myPlans', 'true');
}


$i=0;
if($current_user->authenticated)    $interests=interestedIn($current_user->user_id);
    else $interests=getInterestListByScribble(50);
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

$main_smarty->assign('interest_list', $interest_list);
// show the template
    if(isset($_REQUEST['data']) && $_REQUEST['data']=='scribble'){
        echo "saurav";
        echo $scribble_summary_output;
    }
    else if(isset($_REQUEST['data']) && $_REQUEST['data']=='plans'){
        echo $link_summary_output;
    }
    else{
        $main_smarty->assign('tpl_center', $the_template . '/index_center');
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

