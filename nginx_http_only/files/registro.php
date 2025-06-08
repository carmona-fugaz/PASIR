<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';

    if (!empty($usuario) && !empty($contraseña)) {
        try {
            $db = new PDO('pgsql:host=postgres;port=5432;dbname=postgres', 'postgres', 'postgres');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->execute([
                ':username' => $usuario,
                ':password' => $contraseña
            ]);

            echo "<script>
                alert('Usuario registrado exitosamente: $usuario');
                window.location.href = 'login.php';
            </script>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
                echo "<script>alert('El usuario ya existe');</script>";
            } else {
                echo "<script>alert('Error al registrar usuario: " . $e->getMessage() . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Por favor, completa todos los campos');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #28a745;
            outline: none;
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .btn-register:hover {
            background-color: #218838;
        }
        .login-link {
            text-align: center;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Registro de Usuario</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>
            
            <button type="submit" class="btn-register">Registrarse</button>
        </form>
        
        <div class="login-link">
            <a href="login.php">Volver al Login</a>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $usuario = $_POST['usuario'] ?? '';
        $contraseña = $_POST['contraseña'] ?? '';
        
        if (!empty($usuario) && !empty($contraseña)) {
            // Aquí puedes agregar la lógica para guardar el usuario
            // Por ejemplo, guardar en base de datos
            
            echo "<script>
                alert('Usuario registrado exitosamente: $usuario');
                window.location.href = 'login.php';
            </script>";
        } else {
            echo "<script>alert('Por favor, completa todos los campos');</script>";
        }
    }
    ?>
</body>
</html>
