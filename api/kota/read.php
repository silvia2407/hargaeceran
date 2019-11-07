<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
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
    echo 'ada';
    // kota array
    $kota_arr=array();
    $kota_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
        $kota_list=array(
            "idkota" => $idkota,
            "kota" => $kota
        );
 
        array_push($kota_arr["records"], $kota_list);
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