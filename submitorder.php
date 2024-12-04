<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "regisnlogin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $preferred_date = mysqli_real_escape_string($conn, $_POST['date']);
    $preferred_time = mysqli_real_escape_string($conn, $_POST['time']);
    $cleaning_type = mysqli_real_escape_string($conn, $_POST['cleaning_type']);
    $duration = intval($_POST['duration']);
    $total_cost = floatval($_POST['total_cost']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_number = isset($_POST['payment_number']) ? mysqli_real_escape_string($conn, $_POST['payment_number']) : null;

    $sql = "INSERT INTO orders (user_id, name, email, phone, address, date, time, cleaning_type, duration, total_cost, pay_method, pay_num) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssidss", $user_id, $name, $email, $phone, $address, $preferred_date, $preferred_time, $cleaning_type, $duration, $total_cost, $payment_method, $payment_number);

    if ($stmt->execute()) {
        echo "<script>
                alert('Order submitted successfully!');
                window.location.href = 'dashboard_user.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>