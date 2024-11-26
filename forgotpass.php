<?php
session_start();
// Koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "regisnlogin";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fungsi generate token reset
function generateResetToken($length = 50) {
    return bin2hex(random_bytes($length));
}

$error_message = '';
$success_message = '';

// Proses permintaan reset password
if (isset($_POST['reset_request'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Cek email di database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Generate token reset
        $reset_token = generateResetToken();
        $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Simpan token ke database
        $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $update_stmt->bind_param("sss", $reset_token, $token_expiry, $email);
        
        if ($update_stmt->execute()) {
            // Simulasi pengiriman email (karena ini contoh local)
            $reset_link = "http://localhost/reset_password.php?token=" . $reset_token;
            $success_message = "Link reset password telah dikirim ke email Anda. Silakan periksa inbox.";
            
            // Dalam implementasi nyata, Anda akan mengirim email dengan link reset
            // Contoh menggunakan PHP mail() atau library seperti PHPMailer
            
            // Debug: Tampilkan link reset (hapus ini di production)
            $success_message .= "<br>Debug Link Reset: <a href='$reset_link'>Reset Password</a>";
        } else {
            $error_message = "Terjadi kesalahan. Silakan coba lagi.";
        }
        $update_stmt->close();
    } else {
        $error_message = "Email tidak ditemukan dalam sistem.";
    }
    $stmt->close();
}

// Proses reset password
if (isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak sesuai.";
    } else {
        // Cek token valid dan belum expired
        $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update password baru
            $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
            $update_stmt->bind_param("ss", $new_password, $token);
            
            if ($update_stmt->execute()) {
                $success_message = "Password berhasil direset. Silakan login.";
            } else {
                $error_message = "Gagal mereset password.";
            }
            $update_stmt->close();
        } else {
            $error_message = "Token reset tidak valid atau sudah expired.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Clean - Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .error-message {
            @apply bg-red-500 text-white font-semibold px-4 py-3 rounded relative mb-4 text-center;
        }
        .success-message {
            @apply bg-green-500 text-white font-semibold px-4 py-3 rounded relative mb-4 text-center;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <?php 
        // Cek apakah ada token di URL untuk halaman reset password
        $reset_token = isset($_GET['token']) ? $_GET['token'] : '';
        
        if (empty($reset_token)): 
        ?>
            <!-- Form Permintaan Reset Password -->
            <h2 class="text-2xl font-bold mb-6 text-center">Lupa Password</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block mb-2">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        placeholder="Masukkan email Anda" 
                        class="w-full p-3 border rounded"
                    >
                </div>
                <button 
                    type="submit" 
                    name="reset_request" 
                    class="w-full bg-blue-900 text-white p-3 rounded hover:bg-blue-800 transition"
                >
                    Kirim Link Reset Password
                </button>
            </form>
            <div class="text-center mt-4">
                <a href="tesreg.php" class="text-blue-600 hover:underline">
                    Kembali ke Halaman Login
                </a>
            </div>
        
        <?php else: ?>
            <!-- Form Reset Password -->
            <h2 class="text-2xl font-bold mb-6 text-center">Reset Password Baru</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($reset_token); ?>">
                <div>
                    <label for="new_password" class="block mb-2">Password Baru</label>
                    <input 
                        type="password" 
                        name="new_password" 
                        required 
                        placeholder="Masukkan password baru" 
                        class="w-full p-3 border rounded"
                    >
                </div>
                <div>
                    <label for="confirm_password" class="block mb-2">Konfirmasi Password</label>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        required 
                        placeholder="Konfirmasi password baru" 
                        class="w-full p-3 border rounded"
                    >
                </div>
                <button 
                    type="submit" 
                    name="reset_password" 
                    class="w-full bg-blue-900 text-white p-3 rounded hover:bg-blue-800 transition"
                >
                    Reset Password
                </button>
            </form>
            <div class="text-center mt-4">
                <a href="tesreg.php" class="text-blue-600 hover:underline">
                    Kembali ke Halaman Login
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>