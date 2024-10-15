<?php
session_start();
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


function cadastrarMedicamento($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
        $nomeMed = htmlspecialchars($_POST['nomeMed']);
        $precoMedUni = $_POST['precoMedUni'];
        $quantidadeMedEstoque = $_POST['quantidadeMedEstoque'];
        $categoriaMed = htmlspecialchars($_POST['categoriaMed']);
        $validadeMed = $_POST['validadeMed'];


        if (strlen($nomeMed) > 100 || strlen($categoriaMed) > 100) {
            echo "<script>alert('Nome ou categoria do medicamento muito grande'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
            return;
        }


        $stmt = $conn->prepare("SELECT nomeMed FROM cadastroMedicamento WHERE nomeMed = :nomeMed");
        $stmt->bindParam(':nomeMed', $nomeMed);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Medicamento já cadastrado!'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
        } else {
            $stmt = $conn->prepare("INSERT INTO cadastroMedicamento (nomeMed, precoMedUni, quantidadeMedEstoque, categoriaMed, validadeMed) VALUES (:nomeMed, :precoMedUni, :quantidadeMedEstoque, :categoriaMed, :validadeMed)");
            $stmt->bindParam(':nomeMed', $nomeMed);
            $stmt->bindParam(':precoMedUni', $precoMedUni);
            $stmt->bindParam(':quantidadeMedEstoque', $quantidadeMedEstoque);
            $stmt->bindParam(':categoriaMed', $categoriaMed);
            $stmt->bindParam(':validadeMed', $validadeMed);

            if ($stmt->execute()) {

                echo "<script>alert('Medicamento cadastrado com sucesso!'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
            } else {
                echo "<script>alert('Erro ao cadastrar medicamento!'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
            }
        }
    }
}


function editarMedicamento($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar'])) {
        $idMedicamento = $_POST['idMedicamento'];
        $nomeMed = htmlspecialchars($_POST['nomeMed']);
        $precoMedUni = $_POST['precoMedUni'];
        $quantidadeMedEstoque = $_POST['quantidadeMedEstoque'];
        $categoriaMed = htmlspecialchars($_POST['categoriaMed']);
        $validadeMed = $_POST['validadeMed'];


        if (strlen($nomeMed) > 100 || strlen($categoriaMed) > 100) {
            echo "<script>alert('Nome ou categoria do medicamento muito grande'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
            return;
        }


        $stmt = $conn->prepare("SELECT nomeMed FROM cadastroMedicamento WHERE nomeMed = :nomeMed AND id_medicamento != :idMedicamento");
        $stmt->bindParam(':nomeMed', $nomeMed);
        $stmt->bindParam(':idMedicamento', $idMedicamento);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Medicamento com o mesmo nome já cadastrado!'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
        } else {
            $stmt = $conn->prepare("UPDATE cadastroMedicamento SET nomeMed = :nomeMed, precoMedUni = :precoMedUni, quantidadeMedEstoque = :quantidadeMedEstoque, categoriaMed = :categoriaMed, validadeMed = :validadeMed WHERE id_medicamento = :idMedicamento");
            $stmt->bindParam(':nomeMed', $nomeMed);
            $stmt->bindParam(':precoMedUni', $precoMedUni);
            $stmt->bindParam(':quantidadeMedEstoque', $quantidadeMedEstoque);
            $stmt->bindParam(':categoriaMed', $categoriaMed);
            $stmt->bindParam(':validadeMed', $validadeMed);
            $stmt->bindParam(':idMedicamento', $idMedicamento);

            if ($stmt->execute()) {
                echo "<script>alert('Medicamento editado com sucesso!'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
            } else {
                echo "<script>alert('Erro ao editar medicamento!'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
            }
        }
    }
}


function excluirMedicamento($conn)
{
    if (isset($_GET['id_medicamento'])) {
        $idMedicamento = $_GET['id_medicamento'];

        $stmt = $conn->prepare("DELETE FROM cadastroMedicamento WHERE id_medicamento = :id_medicamento");
        $stmt->bindParam(':id_medicamento', $idMedicamento);

        if ($stmt->execute()) {
            echo "<script>alert('Medicamento excluído com sucesso!'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
        } else {
            echo "<script>alert('Erro ao excluir medicamento!'); history.replaceState(null, '', 'cadastrarMed.php');</script>;";
        }
    }
}


cadastrarMedicamento($conn);
editarMedicamento($conn);
excluirMedicamento($conn);


$searchTerm = isset($_POST['search']) ? htmlspecialchars($_POST['search']) : '';
$orderBy = isset($_POST['order_by']) ? $_POST['order_by'] : 'nomeMed';

$query = "SELECT * FROM cadastroMedicamento WHERE nomeMed LIKE :searchTerm ORDER BY $orderBy ASC";
$stmt = $conn->prepare($query);
$searchWildcard = "%$searchTerm%";
$stmt->bindParam(':searchTerm', $searchWildcard);
$stmt->execute();
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$conn = null;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Medicamentos - Sistema de Farmácia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            padding: 20px;
        }

        #borda,
        .form-container {
            font-size: larger;
            border-radius: 10px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #3e3e3e;
            color: rgb(255, 255, 255);
            width: 100%;
        }
    </style>
