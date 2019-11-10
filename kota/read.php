<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// required to decode jwt
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
if(isset($authHeader)){
    $arr = explode(" ", $authHeader);
    $jwt = $arr[1];
}else{
    $jwt='';
}
 
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
 
        
        // --database connection-- 
        // include database and object files
        include_once '../config/database.php';
        include_once '../objects/kota.php';

        // instantiate database and product object
        $database = new Database();
        $db = $database->getConnection();

        // initialize object
        $kota = new Kota($db);

        // --read products will be here--
        // query products
        $stmt = $kota->read();
        $num = $stmt->rowCount();

        // check if more than 0 record found
        if($num>0){
            // kota array
            $kota_arr=array();
            $kota_arr["list_kota"]=array();

            // retrieve our table contents
            // fetch() is faster than fetchAll()
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);

                $kota_list=array(
                    "idkota" => $idkota,
                    "kota" => $kota
                );

                array_push($kota_arr["list_kota"], $kota_list);
            }

            // set response code - 200 OK
            http_response_code(200);

            // show kota data in json format
            echo json_encode($kota_arr);
        }

        // no products found will be here
        else{

            // set response code - 404 Not found
            http_response_code(404);

            // tell the user no kota found
            echo json_encode(
                array("message" => "Tidak ada daftar kota.")
            );
        }
 
    }
 
    // if decode fails, it means jwt is invalid
    catch (Exception $e){

        // set response code
        http_response_code(401);

        // tell the user access denied  & show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
 
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
?>