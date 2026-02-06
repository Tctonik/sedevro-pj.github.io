<?php
session_start();

// Учетные данные
$correct_username = 'Creator';
$correct_password_hash = password_hash('Isayafg@45', PASSWORD_DEFAULT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $correct_username && password_verify($password, $correct_password_hash)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Неверные учетные данные';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в аккаунт</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-form {
            background: rgba(30, 30, 30, 0.9);
            padding: 3rem;
            border-radius: 16px;
            border: 1px solid rgba(100, 255, 218, 0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .login-form h2 {
            margin-bottom: 2rem;
            color: #64ffda;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #a0a0a0;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #64ffda;
        }
        
        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: #64ffda;
            color: #0a0a0a;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        
        .submit-btn:hover {
            opacity: 0.9;
        }
        
        .error {
            color: #ff6b6b;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form method="POST" class="login-form">
            <h2>Вход в аккаунт</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Логин:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="submit-btn">Войти</button>
        </form>
    </div>
</body>
</html>
