<?php
ini_set('display_errors',1);
ini_set('display_setup_errors',1);
error_reporting(E_ALL);
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "regisnlogin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    // Mengambil data dari form login
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Mencegah SQL injection dengan menggunakan prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    ob_start();
    // Memeriksa apakah pengguna ada di database
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password yang sudah di-hash
        if ($password == $user['password']) {
            // Simpan informasi login di sesi
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'user') {
                header("Location: dashboard_user.html");  // Halaman User
            } elseif ($user['role'] == 'cleaner') {
                header("Location: dashboard_cleaner.html");  // Halaman Cleaner
            }
            exit;
        } else {
            echo "Password salah.";
        }
    } else {
        echo "Email atau role tidak ditemukan.";
    }



    $stmt->close();
}
if (isset($_POST['regis'])) {
    echo "halo";
    $user_name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$user_name', '$email', '$password', '$role')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        header ("location: tesreg.php");
        ob_end_flush();
        
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
// header('Location:services.html');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    
    <!-- Sign In Form -->
    <div class="bg-white shadow-md rounded-lg w-full sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/3 p-8" id="signInForm">
        <h2 class="text-3xl font-bold mb-4">Sign In</h2>
        <p class="mb-4">Use Your Account</p>
        <form action="tesreg.php" method="POST">
            <div class="mb-4">
                <input type="email" name="email" placeholder="Email" class="w-full p-3 border border-gray-300 rounded">
            </div>
            <div class="mb-4">
                <input type="password" name="password" placeholder="Password" class="w-full p-3 border border-gray-300 rounded">
            </div>
            <div class="mb-4">
                <a href="forgotpass.html" class="text-sm text-gray-600">Forgot Your Password?</a>
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="user" class="form-radio text-indigo-600">
                    <span class="ml-2">User</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" name="role" value="cleaner" class="form-radio text-indigo-600">
                    <span class="ml-2">Cleaner</span>
                </label>
            </div>
            <div class="mb-4">
                <button type="submit" name="login" class="w-full bg-blue-900 text-white p-3 rounded">SIGN IN</button>
            </div>
        </form>
        <div class="text-center mt-8">
            <h2 class="text-3xl font-bold mb-4">Hello, Friend!</h2>
            <p class="mb-4">Enter your personal details and start your journey with us.</p>
            <button class="bg-blue-900 text-white p-3 rounded" onclick="toggleForms()">SIGN UP</button>
        </div>
    </div>

    <!-- Sign Up Form -->
    <div class="bg-white shadow-md rounded-lg w-full sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/3 p-8 mt-8 hidden" id="signUpForm">
        <h2 class="text-3xl font-bold mb-4">Sign Up</h2>
        <p class="mb-4">Create Your Account</p>
        <form action="tesreg.php" method="POST">
            <div class="mb-4">
                <input type="text" name="username" placeholder="Username" class="w-full p-3 border border-gray-300 rounded">
            </div>
            <div class="mb-4">
                <input type="email" name="email" placeholder="Email" class="w-full p-3 border border-gray-300 rounded">
            </div>
            <div class="mb-4">
                <input type="password" name="password" placeholder="Password" class="w-full p-3 border border-gray-300 rounded">
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="user" class="form-radio text-indigo-600">
                    <span class="ml-2">User</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" name="role" value="cleaner" class="form-radio text-indigo-600">
                    <span class="ml-2">Cleaner</span>
                </label>
            </div>
            <div class="mb-4">
                <button type="submit" name="regis" class="w-full bg-blue-900 text-white p-3 rounded">SIGN UP</button>
            </div>
        </form>
        <div class="text-center mt-8">
            <h2 class="text-3xl font-bold mb-4">Welcome Back!</h2>
            <p class="mb-4">To keep connected with us, please login with your personal info.</p>
            <button class="bg-blue-900 text-white p-3 rounded" onclick="toggleForms()">SIGN IN</button>
        </div>
    </div>

    <script>
        function toggleForms() {
            const signInForm = document.getElementById('signInForm');
            const signUpForm = document.getElementById('signUpForm');
            signInForm.classList.toggle('hidden');
            signUpForm.classList.toggle('hidden');
        }
    </script>

</body>
</html>
