<?php 
include 'project-constant.php';
include 'date.class.php';
$userName = 'root';
$password = '';
try {
    $dbh = null;
    $dbh = new PDO('mysql:host=localhost;dbname=alumni_mgmt_system', $userName, $password);
} catch (PDOException $e) {
    echo "Connection Error!: " . $e->getMessage() . "<br/>";
    die();
}
class GetCommomOperation {
    public function InsertQuery($db, $table, $cols, $values, $log = false) { //Insert function expects connection variable,tableName, columns(array), values(array)
        $result = array();
        $col_list = implode(",", $cols);
        $param_list = implode(",", array_fill(1, count($cols), "?"));
        $sql = "INSERT INTO $table ($col_list) VALUES ($param_list)";
        $stmt = $db->prepare($sql);
        $result['status'] = $stmt->execute($values);
        $result['lastInsertId'] = $db->lastInsertId();
        if ($log != FALSE) {
            error_log(json_encode($stmt));
            error_log(json_encode($values));
        }
        return $result;
    }
    public function generateRandomUserId($dbh){
        $randId = GetCommomOperation::UserID();
        $values = array($randId);
        $reslt = GetCommomOperation::selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id'), 'user_id = ?', $values);
        if($reslt['total'] > 0){
            $randId = GetCommomOperation::UserID($reslt['values'][0]['user_id']);
        }
        return $randId;
    }
    public function UserID($id = ''){
        if(!empty($id)){
            $first_part1=rand(0,$id);
        }else{
            $first_part1=rand(0,99);
        }
        $starting_digit=5;
        if(strlen($first_part1)==1) { $first_part="0".$first_part1; } else { $first_part=$first_part1; }
        $second_part1=rand(1,999);
        if(strlen($second_part1)==1) { $second_part="00".$second_part1; } elseif(strlen($second_part1)==2) { $second_part="0".$second_part1; } else { $second_part=$second_part1; }
        $third_part1=rand(1,9999);
        if(strlen($third_part1)==1) { $third_part="000".$third_part1; } elseif(strlen($third_part1)==2) { $third_part="00".$third_part1; } elseif(strlen($third_part1)==3) { $third_part="0".$third_part1; } else { $third_part=$third_part1; }
        $userid = $starting_digit.$first_part.$third_part;
        return $userid;
    }
    public function selectData($db, $table, $cols, $condition, $values, $limit = 10000, $log = false) { //Select function expects connection variable,tableName, columns(array), condition(string), value(array)
        $result = array();
        $col_list = implode(",", $cols);
        $sql_select = 'SELECT ' . $col_list . ' FROM ' . $table . ' WHERE ' . $condition . ' LIMIT ' . $limit;
        ;
        $stmt = $db->prepare($sql_select);
        $stmt->execute($values);
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            for ($i = 1; $i <= $rowCount; $i++) {
                $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        if ($log != FALSE) {
            error_log(json_encode($stmt));
            error_log(json_encode($values));
        }
        $result['total'] = $rowCount;
        return $result;
    }
    public function getSessionId(){
        if (!session_id() ) {
            session_start();
        }
        return $_SESSION['SESS_ADMIN_ID'];
    }
    public function check_exist($db, $table, $cols, $condition, $values,$editId='',$edit="false") { //check whether data is already exist in table or not
        $result = $this->selectData($db, $table, $cols, $condition, $values);
        if($edit){
            if ($result['total'] > 0) {
                if($result['values'][0][$cols[0]] == $editId){
                    return false;
                }else{
                    return true;
                }
            } else {
                return false;
            }
        }else{
            if ($result['total'] == 0) {
                return false;
            }else if ($result['total'] > 0) {
                return true;
            } else {
                return false;
            }
        }

    }

//    function checkRedundantz($db, $table, $cols, $condition, $edit = false) {
//        $selectValidData = $dbobj->_select($table, $columns, $condition);
//        if ($selectValidData['total'] > 0) {
//            if(count($selectValidData['values']) > 1){
//                return false;
//            }  else {
//                return true;
//            }
//        }
//    }
    public function UpdatetData($db, $table, $bindCol, $condition, $values, $log = FALSE) { //Insert function expects connection variable,tableName, columns(string), condition(string), value(array)
        $sql_select = "UPDATE $table SET $bindCol WHERE $condition";
        $stmt = $db->prepare($sql_select);
        $stmt->execute($values);
        $count = $stmt->rowCount();
        if ($log != FALSE) {
            $col = $result = array();
            $col = explode(',', $bindCol);
            for($i = 0; $i < count($col); $i++){
                $bindVal = str_replace(' = ?', ' = "'.$values[$i], $col[$i]);
                $result[] = $bindVal;
            } 
            $errLog = "UPDATE $table SET ".implode('",',$result)." WHERE $condition";
            error_log(json_encode($stmt));
            error_log(json_encode($values));
        }
        if ($count != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserData($db, $id) {
        $result = $this->selectData($db, 'alumni_user_login_details', array('registration_id','profile_pic','profession','present_address','super_admin','admin','permanent_address','pincode','blood_group','skills_id','merital_status','dob','course_id','city','state','country','fname','lname','year_of_graduation','concat_ws(" ",fname,lname) as fullName','email_id','mobile_number','gender','user_type','terms_and_condition','year_of_graduation'), 'user_id = ?', array($id));
        return  $result;
    } 
    public function DeleteData($db, $table, $condition, $id, $log = FALSE) { //Insert function expects connection variable,tableName, columns(array), condition(string), value(array)
        $sql_select = "DELETE FROM $table WHERE $condition";
        $stmt = $db->prepare($sql_select);
        $stmt->execute($id);
        $count = $stmt->rowCount();
        if ($log != FALSE) {
            error_log(json_encode($stmt));
            error_log(json_encode($id));
        }
        return $count;
    }

    private function randomPassword() {
        $alphabet = 'ab*defghi%jklmno^pqr&v#xyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    public function getRandPass($password = ''){
        if(!empty($password)){
            $passWord = $password;
            $passEncrypt = password_hash($password, PASSWORD_DEFAULT);
        }else{
            $encryptPass = $passWord = GetCommomOperation::randomPassword();
            $passEncrypt = password_hash($encryptPass, PASSWORD_DEFAULT);
        }
        return array('encrypted' => $passEncrypt, 'decripted' => $passWord);
    }
    public function fileUpload($root, $file) { // fileUpload expects directory path and file properties(array)
        $dirctName = $root . date('Y') . '/' . date('M') . '/';
        if (!is_dir($dirctName)) {
            mkdir($dirctName, 0777, true);
        }
        $fileArr = $file['name'];
        $path = pathinfo($fileArr);
        $filename = $path['filename'];
        $extension = $path['extension'];
        $temp_name = $file['tmp_name'];
        $path_filename_extension = $dirctName . date('Ymd') . '_' . date('H') . '_' . $filename . "." . $extension;
        move_uploaded_file($temp_name, $path_filename_extension);
        return date('Y') . '/' . date('M') . '/' . date('Ymd') . '_' . date('H') . '_' . $filename . "." . $extension;
    }

}

function checkValueEmptyOrNot($value) { //check given value is empty or not
    if (!empty($value) == true && $value != NULL) {
        return $value;
    } else {
        return NULL;
    }
}
?>