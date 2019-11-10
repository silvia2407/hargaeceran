<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header("Access-Control-Max-Age: 3600");

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
        // include database and object files
        include_once '../config/database.php';
        include_once '../objects/hargamingguan.php';
        include_once '../objects/kota.php';
        include_once '../objects/komoditas.php';

        // get database connection
        $database = new Database();
        $db = $database->getConnection();

        // prepare product object
        $hargamingguan = new HargaMingguan($db);

        // set kota property of record to read
        if(isset($_GET['kota'])){
            if($_GET['kota']=='all'){
                $kota=new Kota($db);
                $kotaAll=$kota->read();
                $num=$kotaAll->rowCount();

                $stringkota='';
                $i=1;
                while ($row = $kotaAll->fetch(PDO::FETCH_ASSOC)){

                    if($i==1)
                        $stringkota=$row['idkota'];
                    else {
                        $stringkota=$stringkota.','.$row['idkota'];
                    }    
                    $i++;


                }   
                $hargamingguan->kota=$stringkota;
            }

            else    
                $hargamingguan->kota=$_GET['kota'];
        }
        else {
            die();
        }

        // set komoditas property of record to read
        if(isset($_GET['komoditas'])){
            if($_GET['komoditas']=='all'){
                $komoditas=new Komoditas($db);
                $komoditasAll=$komoditas->read();
                $num=$komoditasAll->rowCount();

                $stringkomoditas='';
                $i=1;
                while ($row = $komoditasAll->fetch(PDO::FETCH_ASSOC)){

                    if($i==1)
                        $stringkomoditas=$row['idkomoditas'];
                    else {
                        $stringkomoditas=$stringkomoditas.','.$row['idkomoditas'];
                    }    
                    $i++;


                }   
                $hargamingguan->komoditas=$stringkomoditas;
            }

            else    
                $hargamingguan->komoditas=$_GET['komoditas'];
        }
        else {
            die();
        }

        // set tahun property of record to read
        if(isset($_GET['tahun'])){
            $hargamingguan->tahun=$_GET['tahun'];
        }
        else {
            die();
        }

        // set bulan property of record to read
        if(isset($_GET['bulan'])){
            if($_GET['bulan']=='all'){

                $hargamingguan->bulan='1,2,3,4,5,6,7,8,9,10,11,12';
            }
            else    
                $hargamingguan->bulan=$_GET['bulan'];
        }
        else {
            die();
        }

        // set minggu property of record to read
        if(isset($_GET['minggu'])){
            if($_GET['minggu']=='all'){

                $hargamingguan->minggu='1,2,3,4,5';
            }
            else    
                $hargamingguan->minggu=$_GET['minggu'];
        }
        else {
            die();
        }

        // read the details of hargamingguan
        $hargamingguan=$hargamingguan->readOne();
        $num = $hargamingguan->rowCount();

        // check if more than 0 record found
        if($num>0){
            // kota array
            $hargamingguan_arr=array();
            $hargamingguan_arr["weeklyPrice"]=array();

            // retrieve our table contents
            // fetch() is faster than fetchAll()
            while ($row = $hargamingguan->fetch(PDO::FETCH_ASSOC)){


                $hargamingguan_list=array(
                    "idkota" => $row['idkota'],
                    "kota" => $row['kota'],
                    "idkomoditas"=>$row['idkomoditas'],
                    "komoditas"=>$row['komoditas'],
                    "kualitas_merk"=>$row['kualitas_merk'],
                    "minggu"=>$row['minggu'],
                    "bulan"=>$row['bulan'],
                    "tahun"=>$row['tahun'],
                    "hargamingguan"=>$row['hargamingguan']

                );

                array_push($hargamingguan_arr["weeklyPrice"], $hargamingguan_list);
            }

            // set response code - 200 OK
            http_response_code(200);

            // show kota data in json format
            echo json_encode($hargamingguan_arr);
        }

        else{
            // set response code - 404 Not found
            http_response_code(404);

            // tell the user product does not exist
            echo json_encode(array("message" => "Tidak ada daftar harga."));
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