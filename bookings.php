<!DOCTYPE HTML>
<html>
<head>
    <title>Current bookings</title>
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

$query = "SELECT *
          FROM booking, room, customer
          WHERE booking.roomID = room.roomID
          AND booking.customerID = customer.customerID
          ORDER BY booking.bookingID";

$result = mysqli_query($DBC, $query);

$rowcount = mysqli_num_rows($result);
?>

<h1>Current Bookings</h1>

<a href="makebooking.php">[Make a new booking]</a>
<a href="index.php">[Return to main page]</a>

<br>
<br>

<table border="2">
<tr>
    <th>Booking (room,dates)</th>
    <th>Customer</th>
    <th>Action</th>
</tr>

<?php
if ($rowcount > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
$id = $row['bookingID'];

echo '<td>' . $row['roomname'] . ', ' . $row['checkin'] . ', ' . $row['checkout'] . '</td>';

echo '<td>' . $row['lastname'] . ',' . $row['firstname'] . '</td>';

        echo '<td>
            <a href="bookingdetail.php?id=' . $id . '">[view]</a>
            <a href="editbooking.php?id=' . $id . '">[edit]</a>
            <a href="reviewbooking.php?id=' . $id . '">[Manage reviews]</a>
            <a href="deletebooking.php?id=' . $id . '">[delete]</a>
        </td>';

        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="3">No bookings found!</td></tr>';
}
?>
</table>

</body>
</html>