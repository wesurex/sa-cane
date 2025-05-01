<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
      }
      .login-card {
        max-width: 400px;
        width: 100%;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
      }
    </style>
  </head>
  <body>

    <main class="login-card bg-white">
      <div class="text-center mb-4">
        <h4 class="fw-bold">Login</h4>
        <p class="text-muted">Acesse sua conta</p>
      </div>
      <form action="auth.php" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" class="form-control" name="email" id="email" required autofocus>
        </div>
        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" class="form-control" name="senha" id="senha" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
        <div class="mt-3 text-center">
          <a href="#" class="text-decoration-none small">Esqueceu a senha?</a>
        </div>
      </form>
    </main>

  </body>
</html>
