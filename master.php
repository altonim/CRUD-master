<?php
// - 👋 Hi, I’m @altonim
// - 👀 I’m interested in the development of new technologies
// - 🌱 I’m currently learning how to create artificial neurons
// - 💞️ I’m looking to collaborate with anyone that is interested in what is interested in
// - 📫 How to reach me -> info@altonim.co.ke

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "chester";
$conn = mysqli_connect($dbServername, $dbUsername,  $dbPassword, $dbName);
if(!$conn){
    echo "DB Error";
    exit();
}
// This will create or add tables or columns that do not exist as per query
// 👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋👋
define("OVERRIGHT_TABLES", true);
//Read oparaions ----------------------------------------->>>>>>>>>>>>>Read From Data>>>>>>>>>>>>>>>
function getColumnNames($table){
  global $conn;
  $sql = 'DESCRIBE '.$table;
  $result = mysqli_query($conn, $sql);
  $rows = array();
  while($row = mysqli_fetch_assoc($result)){
    $rows[] = $row['Field'];
  }
  return $rows;
}

function createStartTable($table)
{
    global $conn;
    // sql to create table
    $sql = "CREATE TABLE $table(
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id TEXT NOT NULL,
            activity_id TEXT NOT NULL,
            status TEXT,
            last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
    if(mysqli_query($conn, $sql)){
      return true;
    }else{
      return false;
    }
}

// ---------------------------------------------------------------------------------------------

// Usage👀👀👀👀👀👀👀👀
// echo getColumnNames('users')[0];
function getSingleRowData($table, $selects, $row, $activity_id, $return){
    global $conn;
    $data = "";
    $query = "SELECT $selects FROM $table WHERE $row = '$activity_id' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
       while($row = mysqli_fetch_array($result)){
            $data = $row[$return];
       }
    }
    return $data;
}

// Usage
// echo getSingleRowData('users', '*', 'id', '7', 'datetime');

// ----------------------------------------------------------------------------------------------
// 👀👀👀👀👀👀👀👀👀👀
// Get all data from a table and parsing an expression for filter
function getData($table, $selects, $expression){
    global $conn;
    $data = array();
    $query = "SELECT $selects FROM $table $expression";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
       while($row = mysqli_fetch_array($result)){
            $data[] = $row;
       }
    }
    return $data;
}
// Usage
// echo var_dump(getData('users', '*', 'ORDER BY id DESC'));

// ----------------------------------------------------------------------------------------------

// insert operations👀👀👀👀👀👀👀👀👀
function insertData($table, $rows, $values){
    global $conn;
    $other = array(); 
    $query = ""; 
    $in_rows = "";
    $in_values="";
    $count = count($values);
    $a = 0;
    $b = 0;
    $inline_count = ($count-1);
    while($a<$count){
        $column = $rows[$a];
        $query_check_column = mysqli_query($conn, "SELECT $column FROM $table");
        if($query_check_column){
            //Column exists
            // echo "Exist <br>";
        }else{
            //Column doesn't exists
            // echo "Does not exist <br>";
            if(OVERRIGHT_TABLES){
                // check if user table exists
                if(!mysqli_query($conn, "SHOW TABLES LIKE $table" )){
                    createStartTable($table);
                }
                // Add the missing column
                $column = str_replace(" ", "_", $column);
                mysqli_query($conn, "ALTER TABLE `{$table}` ADD `{$column}` TEXT");
            }
        }
        if($a == $inline_count){
            $in_rows .= $rows[$a];
        }
        else{
            $in_rows .= $rows[$a].", ";
        }
        $a++;
    }
    while($b<$count){
        if($b == $inline_count){
            $in_values .= "'".$values[$b]."'";
        }else{
            $in_values .= "'".$values[$b]."'".", ";
        }
        $b++;
    }
    // run the query
    $query = "INSERT INTO $table ($in_rows) VALUES ($in_values)";
    $insert = mysqli_query($conn, $query);
    if($insert){
        return $data = true; 
    }else{
        return $data = false;
    }
}

// Usage
// echo insertData('test', array("username", "password", "email", "town", "phone", "static data"), array("maxwell", "pass123", "maxwellyoung254@gmail.com", "Nairobi", "+254757435215", ""));

// ---------------------------------------------------------------------------------------------------

// Update data👀👀👀👀👀
function updateData($rows, $values, $table, $target, $id)
{
    global $conn;
    $other = array(); 
    $query = "";
    $b = 0;
    $expression_query = "";
    $in_values = "";
    $count = count($values); 
    $inline_count = ($count-1);
    while($b<$count){
        if($b == $inline_count){
            $in_values .= $rows[$b]."='".$values[$b]."'";
        }else{
            $in_values .= $rows[$b]."='".$values[$b]."'".", ";
        }
        $b++;
    }
    $expression_query = $in_values;
    // run the query
    $query = "UPDATE $table SET $expression_query WHERE $target = '$id'";
    $insert = mysqli_query($conn, $query);
    if($insert){
        return $data = true;
    }else{
        return $data = false;
    }
}
// Function usage
updateData(array("user_id", "activity_id"), array("1", "54985"), "test", "id", "1");
