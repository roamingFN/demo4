<?php
include_once 'DB.php';
 
if(!empty($_POST['province'])){
    $database = new DB();
    $result = $database->query("SELECT * FROM tbl_amphur WHERE PROVINCE_ID = " . $_POST['province'])->findAll();
    if(!empty($result)){
        foreach ($result as $value) {
            echo '<option value="' . $value->AMPHUR_ID . '">' . $value->AMPHUR_NAME . '</option>';
        }
    }else{
        return false;
    }
}
exit();
?>