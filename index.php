<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Login</title>
</head>

<body>
    <header>
        <div id="fig_top"></div>
    </header>

    <main>
        <section id="section_form">
            <div id="logo_formulario">
                <img src="assets/DevTheBlaze.png" alt="Logo DevTheBlaze">
            </div>
            <div>
                <h1>Login</h1>
            </div>
            <form action="calendario.php" method="POST">
                <div class="input-container">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="txtlogin" id="txtlogin" placeholder="Usuário">
                </div>

                <div class="input-container">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="txtsenha" id="txtsenha" placeholder="Senha">
                </div>

                <!-- Se for só a tela de login, o campo de confirmação não precisa -->
                <!-- Mas se quiser adicionar, descomente abaixo -->
                <!--
                <div class="input-container">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="txtsenha_confirmacao" id="txtsenha_confirmacao"
                        placeholder="Confirmar Senha">
                </div>
                -->

                <button type="submit">Acessar</button>

                <a href="cadastro.php">Cadastre-se</a>
            </form>
        </section>
    </main>

    <footer>
        <div id="fig_bottom"></div>
        <img src="assets/losangos_bottom.png" alt="" id="losangos">
    </footer>
</body>

</html>