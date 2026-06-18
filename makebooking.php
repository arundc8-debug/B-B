<!DOCTYPE HTML>
<html>
<head>
    <title>Make a booking</title>
<link rel="stylesheet"
href="https://code.jquery.com/ui/1.14.2/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="https://code.jquery.com/ui/1.14.2/jquery-ui.js"></script>

<script>
$(function () {
    $("#checkindate").datepicker({
        dateFormat: "yy-mm-dd"
    });

    $("#checkoutdate").datepicker({
        dateFormat: "yy-mm-dd"
    });

    $("#startdate").datepicker({
        dateFormat: "yy-mm-dd"
    });

    $("#enddate").datepicker({
        dateFormat: "yy-mm-dd"
    });
});

function RoomSearch() {
    var startDate = document.getElementById("startdate").value;
    var endDate = document.getElementById("enddate").value;

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("result").innerHTML = this.responseText;
        }
    };

    xhttp.open(
        "GET",
        "roomsearch.php?startDate=" + startDate + "&endDate=" + endDate,
        true
    );

    xhttp.send();
}
</script>
</head>
<body>

<?php
include "checksession.php";
checkUser();
loginStatus();

include "config.php";
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Book')) {
    $roomID = cleanInput($_POST['roomID']);
    $customerID = $_SESSION['userid'];
    $checkin = cleanInput($_POST['checkin']);
    $checkout = cleanInput($_POST['checkout']);
    $contact = cleanInput($_POST['contact']);
    $extras = cleanInput($_POST['extras']);

    $error = 0;
    $msg = "Error: ";

    if (empty($roomID)) {
        $error++;
        $msg .= "Invalid room ";
    }

    if (empty($checkin)) {
        $error++;
        $msg .= "Invalid check in ";
    }

    if (empty($checkout)) {
        $error++;
        $msg .= "Invalid check out ";
    }

    if ($error == 0) {
$query = "INSERT INTO booking 
          (roomID, customerID, checkin, checkout, contact, extras)
          VALUES (?,?,?,?,?,?)";

$stmt = mysqli_prepare($DBC, $query);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($DBC));
}

mysqli_stmt_bind_param($stmt, 'iissss', $roomID, $customerID, $checkin, $checkout, $contact, $extras);

if (mysqli_stmt_execute($stmt)) {
    echo "<h2>Booking added successfully</h2>";
} else {
    echo "<h2>Booking not added</h2>";
    echo mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);

    //     echo "<h2>Booking added successfully</h2>";
    // } else {
    //     echo "<h2>$msg</h2>" . PHP_EOL;
    // 
    } else {
    echo "<h2>$msg</h2>" . PHP_EOL;
}
}

$query = "SELECT roomID, roomname FROM room ORDER BY roomname";
$result = mysqli_query($DBC, $query);
?>

<h1>Make a booking</h1>

<h2>
    <a href="bookings.php">[Return to bookings]</a>
    <a href="index.php">[Return to main page]</a>
</h2>

<form method="POST" action="makebooking.php">

    <p>
        <label for="roomID">Room:</label>
        <select name="roomID" id="roomID" required>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="' . $row['roomID'] . '">' . $row['roomname'] . '</option>';
            }
            ?>
        </select>
    </p>

    <p>
        <label for="checkin">Check in:</label>
        <input
        id="checkindate"
        name="checkin"
        placeholder="YYYY-MM-DD"
        required>
        </p>

    <p>
        <label for="checkout">Check out:</label>
        <input  id="checkoutdate" name="checkout" placeholder="YYYY-MM-DD" required>
    </p>

    <p>
        <label for="contact">Contact number:</label>
        <input type="text" id="contact" name="contact" pattern="\([0-9]{3}\) [0-9]{3} [0-9]{4}" placeholder="(021) 123 4567" required>
    </p>

    <p>
        <label for="extras">Extras:</label>
        <input type="text" id="extras" name="extras" maxlength="200">
    </p>

    <p>
        <input type="submit" name="submit" value="Book">
    </p>

</form>
<hr>

<h2>Searching for room availability</h2>

<form>
    <p>
        <label>Start date:</label>
        <input
    id="startdate"
    name="startdate"
    placeholder="YYYY-MM-DD"
    required>
    </p>

    <p>
        <label>End date:</label>
        <input
    id="enddate"
    name="enddate"
    placeholder="YYYY-MM-DD"
    required>
    </p>

    <button type="button" onclick="RoomSearch()">Search availability</button>
</form>

<div id="result"></div>

<?php
mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>