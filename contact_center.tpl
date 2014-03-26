
<div class="virtual-pop-up-contact box-style">
<div class="login-box">

{if $errorMsg ne ""}
<div class="error">
{$errorMsg}
</div>
{/if}


<div class="sub-title">
<span>Contact Us</span>
</div>

<div id="login-content">
<form action="{$URL_contact}"  method="post" name="contactDetails" id="contactDetails" method="post" enctype="multipart/form-data">
<table>
	<tr>
		<td>
			<div class="input-box">
			<label>Name:</label><br/>
			<input type="text"  name="username"  id="username" class="text validate[required]" value="{if isset($login_username)}{$login_username}{/if}" tabindex="10" />
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="input-box">
			<label>Email:</label><br/>
			<input type="text" name="emailaddress" id="emailaddress"  class="text validate[required,custom[email]]"  value="{if isset($login_email)}{$login_email}{/if}" tabindex="10" />
			</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="input-box">
		<label>Type:</label><br/>
		&nbsp;<select id="make-plan-type-select"  class="styled" name="type" >
		<option value="0">--Select--</option>
		<option value="1">Apply for a job</option>
        		<option value="2">Claim a brand</option>
        		<option value="3">Report an error</option>
        		<option value="4">Suggestions</option>
		</select>  <br/>     
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="input-box">
		<input id="reg_terms"  type="checkbox" name="terms">   
		<label>Are you a Shaukk member</label>									 
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="input-area">
		<label>Query:</label><br/>
		<input type="hidden" name="processcontact" id="something" value="1"/>
		
		
		<textarea name="query" id="query"     ></textarea>
		</div>
		</td>

	</tr>
	<tr>
        <td>
            {$captcha_html}
        </td>
        <td>
            <div class="register-button next" onclick="contact_valid()" ></div>
        </td>
	</tr>
</table>
</form>
</div>

{checkActionsTpl location="tpl_login_bottom"}

</div>
</div>

