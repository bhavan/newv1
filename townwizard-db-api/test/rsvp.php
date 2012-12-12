<?php

require_once('../../jevents.php');
include_once('../user-api.php');

?>

<?php if(empty($_POST)) { ?>

<html>
<head></head>
<body>
    Please enter your reponse (Y, N, or M)<br/><br/>
    
    <form id="rsvp_form" method="post" action="rsvp.php">
        <input type="hidden" value="123456" name="eventId" />
        <input type="hidden" value="1304254800" name="eventDate" />
        <input type="text" name="value" /><br/>
        <input type="submit" name="Submit" />
    </form>

</body>
</html>

<?php } else { 

    tw_create_rsvp($_POST);
    
    echo var_dump(tw_get_rsvps_by_user());
    echo '<br/>';

    echo var_dump(tw_get_rsvps_by_user(1304254800, 1304254900));
    echo '<br/>';

    echo var_dump(tw_get_rsvps_by_event(123456));
    echo '<br/>';

    echo var_dump(tw_get_rsvps_by_event(123456, 1304254800));
    echo '<br/>';
} ?>