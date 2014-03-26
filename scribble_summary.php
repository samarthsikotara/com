<?php

if(!defined('mnminclude')){header('Location: ../404error.php');die();}
	global $main_smarty, $new_search, $current_user;
    $scribble = new Scribble;
    if(isset($sc_id)){
        $where = '`scribble_id` IN ('.implode(',',$sc_id).')';
    }
    else $where="1";
    $scribble_sql="SELECT `scribble_id` FROM ".table_scribble." WHERE ";
    $scribble_sql=$scribble_sql.$where;

    $scribbles = $db->get_col($scribble_sql);
    //print_r($links);
    $s_results = $scribbles;


/*
	if($the_results){
		// find out if the logged in user voted / reported each of
		// the stories that the search found and cache the results
		require_once(mnminclude.'votes.php');
//      DB 03/02/09
//		$vote = new Vote;
//		$vote->type='links';
//		$vote->user=$current_user->user_id;
//		$vote->link=$the_results;
//		$results = $vote->user_list_all_votes();
/////
		$vote = '';
		$results = ''; // we don't actually need the results 
				// we're just calling this to cache the results
				// so when we foreach the links we don't have to 
				// run 1 extra query for each story to determine
				// current user votes
  
		// setup the link cache
		$sql = "SELECT " . table_links . ".* FROM " . table_links . " WHERE "; 
		$sql_saved = "SELECT * FROM " . table_saved_links . " WHERE saved_user_id=" . $current_user->user_id . " AND ";
		$ids = array();
		foreach($the_results as $link_id) {
			// first make sure we don't already have it cached
			if(!isset($cached_links[$link_id])){
				$ids[] = $link_id;
			}
			if(!isset($cached_saved_links[$link_id])){
				$saved_ids[] = $link_id;
			}
		}
  		// To do: how this caching is done and if this is working or not


		// if count  = 0 then all the links are already cached
		// so don't touch the db
		// if count  > 0 then there is at least 1 link to get
		// so get the SQL and add results to the cache

		if ( count ( $ids ) ) {
			$sql .= 'link_id IN ('.implode(',',$ids).')';
			foreach ( $db->get_results($sql) as $row ) {
				$cached_links[$row->link_id] = $row;
				if(!isset($link_authors[$row->link_author])){
					$link_authors[$row->link_author] = $row->link_author;
				}
			}
		}

		// get all authors at once from the users table
		$sql = 'SELECT  *  FROM ' . table_users . ' WHERE ';
		if ( count ( $link_authors ) ) {
			$sql .= 'user_id IN (' . implode(',', $link_authors) . ')';

			foreach ( $db->get_results($sql) as $user ) {
				$cached_users[$user->user_id] = $user;
			}
		}

		// user saved _links
		if ( count ( $saved_ids ) ) {
			$sql_saved .= 'saved_link_id IN ('.implode(',',$ids).')';
			$results = $db->get_results($sql_saved);

			if($results){
				foreach($results as $row){
					$sl[$row->saved_link_id] = 1;
				}
			}
			
			foreach($the_results as $link_id) {
				if(isset($sl[$link_id])){
					$cached_saved_links[$link_id] = 1;
				} else {
					$cached_saved_links[$link_id] = 0;
				}
			}
		}
		// end link cache setup
	}
    */

	if(!isset($link_summary_output)){$link_summary_output = '';}
    if ($scribbles) {
			foreach($scribbles as $scribble_id) {
				$scribble->id=$scribble_id;
				$scribble->read();
                $scribble_summary_output .= $scribble->print_summary('summary', true, 'scribble_summary.tpl');


            }
	}

	if(isset($fetch_scribble_summary) && $fetch_scribble_summary == true){
		$main_smarty->assign('scribble_summary_output', $scribble_summary_output);
	} elseif(isset($fetchVariable) && $fetchVariable==true) {
            $sc_output=$scribble_summary_output;
    }
    else{
		echo $scribble_summary_output;
	}
?>
