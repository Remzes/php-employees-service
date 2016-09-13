<?php //begining of php code
 //declaring new variables
 $servername = "localhost";
 $username = "root";
 $password = "";
  //connecting to database
  $connSQL = mysqli_connect($servername, $username, $password, "project");
 $counter = 0;
 $checking = [];
 $fname = "";
 $lname = "";
 $date = "";
 $department = "";
 $ext;
 $selectID;
 $limit;
 $errorSearch;
 $recResult;

 //function to select all - EXPERIMENTAL FUNCTION - DON'T USE IN THE SYSTEM
 //function selectAll(){
 //global $servername;
 //global $username;
 //global $password;
 //global $connSQL;
 // $connSQL = mysqli_connect($servername, $username, $password, "project");
 //$sql = "SELECT * FROM employees";
 //$result = mysqli_query($connSQL,$sql);
 //  while($row = mysqli_fetch_assoc($result)) {
 //    foreach ($row as $name => $value){
 //      echo "$name: $value \n";
 //    }
 //   echo "<br/>";
 // }
 //}
 
 function look($data) { //clean variable
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
 }

  //variable to count right fields
  //array to check inputs 'true' or 'false'

 function looking() {
 global $counter;
 global $checking;
 global $fname; 
 global $lname;
 global $date;
 global $department;
 global $fnameError;
 global $lnameError;
 global $dateError;
 global $deptError;

   if(!(empty($_POST["fname"])))  {//check the 'fname' input
   $checking[0] = true;
   $fname = look($_POST["fname"]);
  } else {
   $checking[0] = false;
   $fnameError = "Required";
  }

  if(!(empty($_POST["lname"]))) { //check the 'lname' input
   $checking[1] = true;
   $lname = look($_POST["lname"]);
 } else {
   $checking[1] = false;
   $lnameError = "Required";
 }
  
 if(!($_POST["department"] == "FAIL")){ //check the "select box" input
  $checking[2] = true;
  $department = $_POST["department"];
  } else {
  $checking[2] = false;
  $deptError = "Choose option";
 }

 $regex = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_POST["date"]); //Regular expression for date
 if(!(empty($_POST["date"])) && $regex){
  $checking[3] = true;
  $date = $_POST["date"];
 } else if (!$regex){
  $dateError = "Date is not in right format";
  $checking[3] = false;
 }
 
 foreach($checking as $check){
     if ($check == true){
       $counter++;
     }
  }
}

