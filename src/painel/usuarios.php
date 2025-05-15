<?php
session_start();
require_once '../config/db.php';

// Verifica se está logado como admin
if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_tipo'] !== 'administrador') {
    header("Location: /login.php");
    exit;
}

$erro = '';
$sucesso = '';

// Processar envio do formulário (criar ou atualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $tipo = $_POST['tipo'] ?? 'cliente';
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    $id_form = $_POST['id'] ?? null;

    if (empty($nome) || empty($email) || empty($tipo)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } elseif ($senha && strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif ($senha && $senha !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } else {
        // Verifica se email já existe em outro usuário
        $sqlCheck = "SELECT id FROM clientes WHERE email = :email";
        $paramsCheck = [':email' => $email];
        if ($id_form) {
            $sqlCheck .= " AND id != :id";
            $paramsCheck[':id'] = $id_form;
        }
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute($paramsCheck);
        if ($stmtCheck->fetch()) {
            $erro = "Já existe um usuário com este e-mail.";
        } else {
            if ($id_form) {
                // Atualizar usuário
                if ($senha) {
                    $sql = "UPDATE clientes SET nome = :nome, email = :email, tipo = :tipo, senha = :senha WHERE id = :id";
                    $params = [
                        ':nome' => $nome,
                        ':email' => $email,
                        ':tipo' => $tipo,
                        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
                        ':id' => $id_form
                    ];
                } else {
                    $sql = "UPDATE clientes SET nome = :nome, email = :email, tipo = :tipo WHERE id = :id";
                    $params = [
                        ':nome' => $nome,
                        ':email' => $email,
                        ':tipo' => $tipo,
                        ':id' => $id_form
                    ];
                }
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $sucesso = "Usuário atualizado com sucesso.";
            } else {
                // Criar novo usuário (senha obrigatória)
                if (empty($senha)) {
                    $erro = "A senha é obrigatória para novo usuário.";
                } else {
                    $sql = "INSERT INTO clientes (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':nome' => $nome,
                        ':email' => $email,
                        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
                        ':tipo' => $tipo
                    ]);
                    $sucesso = "Usuário criado com sucesso.";
                }
            }
        }
    }
    // Evitar reenvio do form ao atualizar a página
    if ($sucesso) {
        header("Location: usuarios.php?sucesso=" . urlencode($sucesso));
        exit;
    }
}

// Excluir usuário
if (isset($_GET['acao'], $_GET['id']) && $_GET['acao'] === 'excluir') {
    $id = $_GET['id'];
    if ($id == $_SESSION['cliente_id']) {
        $erro = "Você não pode excluir seu próprio usuário.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $sucesso = "Usuário excluído com sucesso.";
        header("Location: usuarios.php?sucesso=" . urlencode($sucesso));
        exit;
    }
}

if (isset($_GET['sucesso'])) {
    $sucesso = $_GET['sucesso'];
}

// ==== PAGINAÇÃO ====
// Quantos usuários por página
$limite = 20;

// Página atual (vinda da URL)
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$inicio = ($pagina - 1) * $limite;

// Buscar usuários paginados
$stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM clientes ORDER BY id DESC LIMIT :limite OFFSET :inicio");
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total de usuários para calcular páginas
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$totalPaginas = ceil($totalUsuarios / $limite);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Gerenciar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a href="dashboard.php" class="navbar-brand">Painel Admin</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            <li class="nav-item"><a href="/logout.php" class="nav-link btn btn-outline-danger btn-sm">Sair</a></li>
        </ul>
    </div>
</nav>

