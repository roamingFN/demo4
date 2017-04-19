<?php
include_once 'DB.php';
 
if(!empty($_POST['amphoe'])){
    $database = new DB();
    $result = $database->query("SELECT * FROM tbl_district WHERE AMPHUR_ID = " . $_POST['amphoe'])->findAll();
    if(!empty($result)){
        foreach ($result as $value) {
            echo '<option value="' . $value->DISTRICT_ID . '">' . $value->DISTRICT_NAME . '</option>';
        }
    }else{
        return false;
    }
}
exit();
?>