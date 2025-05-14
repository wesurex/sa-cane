<?php
require_once '../config/db.php';
require_once '../controllers/ClienteController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteController = new ClienteController($pdo);
    $clienteController->registrar($_POST);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 600px;">
      <h2 class="text-center mb-4">Cadastro de Cliente</h2>
      <form method="POST">
        <div class="mb-3"><label class="form-label">Nome completo</label><input type="text" class="form-control" name="nome" required></div>
        <div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" name="email" required></div>
        <div class="mb-3"><label class="form-label">Telefone</label><input type="text" class="form-control" id="telefone" name="telefone" required></div>
        <div class="mb-3"><label class="form-label">Senha</label><input type="password" class="form-control" name="senha" required></div>
        <div class="row">
          <div class="col-md-4 mb-3"><label class="form-label">CEP</label><input type="text" class="form-control" id="cep" name="cep" required></div>
          <div class="col-md-8 mb-3"><label class="form-label">Rua</label><input type="text" class="form-control" name="rua" required></div>
          <div class="col-md-4 mb-3"><label class="form-label">Número</label><input type="text" class="form-control" name="numero" required></div>
          <div class="col-md-8 mb-3"><label class="form-label">Complemento</label><input type="text" class="form-control" name="complemento" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Bairro</label><input type="text" class="form-control" name="bairro" required></div>
          <div class="col-md-4 mb-3"><label class="form-label">Cidade</label><input type="text" class="form-control" name="cidade" required></div>
          <div class="col-md-2 mb-3"><label class="form-label">UF</label><input type="text" class="form-control" name="estado" maxlength="2" required></div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/imask"></script>
  <script>
    const cep = document.getElementById('cep');
    const telefone = document.getElementById('telefone');

    IMask(cep, { mask: '00000-000' });
    IMask(telefone, { mask: '(00) 00000-0000' });

    cep.addEventListener('blur', function () {
      const cleanCep = this.value.replace(/\D/g, '');
      if (cleanCep.length !== 8) return;

      fetch(`https://viacep.com.br/ws/${cleanCep}/json/`)
        .then(res => res.json())
        .then(data => {
          if (!data.erro) {
            document.querySelector('input[name="rua"]').value = data.logradouro;
            document.querySelector('input[name="bairro"]').value = data.bairro;
            document.querySelector('input[name="cidade"]').value = data.localidade;
            document.querySelector('input[name="estado"]').value = data.uf;
          } else {
            alert('CEP não encontrado!');
          }
        })
        .catch(err => {
          console.error('Erro ao buscar CEP:', err);
        });
    });
  </script>
</body>
</html>