<main class="container mt-5">
    <h2>Gerenciar Usuários</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" id="btnNovoUsuario">Adicionar Usuário</button>
    </div>

    <!-- Barra de pesquisa -->
    <div class="mb-3">
        <input type="text" id="inputBusca" class="form-control" placeholder="Buscar por nome ou e-mail...">
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Nome</th><th>E-mail</th><th>Tipo</th><th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaUsuarios">
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id']) ?></td>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['tipo']) ?></td>
                    <td>
                        <button
                            class="btn btn-sm btn-warning btn-editar"
                            data-id="<?= $u['id'] ?>"
                            data-nome="<?= htmlspecialchars($u['nome'], ENT_QUOTES) ?>"
                            data-email="<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>"
                            data-tipo="<?= $u['tipo'] ?>"
                        >Editar</button>

                        <?php if ($u['id'] != $_SESSION['cliente_id']): ?>
                            <a href="usuarios.php?acao=excluir&id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este usuário?');">Excluir</a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled>Excluir</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Botões de paginação -->
    <nav aria-label="Paginação usuários">
        <ul class="pagination justify-content-center">
            <?php if ($pagina > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($pagina < $totalPaginas): ?>
                <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>" aria-label="Próximo">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</main>

<!-- Modal de Cadastro/Edição -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="formUsuario">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUsuarioLabel">Adicionar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="inputId" value="">

                <div class="mb-3">
                    <label for="inputNome" class="form-label">Nome *</label>
                    <input type="text" name="nome" id="inputNome" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="inputEmail" class="form-label">E-mail *</label>
                    <input type="email" name="email" id="inputEmail" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="inputTipo" class="form-label">Tipo *</label>
                    <select name="tipo" id="inputTipo" class="form-select" required>
                        <option value="cliente">Cliente</option>
                        <option value="cabeleireiro">Cabeleireiro</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="inputSenha" class="form-label" id="labelSenha">Senha *</label>
                    <input type="password" name="senha" id="inputSenha" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="inputConfirmar" class="form-label" id="labelConfirmarSenha">Confirmar senha *</label>
                    <input type="password" name="confirmar" id="inputConfirmar" class="form-control" required>
                </div>

                <small class="text-muted">* Campos obrigatórios</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Abrir modal para novo usuário
    document.getElementById('btnNovoUsuario').addEventListener('click', () => {
        document.getElementById('modalUsuarioLabel').innerText = 'Adicionar Usuário';
        document.getElementById('formUsuario').reset();
        document.getElementById('inputId').value = '';
        document.getElementById('inputSenha').required = true;
        document.getElementById('inputConfirmar').required = true;
        document.getElementById('labelSenha').innerText = 'Senha *';
        document.getElementById('labelConfirmarSenha').innerText = 'Confirmar senha *';
        new bootstrap.Modal(document.getElementById('modalUsuario')).show();
    });

    // Abrir modal para editar usuário
    document.querySelectorAll('.btn-editar').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('modalUsuarioLabel').innerText = 'Editar Usuário';
            document.getElementById('inputId').value = button.dataset.id;
            document.getElementById('inputNome').value = button.dataset.nome;
            document.getElementById('inputEmail').value = button.dataset.email;
            document.getElementById('inputTipo').value = button.dataset.tipo;
            // Senha não é obrigatória para edição
            document.getElementById('inputSenha').value = '';
            document.getElementById('inputConfirmar').value = '';
            document.getElementById('inputSenha').required = false;
            document.getElementById('inputConfirmar').required = false;
            document.getElementById('labelSenha').innerText = 'Senha (deixe vazio para não alterar)';
            document.getElementById('labelConfirmarSenha').innerText = 'Confirmar senha (deixe vazio para não alterar)';
            new bootstrap.Modal(document.getElementById('modalUsuario')).show();
        });
    });

    // Busca dinâmica via AJAX (se quiser, posso ajudar a integrar)
    // Por enquanto, essa busca só filtra a tabela atual na página (frontend)
    const inputBusca = document.getElementById('inputBusca');
    inputBusca.addEventListener('input', () => {
        const filtro = inputBusca.value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaUsuarios tr');
        linhas.forEach(linha => {
            const nome = linha.cells[1].textContent.toLowerCase();
            const email = linha.cells[2].textContent.toLowerCase();
            if (nome.includes(filtro) || email.includes(filtro)) {
                linha.style.display = '';
            } else {
                linha.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