</head>

<body background="pil.png">
    <div class="container mt-5">
        <h2 class="text-center">Gerenciamento de Medicamentos</h2>

        <div class="text-center mb-4">
            <a href="menu.php" class="btn btn-primary">Ir para o Menu</a>
        </div>

        <div class="form-container mb-4">
            <h3>Cadastrar Medicamento</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="nomeMed" class="form-label">Nome do Medicamento</label>
                    <input type="text" class="form-control" id="nomeMed" name="nomeMed" required>
                </div>
                <div class="mb-3">
                    <label for="precoMedUni" class="form-label">Preço Unitário</label>
                    <input type="number" class="form-control" id="precoMedUni" name="precoMedUni" required step="0.01">
                </div>
                <div class="mb-3">
                    <label for="quantidadeMedEstoque" class="form-label">Quantidade em Estoque</label>
                    <input type="number" class="form-control" id="quantidadeMedEstoque" name="quantidadeMedEstoque" required>
                </div>
                <div class="mb-3">
                    <label for="categoriaMed" class="form-label">Categoria</label>
                    <input type="text" class="form-control" id="categoriaMed" name="categoriaMed" required>
                </div>
                <div class="mb-3">
                    <label for="validadeMed" class="form-label">Data de Validade</label>
                    <input type="date" class="form-control" id="validadeMed" name="validadeMed" required>
                </div>
                <button type="submit" name="cadastrar" class="btn btn-success">Cadastrar Medicamento</button>
            </form>
        </div>

        <div id="borda">
            <h3>Lista de Medicamentos</h3>

            <form method="POST" class="mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="search" placeholder="Pesquisar por nome do medicamento" value="<?php echo $searchTerm; ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Pesquisar</button>
                    </div>
                </div>
            </form>

            <form method="POST" class="mb-4">
                <select class="form-select" name="order_by" onchange="this.form.submit()">
                    <option value="nomeMed" <?php echo ($orderBy == 'nomeMed') ? 'selected' : ''; ?>>Ordenar por Nome</option>
                    <option value="precoMedUni" <?php echo ($orderBy == 'precoMedUni') ? 'selected' : ''; ?>>Ordenar por Preço</option>
                    <option value="quantidadeMedEstoque" <?php echo ($orderBy == 'quantidadeMedEstoque') ? 'selected' : ''; ?>>Ordenar por Quantidade</option>
                </select>
            </form>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço Unitário</th>
                        <th>Quantidade em Estoque</th>
                        <th>Categoria</th>
                        <th>Validade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($medicamentos) > 0) : ?>
                        <?php foreach ($medicamentos as $medicamento) : ?>
                            <tr>
                                <td><?php echo $medicamento['id_medicamento']; ?></td>
                                <td><?php echo $medicamento['nomeMed']; ?></td>
                                <td><?php echo number_format($medicamento['precoMedUni'], 2, ',', '.'); ?></td>
                                <td><?php echo $medicamento['quantidadeMedEstoque']; ?></td>
                                <td><?php echo $medicamento['categoriaMed']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($medicamento['validadeMed'])); ?></td>
                                <td>
                                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $medicamento['id_medicamento']; ?>">Editar</button>
                                    <a href="?id_medicamento=<?php echo $medicamento['id_medicamento']; ?>" class="btn btn-danger">Excluir</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal<?php echo $medicamento['id_medicamento']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Editar Medicamento</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="idMedicamento" value="<?php echo $medicamento['id_medicamento']; ?>">
                                                <div class="mb-3">
                                                    <label for="nomeMed" class="form-label">Nome do Medicamento</label>
                                                    <input type="text" class="form-control" name="nomeMed" value="<?php echo $medicamento['nomeMed']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="precoMedUni" class="form-label">Preço Unitário</label>
                                                    <input type="number" class="form-control" name="precoMedUni" value="<?php echo $medicamento['precoMedUni']; ?>" required step="0.01">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="quantidadeMedEstoque" class="form-label">Quantidade em Estoque</label>
                                                    <input type="number" class="form-control" name="quantidadeMedEstoque" value="<?php echo $medicamento['quantidadeMedEstoque']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="categoriaMed" class="form-label">Categoria</label>
                                                    <input type="text" class="form-control" name="categoriaMed" value="<?php echo $medicamento['categoriaMed']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="validadeMed" class="form-label">Data de Validade</label>
                                                    <input type="date" class="form-control" name="validadeMed" value="<?php echo $medicamento['validadeMed']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" name="editar" class="btn btn-primary">Salvar Alterações</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum medicamento encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="footer">
        <footer>Todos os direitos reservados ©</footer>
    </footer>
</body>

</html>