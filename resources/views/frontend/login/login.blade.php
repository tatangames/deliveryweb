<!DOCTYPE html>
<html lang="es">

<head>
    <title>Sivar App - Panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link href="{{ asset('images/icono-sistema.png') }}" rel="icon">
    <!--Fontawesome CDN.-->
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <!-- comprimido de librerias -->
    <link href="{{ asset('css/login/login.css') }}" type="text/css" rel="stylesheet" />
    <!-- libreria para alertas -->
    <link rel="stylesheet" href="{{asset('css/login/styleLogin.css')}}">

    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.min.css') }}" rel="stylesheet">

</head>

<style>

    body {
        background-image: url({{ asset('images/fondo-login.jpg') }});
    }

</style>

<body>
<div class="container">
    <div class="d-flex justify-content-center h-100">

        <div class="card " style="height: 450px;">
            <div class="card-header text-center">

                <div class="row text-center d-flex" style="position: relative; top: -10px;">
                    <div class="col-md-12" style="margin-top: 5px">
                        <img src="{{ asset('images/logologin.png') }}" width="125" height="130px" srcset="">
                    </div>
                </div>
                <h3 style="position: relative; top: -5px;"> ADMINISTRACION
                    DELIVERY SIVAR</h3>
            </div>
            <div class="card-body" >
                <form class=" validate-form">
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input id="usuario" maxlength="50" type="text" class="form-control" required placeholder="Usuario">
                    </div>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input id="password" maxlength="16" type="password" class="form-control" required placeholder="Contraseña">
                    </div>
                    <br>
                    <br>
                    <div class="form-group text-center">
                        <input type="button" value="ACCEDER" onclick="login()" id="btnLogin" class="btn  login_btn">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/sweetalert2.all.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/alertaPersonalizada.js') }}" type="text/javascript"></script>

</body>

</html>
<script>

    var inputPassword = document.getElementById("password");
    var inputUsuario = document.getElementById("usuario");

    inputPassword.addEventListener("keyup", function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            login();
        }
    });

    inputUsuario.addEventListener("keyup", function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            login();
        }
    });

    function login() {

        var usuario = document.getElementById('usuario').value;
        var password = document.getElementById('password').value;

        if(usuario === ''){
            toastMensaje("error", "Usuario es requerido");
            return;
        }

        if(password === ''){
            toastMensaje("error", "Contraseña es requerido");
            return;
        }

        if(usuario.length > 50){
            toastMensaje("error", "Máximo 50 caracteres");
            return;
        }

        if(password.length > 16){
            toastMensaje("error", "Máximo 16 caracteres");
            return;
        }

        openLoading()

        let formData = new FormData();
        formData.append('usuario', usuario);
        formData.append('password', password);

        axios.post('/login', formData, {
        })
            .then((response) => {
                closeLoading()
                verificar(response);
            })
            .catch((error) => {
                closeLoading()
                toastMensaje("error", "Error en la respuesta");
            });
    }

    function verificar(response) {

        if (response.data.success === 0) {
            toastMensaje("error", "Validación incorrecta");
        } else if (response.data.success === 1) {
            window.location = response.data.ruta;
        } else if (response.data.success === 2) {
            toastMensaje("error", "Contraseña incorrecta");
        }else if (response.data.success === 3) {
            toastMensaje("error", "Usuario no encontrado");
        }
        else if (response.data.success === 4) {
            alertaMensaje('warning' ,'Usuario Bloqueado', 'Contactar al Administrador');
        }
        else {
            toastMensaje("error", "Error");
        }
    }


</script>
