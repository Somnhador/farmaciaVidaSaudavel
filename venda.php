<?php
session_start();
require 'conexao.php';


function venderMedicamento($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vender'])) {
        $idMedicamento = $_POST['idMedicamento'];
        $quantidadeVendida = $_POST['quantidadeVendida'];


        $stmt = $conn->prepare("SELECT quantidadeMedEstoque FROM cadastroMedicamento WHERE id_medicamento = :idMedicamento");
        $stmt->bindParam(':idMedicamento', $idMedicamento);
        $stmt->execute();
        $medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($medicamento && $medicamento['quantidadeMedEstoque'] >= $quantidadeVendida) {

            $novaQuantidade = $medicamento['quantidadeMedEstoque'] - $quantidadeVendida;
            $stmt = $conn->prepare("UPDATE cadastroMedicamento SET quantidadeMedEstoque = :novaQuantidade WHERE id_medicamento = :idMedicamento");
            $stmt->bindParam(':novaQuantidade', $novaQuantidade);
            $stmt->bindParam(':idMedicamento', $idMedicamento);
            $stmt->execute();

            echo "<script>alert('Venda realizada com sucesso!'); history.replaceState(null, '', 'venda.php');</script>;";
        } else {
            echo "<script>alert('Estoque insuficiente para realizar a venda!'); history.replaceState(null, '', 'venda.php');</script>;";
        }
    }
}


venderMedicamento($conn);


$searchTerm = isset($_POST['search']) ? htmlspecialchars($_POST['search']) : '';
$orderDirection = isset($_POST['order']) && $_POST['order'] == 'desc' ? 'DESC' : 'ASC';


$query = "SELECT * FROM cadastroMedicamento WHERE nomeMed LIKE :searchTerm ORDER BY quantidadeMedEstoque $orderDirection";
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
    <title>Venda de Medicamentos - Sistema de Farmácia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }

        .table {
            color: #ffffff;
            background-color: rgba(0, 0, 0, 0.5);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }

        .container {
            color: #ffffff;
            margin-top: 30px;
            border-radius: 10px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
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
        <h2 class="text-center">Venda de Medicamentos</h2>

        <div class="text-center mb-4">
            <a href="menu.php" class="btn btn-primary">Ir para o Menu</a>
        </div>

        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search" placeholder="Pesquisar por nome do medicamento" value="<?php echo $searchTerm; ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" name="order" value="<?php echo $orderDirection == 'ASC' ? 'desc' : 'asc'; ?>" class="btn btn-secondary w-100">
                        Ordenar por Estoque: <?php echo $orderDirection == 'ASC' ? 'Decrescente' : 'Crescente'; ?>
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </div>
            </div>
        </form>

        <div>
            <h3>Medicamentos</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço Unitário</th>
                            <th>Quantidade em Estoque</th>
                            <th>Categoria</th>
                            <th>Data de Validade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicamentos as $medicamento) : ?>
                            <tr style="background-color: <?php echo ($medicamento['quantidadeMedEstoque'] == 0) ? '#6c757d' : ''; ?>;">
                                <td><?php echo $medicamento['id_medicamento']; ?></td>
                                <td><?php echo $medicamento['nomeMed']; ?></td>
                                <td><?php echo number_format($medicamento['precoMedUni'], 2, ',', '.'); ?></td>
                                <td><?php echo $medicamento['quantidadeMedEstoque']; ?></td>
                                <td><?php echo $medicamento['categoriaMed']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($medicamento['validadeMed'])); ?></td>
                                <td>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#venderModal<?php echo $medicamento['id_medicamento']; ?>">Vender</button>
                                </td>
                            </tr>

                            <div class="modal fade" id="venderModal<?php echo $medicamento['id_medicamento']; ?>" tabindex="-1" aria-labelledby="venderModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="venderModalLabel" style="color: #000;">Venda de Medicamento: <?php echo $medicamento['nomeMed']; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="idMedicamento" value="<?php echo $medicamento['id_medicamento']; ?>">
                                                <div class="mb-3">
                                                    <label for="quantidadeVendida" class="form-label" style="color: #000;">Quantidade a Vender</label>
                                                    <input type="number" class="form-control" id="quantidadeVendida" name="quantidadeVendida" min="1" max="<?php echo $medicamento['quantidadeMedEstoque']; ?>" required>
                                                </div>
                                                <button type="submit" name="vender" class="btn btn-success">Vender</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="footer">
        <footer>Todos os direitos reservados ©</footer>
    </div>
</body>

</html>