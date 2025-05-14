<?php
function carregarEnv($path = __DIR__ . '/.env') {
    if (!file_exists($path)) return;

    $linhas = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        if (trim($linha) === '' || str_starts_with($linha, '#')) continue;

        list($chave, $valor) = explode('=', $linha, 2);
        $chave = trim($chave);
        $valor = trim($valor);

        $_ENV[$chave] = $valor;
        putenv("$chave=$valor"); // torna acessível via getenv()
    }
}

carregarEnv();
