<?php

define("TOWNWIZARD_DB_USERS_URL", "http://localhost:8080/tw/users");

/***
    Takes a user registration POST form data, encodes it in JSON and
    sends the JSON to the service.

    Returns an array of two elements: HTTP status code and response message.

    Possible HTTP statuses:
        201 (Created) in case of success
        400 (Bad request) in case when email is invalid or missing, or password is missing
        409 (Conflict) in case when email is a duplicate
        500 (Server error) in case of a generic server error
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

    $user_json = json_encode($parameters);

    return _tw_post_json(TOWNWIZARD_DB_USERS_URL, $user_json);
}

/***
    Gets a user from the service by id (which can be numeric id or email).
    Returns an array of two elements: HTTP status code and string containing 
    a user JSON representation.

    Possible HTTP statuses:
        200 (Ok) when a user is found
        404 (Not found) when a user is not found
        500 (Server error) in case of a generic server error

    Usage example:

        list($status, $response_msg) = tw_get_user("vmazheru@salzinger.com");

        if($status != 404) {
            $user = json_decode($response_msg);
            echo "Welcome, {$user->username";
        } else {
            echo "Welcome, stranger"; 
        }

***/
function tw_get_user($id) {
    return _tw_get_json(TOWNWIZARD_DB_USERS_URL, $id);
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