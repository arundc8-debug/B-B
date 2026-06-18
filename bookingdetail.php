<!DOCTYPE HTML>
<html>
<head>
    <title>Booking details view</title>
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];

    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>";
        exit;
    }
}

$query = "SELECT *
          FROM booking, room, customer
          WHERE booking.roomID = room.roomID
          AND booking.customerID = customer.customerID
          AND booking.bookingID = " . $id;
$result = mysqli_query($DBC, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($DBC));
}

$rowcount = mysqli_num_rows($result);
?>

<h1>Booking details view</h1>

<h2>
    <a href="bookings.php">[Return to bookings]</a>
    <a href="index.php">[Return to main page]</a>
</h2>

<?php
if ($rowcount > 0) {
    $row = mysqli_fetch_assoc($result);

    echo "<fieldset><legend>Booking Detail #$id</legend><dl>";

    echo "<dt>Room name: </dt>";
    echo "<dd>" . $row['roomname'] . "</dd>";

    echo "<dt>Check in: </dt>";
    echo "<dd>" . $row['checkin'] . "</dd>";

    echo "<dt>Check out: </dt>";
    echo "<dd>" . $row['checkout'] . "</dd>";

    echo "<dt>Customer: </dt>";
    echo "<dd>" . $row['firstname'] . " " . $row['lastname'] . "</dd>";

    echo "<dt>Contact number: </dt>";
    echo "<dd>" . $row['contact'] . "</dd>";

    echo "<dt>Booking extras: </dt>";
    echo "<dd>" . $row['extras'] . "</dd>";

    echo "<dt>Room review: </dt>";
    echo "<dd>" . $row['review'] . "</dd>";

    echo "</dl></fieldset>";
} else {
    echo "<h2>No booking found!</h2>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>