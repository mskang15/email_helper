<?php

$current_api  = "2.0";
$method       = $_SERVER['REQUEST_METHOD'];
$HTTP_HEADERS = getallheaders();
$apikeyauth   = false;

$action = isset($_GET['a']) ? $_GET['a'] : NULL;
$q      = isset($_GET['q']) ? $_GET['q'] : NULL;
$r      = isset($_GET['r']) ? $_GET['r'] : NULL;

reset($HTTP_HEADERS);
while (list($hkey, $kval) = each($HTTP_HEADERS)) {
    $lc_hkey = strtolower($hkey);
    switch ( $lc_hkey ) {
        case 'content-type':
            $contextType = $kval;
            break;
    }
}
reset($HTTP_HEADERS);
$_API = array();
switch ( $method ) {
    case 'GET':
        if (isset($_GET)){
            while (list($key, $val) = each($_GET)) {
                $_API[$key] = $val;
            }
        }
        break;
    case 'POST':
        if (isset($_POST)){
            while (list($key, $val) = each($_POST)) {
                $_API[$key] = $val;
            }
        }
        if (isset($contextType) && (preg_match("/application\/json/", strtolower($contextType)))){
            $jsonstr = file_get_contents('php://input');
            if (!empty($jsonstr)){
                $apiobject = json_decode($jsonstr, true);
                if (!empty($apiobject)){
                    $_API = array_merge($_API, $apiobject);
                }
            }
        }
        break;
    case 'PUT':
    case 'PATCH':
    case 'DELETE':
        if (isset($contextType) && (preg_match("/application\/json/", strtolower($contextType)))){
            $jsonstr = file_get_contents('php://input');
            if (!empty($jsonstr)){
                $apiobject = json_decode($jsonstr, true);
                if (!empty($apiobject)){
                    $_API = array_merge($_API, $apiobject);
                }
            }
        }else{
            parse_str(file_get_contents("php://input"), $_API);
        }
        break;
}

reset($HTTP_HEADERS);
while (list($hkey, $kval) = each($HTTP_HEADERS)) {
    $lc_hkey = strtolower($hkey);
    switch ( $lc_hkey ) {
        case 'x-api-hash':
            $apihash = $kval;
            break;
        case 'x-api-key':
            $apikey = $kval;
            break;
        case 'x-http-method-override':
            $method = $kval;
            break;
        case 'x-api-token':
            $api_token = $kval;
            if($access_token_info = check_access_token($authproto, $newauthserver, $api_token)) {
                $role = $access_token_info["role"];
                $access_token = $access_token_info["access_token"];
                $uid = $access_token_info["uid"];
            }
            break;
        case 'content-type':
            $contextType = $kval;
            break;
    }
}
reset($HTTP_HEADERS);

if (empty($apihash)){
    $apihash = isset($_API['apihash']) ? $_API['apihash'] : NULL;
}
if (empty($apikey)){
    $apikey = isset($_API['apikey']) ? $_API['apikey'] : NULL;
}


if (isset($apihash) && isset($apikey)){
    //error_log($_API);
    if (isset($_API['hashorder'])){
        $ui['hashorder']  = $_API['hashorder'];
        $ui['hashmethod'] = $_API['hashmethod'];
        $hashorder        = explode(",", $_API['hashorder']);
        while (list($hkey, $hval) = each($hashorder)) {
            $ui[$hval] = isset($_API[$hval]) ? $_API[$hval] : false;
        }
    }
    // TODO: this needs to be offloaded to ude

//    list($role, $access_token_info['username']) = checkAPIKey($db, $ui, $apikey, $apihash, $r);
}

reset($_API);
while(list($key,$val) = each($_API)){
    if (!is_array($val)){
        if (preg_match("/^base64;/", $val)){
            $base64val  = preg_replace("/^base64;/", "", $val);
            $newval     = base64_decode($base64val);
            $_API[$key] = $newval;
        }
    }
}
reset($_API);
/*
if (isset($HTTP_HEADERS['X-Api-Hash'])){
    $apihash = $HTTP_HEADERS['X-Api-Hash'];
}

if (isset($HTTP_HEADERS['X-Api-Key'])){
    $apikey = $HTTP_HEADERS['X-Api-Key'];
}

if (isset($HTTP_HEADERS['X-HTTP-Method-Override'])){
    $method = $HTTP_HEADERS['X-HTTP-Method-Override'];
}
*/

//$mimeaccept = getBestSupportedMimeType();
//$raccept    = array();
//foreach ($mimeaccept as $mimetype => $qval){
//    if ($qval){ // if q is zero... it's not accepted
//        if ($mimetype == "*/*"){
//            if (!empty($r)){
//                $raccept[] = $r;
//            }else{
//                $r         = "json";
//                $raccept[] = "json";
//            }
//        }elseif (preg_match("/application\/json/", $mimetype)){
//            $raccept[] = "json";
//        }elseif (preg_match("/text\/plain/", $mimetype)){
//            $raccept[] = "dump";
//        }elseif (preg_match("/text\/html/", $mimetype)){
//            $raccept[] = "html";
//        }
//    }
//}
//if (empty($r) && (!empty($raccept))){
//    $r = $raccept[0];
//}elseif (empty($r) && (empty($raccept))){
//    $r         = 'json';
//    $raccept[] = 'json';
//}
//if (!in_array($r, $raccept)){
//    $resultarray['code']    = "406";
//    $resultarray['status']  = "406 No acceptable mime-types.";
//    $resultarray['message'] = "The server cannot provide the response within the acceptable mime-types provided.";
//    api_response($resultarray, 'txt', 406, "Not Acceptable");
//    exit;
//}