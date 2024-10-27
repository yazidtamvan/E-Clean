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

            header("location: dashboard.html");
           
            // Redirect ke halaman dashboard setelah login
            // echo "Password benar.";
           
            // exit;
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
<html>
<head>
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
    <div id="container" class="bg-white shadow-lg rounded-lg flex w-3/4 max-w-4xl">
        <div id="signInForm" class="w-1/2 p-8">
            <h2 class="text-3xl font-bold mb-2">Sign In</h2>
            <p class="text-gray-600 mb-6">Use Your Account</p>
            <form  action="tesreg.php" method="POST">
                <input type="email" name="email" placeholder="email" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <input type="password" name="password" placeholder="password" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <a href="#" class="text-gray-600 mb-6 block">Forgot Your Password</a>
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
                <button type="submit" name="login" id class="w-full bg-gray-800 text-white p-3 rounded">SIGN IN</button>
            </form>
        </div>
        <div id="signUpForm" class="w-1/2 p-8 hidden">
            <h2 class="text-3xl font-bold mb-2">Sign Up</h2>
            <p class="text-gray-600 mb-6">Create Your Account</p>
            <form  action="tesreg.php" method="POST">
                <input type="text" name="username" placeholder="username" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <input type="email" name="email"placeholder="email" class="w-full p-3 mb-4 border border-gray-300 rounded">
                <input type="password"name="password" placeholder="password" class="w-full p-3 mb-4 border border-gray-300 rounded">
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
        <div class="bg-blue-900 text-white p-8 rounded-r-lg flex flex-col justify-center items-center">
            <h2 class="text-3xl font-bold mb-2" id="greeting">Hello, Friend!</h2>
            <p class="mb-6 text-center" id="description">Enter your personal details and start your journey with us</p>
            <button id="toggleButton" class="bg-gray-800 text-white p-3 rounded" type="submit" name="regis">SIGN UP</button>
        </div>
    </div>

    <script>
        Headers
        const signInForm = document.getElementById('signInForm');
        const signUpForm = document.getElementById('signUpForm');
        const toggleButton = document.getElementById('toggleButton');
        const greeting = document.getElementById('greeting');
        const description = document.getElementById('description');

        toggleButton.addEventListener('click', () => {
            if (signInForm.classList.contains('hidden')) {
                signInForm.classList.remove('hidden');
                signUpForm.classList.add('hidden');
                toggleButton.textContent = 'SIGN UP';
                greeting.textContent = 'Hello, Friend!';
                description.textContent = 'Enter your personal details and start your journey with us';
            } else {
                signInForm.classList.add('hidden');
                signUpForm.classList.remove('hidden');
                toggleButton.textContent = 'SIGN IN';
                greeting.textContent = 'Welcome Back!';
                description.textContent = 'To keep connected with us please login with your personal info';
            }
        });
    </script>
</body>
</html>