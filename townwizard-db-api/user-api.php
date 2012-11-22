<?php

define("TOWNWIZARD_DB_USERS_URL", "http://localhost:8080/tw/users");
define("TOWNWIZARD_DB_USER_LOGIN_URL", "http://localhost:8080/tw/users/login");

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
    Makes a request to Town Wizard DB to look for a user by the email and password.
    If user is found, put the user's name to the session.

    Return:
      - "success" on HTTP status 200 (user found)
      - "failure" on HTTP status 404 (user not found)
      - error message on HTTP status 500 (server error) or when the server is down
***/
function tw_login($post) {
    $parameters = array();
    $parameters["email"] = $post["email"];
    $parameters["password"] = $post["password"];
    
    $json = json_encode($parameters);
    
    list($status, $response_msg) = _tw_post_json(TOWNWIZARD_DB_USER_LOGIN_URL, $json);

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