function insertValues(){ //function to insert values into the Database
 global $fname;
 global $lname;
 global $date;
 global $department;
 global $connSQL;
 global $limit;
 global $selectID;
 switch ($department) {
  case "Exe":
    $limit = 30;
  break;
  case "Shp":
  case "Sal":
    $limit = 25;
  break;
  case "Mkt": 
    $limit = 15;
  break; 
  default: 
    $limit = "10";
  break; 
}
 global $ext; 
 $ext = 3456;
 $sql = "INSERT INTO employees (employee_id, first_name, last_name, dept_code, hire_date, credit_limit, phone_ext) VALUES ('', '$fname', '$lname' , '$department' ,'$date','$limit', '$ext')";
  if(!mysqli_query($connSQL, $sql)){
   echo "Error: " . mysqli_error($connSQL);
  } 
}   

 if(isset($_POST["submit"])){ //execute code for the first form after "submit" button
 looking(); 
 if ($counter == count($checking)) {
 insertValues();
 $stat = "SELECT MAX(employee_id) FROM employees";
 $selectID = mysqli_query($connSQL, $stat);
 while ($row = mysqli_fetch_assoc($selectID)){
  foreach ($row as $key => $value){
    $tryIt = $value;
  }
 }
 echo "<html>";
 echo "<head>";
 echo "<title>Employees</title>";
 echo "<style>";
 echo "* { padding: 0; margin: 0; font-size: 26px;}";
 echo " .lable { font-weight: bold; } ";
 echo ".wrapper {";
 echo "width: 600px;";
 echo "margin: 0 auto;";
 echo "padding: 0;";
 echo "}";
 echo ".titles {";
 echo "text-align: center;";
 echo "}";
 echo "</style>";
 echo "<link rel=\"stylesheet\" href=\"employees.css\">";
 echo "</head>";
 echo "<body>";
 echo "<div class=\"wrapper\" style=\"margin:0 auto; height: 800px;\">";
 echo "<div class=\"first-form\" style=\"margin:0 auto; float: none; width: 500px; height: 480px;\">";
 echo "<h1>Everything done successfully!</h1><br/>";
 echo "<h2 style=\"color: green; font-size: 32px;\">You inserted next values</h2><br/>";
 echo "<span class=\"lable\">Full name: </span>" . $fname . " " .$lname . "<br/>";
 echo "<span class=\"lable\">Department code: </span>" . $department . "<br/>";
 echo "<span class=\"lable\">Hired date: </span>" . $date . "<br/>";
 echo "<h2 style=\"color: green; font-size: 32px; margin-top: 20px;\">Derived attributes</h2><br/>";
 echo "<span class=\"lable\">Employee ID: </span>" . $tryIt . "<br/>";
 echo "<span class=\"lable\">Credit limit: </span>" . $limit . "<br/>";
 echo "<span class=\"lable\">Phone extension: </span>" . $ext . "<br/>";
 echo "</div>";
 echo "</div>";
 echo "</body>";
 echo "</html>";
 exit();
  }
 }

 function searchTable(){ //search the data in the database
 global $connSQL;
 global $recResult;
 global $errorSearch;
 $arrCnt = 0;
 $array = [];
 $i = 1;
 if (!empty($_POST["searching"]) && !empty($_POST["pattern"])){
 $opt = $_POST["pattern"];
 foreach($opt as $key => $value){
  $array[$arrCnt] = trim($value);
  $arrCnt++; 
 }
 $checkLength = count($array);
 if ($checkLength > 2 && !empty($_POST["searching"])) {
  $errorSearch = "Choose no more than 2 fields to check";
 } else {
 if ($checkLength == 1){ //if the user chose 1 field to search
 $search = trim($_POST["searching"]);
 $sqlQuery = "SELECT * FROM employees WHERE $array[0]='$search'";
  $resulted = mysqli_query($connSQL,$sqlQuery);
 $recResult .= "<table class=\"search-table\">";
 $recResult .= "
  <tr class=\"first-tr\"> 
   <td>Employee ID</td>
   <td>First Name</td>
   <td>Last Name</td>
   <td>Dep. Code</td>
   <td>Hire Date</td>
   <td>Credit Limit</td>
   <td>Phone Ext.</td>
   <td>Email Adress</td>
  </tr>
 ";
 $recResult .= "<tr>";
 while($row = mysqli_fetch_assoc($resulted)) {
  $fname = $row["first_name"];
  $lname = $row["last_name"];
  $dept = $row["dept_code"];
  foreach ($row as $key => $value){
     $recResult .= "<td>$value</td>";
}
  $recResult .= "<td style=\"width:160px;\"><a href=\"mailto:$fname-$lname@$dept.com\" target=\"_blank\">Send Email</a></td>";
  $recResult .= "</tr>";
  $memorize++;
  }
  $recResult .= "</table>";
 if ($memorize != 0){
  } else {
   $recResult .= "No relevant rows...Try again...";
 } 
} else if ($checkLength == 2) { //if user chose 2 fields to search
 $search = [];
 $search = split(",",$_POST["searching"]);
 $sqlQuery = "SELECT * FROM employees WHERE $array[0] = '$search[0]' AND $array[1] = '$search[1]'";
 $resulted = mysqli_query($connSQL, $sqlQuery);
 $recResult .= "<table class=\"search-table\">";
 $recResult .= "
  <tr class=\"first-tr\"> 
   <td>Employee ID</td>
   <td>First Name</td>
   <td>Last Name</td>
   <td>Dep. Code</td>
   <td>Hire Date</td>
   <td>Credit Limit</td>
   <td>Phone Ext.</td>
   <td>Email Adress</td>
  </tr>
 ";
 $recResult .= "<tr>";
 while($row = mysqli_fetch_assoc($resulted)) {
  $fname = $row["first_name"];
  $lname = $row["last_name"];
  $dept = $row["dept_code"];
 foreach ($row as $key => $value){
     $recResult .= "<td>$value</td>";
    }
    $recResult .= "<td style=\"width:160px;\"><a href=\"mailto:$fname-$lname@$dept.com\" target=\"_blank\">Send Email</a></td>";
  $recResult .= "</tr>";
  $memorize++;
  }
  $recResult .= "</table>";
  if ($memorize != 0){
   } else {
   $recResult = "<p style=\"text-align: center; background-color: rgba(0,0,0,0.5); font-size: 30px; color: red; clear:both; position: relative; top: 30px;\">No relevant rows...Try again...";
   }
  }
 }
}else{
  $errorSearch = "Please, complete all fields in this form";
 }
}
  if(isset($_POST["search"])){ //execute code when 'search' button is activated
 searchTable();
 }
;?>

<html>
<head>
<title>Employees</title>
<style>
</style>
<link rel="stylesheet" href="employees.css">
</head>
<body>
<div class="wrapper">
<div class="first-form">
<h1>Employee table</h1>
<form action="employees.php" method="POST">
<label>First name: </label><input type="text" name="fname" value="<?php echo $fname; ?>"><br/><span class="error"><?php echo $fnameError; ?></span><br/>
<label>Last name: </label><input type="text" name="lname" value="<?php echo $lname; ?>"><br/><span class="error"><?php echo $lnameError; ?></span><br/>
<label>Department: </label><select name="department" class="first-select">
<option value="FAIL">--Choose Department--</option>
<option value="Act">Accounting</option>
<option value="Mkt">Marketing</option>
<option value="Sal">Sales</option>
<option value="Shp">Shipping</option>
<option value="Exe">Executive</option>
</select></br><span class="error"><?php echo $deptError; ?></span><br/> 
<label>Date of hired: </label><input type="text" placeholder="YYYY-MM-DD" name="date" value="<?php echo $date; ?>"><span class="error"><br/><?php echo $dateError; ?></span><br/>
<input type="submit" value="Send it" name="submit">
</form>
</div>
<div class="second-form">
<form action="employees.php" method="POST">
<h1>Search Table</h2>
<p style="margin: 0;"><span style="color: red;">Note!</span> You ',' to search 2-field queries</p>
<span class="error-two"><?php echo $errorSearch; ?></span><br/>
<div class="inside-second-form">
<label>Choose Pattern to search</label><select name='pattern[]' multiple size="8" class="second-select">
<option value="FAIL" disabled>Choose Pattern</option>
<option value="employee_id">Employee ID</option>
<option value="first_name">First Name</option>
<option value="last_name">Last Name</option>
<option value="hire_date">Hire Date</option>
<option value="dept_code">Department</option>
<option value="phone_ext">Phone Ext.</option>
<option value="credit_limit">Credit Limit</option>
</select><br/>
<div class="type-div">
<label>Type here: </label><input type="text" name="searching"><br/>
<input type="submit" name="search" value="Search"><br/>
</div>
</div>
</form>
</div>
<?php echo $recResult; ?>
</body>
</html>
