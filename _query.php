<?php
    include "_config.php";
    global $pdo;

    $customer_id = "";
    $customer_name = "";

    $output = "<RequestDoc>";

    $conn = $pdo->open();

    $sqlAppend = "SELECT customer_id, customer_type, Date_Of_Birth, Date_Incorp, REGISTRATION_NO, Address_Line1, 
                            Address_Line2, Town_City, Country, Contact_Name, Contact_Number, Num_Shares, Share_Price, 
                            f_name FROM tbl_customer c, tbl_file f WHERE c.f_id = f.f_id ";

    if (!empty($_GET["customer_id"])) {
        $customer_id = $_GET["customer_id"];
        $sqlAppend .= " AND customer_id = :customer_id";
    }

    if (!empty($_GET["customer_name"])) {
        $customer_name = $_GET["customer_name"];
        $sqlAppend .= " AND lower(Contact_Name) LIKE lower(:customer_name)";
    }

    $sql = $conn->prepare($sqlAppend);

    if (!empty($_GET["customer_id"])) {
        $sql->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
    }

    if (!empty($_GET["customer_name"])) {
        $str = "%" . $customer_name . "%";
        $sql->bindParam(':customer_name', $str, PDO::PARAM_STR);
    }
    $sql->execute();
    $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_GET["csvexport"]) && $_GET["csvexport"] == "true") {
        $fp = fopen('test.csv', 'w');
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        echo "CSV File created successfully.";
    } else {
        foreach ($rows as $row) {
            $output .= "<DataItem_Customer>";
            $output .= "<customer_id>" . $row["customer_id"] . "</customer_id>";
            $output .= "<Customer_Type>" . $row["customer_type"] . "</Customer_Type>";

            if (!is_null($row["Date_Of_Birth"])) {
                $output .= "<Date_Of_Birth>" . $row["Date_Of_Birth"] . "</Date_Of_Birth>";
            } else {
                $output .= "<Date_Of_Birth/>";
            }

            if (!is_null($row["Date_Incorp"])) {
                $output .= "<Date_Incorp>" . $row["Date_Incorp"] . "</Date_Incorp>";
            } else {
                $output .= "<Date_Incorp/>";
            }

            if (!is_null($row["REGISTRATION_NO"])) {
                $output .= "<REGISTRATION_NO>" . $row["REGISTRATION_NO"] . "</REGISTRATION_NO>";
            } else {
                $output .= "<REGISTRATION_NO/>";
            }

            $output .= "<Mailing_Address>";
            $output .= "<Address_Line1>" . $row["Address_Line1"] . "</Address_Line1>";
            $output .= "<Address_Line2>" . $row["Address_Line2"] . "</Address_Line2>";
            $output .= "<Town_City>" . $row["Town_City"] . "</Town_City>";
            $output .= "<Country>" . $row["Country"] . "</Country>";
            $output .= "</Mailing_Address>";

            $output .= "<Contact_Details>";
            $output .= "<Contact_Name>" . $row["Contact_Name"] . "</Contact_Name>";
            $output .= "<Contact_Number>" . $row["Contact_Number"] . "</Contact_Number>";
            $output .= "</Contact_Details>";

            $output .= "<Shares_Details>";
            $output .= "<Num_Shares>" . $row["Num_Shares"] . "</Num_Shares>";
            $output .= "<Share_Price>" . $row["Share_Price"] . "</Share_Price>";
            $output .= "<Balance>" . $row["Num_Shares"] * $row["Share_Price"] . "</Balance>";
            $output .= "</Shares_Details>";

            $output .= "</DataItem_Customer>";
        }
        header('Content-Type: text/xml');
        $output .= "</RequestDoc>";
        echo $output;
    }
    $pdo->close();
?>
