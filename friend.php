<?php
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

if(!defined('mnminclude')){header('Location: ../404error.php');die();}

class Friend {
	var $friend = "";

	function remove($friend)
	{
		global $db,$current_user;
		if (!is_numeric($friend)) die();

		$sql = "Delete from " . table_friends . " where friend_from = " . $current_user->user_id . " and friend_to = $friend;";
		//echo $sql;
		$db->query($sql);

		$friend_status = $this->get_friend_status($friend);
		if ($friend_status){die("there was an error");}
		
	}
	
	function add($friend)
	{
		global $db, $current_user;
		if (!is_numeric($friend)) die();
		
		if ($current_user->user_id == 0) {
        echo "<span class='success' style='border:solid1px#269900;padding:2px2px2px2px'>Please <a href=" .my_base_url.my_pligg_base. "/login.php?return=/user.php?login=god&amp;view=addfriend>login</a></span><br/>";
        return;
        }
		
		$friend_status = $this->get_friend_status($friend);
		if (!$friend_status){
			//echo "INSERT INTO " . table_friends . " (friend_from, friend_to) values ($current_user->user_id, $friend);";
			$db->query("INSERT IGNORE INTO " . table_friends . " (friend_from, friend_to) values (" . $current_user->user_id . ", " . $friend . ");");

			$friend_status = $this->get_friend_status($friend);
            if ($friend_status){return true;}
			if (!$friend_status){die("there was an error");}

		}
	}
	
	function get_friend_list($user_id, $limit=20)
	{	
		// returns an array of people you've added as a friend
		global $db, $current_user;
		$friends = $db->get_results("SELECT " . table_users . ".user_login," . table_users.".user_names, " . table_users . ".user_avatar_source, " . table_users . ".user_email, " . table_users . ".user_id FROM " . table_friends . " INNER JOIN " . table_users . " ON " . table_friends . ".friend_to = " . table_users . ".user_id WHERE (((" . table_friends . ".friend_from)= " . $user_id . ")) LIMIT ".$limit ,ARRAY_A);
		return $friends;

	}
    function get_friends($user_id, $limit=20, $query=""){
        global $db, $current_user;
        if($query!=""){
            $optional= "AND ".table_users.".user_names LIKE '%".$query."%'";
        }
        $sql="SELECT " . table_users . ".user_login," . table_users.".user_names, " . table_users . ".user_avatar_source, " . table_users . ".user_email, " . table_users . ".user_id FROM " . table_friends . " INNER JOIN " . table_users . " ON " . table_friends . ".friend_to = " . table_users . ".user_id WHERE " . table_friends . ".friend_from= " . $user_id." ".$optional." AND " . table_friends . ".friend_to IN (SELECT " . table_friends . ".friend_from FROM " . table_friends . " WHERE " . table_friends . ".friend_to=".$user_id.")";
        
		
        return $db->get_results($sql ,ARRAY_A);
    }

	function get_friend_list_2($user_id, $limit=20)
	{
		// returns an array of people who have added you as a friend
		global $db, $current_user;
		$friends = $db->get_results("SELECT " . table_users . ".user_login, " . table_users . ".user_avatar_source, " . table_users . ".user_email, " . table_users . ".user_id FROM " . table_friends . " INNER JOIN " . table_users . " ON " . table_friends . ".friend_from = " . table_users . ".user_id WHERE ((" . table_friends . ".friend_to)= " . $user_id . ") LIMIT ".$limit ,ARRAY_A);
		return $friends;
	}

