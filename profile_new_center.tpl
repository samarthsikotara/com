<div class="mob-plan-page-main-wrapper">
	<div class="mob-profile-page-username">
		<div class="mob-profile-page-main-image">
		 <img src="{$Avatar_ImgSmall}" alt="Avatar"/>
		</div>
		<div class="mob-profile-info-name">
		<span class="mob-profile-page-info-name">{$user_names}</span><br/>
		<span class="mob-profile-page-info-place">Mumbai,India</span>
		</div>
	
	{if $isCurrentUser eq "true"}
	<div/>
	{else}
        {if $user_type eq "user"}
            <div class="mob-plan-profile-buttons">
            {if $isFriend eq "accepted"}
                            <div class="mob-plan-buttons-friend universal-green-button friend-button"> <span>Friends</span>
            {elseif $isFriend eq "pending"}
                <div class="mob-plan-buttons-friend universal-red-button add-friend-button"> <span>Request Sent</span>
            {else}
                <div class="mob-plan-buttons-friend universal-blue-button add-friend-button" onClick="addFriend(event, {$user_id})"><span> Add Friend</span>
            {/if}
                    </div>

            </div>
        {else}
                {if $isFollowing eq "following"}
                <div class="mob-plan-buttons-friend universal-green-button follower-button" onClick="unfollowPerson(event, {$user_id})"> <span>Following</span>
                    {else}
                    <div class="mob-plan-buttons-friend universal-blue-button add-follower-button" onClick="followPerson(event, {$user_id})"> <span>Follow</span>
                        {/if}
                    </div>
        {/if}
	{/if}
	</div>
	<div class="mob-profile-buttons display-none">
		<div class="mob-profile-plans"><div class="mob-profile-plans-count"><span>12</span></div><span>Plans</span>
		</div>
		<div class="mob-profile-followers"><div class="mob-profile-followers-count"><span>12</span></div><span>Interests</span>
		</div>
		<div class="mob-profile-con"><div class="mob-profile-con-count"><span>12</span></div><span>Connections</span>
		</div>
	</div>
	<div class="mob-profile-content"><p>{$user_desc}</p>
	</div>
	<div class="mob-profile-tab">
			
			<span>INTERESTS </span>
			
	</div> 
	<div class="mob-profile-content">
		<ul class="mob-box-list">
			{section name=record loop=$interest}
			<li id="mob-interest-list-{$interest[record].id}" class="mob-box-list-images" >
				<div class="mob-interest-list-image-wrapper">
					<img src="{$interest[record].photo}"/>
				</div>
			</li>
			{/section}
		 </ul>
	</div>
	<div class="mob-profile-tab"><span>
            {if $user_type eq "user"}FRIENDS{else}FOLLOWERS{/if}</span>
	</div>	
	<div class="mob-profile-content">
		<ul>
			{section name=record loop=$friend}
					<a href="{$friend[record].url}" title="{$friend[record].name}">
						<li>
							<div class="mob-profile-conn-wrapper">
									<div class="mob-prof-conn-img-warpper">
										<img src="{$friend[record].avatar}">
									</div>
									<div class="mob-prf-conn-first-row">
										 <span>{$friend[record].name}</span>
									</div>
						    </div>
						</li>
					</a>
				{/section}
		</ul>
	</div>

	<div class="mob-profile-location display-none">
		<div class="mob-profile-tab"><span>LOCATION</span>
		</div>
	</div>
</div>