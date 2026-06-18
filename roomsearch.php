<?php
include "config.php";

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

$query = "SELECT *
          FROM room
          WHERE roomID NOT IN (
              SELECT roomID FROM booking
              WHERE checkin >= '$startDate'
              AND checkout <= '$endDate'
          )";

$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

if ($rowcount > 0) {
    echo "<h3>Available rooms</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Room</th><th>Type</th><th>Beds</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['roomname'] . "</td>";
        echo "<td>" . $row['roomtype'] . "</td>";
        echo "<td>" . $row['beds'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<h3>No rooms available</h3>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>