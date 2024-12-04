<?php
ini_set('display_errors',1);
ini_set('display_setup_errors',1);
error_reporting(E_ALL);
session_start();

// Connect to the database
include "database.php";
$login_error = '';
$register_error = '';

if (isset($_POST['login'])) {
    // Mengambil data dari form login
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek khusus untuk admin
    if ($email == 'admin@gmail.com' && $password == '12345678') {
        $_SESSION['role'] = 'admin';
        header("Location: admin.php");
        exit;
    }

    // Mencegah SQL injection dengan menggunakan prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Memeriksa apakah pengguna ada di database
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if ($password == $user['password']) {
            // Simpan informasi login di sesi
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect semua login ke order.php
            header("Location: dashboard_user.php");
            exit;
        } else {
            $login_error = "Password yang Anda masukkan salah";
        }
    } else {
        $login_error = "Email tidak ditemukan";
    }
    $stmt->close();
}

if (isset($_POST['regis'])) {
    $user_name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check if email already exists
    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        $register_error = "Email sudah terdaftar. Silakan gunakan email lain.";
    } else {
        // Default role menjadi 'user'
        $default_role = 'user';
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $user_name, $email, $password, $default_role);
        
        if ($stmt->execute()) {
            header("location: tesreg.php");
            exit;
        } else {
            $register_error = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        }
        $stmt->close();
    }
    $check_email->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Clean Login/Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f6f8f9 0%, #e5ebee 100%);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <!-- Sign In Form -->
        <div id="signInForm" class="<?php echo isset($_POST['regis']) ? 'hidden' : ''; ?> bg-white shadow-2xl rounded-xl p-8">
            <div class="text-center mb-6">
                <h1 class="text-4xl font-bold text-blue-900 mb-2">E-Clean</h1>
                <p class="text-gray-500">Sign in to manage your cleaning service</p>
            </div>

            <?php if (!empty($login_error)): ?>
                <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form action="tesreg.php" method="POST" class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-600">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" required 
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-600">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" required 
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit" name="login" 
                    class="w-full bg-blue-900 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Sign In
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="forgotpass.php" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
            </div>

            <div class="mt-6 text-center">
                <p class="text-gray-600">Don't have an account? 
                    <button onclick="toggleForm()" class="text-blue-600 hover:underline">Sign Up</button>
                </p>
            </div>
        </div>

        <!-- Sign Up Form -->
        <div id="signUpForm" class="<?php echo isset($_POST['regis']) ? '' : 'hidden'; ?> bg-white shadow-2xl rounded-xl p-8">
            <div class="text-center mb-6">
                <h1 class="text-4xl font-bold text-blue-900 mb-2">Create Account</h1>
                <p class="text-gray-500">Join E-Clean community today</p>
            </div>

            <?php if (!empty($register_error)): ?>
                <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
                    <?php echo $register_error; ?>
                </div>
            <?php endif; ?>

            <form action="tesreg.php" method="POST" class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-600">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="username" required 
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-600">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" required 
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-600">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" required 
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit" name="regis" 
                    class="w-full bg-blue-900 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Sign Up
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">Already have an account? 
                    <button onclick="toggleForm()" class="text-blue-600 hover:underline">Sign In</button>
                </p>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const signInForm = document.getElementById('signInForm');
            const signUpForm = document.getElementById('signUpForm');
            
            signInForm.classList.toggle('hidden');
            signUpForm.classList.toggle('hidden');
        }
    </script>
</body>
</html>