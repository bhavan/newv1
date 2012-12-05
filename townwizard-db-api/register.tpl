<style type="text/css">
  .registration_form td {
  	padding: 7px;
  }
</style>

<div id="registration_dialog" style="display:none;width:300px;height:600px;position:absolute;top:100px;right:100px;z-index:10000;opacity:1;border:1px solid;background:white">
<div style="padding:10px">Register here!</div>
<div id="registration_error" style="padding:10px;display:none;color:red"></div>
<form id="registration_form" method="post" action="register.php">
	<table class="registration_form">
		<tr><td>Email: *</td><td><input name="email" /></td></tr>
		<tr><td>Password: *</td><td><input name="password" type="password" /></td></tr>
		<tr><td>Username:</td><td><input name="username"/></td></tr>
		<tr><td>First Name:</td><td><input name="firstName"/></td></tr>
		<tr><td>Last Name:</td><td><input name="lastName"/></td></tr>
		<tr><td>Gender:</td><td><input name="gender"/></td></tr>
		<tr><td>Year of birth:</td><td><input name="year"/></td></tr>
		<tr><td>Mobile phone:</td><td><input name="mobilePhone"/></td></tr>
		<tr><td>Address 1:</td><td><input name="address1"/></td></tr>
		<tr><td>Address 2:</td><td><input name="address2"/></td></tr>
		<tr><td>City:</td><td><input name="city"/></td></tr>
		<tr><td>State:</td><td><input name="state"/></td></tr>
		<tr><td>Zip:</td><td><input name="postalCode"/></td></tr>
		<tr>
      <td><input type="button" value="Cancel" onclick="$('#registration_dialog').hide();"/></td>
      <td><input type="button" value="Sign up" onclick="tw_register();"/></td>
    </tr>
   </table>
</form>
</div>

<div id="login_dialog" style="display:none;width:300px;height:150px;position:absolute;top:100px;right:100px;z-index:10000;opacity:1;border:1px solid;background:white">
<div style="padding:10px">Sign in</div>
<div id="login_error" style="padding:10px;display:none;color:red">&nbsp;</div>
<form id="login_form">
    <table class="registration_form">
        <tr><td>Email: *</td><td><input name="email" /></td></tr>
        <tr><td>Password: *</td><td><input name="password" type="password" /></td></tr>
        <tr>
          <td><input type="button" value="Cancel" onclick="$('#login_dialog').hide();$('#login_error').html('');"/></td>
          <td><input type="button" value="Sign in" onclick="tw_login();"/><input type="hidden" name="townwizard_login" value="true"/></td>
        </tr>
   </table>
</form>
</div>

<script>
  function tw_register() {
    $.ajax({
        url: "townwizard-db-api/register.php",
        type: "post",
        data: $('#registration_form').serialize(),        
        success: function(response) {
            if(response == 'success') {                
                window.location.href = window.location.href;
            } else if(response == 'failure') {
                $('#registration_error').html('Please, fill all required fields').show();
            } else if(response == 'conflict') {
                $('#registration_error').html('This email is already registered').show();
            } else {
                $('#registration_error').html(response).show();
            }
        }
    });
  }

  function fb_login() {
    window.open("/townwizard-db-api/fb-login.php", "_blank", "height=200,width=400,status=no,toolbar=no,menubar=no");
  }

  function tw_login_with(user_data) {
    $.ajax({
        url: "townwizard-db-api/login.php",
        type: "post",
        data: user_data,        
        success: function(response) {            
            if(response == 'success') {
                window.location.href = window.location.href;
            }
        }
    });
  }

  function tw_login() {
    $.ajax({
        url: "townwizard-db-api/login.php",
        type: "post",
        data: $('#login_form').serialize(),        
        success: function(response) {
            if(response == 'success') {                
                window.location.href = window.location.href;
            } else if(response == 'failure') {
                $('#login_error').html('Incorrect email and/or password').show();
            } else {
                $('#login_error').html(response).show();
            }
        }
    });
  }

  function tw_logout() {
    $.ajax({
        url: "townwizard-db-api/logout.php",
        type: "get",                
        success: function() {            
            window.location.href = window.location.href;
        }
    });
  }
  
</script>