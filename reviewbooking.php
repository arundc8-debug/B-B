<!DOCTYPE HTML>
<html>
<head>
    <title>Room Review</title>
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

if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {
    $id = cleanInput($_POST['id']);
    $review = cleanInput($_POST['review']);

    $query = "UPDATE booking SET review=? WHERE bookingID=?";

    $stmt = mysqli_prepare($DBC, $query);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($DBC));
    }

    mysqli_stmt_bind_param($stmt, 'si', $review, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<h2>Review updated</h2>";
}

$query = "SELECT *
          FROM booking, customer
          WHERE booking.customerID = customer.customerID
          AND booking.bookingID = " . $id;

$result = mysqli_query($DBC, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($DBC));
}

$row = mysqli_fetch_assoc($result);
?>

<h1>Edit/add room review</h1>

<a href="bookings.php">[Return to bookings Listing]</a>
<a href="index.php">[Return to main page]</a>

<form method="POST" action="reviewbooking.php">

    <h2>Review made by <?php echo $row['firstname']; ?></h2>

    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <p>
        <label for="review">Room review:</label>
        <textarea
            id="review"
            name="review"
            minlength="5"
            maxlength="500"><?php echo $row['review']; ?></textarea>
    </p>

    <button type="submit" name="submit" value="Update">Update</button>

</form>

<?php
mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>