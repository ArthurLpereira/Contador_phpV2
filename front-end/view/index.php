<?php
$mensagem = null;
$icone = 'error';
$titulo = 'Erro';
$redirecionar = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['txtnome'];
    $senha = $_POST['txtsenha'];

    $dados = [
        'nome' => $nome,
        'senha' => $senha,
    ];

    $json = json_encode($dados);

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $json
        ]
    ];

    $context = stream_context_create($opts);
    $result = file_get_contents('http://localhost/arthur/contador_phpv2/back-end/endpoints/login_user.php', false, $context);

    if ($result === false) {
        $mensagem = 'Erro ao conectar com o servidor.';
    } else {
        $user = json_decode($result, true);

        if (isset($user['erro'])) {
            $mensagem = $user['erro'];
        } else {
            session_start();
            $_SESSION['id'] = $user['usuario']['id_usuario'];
            $_SESSION['nome'] = $user['usuario']['nome_usuario'];
            $_SESSION['nivel'] = $user['usuario']['nivel_usuario'];
            $_SESSION['senha'] = $senha;
            unset($senha);

            $mensagem = $user['mensagem'] ?? 'Login realizado com sucesso!';
            $icone = 'success';
            $titulo = 'Sucesso';
            $redirecionar = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Login</title>
</head>

<body>
    <header>
        <div id="fig_top"></div>
    </header>

    <main>
        <section id="section_form">
            <div id="logo_formulario">
                <img src="../assets/DevTheBlaze.png" alt="Logo DevTheBlaze">
            </div>
            <div>
                <h1>Login</h1>
            </div>
            <form method="POST">
                <div class="input-container">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="txtnome" id="txtlogin" placeholder="Usuário">
                </div>

                <div class="input-container">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="txtsenha" id="txtsenha" placeholder="Senha">
                </div>
                <button type="submit">Acessar</button>

                <a href="cadastro.php">Cadastre-se</a>
            </form>
        </section>
    </main>

    <footer>
        <div id="fig_bottom"></div>
        <img src="../assets/losangos_bottom.png" alt="" id="losangos">
    </footer>

    <?php if ($mensagem): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: '<?= $icone ?>',
                title: '<?= $titulo ?>',
                text: <?= json_encode($mensagem) ?>
            }).then(() => {
                <?php if ($redirecionar): ?>
                    window.location.href = './calendario.php';
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>

</body>

</html>