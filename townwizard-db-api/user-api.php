<?php

define("TOWNWIZARD_DB_USERS_URL", "http://tw-db.com/users");
define("TOWNWIZARD_DB_USER_LOGIN_URL", "http://tw-db.com/users/login");
define("TOWNWIZARD_DB_USER_LOGIN_WITH_URL", "http://tw-db.com/users/loginwith");
define("TOWNWIZARD_DB_FB_LOGIN_URL", "http://tw-db.com/login/fb");
define("TOWNWIZARD_DB_RATINGS_URL", "http://tw-db.com/ratings");
define("TOWNWIZARD_DB_RSVPS_URL", "http://tw-db.com/rsvps");

/***
    Takes a user registration POST form data, encodes it in JSON and
    sends the JSON to the service.

    If the user is created successfully, log the user in.

    Return:
      - "success" on HTTP status 201 (created) when the user is created
      - "failure" on HTTP status 400 (bad request) when the user data is incomplete or invalid
      - "conflict" on HTTP status 409 (conflict) when a user's email is already registered
      - error message on HTTP status 500 (server error) or when the server is down
***/
function tw_create_user($post) {
    $parameters = array();
    $parameters["email"] = $post["email"];
    $parameters["password"] = $post["password"];
    $parameters["username"] = $post["username"];
    $parameters["firstName"] = $post["firstName"];
    $parameters["lastName"] = $post["lastName"];
    $parameters["gender"] = $post["gender"];
    $parameters["year"] = $post["year"];
    $parameters["mobilePhone"] = $post["mobilePhone"];
    $parameters["registrationIp"] = $_SERVER['REMOTE_ADDR'];

    $parameters["address"] = array();
    $parameters["address"]["address1"] = $post["address1"];
    $parameters["address"]["address2"] = $post["address2"];
    $parameters["address"]["city"] = $post["city"];
    $parameters["address"]["state"] = $post["state"];
    $parameters["address"]["postalCode"] = $post["postalCode"];

    $json = json_encode($parameters);

    list($status, $response_msg) = _tw_post_json(TOWNWIZARD_DB_USERS_URL, $json);
    
    if($status == 500) {
        $result = $response_msg; //error
    } else if ($status == 0) {
        $result = "Server down";
    } else if ($status == 400) {
        $result = "failure";
    } else if ($status == 409) {
        $result = "conflict";
    } else {
        $user = json_decode($response_msg);
        _tw_login($user);
        $result = "success";
    }

    return $result;
}

/***
    Takes POST form data for rating (value, site id, user id, content id, and content type)
    and sends JSON to the service.

    Return:
      - rating object or rating json depending on the value of $return_json on HTTP code 201 (created)
      - "failure" on HTTP status 400 (bad request)
      - "failure :" plus error message on HTTP status (500) or when the service is down
***/
function tw_create_rating($post, $return_json = false) {
    $parameters = array();
    $parameters['userId'] = $_SESSION['tw_user']->id;
    $parameters['siteId'] = $_SESSION['c_db_id'];
    $parameters['contentId'] = $post['contentId'];
    $parameters['contentType'] = $post['contentType'];
    $parameters['value'] = $post['value'];

    $json = json_encode($parameters);

    list($status, $response_msg) = _tw_post_json(TOWNWIZARD_DB_RATINGS_URL, $json);
    
    if($status == 500) {
        $result = "failure: " . $response_msg;
    } else if ($status == 0) {
        $result = "Server down";
    } else if ($status == 400) {
        $result = "failure";
    } else {
        $result = $response_msg;
    }

    return $result;
}

/***
    Takes a comma-separated list of content ids and a content type, and 
    get the ratings for these ids and the content type (same for all content ids)    

    Return:
     - array of rating objects on  HTTP status 200 or json received from the server
       if $return_json is set to true
     - NULL in other cases
***/
function tw_get_ratings($content_ids, $content_type, $return_json = false) {
    $user_id = $_SESSION['tw_user']->id;
    $site_id = $_SESSION['c_db_id'];

    $id = $content_type.'/'.$site_id.'/'.$user_id.'/'. $content_ids;    
    list($status, $response_msg) = _tw_get_json(TOWNWIZARD_DB_RATINGS_URL, $id);
    if($status == 200) {
        if($return_json) return $response_msg;
        else return json_decode($response_msg);
    }
    return NULL;
}

/***
    Takes a comma-separated list of content ids and a content type, and 
    get average ratings for these ids and the content type (same for all content ids)    

    Return:
     - array of rating objects on  HTTP status 200 or json received from the server
       if $return_json is set to true
     - NULL in other cases
***/
function tw_get_avg_ratings($content_ids, $content_type, $return_json = false) {
    $site_id = $_SESSION['c_db_id'];

    $id = $content_type.'/'.$site_id.'/'. $content_ids;    
    list($status, $response_msg) = _tw_get_json(TOWNWIZARD_DB_RATINGS_URL, $id);
    if($status == 200) {
        if($return_json) return $response_msg;
        else return json_decode($response_msg);
    }
    return NULL;
}

