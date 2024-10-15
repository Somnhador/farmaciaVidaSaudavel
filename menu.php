<?php
session_start();

$tipoDeConta = isset($_SESSION['tipoDeConta']) ? $_SESSION['tipoDeConta'] : null;

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal - Farmácia Vida Saudável</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }

        .navbar {
            color: #ffffff;
            background-color: #3e3e3e;
        }

        .navbar-brand,
        .nav-link {
            color: #ffffff !important;
        }

        .nav-link:hover {
            color: #8cadc2 !important;
        }

        #hoverSair1:hover,
        #hoverSair2:hover {
            color: #ad1212 !important;
        }

        .container {
            color: #ffffff;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
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
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Farmácia Vida Saudável</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="venda.php"><i class="fas fa-shopping-cart"></i> Realizar Venda</a>
                    </li>
                    <?php if ($tipoDeConta == "admin") : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cadastrarMed.php"><i class="fas fa-plus"></i> Gerenciar Medicamentos</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="?logout=true" id="hoverSair2"><i class="fas fa-sign-out-alt" id="hoverSair1"></i> Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Bem-vindo ao Sistema da Farmácia Vida Saudável</h2>
        <p class="text-center">Para que a opção de "Gerenciar Medicamentos" apareça, logue como administrador!</p>
    </div>

    <div class="footer">
        <footer>Todos os direitos reservados ©</footer>
    </div>
</body>

</html>