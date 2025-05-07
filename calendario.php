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
        <div class="menu">
            <button class="hamburguer">
                <div id="barra1" class="barra"></div>
                <div id="barra2" class="barra"></div>
                <div id="barra3" class="barra"></div>
            </button>


            <nav>
                <ul>
                    <li>
                        <a href="estoque.php">Estoque</a>
                    </li>
                    <li>
                        <a href="calendario.php">Calendario</a>
                    </li>
                    <li>
                        <a href="relatorio.php">Contagem</a>
                    </li>
                    <li><a href="index.php">Sair</a></li>
                </ul>
            </nav>
        </div>

        <section id="background_calendar">
            <div class="calendar-container">
                <div class="calendar-header">
                    <button onclick="changeMonth(-1)">&#9665;</button>
                    <span id="month-year"></span>
                    <button onclick="changeMonth(1)">&#9655;</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>DOMINGO</th>
                            <th>SEGUNDA-FEIRA</th>
                            <th>TERÇA-FEIRA</th>
                            <th>QUARTA-FEIRA</th>
                            <th>QUINTA-FEIRA</th>
                            <th>SEXTA-FEIRA</th>
                            <th>SÁBADO</th>
                        </tr>
                    </thead>
                    <tbody id="calendar-body"></tbody>
                </table>
            </div>
            <div class="selected-day" id="selected-day">Nenhum dia selecionado</div>
        </section>

        <div id="btn_confirmar_dia">
            <a href="categorias.html"><button>Concluído</button></a>
        </div>
    </main>

    <script src="js/script.js"></script>

    <footer>
        <div id="fig_bottom"></div>
        <img src="assets/losangos_bottom.png" alt="" id="losangos">
    </footer>
    <script>
        const abrir_menu = document.getElementsByClassName('hamburguer')[0];
        const menu = document.getElementsByClassName('menu')[0];
        abrir_menu.addEventListener('click', () => {
            abrir_menu.classList.toggle('aberto');
            menu.classList.toggle('ativo');
        });
    </script>
</body>

</html>