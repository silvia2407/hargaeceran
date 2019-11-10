<?php
class HargaMingguan{
 
    // database connection and table name
    private $conn;
    private $table_name = "hargamingguan";
 
    // object properties
    public $idhargamingguan;
    public $hargamingguan;
    public $minggu;
    public $bulan;
    public $tahun;
    public $idkomoditas;
    public $komoditas;
    public $idkota;
    public $kota;
    public $idkuaitas_merk;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    
    // used when get list price of komoditas by time
    function readOne(){
        try{
            // query to read
            $query = "SELECT
                        h.hargamingguan as hargamingguan, h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk, h.minggu, h.bulan, h.tahun
                    FROM
                        " . $this->table_name . " h
                        LEFT JOIN
                            kota kt 
                                ON h.idkota = kt.idkota
                        LEFT JOIN
                            komoditas km
                                ON h.idkomoditas=km.idkomoditas
                        LEFT JOIN
                            kualitas_merk merk
                                ON h.idkualitas_merk=merk.idkualitas_merk
                    WHERE
                        h.idkota in ( ".$this->kota." ) and h.idkomoditas in ( ".$this->komoditas." ) and h.minggu in ( ".$this->minggu." ) and h.bulan in ( ".$this->bulan." ) and h.tahun in ( ".$this->tahun." )
                    ORDER BY 
                        h.idkota ASC";
            
            //echo $query;
            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            // execute query
            $stmt->execute();

            return $stmt;
        } catch (Exception $ex) {
            // set response code
            http_response_code(401);

            // tell the user access denied
            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
        
    }
    
    // growth month on month by komoditas, kota, dan merk
    function mOm(){
        try{
            if ($this->bulan==1){
                $bulan_before=12;
                $tahun_before=$this->tahun-1;
            }else{
                $bulan_before=$this->bulan-1;
                $tahun_before=$this->tahun;
            }
                // query to read single record
                $query = "
                        SELECT ((AVG_now-AVG_before)/AVG_before)*100 as growth, AVG_before, AVG_now, now.idkota, now.kota, now.idkomoditas, now.komoditas, now.kualitas_merk
                        FROM
                            (SELECT
                                AVG(h.hargamingguan) as AVG_now, h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk
                            FROM
                                " . $this->table_name . " h
                                LEFT JOIN
                                    kota kt 
                                        ON h.idkota = kt.idkota
                                LEFT JOIN
                                    komoditas km
                                        ON h.idkomoditas=km.idkomoditas
                                LEFT JOIN
                                    kualitas_merk merk
                                        ON h.idkualitas_merk=merk.idkualitas_merk
                            WHERE
                                h.idkota in ( ".$this->kota." ) and h.idkomoditas in ( ".$this->komoditas." ) and h.bulan=".$this->bulan." and h.tahun=".$this->tahun." 
                            GROUP BY
                                h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk                    
                            ORDER BY 
                                h.idkota ASC
                            ) now
                        LEFT JOIN
                            (SELECT
                                AVG(h.hargamingguan) as AVG_before, h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk
                            FROM
                                " . $this->table_name . " h
                                LEFT JOIN
                                    kota kt 
                                        ON h.idkota = kt.idkota
                                LEFT JOIN
                                    komoditas km
                                        ON h.idkomoditas=km.idkomoditas
                                LEFT JOIN
                                    kualitas_merk merk
                                        ON h.idkualitas_merk=merk.idkualitas_merk
                            WHERE
                                h.idkota in ( ".$this->kota." ) and h.idkomoditas in ( ".$this->komoditas." ) and h.bulan=".$bulan_before." and h.tahun=".$tahun_before." 
                            GROUP BY
                                h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk                    
                            ORDER BY 
                                h.idkota ASC
                            ) before_
                    ON
                        now.idkota=before_.idkota
                    and
                        now.kota=before_.kota
                    and
                        now.idkomoditas=before_.idkomoditas
                    and
                        now.komoditas=before_.komoditas
                    and
                        now.kualitas_merk=before_.kualitas_merk
                    ";
            
            
            //echo $query;
            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            // execute query
            $stmt->execute();

            return $stmt;
        } catch (Exception $ex) {
            // set response code
            http_response_code(401);

            // tell the user access denied
            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
        
    }
    
    // growth year on year by komoditas, kota, dan merk
    function yOy(){
        try{
                $tahun_before=$this->tahun-1;
            
                // query to read single record
                $query = "
                        SELECT ((AVG_now-AVG_before)/AVG_before)*100 as growth, AVG_before, AVG_now, now.idkota, now.kota, now.idkomoditas, now.komoditas, now.kualitas_merk
                        FROM
                            (SELECT
                                AVG(h.hargamingguan) as AVG_now, h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk
                            FROM
                                " . $this->table_name . " h
                                LEFT JOIN
                                    kota kt 
                                        ON h.idkota = kt.idkota
                                LEFT JOIN
                                    komoditas km
                                        ON h.idkomoditas=km.idkomoditas
                                LEFT JOIN
                                    kualitas_merk merk
                                        ON h.idkualitas_merk=merk.idkualitas_merk
                            WHERE
                                h.idkota in ( ".$this->kota." ) and h.idkomoditas in ( ".$this->komoditas." ) and h.bulan=".$this->bulan." and h.tahun=".$this->tahun." 
                            GROUP BY
                                h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk                    
                            ORDER BY 
                                h.idkota ASC
                            ) now
                        LEFT JOIN
                            (SELECT
                                AVG(h.hargamingguan) as AVG_before, h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk
                            FROM
                                " . $this->table_name . " h
                                LEFT JOIN
                                    kota kt 
                                        ON h.idkota = kt.idkota
                                LEFT JOIN
                                    komoditas km
                                        ON h.idkomoditas=km.idkomoditas
                                LEFT JOIN
                                    kualitas_merk merk
                                        ON h.idkualitas_merk=merk.idkualitas_merk
                            WHERE
                                h.idkota in ( ".$this->kota." ) and h.idkomoditas in ( ".$this->komoditas." ) and h.bulan=".$this->bulan." and h.tahun=".$tahun_before." 
                            GROUP BY
                                h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk                    
                            ORDER BY 
                                h.idkota ASC
                            ) before_
                    ON
                        now.idkota=before_.idkota
                    and
                        now.kota=before_.kota
                    and
                        now.idkomoditas=before_.idkomoditas
                    and
                        now.komoditas=before_.komoditas
                    and
                        now.kualitas_merk=before_.kualitas_merk
                    ";
            
            
            //echo $query;
            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            // execute query
            $stmt->execute();

            return $stmt;
        } catch (Exception $ex) {
            // set response code
            http_response_code(401);

            // tell the user access denied
            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
        
    }
    
    // used when get list price of komoditas by time
    function monthlyPrice(){
        try{
            // query to read
            $query = "SELECT
                        AVG(h.hargamingguan) as monthlyPrice, h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk, h.bulan, h.tahun
                    FROM
                        " . $this->table_name . " h
                        LEFT JOIN
                            kota kt 
                                ON h.idkota = kt.idkota
                        LEFT JOIN
                            komoditas km
                                ON h.idkomoditas=km.idkomoditas
                        LEFT JOIN
                            kualitas_merk merk
                                ON h.idkualitas_merk=merk.idkualitas_merk
                    WHERE
                        h.idkota in ( ".$this->kota." ) and h.idkomoditas in ( ".$this->komoditas." ) and h.bulan in ( ".$this->bulan." ) and h.tahun in ( ".$this->tahun." )
                    GROUP BY
                        h.idkota, kt.kota, km.idkomoditas, km.komoditas, merk.kualitas_merk, h.bulan, h.tahun
                    ORDER BY 
                        h.idkota ASC";
            
            //echo $query;
            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            // execute query
            $stmt->execute();

            return $stmt;
        } catch (Exception $ex) {
            // set response code
            http_response_code(401);

            // tell the user access denied
            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
        
    }
}
?>