<html>
<head>
    <title>Alcaldía Metapán | Panel</title>
    <style>
        body{
            font-family: Arial;
        }
        @page {
            margin: 145px 25px;
            /* margin-bottom: 10%;*/
        }
        header { position: fixed;
            left: 0px;
            top: -160px;
            right: 0px;
            height: 100px;
            text-align: center;
            font-size: 12px;
        }
        header h1{
            margin: 10px 0;
        }
        header h2{
            margin: 0 0 10px 0;
        }
        footer {
            position: fixed;
            left: 0px;
            bottom: -10px;
            right: 0px;
            height: 10px;
            /* border-bottom: 2px solid #ddd;*/
        }

        footer table {
            width: 100%;
        }
        footer p {
            text-align: right;
        }
        footer .izq {
            margin-top: 20px; !important;
            margin-left: 20px;
            text-align: left;
        }

        .content {
            padding: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        .content img {
            margin-right: 15px;
            float: right;
        }

        .content h3{
            font-size: 20px;

        }
        .content p{
            margin-left: 15px;
            display: block;
            margin: 2px 0 0 0;
        }

        hr{
            page-break-after: always;
            border: none;
            margin: 0;
            padding: 0;
        }

        #tabla {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 35px;
            text-align: center;
        }

        #tabla td{
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 15px;
        }

        #tabla th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        #tabla th {
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #f2f2f2;
            color: #1E1E1E;
            text-align: center;
            font-size: 16px;
        }

        .fecha{
            font-size: 16px;
            margin-left: 17px;
            text-align: justify;
        }

        .titulo{
            font-family: Arial;
            font-size: 20px;
            font-style: normal;
            text-align: center;
        }

    </style>
<body>
<header style="margin-top: 25px">
    <div class="row">

        <div class="row">
            <center><p class="titulo">
                    REPORTE ORDENES<br>
                </p>
                <p class="titulo">
                    {{ $servicio }}<br>
                </p>
                <p class="titulo">De: {{ $f1 }}  Hasta: {{ $f2 }}</font></p></center>
        </div>

    </div>
</header>

<br>
<div id="content">

    <table id="tabla" style="width: 95%">
        <thead>
        <tr>
            <th style="text-align: center; width: 15%">#</th>
            <th style="text-align: center; width: 15%"># Orden</th>
            <th style="text-align: center; width: 15%">Fecha</th>
            <th style="text-align: center; width: 15%">Comisión</th>
            <th style="text-align: center; width: 15%">Consumido</th>
            <th style="text-align: center; width: 15%">Envío</th>
            <th style="text-align: center; width: 25%">Pago</th>
        </tr>
        </thead>
        @foreach($orden as $dato)
            <tr>
                <td>{{ $dato->contador }}</td>
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->fecha_orden }}</td>
                <td>{{ $dato->comi }}%</td>
                <td>${{ $dato->precio_consumido }}</td>
                <td>${{ $dato->precio_envio }}</td>
                <td>{{ $dato->metodo }}</td>
            </tr>
        @endforeach

        <tr>
            <th style="text-align: center; width: 15%">TOTAL</th>
            <th style="text-align: center; width: 15%"></th>
            <th style="text-align: center; width: 15%"></th>
            <th style="text-align: center; width: 15%"></th>
            <th style="text-align: center; width: 15%; font-family: Arial; font-size: 16px">${{ $consumido }}</th>
            <th style="text-align: center; width: 15%; font-family: Arial; font-size: 16px">${{ $envio }}</th>
            <th style="text-align: center; width: 25%"></th>
        </tr>

    </table>

</div>

</body>
</html>
