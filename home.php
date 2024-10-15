<?php
session_start();

$tipoDeConta = isset($_SESSION['tipoDeConta']) ? $_SESSION['tipoDeConta'] : null;
try {
    require 'conexao.php';

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Falha na conexão: " . $e->getMessage());
}

function alert($msg)
{
    echo "<script type='text/javascript'>alert('$msg');</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $nome = $_POST['nome'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipoDeConta = $_POST['tipoDeConta'];

    if (strlen($nome) > 20 || strlen($senha) > 255) {
        echo "<script>alert('Nome ou senha muito grande!'); history.replaceState(null, '', 'home.php');</script>;";
    } else {
        $stmt = $conn->prepare("SELECT nome FROM login WHERE nome = :nome");
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Nome de usuário já existe!'); history.replaceState(null, '', 'home.php');</script>;";
        } else {
            $stmt = $conn->prepare("INSERT INTO login (nome, senha, tipoDeConta) VALUES (:nome, :senha, :tipoDeConta)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':senha', $senha);
            $stmt->bindParam(':tipoDeConta', $tipoDeConta);

            if ($stmt->execute()) {
                echo "<script>alert('Cadastro realizado com sucesso!'); history.replaceState(null, '', 'home.php');</script>;";
            } else {
                echo "<script>alert('Erro ao cadastrar!'); history.replaceState(null, '', 'home.php');</script>;";
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT * FROM login WHERE nome = :nome");
    $stmt->bindParam(':nome', $nome);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if (password_verify($senha, $user['senha'])) {
            $_SESSION['tipoDeConta'] = $user['tipoDeConta'];
            header("Location: menu.php");
            exit();
        } else {
            echo "<script>alert('Nome de usuário ou senha incorretos!'); history.replaceState(null, '', 'home.php');</script>;";
        }
    } else {
        echo "<script>alert('Nome de usuário ou senha incorretos!'); history.replaceState(null, '', 'home.php');</script>;";
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login e Cadastro - Farmácia Vida Saudável</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        #cardd {
            font-size: larger;
            border-radius: 10px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #3e3e3e;
            color: rgb(255, 255, 255);
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body background="pil.png">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3" id="cardd">
                <h2 class="text-center">Login e Cadastro</h2>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Cadastro</button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="loginNome" class="form-label">Nome de Usuário</label>
                                <input type="text" class="form-control" id="loginNome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginSenha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="loginSenha" name="senha" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Entrar</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="registerNome" class="form-label">Nome de Usuário</label>
                                <input type="text" class="form-control" id="registerNome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="registerSenha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="registerSenha" name="senha" required>
                            </div>
                            <div class="mb-3">
                                <label for="registerTipoConta" class="form-label">Tipo de Conta</label>
                                <select class="form-select" id="registerTipoConta" name="tipoDeConta" required>
                                    <option value="funcionario">Funcionário</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" name="register" class="btn btn-success w-100">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <footer>Todos os direitos reservados ©</footer>
    </div>
</body>

</html>