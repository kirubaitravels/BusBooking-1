<?php
/*
Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password)
*/

// role id take it from session
$role_id = "943340196v";

$link = mysqli_connect("localhost", "root","","busticketing");

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Escape user inputs for security
$route_name = $_POST['route_name'];
$start_station = mysqli_real_escape_string($link, $_POST['start_station']);
$date = mysqli_real_escape_string($link, $_POST['date']);
$time = $_POST['time'];
$seats = $_POST['seats'];

/**
echo $route_name;
echo "<br>";
echo $start_station;
echo "<br>";
echo $date;
echo "<br>";
echo $time;
echo "<br>";
echo $seats;
echo "<br>";
*/

// attempt search query execution - search for the required bus
$sql = "SELECT IFNULL((SELECT seats_available FROM active_busses WHERE route_no='$route_name' AND start_loc= '$start_station' AND date='$date' AND time='$time'),'not found')";
$key = "IFNULL((SELECT seats_available FROM active_busses WHERE route_no='$route_name' AND start_loc= '$start_station' AND date='$date' AND time='$time'),'not found')";
$result = mysqli_query( $link,$sql) or die('Could not look up user information; ' . mysqli_error($link));
$row  = mysqli_fetch_array($result,MYSQLI_ASSOC);


if($row[$key]=='not found'){
    echo '<script type="text/javascript">alert("Unfortunately there is not a bus available for your need."); </script>';
    echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
}
if($row[$key] < $seats){
    echo '<script type="text/javascript">alert("Unfortunately there is not a bus available for your need."); </script>';
    echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
}
else{
    // bus is available and need to extract the bus number
    $sql2 = "SELECT bus_id FROM active_busses WHERE route_no='$route_name' AND start_loc='$start_station' AND date='$date' AND time='$time'";
    $result2 = mysqli_query( $link,$sql2) or die('Could not look up user information; ' . mysqli_error($link));
    $row2  = mysqli_fetch_array($result2,MYSQLI_ASSOC);

    $bus_id = $row2['bus_id'];
    $sql_insert = "INSERT INTO bookings (role_id , route_id , date , time , seats , bus_id ) 
    VALUES ('$role_id', '$route_name', '$date', '$time', '$seats','$bus_id')";
    $result3 = mysqli_query( $link,$sql_insert) or die('Could not look up user information; ' . mysqli_error($link));

    // update active bookings table
    $sql_update = "UPDATE active_busses SET seats_available = seats_available - $seats WHERE route_no='$route_name' AND date='$date' AND time='$time' AND seats_available > 0  ;";
    $result4 = mysqli_query( $link,$sql_update) or die('Could not look up user information; ' . mysqli_error($link));

    echo "<div align='center'><h2>Your booking has been succesfully placed. Your bus number is : </h2></div>";
    echo "<div align='center'><h1>".$bus_id."</h1></div>" ;
    echo "<div align='center'><h3> Your NIC number is ".$role_id.". Bring your NIC as a proof of booking.</h3></div>" ;
}


/*
$key = "IFNULL((SELECT role FROM login WHERE username='$username' AND password= '$password'),'not found')";

$sql1 = "SELECT role_id FROM login WHERE username='$username' ";
$result1 = mysqli_query( $link,$sql1) or die('Could not look up user information; ' . mysqli_error($link));
$row1  = mysqli_fetch_array($result1,MYSQLI_ASSOC);

$role_id =  $row1['role_id'];

session_start();
$_SESSION['role_id'] = $role_id;

if ($row[$key] == 'cus') {
    readfile('customer/customerhomepage.php');
}
if($row[$key]=='adm'){
    readfile('Administrator/adminhomepage.php');
}
if($row[$key]=='ope'){
    readfile('operator/operatorhome.php');
}
else if($row[$key] == 'not found'){
    echo "Your username or password is wrong. Try again" ;
}
*/
// close connection
mysqli_close($link);
?>