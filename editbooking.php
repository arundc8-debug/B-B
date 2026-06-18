<!DOCTYPE HTML>
<html>
<head>
    <title>Edit_a_booking</title>
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];

    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>";
        exit;
    }
}

if (isset($_POST['submit']) and $_POST['submit'] == 'Update') {
    $id = cleanInput($_POST['id']);
    $roomID = cleanInput($_POST['roomID']);
    $checkin = cleanInput($_POST['checkin']);
    $checkout = cleanInput($_POST['checkout']);
    $contact = cleanInput($_POST['contact']);
    $extras = cleanInput($_POST['extras']);
    $review = cleanInput($_POST['review']);

    $query = "UPDATE booking 
              SET roomID=?, checkin=?, checkout=?, contact=?, extras=?, review=?
              WHERE bookingID=?";

    $stmt = mysqli_prepare($DBC, $query);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($DBC));
    }

    mysqli_stmt_bind_param($stmt, 'isssssi', $roomID, $checkin, $checkout, $contact, $extras, $review, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<h2>Booking updated</h2>";
}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];

$query = "SELECT * FROM booking WHERE bookingID=" . $id;
$result = mysqli_query($DBC, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($DBC));
}

$row = mysqli_fetch_assoc($result);

$roomquery = "SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomname";
$roomresult = mysqli_query($DBC, $roomquery);
?>

<h1>Edit a Booking</h1>

<a href="bookings.php">[Return to bookings Listing]</a>
<a href="index.php">[Return to main page]</a>

<form method="POST" action="editbooking.php">

    <h2>Editing booking for test</h2>

    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <p>
        <label for="roomID">Room (name,type,bed):</label>
        <select id="roomID" name="roomID" required>
            <?php
            while ($room = mysqli_fetch_assoc($roomresult)) {
                $selected = "";

                if ($room['roomID'] == $row['roomID']) {
                    $selected = "selected";
                }

                echo '<option value="' . $room['roomID'] . '" ' . $selected . '>';
                echo $room['roomname'] . ': ' . $room['roomtype'] . ', ' . $room['beds'] . ' bed';
                echo '</option>';
            }
            ?>
        </select>
    </p>

    <p>
        <label for="checkin">Check-in date:</label>
        <input
        id="checkindate"
        name="checkin"
        value="<?php echo $row['checkin']; ?>"
        placeholder="YYYY-MM-DD"
        required>
    </p>

    <p>
        <label for="checkout">Check-out date:</label>
        <input
        id="checkoutdate"
        name="checkout"
        value="<?php echo $row['checkout']; ?>"
        placeholder="YYYY-MM-DD"
        required>
    </p>

    <p>
        <label for="contact">Contact number:</label>
        <input
            id="contact"
            name="contact"
            value="<?php echo $row['contact']; ?>"
            placeholder="(###) ### ####"
            pattern="\(\d{3}\) \d{3} \d{4}"
            required
            maxlength="14">
    </p>

    <p>
        <label for="extras">Booking extras:</label>
        <textarea id="extras" name="extras" maxlength="500"><?php echo $row['extras']; ?></textarea>
    </p>

    <p>
        <label for="review">Room review:</label>
        <textarea id="review" name="review" maxlength="500"><?php echo $row['review']; ?></textarea>
    </p>

    <button type="submit" name="submit" value="Update">Update</button>
    <a href="bookings.php">[Cancel]</a>

</form>

<?php
mysqli_free_result($result);
mysqli_free_result($roomresult);
mysqli_close($DBC);
?>

</body>
</html>