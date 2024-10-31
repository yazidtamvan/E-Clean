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
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div id="container" class="bg-white shadow-lg rounded-lg flex flex-col lg:flex-row w-full max-w-4xl">
        <!-- Form Login -->
        <div id="signInForm" class="w-full lg:w-1/2 p-8">
            <h2 class="text-3xl font-bold mb-4">Sign In</h2>
            <p class="text-gray-600 mb-6">Use Your Account</p>
            <form action="tesreg.php" method="POST">
                <input type="email" name="email" placeholder="Email" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <input type="password" name="password" placeholder="Password" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <a href="forgotpass.html" class="text-gray-600 mb-6 block">Forgot Your Password?</a>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="role" value="user" class="form-radio text-blue-600">
                        <span class="ml-2">User</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" name="role" value="cleaner" class="form-radio text-blue-600">
                        <span class="ml-2">Cleaner</span>
                    </label>
                </div>
                <button type="submit" name="login" class="w-full bg-gray-800 text-white p-3 rounded">SIGN IN</button>
            </form>
        </div>

        <!-- Form Signup -->
        <div id="signUpForm" class="w-full lg:w-1/2 p-8 hidden">
            <h2 class="text-3xl font-bold mb-4">Sign Up</h2>
            <p class="text-gray-600 mb-6">Create Your Account</p>
            <form action="tesreg.php" method="POST">
                <input type="text" name="username" placeholder="Username" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <input type="email" name="email" placeholder="Email" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <input type="password" name="password" placeholder="Password" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="role" value="user" class="form-radio text-blue-600">
                        <span class="ml-2">User</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" name="role" value="cleaner" class="form-radio text-blue-600">
                        <span class="ml-2">Cleaner</span>
                    </label>
                </div>
                <button type="submit" name="regis" class="w-full bg-gray-800 text-white p-3 rounded">SIGN UP</button>
            </form>
        </div>

        <!-- Greeting & Toggle Section -->
        <div class="bg-blue-900 text-white p-8 flex flex-col justify-center items-center w-full lg:w-1/2">
            <h2 class="text-3xl font-bold mb-4" id="greeting">Hello, Friend!</h2>
            <p class="mb-6 text-center" id="description">Enter your personal details and start your journey with us.</p>
            <button id="toggleButton" class="bg-gray-800 text-white p-3 rounded">SIGN UP</button>
        </div>
    </div>

    <script>
        const signInForm = document.getElementById('signInForm');
        const signUpForm = document.getElementById('signUpForm');
        const toggleButton = document.getElementById('toggleButton');
        const greeting = document.getElementById('greeting');
        const description = document.getElementById('description');

        toggleButton.addEventListener('click', () => {
            signInForm.classList.toggle('hidden');
            signUpForm.classList.toggle('hidden');
            if (signUpForm.classList.contains('hidden')) {
                toggleButton.textContent = 'SIGN UP';
                greeting.textContent = 'Hello, Friend!';
                description.textContent = 'Enter your personal details and start your journey with us.';
            } else {
                toggleButton.textContent = 'SIGN IN';
                greeting.textContent = 'Welcome Back!';
                description.textContent = 'To keep connected with us, please login with your personal info.';
            }
        });
    </script>
</body>
</html>