	function get_friend_status($friend)
	{
		global $db, $current_user;
		if (!is_numeric($friend)) die();

		$sql = "SELECT " . table_users . ".user_id FROM " . table_friends . " INNER JOIN " . table_users . " ON " . table_friends . ".friend_to = " . table_users . ".user_id WHERE " . table_friends . ".friend_from=" . $current_user->user_id . " and " . table_friends . ".friend_to=".$friend.";";
		
		$friends = $db->get_var($sql);
		
		return $friends;
		
	}
	function get_friend_list_userid($user_id, $limit=20)
	{	
		// returns an array of people you've added as a friend
		global $db, $current_user;
		$friends = $db->get_col("SELECT ". table_users . ".user_id FROM " . table_friends . " INNER JOIN " . table_users . " ON " . table_friends . ".friend_to = " . table_users . ".user_id WHERE (((" . table_friends . ".friend_from)= " . $user_id . ")) LIMIT ".$limit);
		
		return implode(",",$friends);

	}
	function friends_suggesions()
	{
		//getting current user interest 
		global $db, $current_user;
		$frindlist =$this->get_friend_list_userid($current_user->user_id,1000);
		$sqlint="select intrest from ".table_fkeyword." where userid=".$current_user->user_id."";
		
		$intrest_list=$db->get_var($sqlint);
		$sql_user="select distinct user_id  from ".table_interest_member." where 	interest_id IN(".$intrest_list.")  and  user_id NOT IN  (".$current_user->user_id.",".$frindlist.")";
		$main_q="SELECT user_login ,user_names,user_avatar_source,user_email,user_id FROM ".table_users." WHERE user_id IN (".implode(",",$dlist=$db->get_col($sql_user)).")";
		$suggest_fl=$db->get_results($main_q);
		
		
		return $suggest_fl;
	}
	function isFriendRequestSent($id){
        if(is_numeric($id)){
                global  $db,$current_user;
                  $sql_frnd_req1="select count(*) from ".table_frnd_req." where friend_from=".$current_user->user_id." and status='pending' ";
                  $rest1=$db->get_var($sql_frnd_req1);
                  if($rest1>0) return true;
                  else return false;
        }

    }
	function can_send_frnd_req($id)
	{
        if(is_numeric($id)){
		global  $db,$current_user;

		  $sql_frnd_req2="select count(*) from ".table_frnd_req." where friend_from=".$current_user->user_id." and friend_to=".$id." ";
		  $rest2=$db->get_var($sql_frnd_req2);
		if(($rest1<20) &&  $this->isFriendRequestSent($id)){
		return true;
		}else{
		return false;
		}
        }
        else return true;
		
	}
	function send_req($id,$action)
	{
	// 	request maye be of any type friend/follower/ following
		//var for requested user id used for validation
		if(is_numeric($id))
		{
		switch ($action)
			{
			
			case 'friend_req':
			
					// sending friend request 
					$can_send=$this->can_send_frnd_req($id);
					if($can_send){
						global $db ,$current_user;
						//echo "friend added successfully";
						$sql_insert="INSERT  INTO " . table_frnd_req . " (friend_from , friend_to,status) values (" . $current_user->user_id . ", " . $id . ",'pending')";
						if($db->query($sql_insert)) return true; else return false;
					}else{
					//echo "friend request unsuccessfully";
						return false;
					}
					
					break;
			case 'following_req':
					/// insert query to the follow table 		
						global $db ,$current_user;
						return $this->add($id);
					
			
					break;
			default:
						return false;
			break;
											
			}
			
			
			
			
			
		}
		
	}
	
	
	function delete_friend($id)
	{
		if(($id!="")){
			// delete query to friend 
			$sql = "Delete from " . table_friends . " where friend_from = " . $current_user->user_id . " and friend_to = ".$id."";
			//echo $sql;
			$query_res=$db->query($sql);
			if($query_res){
			return true;
			}else{
			return false;
			}	
		}else{
		return false;
		}
				
	
	}
	function delete_follower($id)
	{
		if(($id!="")){
			// delete query for unset follower
			$sql = "Delete from " . table_friends . " where friend_from = " . $current_user->user_id . " and friend_to = ".$id."";
			$query_res=$db->query($sql);
				if($query_res){
					return true;
					}else {
					return false;
					}

		}
		else
		{
		return false;
		}			
	}
	function delete_following($id)
	{
		if(($id!="")){
			// delete query for unset follower
			$sql = "Delete from " . table_friends . " where friend_from = " . $id . " and friend_to = ".$current_user->user_id."";
			$query_res=$db->query($sql);
					if($query_res){
					return true;
					}else{
					return false;
					}
		}
		else{	
		return false;
		}	
			
	}
	
	
	
}

?>