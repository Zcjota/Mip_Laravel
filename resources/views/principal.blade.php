<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema Ventas Laravel Vue Js- Intecruz">
    <meta name="author" content="Intecruz.com">
    <meta name="keyword" content="Sistema ventas Laravel Vue Js, Sistema compras Laravel Vue Js">
    <link rel="shortcut icon" href="/img/favicon.png">
    <title>Sistema MIP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- css comprimido -->
    <link href="/css/plantilla.css" rel="stylesheet">

    <script>
        function limpiarYCerrarSesion() {
            // Limpiar el Local Storage
            localStorage.clear();
            
        }
    </script>
    <style>
        .selected {

            background-color: rgb(195, 224, 171) !important;
            font-weight: bold;
            color: black;

        }

        tr.highlighted td {
            background: red;
        }

        .tabla {
            width: 100%;
        }

        .tabla tr:nth-child(even) {
            background-color: #ddd;
        }

        .tabla tr:hover td {
            background-color: black;
            color: white;
        }

        .centroReserva {
            background: #E46774;
            text-align: center;
            color: beige;
        }

        .centroHorario {
            text-align: center;
        }

        .centroTransporte {
            background: rgb(231, 224, 155);
            text-align: center;
            color: black;
        }

        .encabezadoFijo {
            position: sticky;
            top: 0;
            background-color: #243648f0;
            color: #ECF0F1;
            height: 40px;
            /*text-align: center;*/
        }

        th {
            position: sticky;
            top: 0;
            background-color: #243648f0;
            color: #ECF0F1;
            height: 40px;
        }

        .lista {
            background-color: black;
        }

        .sidebar .nav-dropdown.open {
            background-color: #4dbd74;
        }

        /*
    .tabla tr:hover td{
    background-color: black ;
    color: white ;
    }
    */
    </style>
</head>

<body style="background-color: #a5a9ac;" class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">

    <div id="app">
        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>

            <ul class="nav navbar-nav ml-auto">
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
                        <img src="{{$imagen}}" class="img-avatar" alt="admin@bootstrapmaster.com">
                        <span class="d-md-down-none" onclick="ir()"><b>{{$nombre}}</b></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" id="menu">
                        <div class="dropdown-header text-center">
                            <strong>Cuenta </strong>
                        </div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();limpiarYCerrarSesion();">
                            <i class="fa fa-power-off" aria-hidden="true"></i>
                            Cerrar sesión</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                        <a class="dropdown-item" href="#"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Perfil</a>
                    </div>
                </li>
                &nbsp;&nbsp;&nbsp;&nbsp;

            </ul>


        </header>

        <div class="app-body">
            @include('plantilla.sidebar')

            <!-- Contenido Principal -->
            @yield('contenido')
            <!-- /Fin del contenido principal -->
        </div>
    </div>


    <footer class="app-footer" style="margin-top:auto;">
        <span><a href="#">Intecruz</a> &copy; 2019</span>
        <span class="ml-auto">Desarrollado por <a href="#">Intecruz</a></span>
        <img src="img/logo_intecruz.png" style="width:100px;">
    </footer>


    <script>
        var cambiar = 1;

        function ir() {
            if (cambiar == 1) {
                cambiar = 0;
                document.getElementById("menu").className = "dropdown-menu dropdown-menu-right show";
            } else {
                cambiar = 1;
                document.getElementById("menu").className = "dropdown-menu dropdown-menu-right";
            }
        }
    </script>
    <script src="js/app.js"></script>
    <script src="js/plantilla.js"></script>


</body>

</html>