/***
    Takes POST form data for rsvp (value, site id, user id, and event id)
    and sends JSON to the service.

    Return:
      - "success" on HTTP code 201 (created)
      - "failure" on HTTP status 400 (bad request)
      - error message on HTTP status (500) or when the service is down
***/
function tw_create_rsvp($post) {
    $parameters = array();
    $parameters['userId'] = $_SESSION['tw_user']->id;
    $parameters['siteId'] = $_SESSION['c_db_id'];
    $parameters['eventId'] = $post['eventId'];
    $parameters['value'] = $post['value'];
    $event_date = $post['eventDate'];
    if(!empty($eventDate)) {
        $parameters['eventDate'] = $eventDate * 1000;
    }

    $json = json_encode($parameters);

    list($status, $response_msg) = _tw_post_json(TOWNWIZARD_DB_RSVPS_URL, $json);
    
    if($status == 500) {
        $result = $response_msg;
    } else if ($status == 0) {
        $result = "Server down";
    } else if ($status == 400) {
        $result = "failure";
    } else {
        $result = "success";
    }

    return $result;
}

/***
    Get rsvps for a current logged in user

    Takes from and to (dates in seconds) parameters to narrow the search.
    If events don't have dates on the townwizard db side, the events will be included, too.

    Return:
     - array of RSVP objects on  HTTP status 200
     - NULL in other cases
***/
function tw_get_rsvps_by_user($from = NULL, $to = NULL) {
    $user_id = $_SESSION['tw_user']->id;
    $id = $user_id;

    if(!empty($from) || !empty($to)) {
        $id = $id.'?from='.$from.'&to='.$to;
    }

    list($status, $response_msg) = _tw_get_json(TOWNWIZARD_DB_RSVPS_URL, $id);
    if($status == 200) {
        $rsvps = json_decode($response_msg);
        return $rsvps;
    }
    
    return NULL;
}

/***
    Get rsvps for an event

    Takes event id and optionally event date as parameters.
    If event date is available it's recommended to pass it for 
    townwizard db to update/create event with the date.

    Return:
     - array of RSVP objects on  HTTP status 200
     - NULL in other cases
***/
function tw_get_rsvps_by_event($event_id, $event_date = NULL) {
    $site_id = $_SESSION['c_db_id'];
    
    $id = $site_id . "/" . $event_id;
    if(!empty($event_date)) {
        $id = $id.'?d='.$event_date;
    }

    list($status, $response_msg) = _tw_get_json(TOWNWIZARD_DB_RSVPS_URL, $id);
    if($status == 200) {
        $rsvps = json_decode($response_msg);
        return $rsvps;
    }
    
    return NULL;
}

/***
    Gets a user from the service by id (which can be numeric id or email).    

    Return:    
        - user object for HTTP status 200 (Ok) when a user is found
        - NULL for HTTP statuses 404 (Not found) and 500 (server error)
***/
function tw_get_user($id) {
    list($status, $response_msg) = _tw_get_json(TOWNWIZARD_DB_USERS_URL, $id);
    if($status == 200) {
        $user = json_decode($response_msg);
        return $user;
    }
    return NULL;
}

/***
    Gets a user from the service by id, and put the users' name to the session.
***/
function tw_login_with_id($id) {
    $user = tw_get_user($id);
    _tw_login($user);
}

/***
    Makes a request to Town Wizard DB to look for a user by the email and password.
    If user is found, put the user's name to the session.

    Return:
      - "success" on HTTP status 200 (user found)
      - "failure" on HTTP status 404 (user not found)
      - error message on HTTP status 500 (server error) or when the server is down
***/
function tw_login($post) {
    if(!empty($post['townwizard_login'])) {
        $url = TOWNWIZARD_DB_USER_LOGIN_URL;
        $parameters = Array();
        $parameters['email'] = $post['email'];
        $parameters['password'] = $post['password'];        
    } else {
        $url = TOWNWIZARD_DB_USER_LOGIN_WITH_URL;
        $parameters = $post;
    }
    
    $json = json_encode($parameters);

    list($status, $response_msg) = _tw_post_json($url, $json);

    if($status == 500) {
        $result = $response_msg; //error
    } else if ($status == 0) {
        $result = "Server down";
    } else {
        $user = json_decode($response_msg);
        if(!empty($user -> id)) {
            _tw_login($user);
            $result = "success";
        } else {
            $result = "failure";        
        }
    }

    return $result;
}

/***
    Remove user name from the session, and return "success".
***/
function tw_logout() {    
    unset($_SESSION['tw_user_name']);
    unset($_SESSION['tw_user']);
    return "success";
}

// Private functions
////////////////////
function _tw_login($user) {
    if($user -> firstName) {
        $user_name = $user -> firstName;    
    } else if($user -> username) {
        $user_name = $user -> username;
    } else {
        $user_name = substr($user -> email, 0, strpos($user -> email, '@'));
    }
    $_SESSION['tw_user_name'] = $user_name;
    $_SESSION['tw_user'] = $user;
}


function _tw_post_json($url, $json) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)));
    
    $response_msg = curl_exec($ch);
    $status = curl_getinfo($ch);
    $status_code = $status["http_code"];
    curl_close($ch);

    return array($status_code, $response_msg);
}

function _tw_get_json($url, $id) {
    $ch = curl_init("$url/$id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response_msg = curl_exec($ch);
    $status = curl_getinfo($ch);
    $status_code = $status["http_code"];
    curl_close($ch);

    return array($status_code, $response_msg);
}

?>