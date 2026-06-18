<!DOCTYPE HTML>
<html>
<head>
    <title>Delete booking</title>
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];

    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>";
        exit;
    }
}

if (isset($_POST['submit']) and $_POST['submit'] == 'Delete') {
    $id = cleanInput($_POST['id']);

    $query = "DELETE FROM booking WHERE bookingID=?";
    $stmt = mysqli_prepare($DBC, $query);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($DBC));
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<h2>Booking deleted</h2>";
}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];

$query = "SELECT *
          FROM booking, room
          WHERE booking.roomID = room.roomID
          AND booking.bookingID = " . $id;

$result = mysqli_query($DBC, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($DBC));
}

$rowcount = mysqli_num_rows($result);
?>

<h1>Booking preview before deletion</h1>

<a href="bookings.php">[Return to bookings Listing]</a>
<a href="index.php">[Return to main page]</a>

<br>
<br>

<?php
if ($rowcount > 0) {
    $row = mysqli_fetch_assoc($result);
?>

<fieldset>
    <legend>Booking details #<?php echo $id; ?></legend>
    <dl>
        <dt>Room name:</dt>
        <dd><?php echo $row['roomname']; ?></dd>

        <dt>Checkin date:</dt>
        <dd><?php echo $row['checkin']; ?></dd>

        <dt>Checkout date:</dt>
        <dd><?php echo $row['checkout']; ?></dd>
    </dl>
</fieldset>

<br>

<form method="POST" action="deletebooking.php">
    <h2>Are you sure you want to delete this booking?</h2>

    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <button type="submit" name="submit" value="Delete">Delete</button>
    <a href="bookings.php">[Cancel]</a>
</form>

<?php
} else {
    echo "<h2>No booking found!</h2>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>