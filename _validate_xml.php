<?php
    include "_config.php";

    use \Firebase\JWT\JWT;
    $config = Factory::fromFile('config/config.php', true);
    $key = "DPJacoUfCSxVlXvhoLgbEaq2B4vffMSK";
    $tokenId    = base64_encode(mcrypt_create_iv(32));
    $issuedAt   = time();
    $notBefore  = $issuedAt + 10;
    $expire     = $notBefore + 60;
    $serverName = $config->get('serverName');

    $data = [
        'iat'  => $issuedAt,
        'jti'  => $tokenId,
        'iss'  => $serverName,
        'nbf'  => $notBefore,
        'exp'  => $expire,
        'data' => [
            'userId'   => $rs['id'],
            'userName' => $username,
        ]
    ];

    $secretKey = base64_decode($config->get('jwtKey'));

    $jwt = JWT::encode(
        $data,
        $secretKey,
        'HS512'
    );

    $output = array();
    $status = "";
    $msg = "";
    $good_records = 0;
    $bad_records = 0;

    if(!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])){
        $status = "404";
        $msg = "No File was found. Please Try Again.";
    } else {
        $xml_file = @simplexml_load_file($_FILES['file']['tmp_name']);

        if($xml_file === false){
            $status = "400";
            $msg = "XML file is not valid. Please Try Again.";
        } else {
            $insert1 = insert_file_record($_FILES['file']['name'], $xml_file->Doc_Ref);

            if(is_numeric($insert1)){
                foreach($xml_file->Doc_Data->DataItem_Customer as $CustomerDataItem){
                    $check = check_rules_validity($CustomerDataItem);
                    if($check) {
                        $insert2 = insert_valid_records($CustomerDataItem, $insert1);
                        if(!$insert2){
                            $status = "400";
                            $msg = $insert2;
                        } else {
                            $good_records++;
                        }
                    } else {
                        $insert3 = insert_invalid_records($CustomerDataItem, $insert1, $check);
                        if(!$insert3){
                            $status = "400";
                            $msg = $insert3;
                        } else {
                            $bad_records++;
                        }
                    }
                }
                $status = "200";
                $msg = "Number of Good Record Inserted is ".$good_records." and Number of Bad Record Inserted is ".$bad_records;
            } else {
                $status = "400";
                $msg = $insert1;
            }
        }
    }

    $output["status"] = $status;
    $output["message"] = $msg;
    echo json_encode($output);

    function insert_file_record($file, $Doc_Ref): string{
        global $pdo;
        $code = "";

        $target_dir = "dir/";
        if(!file_exists($target_dir)){
            mkdir($target_dir);
        }

        $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $target_file = $target_dir.$Doc_Ref.".".$fileExt;
        $allowed_file_ext = array("xml");

        if(!file_exists($target_file)){
            if(in_array($fileExt, $allowed_file_ext)){
                if($_FILES["file"]["size"] < 2097152){
                    if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                        $conn = $pdo->open();
                        try{
                            $sql = $conn->prepare("INSERT INTO tbl_file (f_name, f_path) VALUES (:f_name, :f_path)");

                            if($sql->execute([':f_name'=>$Doc_Ref, ':f_path'=>$target_file])){
                                $code = intval($conn->lastInsertId());
                            }
                        } catch (PDOException $e){
                            $code = $e;
                        }
                        $pdo->close();
                    }
                } else {
                    $code = "Files more than 2MB are not allowed.";
                }
            } else {
                $code = "File extension other than XML are not allowed.";
            }
        } else {
            $code = "File with reference ".$Doc_Ref." already exists. Kindly try another one.";
        }

        return $code;
    }

    function check_rules_validity($CustomerDataItem): string{
        if($CustomerDataItem->Customer_Type == "Individual"){
            if($CustomerDataItem->Date_Of_Birth != ""){
                if(get_Age($CustomerDataItem->Date_Of_Birth) < 18){
                    return "For individuals, customer must be at least 18 years old.";
                }
            }
        }

        $num_of_shares = $CustomerDataItem->Shares_Details->Num_Shares;
        if(!is_int($num_of_shares) && $num_of_shares <= 0){
            return "Number of shares must be an integer and greater than 0.";
        }

        $shares_price = $CustomerDataItem->Shares_Details->Share_Price;
        if(!preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $shares_price) || $shares_price <= 0){
            return "Share Price must be a number greater than 0 up to two decimal places.";
        }

        return true;
    }

    function insert_valid_records($CustomerDataItem, $file_id): PDOException|Exception|bool|string{
        global $pdo;
        $code = "";
        $conn = $pdo->open();

        try{
            $sql = $conn->prepare("INSERT INTO tbl_customer (customer_id, customer_type, Date_Of_Birth, Date_Incorp, REGISTRATION_NO, 
                                                                Address_Line1, Address_Line2, Town_City, Country, Contact_Name, Contact_Number, 
                                                                Num_Shares, Share_Price, f_id) 
                                        VALUES (:customer_id, :customer_type, :Date_Of_Birth, :Date_Incorp, :REGISTRATION_NO, 
                                                :Address_Line1, :Address_Line2, :Town_City, :Country, :Contact_Name, :Contact_Number, 
                                                :Num_Shares, :Share_Price, :f_id)");

            if($sql->execute([':customer_id'=>$CustomerDataItem->customer_id, ':customer_type'=>$CustomerDataItem->Customer_Type,
                ':Date_Of_Birth'=>$CustomerDataItem->Date_Of_Birth, ':Date_Incorp'=>date($CustomerDataItem->Date_Incorp),
                ':REGISTRATION_NO'=>$CustomerDataItem->Registration_No, ':Address_Line1'=>$CustomerDataItem->Mailing_Address->Address_Line1,
                ':Address_Line2'=>$CustomerDataItem->Mailing_Address->Address_Line2, ':Town_City'=>$CustomerDataItem->Mailing_Address->Town_City,
                ':Country'=>$CustomerDataItem->Mailing_Address->Country, ':Contact_Name'=>$CustomerDataItem->Contact_Details->Contact_Name,
                ':Contact_Number'=>$CustomerDataItem->Contact_Details->Contact_Number, ':Num_Shares'=>$CustomerDataItem->Shares_Details->Num_Shares,
                ':Share_Price'=>$CustomerDataItem->Shares_Details->Share_Price, ':f_id'=>$file_id])){

                $code = true;
            }
        } catch (PDOException $e){
            $code = $e;
        }
        $pdo->close();

        return $code;
    }

    function insert_invalid_records($CustomerDataItem, $file_id, $message): PDOException|Exception|bool|string{
        global $pdo;
        $code = "";
        $conn = $pdo->open();
        try{
            $sql = $conn->prepare("INSERT INTO tbl_error (e_customer_id, e_message, f_id) 
                                        VALUES (:e_customer_id, :e_message, :f_id)");

            if($sql->execute([":e_customer_id"=>$CustomerDataItem->customer_id, ":e_message"=>$message, ':f_id'=>$file_id])){
                $code = true;
            }
        } catch (PDOException $e){
            $code = $e;
        }
        $pdo->close();

        return $code;
    }

    function get_Age($date){
        $date = explode("/", $date);
        return (date("md", date("U", mktime(0, 0, 0, $date[0], $date[1], $date[2]))) > date("md")
                ? ((date("Y") - $date[2]) - 1) : (date("Y") - $date[2]));
    }
?>
