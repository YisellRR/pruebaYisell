<?php

require('../../principal.php'); // PRIMERO ! IMPORTANTE !
require('../../funciones.php');
// require('../r/qr/qrlib.php');
require("../../r/rotation.php");



if (session_status() == PHP_SESSION_NONE) {
    session_start();
} // PRIMERO iniciar si aun no se ha hecho....





$operacion = 1;


$destino_reporte = "i"; // destino i, s, d


// INICIALMENTE, imprimir o enviar el PDF
$imprimir_pdf = true;  // ---------------------------------------------        IMPRIMIR O ENVIAR EL DOCUMENTO EN PDF 


$imprimir_datos = 1; // 0 - 1 
$imprimir_formato = 1; // 0 - 1


// $mail = "toro.pampa.py@hotmail.com";
// $direccion = "GRAL. ELIZARDO AQUINO N° 558 - LIMPIO - PY";

$ancho_total = 195;

$tamano_fuente_default = $tamano_fuente_reportes;
$tamano_fuente_titulo = $tamano_fuente_default;

$ancho_celda_default = 90;
$ancho_celda_membrete = 90;
$ancho_celda_membrete_derecha = 44;

$tipo_fuente = 'Arial';



$logo_top = 20;
$logo_left = 30;
$logo_width = 120;
$logo_height = 80;

$membrete_top = 10;
$membrete_left = 10;
$membrete_width = 140;
$membrete_height = 80;


// para el modelo de factura 
$logo_top = 0;
$logo_left = 0;
$logo_width = 0;
$logo_height = 0;


$bordes = 1; // BORDES EN CADA CELDA 

$rellenar = false;


$url_logo = 'https://upload.wikimedia.org/wikipedia/commons/a/ab/Logo_TV_2015.png';



$titulo_documento = 'Reporte CRT';

$titulo_reporte_hoja = "Fletes Internacionales";

$sub_titulo_reporte_hoja = "Uso Interno";

$creador_documento = 'Desarrollador';
$asunto_documento = 'Factura Nro. 001-002-002656';




// cantidad de vias de este reporte
$reporte_cant_vias = 1; // DEFAULT 
$altura_via = 140;





$margen_izquierdo = 5;
$margen_derecha = 5;

$punto_x_inicial = 5;
$punto_y_inicial = 10;

$margen_logo_texto = 0;


$tamano_hoja = 0; // 1 A4, 2 OFICIO, 3 CARTA, 4 PERSONALIZADO
$orientacion_hoja = 1; // 0 horizontal, 1 vertical

// SI HOJA ES A4
$ancho_hoja = 210;
$alto_hoja = 297;







// SI HOJA A4

$grosor_borde = 0.3;

$membrete_alto = 20; // el alto del recuadro del membrete, en su totalidad

$alto_renglon_formato = 6; //

$membrete_izquierda_ancho = 100;

$detalles_encabezado_alto = 3.5; // creo que podrian ser iguales, o algo asi
$detalles_pie_alto = 5;

// $cuerpo_valores_alto = 56 ; // 56 para duplicado

$alto_renglon_division = 7.5;

$alto_renglon_detalles = 3.5;

$membrete_muestra_encabezado_alto = $alto_renglon_formato * 3; // en 3 filas se reparten los datos del encabezado de la factura

$tamano_fuente_titulos = 7;
$tamano_fuente_datos = 7;

$columna_cantidad_ancho = 20;
$columna_unitario_ancho = 20;

// inicialmente las 3 columnas de valores serian iguales
$columna_exentas_ancho = 27;

// FIN SI HOJA A4



$suma_cantidad = NULL ;
$suma_oc_env = NULL ;
$suma_oc_entr = NULL ;
$suma_oc_anul = NULL ;
$suma_cant_cg = NULL ;
$suma_saldo_cargado = NULL ;




// MIENTRAS, PARA PROBAR CONFIGURACION

// EN ESTA UBICACION PARA SOBREESCRIBIR LOS VALORES ANTERIORES DE LA CONFIGURACION



if (isset($_GET["orientacion_hoja"])) {
    $orientacion_hoja = $_GET["orientacion_hoja"];
}

if (isset($_GET["ancho_hoja"])) {
    $ancho_hoja = $_GET["ancho_hoja"];
}
if (isset($_GET["alto_hoja"])) {
    $alto_hoja = $_GET["alto_hoja"];
}

if (isset($_GET["reporte_cant_vias"])) {
    $reporte_cant_vias = $_GET["reporte_cant_vias"];
}

if (isset($_GET["grosor_borde"])) {
    $grosor_borde = $_GET["grosor_borde"];
}
if (isset($_GET["membrete_alto"])) {
    $membrete_alto = $_GET["membrete_alto"];
}
if (isset($_GET["alto_renglon_formato"])) {
    $alto_renglon_formato = $_GET["alto_renglon_formato"];
}
if (isset($_GET["membrete_izquierda_ancho"])) {
    $membrete_izquierda_ancho = $_GET["membrete_izquierda_ancho"];
}
if (isset($_GET["detalles_encabezado_alto"])) {
    $detalles_encabezado_alto = $_GET["detalles_encabezado_alto"];
}
if (isset($_GET["detalles_pie_alto"])) {
    $detalles_pie_alto = $_GET["detalles_pie_alto"];
}
if (isset($_GET["alto_renglon_division"])) {
    $alto_renglon_division = $_GET["alto_renglon_division"];
}
if (isset($_GET["alto_renglon_detalles"])) {
    $alto_renglon_detalles = $_GET["alto_renglon_detalles"];
}

if (isset($_GET["tamano_fuente_titulos"])) {
    $tamano_fuente_titulos = $_GET["tamano_fuente_titulos"];
}
if (isset($_GET["tamano_fuente_datos"])) {
    $tamano_fuente_datos = $_GET["tamano_fuente_datos"];
}

if (isset($_GET["columna_cantidad_ancho"])) {
    $columna_cantidad_ancho = $_GET["columna_cantidad_ancho"];
}
if (isset($_GET["columna_unitario_ancho"])) {
    $columna_unitario_ancho = $_GET["columna_unitario_ancho"];
}
if (isset($_GET["columna_exentas_ancho"])) {
    $columna_exentas_ancho = $_GET["columna_exentas_ancho"];
}




// FIN VALORES A CONFIGURAR 


$encabezado_factura_alto = $membrete_alto + $membrete_muestra_encabezado_alto + ($detalles_encabezado_alto * 2);
$pie_factura_alto = $detalles_pie_alto * 3;


if ($logo_height == 0) {
    $logo_height = $membrete_alto - 2;
} // 2 mm para los margenes, y no tapar los bordes



$columna_iva_05_ancho = $columna_exentas_ancho;
$columna_iva_10_ancho = $columna_exentas_ancho;


$liq_iva_ancho_titulos = 16;


// HASTA AHORA..... 
$ancho_total = $columna_cantidad_ancho + $columna_unitario_ancho + $columna_exentas_ancho + $columna_iva_05_ancho + $columna_iva_10_ancho;

$restante = $ancho_hoja - ($ancho_total + $margen_izquierdo + $margen_derecha);

$columna_descripcion_ancho = $restante;

// entonces, ahora tendremos el ancho total
$ancho_total = $columna_cantidad_ancho + $columna_descripcion_ancho + $columna_unitario_ancho + $columna_exentas_ancho + $columna_iva_05_ancho + $columna_iva_10_ancho;


$membrete_muestra_derecha_ancho = ($ancho_total - $membrete_izquierda_ancho);



$id = NULL;


// AL FINAL, CALCULAR LA ALTURA DE LA VIA

$altura_via = ($membrete_alto + $membrete_muestra_encabezado_alto + ($detalles_encabezado_alto * 2) + ($detalles_pie_alto * 3));

$alto_disponible = $alto_hoja / $reporte_cant_vias;

// copias 
$copias = $reporte_cant_vias - 1; // 1 es el original

$cuerpo_valores_alto = $alto_disponible - $altura_via - $alto_renglon_division - ($alto_renglon_division / $reporte_cant_vias); // alto_renglon_division


######################################################################################################





// -------------------------------------------------                        EVITAR CAMBIO DE SESION ENTRE PESTANHAS                 -----------------------------------------------------------
################################################################################################################################################################################################

// luego de cargar principal.php e iniciar la sesion con session_start, verificar la sesion actual en este script y compararla con la sesion que solicita datos a este script 

$id_usuario_llamada = 0;   // esto deberia obligar al usuario a iniciar sesion, o a recargar el formulario, en caso de cambio de sesion 
$id_cliente_llamada = 0;
$errores_de_sesion = 0;

// if (isset($_GET['id_u'])) {
//     $id_usuario_llamada = intval($_GET['id_u']);
// } // id_usuario que hace la llamada a este script
// if (isset($_GET['id_c'])) {
//     $id_cliente_llamada = intval($_GET['id_c']);
// } // id_cliente que hace la llamada a este script 


// if (isset($_POST['id_u'])) {
//     $id_usuario_llamada = intval($_POST['id_u']);
// } // id_usuario que hace la llamada a este script
// if (isset($_POST['id_c'])) {
//     $id_cliente_llamada = intval($_POST['id_c']);
// } // id_cliente que hace la llamada a este script 



if (($id_usuario_llamada + $id_cliente_llamada) == 0) {
    $errores_de_sesion = -1;
} // en los primeros, si no se ha enviado via post los datos solicitados mas arriba, no continuar

if ($_SESSION['id_usuario'] <> $id_usuario_llamada) {
    $errores_de_sesion = -2;
}             // por cuestiones de permiso y otros
if ($_SESSION['cd_id_cliente'] <> $id_cliente_llamada) {
    $errores_de_sesion = -3;
}          // puede que sea el mismo usuario, pero esto evitara devolver datos de terceros

// if ($errores_de_sesion < 0) {

//     $datos_sesion = [
//         'cod_error' => $errores_de_sesion,
//         'respuesta' => $respuesta_sesion_invalida,
//         'id' => -1,
//         'n_doc' => -1
//     ];

//     echo json_encode($datos_sesion);

//     exit; // NO CONTINUAR 

// }

// -------------------------------------------------                        FIN EVITAR CAMBIO DE SESION ENTRE PESTANHAS                 -------------------------------------------------------
################################################################################################################################################################################################


$url_logo = urlServidor() . $carpeta_raiz . $_SESSION["url_logo_reportes"];

$_SESSION["url_logo"] = $url_logo;

$_SESSION["impreso_por"] = fecha_impresion( $_SESSION["nombre_usuario"] );


if ($imprimir_pdf) {

    


    $mostrar = true;
   

    // if (isset($_GET['f_ini'])) { // AL MENOS UNO DE LOS DATOS QUE SOLICITA EL FORMULARIO DE BUSQUEDA
    if ($id == NULL) { // AL MENOS UNO DE LOS DATOS QUE SOLICITA EL FORMULARIO DE BUSQUEDA

        // require('../funciones.php');
        require('../../conn/conexion.php');

        class PDF extends FPDF {

            private $pos_x_inicial;
                private $pos_y_inicial;
                private $posicion_x_condiciones;
                private $posicion_y_condición;
                private $margen_top;
                private $margen_izquierdo;
                private $margen_derecho;
                private $titulo;
                private $sub_titulo;

                public $fecha_inicio;
                public $fecha_fin;

            public $id_cliente;

            public $proceso;
            public $estado;
            public $cliente;

            public function setMargen_seguro($x=10,$y=10,$iz=10,$dr=10){

                if($x == NULL){
                    $x = 10;
                }

                if($y == NULL){
                    $y = 10;
                }

                if($iz == NULL){
                    $iz = 10;
                }

                if($dr == NULL){
                    $dr = 10;
                }

                $this->margen_top = $y;
                $this->margen_izquierdo = $x;
                $this->margen_derecho = $dr;
                
            }

            public function getPos_y_inicial_cabecera()
            {
                return $this->pos_y_inicial;
            }

        
            public function setPos_y_inicial_cabecera($pos_y_inicial)
            {
                $this->pos_y_inicial = $pos_y_inicial;

            
            }

            public function getPos_x_inicial_cabecera()
            {
                return $this->pos_x_inicial;
            }

        
            public function setPos_x_inicial_cabecera($pos_x_inicial)
            {
                $this->pos_x_inicial = $pos_x_inicial;

            
            }


            public function setTitulo($titulo)
                {
                    $this->titulo = $titulo;

                
                }
                public function setSub_titulo($sub_titulo){
                    $this->sub_titulo = $sub_titulo;
                }



            function Header() {

                $tipo_fuente = "arial";
                //$logo_top = $_SESSION["logo_vales_top"];
                //$logo_left = $_SESSION["logo_vales_left"];
                // $logo_width = $_SESSION["logo_vales_width"];
                // $logo_height = $_SESSION["logo_vales_height"];
                $logo_width = 0;
                $logo_height = 0;

               
                $logo_left = 10;
                $logo_top = 5;

                $bordes = 0;

                $pos_x_inicial = 10;

                $pos_y_inicial = 5;

                
                $logo_left = 10;
                $logo_top = 10;

                $ancho_logo_reportes = 35;

                $alto_logo_reportes = 0;

                if($_SESSION["cd_id_cliente"] == 43) {
                    $ancho_logo_reportes = 20;
                } 
                if($_SESSION["cd_id_cliente"] == 13) {
                    $ancho_logo_reportes = 25;
                    $logo_top = 6;
                } 
                if($_SESSION["cd_id_cliente"] == 58) {
                    $logo_top = 3;
                }

                $posicion_x_condiciones = $this->GetPageWidth() / 2;
                $posicion_y_condiciones = 15;
                
                // Logo
                $tamaño = getimagesize($_SESSION["url_logo"]);
                $calulo = $tamaño[0] - $tamaño[1];

                if ($calulo < 20){

                    //file_put_contents("dim_imagen.json",json_encode($tamaño));
                    $logo_left = 10;
                    $logo_top = 5;

                    $logo_width = 0;
                    $logo_height = 20;

                    $this->Image($_SESSION["url_logo"], $logo_left, $logo_top, $logo_width, $logo_height); // L, T, W, H

                } else {

                    // file_put_contents("dim_imagen_2.json",json_encode($tamaño));

                    $logo_left = 10;
                   
                    $logo_width = 20;
                    $logo_height = 0;
                    
                    if($tamaño[1]<70){
                        $logo_top = 15;
                    }else{
                        $logo_top = 10;
                    }

                    $this->Image($_SESSION["url_logo"], $logo_left, $logo_top, $logo_width, $logo_height); // L, T, W, H

                }


                $ancho_celda = 280;
            
                // Salto de línea

                $posicion_titulo = $this->margen_izquierdo ;
                    
                $this->SetY($this->margen_top);

                $ancho_celda_titulo = $this->GetPageWidth()-($this->margen_derecho + $this->margen_izquierdo);

                $this->SetX($posicion_titulo);

                $ancho_celda = $ancho_celda_titulo;

                $bordes = 0;

               // Titulo Principal

               $texto = $this->titulo;

               $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

               $this->SetFont($tipo_fuente, 'B', 10); // ajustar el tamanho de fuente que se va a imprimir

               $rellenar = "";

               $this->Cell($ancho_celda, 5, utf8_decode($texto), $bordes, $siguiente_linea, 'R', $rellenar);

                    //  subtitulo

               $texto = $this->sub_titulo;
               
               $this->SetX($posicion_titulo);

               $this->SetFont($tipo_fuente, 'B', 7); 

               $this->Cell($ancho_celda, 5, utf8_decode($texto), $bordes, $siguiente_linea, 'R', $rellenar);

            
                $tamanio_nombre = strlen($this->solicitante);
            

                $rellenar = "";

               
                if( $tamanio_nombre >10){
                    $nombre_solicitante = substr($this->solicitante,0,15);
                }else{
                    $nombre_solicitante = $this->solicitante;
                }


                $tamanio_desc = strlen($this->descripcion);
                
                 

                if( $tamanio_desc >10){
                   $descripcion = substr($this->descripcion,0,15);
               }else{
                   $descripcion = $this->descripcion;
               }
                
                
                $encabezados_busqueda = array(

                    "Periodo:",
                    $this->fecha_inicio . " - ".$this->fecha_fin,
                    "Estado:",
                    $this->estado,
                    "Cliente:",
                    ($this->id_cliente == 0) ? "<Todos>" : $this->cliente,

                );

                $tamaños_enca = array(
                    12
                    ,30
                    ,10
                    ,22
                    ,15
                    ,25
                );
                $cantidad = count($encabezados_busqueda);
            


                $condiciones = "Desde: ";
                $condiciones .= $this->fecha_inicio . " - ";
                $condiciones .= "Hasta: ";
                $condiciones .= $this->fecha_fin . " ";
                $condiciones .= "\n";
                $condiciones .= "Estado: ";
                $condiciones .= $this->estado . " - ";
                $condiciones .= "Cliente: ";
                $condiciones .= ($this->id_cliente == 0) ? " <Todos> " : $this->cliente . " ";

                $bordes  = 0;
                $alineacion = "L";
                $siguiente_linea = 0;
                $this->SetFont($tipo_fuente, '', 8);

                $this->SetY($posicion_y_condiciones);
                $this->SetX(40);

                $texto = $condiciones;

                $ancho_celda = 180;
                    
                $this->SetY(10);

                $this->SetX(60);

                $this->MultiCell($ancho_celda, 3.5, utf8_decode($texto), $bordes, $alineacion, $rellenar);

                $this->SetY(25);

                $encabezados_tabla = array(
                    "CRT",                //0
                    "Factura",              //2
                    "Exportador",           //3
                    "Origen",               //4          
                    "Destino",              //5
                    "Ad. de Salida",        //6
                    "Ad. de Ingreso",       //7
                    "Producto",             //8
                    "Valor",                //9
                    "TM",                   //10
                    "A Embarcar",           //11    //Peso total
                    "Obs.",                 //12
                    "%",                    //13
                  
                );

                
                $tamaños_enca = array(
                   
                    20, //1
                    25, //2
                    30, //3
                    25, //4
                    25, //5
                    20, //6
                    20, //7
                    15, //8
                    15, //9
                    15, //10
                    15, //11
                    40, //12
                    10 //13

                );

            
                $cantidad = count($encabezados_tabla);
            


                $siguiente_linea = 0;
                $bordes  = "T,B" ;
               // $bordes  = 1 ;

                $alineacion = "C";
            
                $this->SetX($pos_x_inicial);

                for ($i=0; $i < $cantidad; $i++) { 

                    $this->SetFont($tipo_fuente, '', 7); // ajustar el tamanho de fuente que se va a imprimir

                    $texto = $encabezados_tabla[$i];

                    $ancho_celda = $tamaños_enca[$i];

                    if($i == $cantidad -1){

                        $siguiente_linea = 1;

                    }
                    
                    $this->Cell($ancho_celda, 5, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                }

            }

            function Footer() {

                $inicio = 195;
                $bordes = "T" ; // bordes
                $alto_renglon_celda = 5;
            
                $this->SetFont($_SESSION["fuente_reportes"],'',7);
            
                $this->SetY($inicio + 6); // volver al inicio                                
                $this->Cell(0, $alto_renglon_celda, $_SESSION['impreso_por'], $bordes, 0, "L" ); // usuario
            
                $this->SetY($inicio + 6); // volver al inicio                                
                $this->Cell(0, $alto_renglon_celda, utf8_decode( "Pág. ".$this->PageNo()."/{nb}" ),$bordes, 0, "C"); // NUMERO de pagina                               
            
                $this->SetY($inicio + 6); // volver al inicio                                
                $this->Cell(0, $alto_renglon_celda, utf8_decode( "www.maxflet.com" ), $bordes, 0, "R"); // app_name
                
            }
        }


        if (($estado_habilitacion_usuario + $estado_habilitacion_cliente) > 1) { // SISTEMA HABILITADO ...

            $id_cliente = $_SESSION["cd_id_cliente"] ;
            $nivel_usuario = $nivel_actual_del_usuario ; 

            $f_ini = '' ;
            $f_fin = '' ;
                                    
            $id_estado = 0 ;

            $estado = -2 ;

            if ( isset ( $_POST["f_ini"] ) ) { $f_ini = limpiar_texto( $_POST["f_ini"] ) ; $v_f_ini = $_POST["f_ini"] ; }
            if ( isset ( $_POST["f_fin"] ) ) { $f_fin = limpiar_texto( $_POST["f_fin"] ) ; $v_f_fin = $_POST["f_fin"] ; }
            
                                        
            if ( isset ( $_POST["estado"] ) ) { $id_estado = intval( $_POST["estado"] ) ; }

            if ( isset ( $_POST["cliente"] ) ) { $id_solicitante = intval( $_POST["cliente"] ) ; }
                                            
            if ( isset ( $_POST["descripcion"] ) ) { $descripcion = limpiar_texto( $_POST["descripcion"] ) ; }

            $columna_fecha_buscar = 'fecha_doc' ;  

            if ( strlen( $f_ini . $f_fin ) > 0 ) { 

                if ( $f_ini == $f_fin ) { 

                    if ( strpos ( $f_ini, ' ' ) > 0 ) { 

                        $fecha_tmp = explode(' ', $f_ini) ;

                        $f_ini = fecha_a_formato_mysql($fecha_tmp[0]) ;

                        $fecha_tmp = explode(' ', $f_fin) ;

                        $f_fin = fecha_a_formato_mysql($fecha_tmp[0]) ;

                        $columna_fecha_buscar = 'fecha_doc' ; 

                    } else {

                        $f_ini = fecha_a_formato_mysql($f_ini) ;
                        $f_fin = fecha_a_formato_mysql($f_fin) ;

                    }

                } else { // NO son iguales....

                    if ( strlen( $f_ini ) > 0 ) {

                        $f_ini = fecha_a_formato_mysql($f_ini) ;

                    }

                    if ( strlen( $f_fin ) > 0 ) {

                        $f_fin = fecha_a_formato_mysql($f_fin) ;

                    }

                }

            }

            // FIN AJUSTAR FECHAS DE BUSQUEDA


            $orden = " ORDER BY  id " ;

            $sent_buscar = "SELECT * FROM v_lotes_crt WHERE id_estado > 0 AND id_cliente = " . $id_cliente ; // id_estado > 1 (que hayan sido entregados)

            $sent_buscar .= ( $n_pedido > 0 ) ? " AND n_pedido = " . $n_pedido : "" ;

            $sent_buscar .= ( $id_solicitante > 0 ) ? " AND id_solicitante = " . $id_solicitante : "" ;

            if ( $estado > -1 ) {

                $sent_buscar .= ( $estado == 1 ) ? " AND id_estado > 0 " : "" ; // NO NULOS
    
                $sent_buscar .= ( $estado == 0 ) ? " AND id_estado = 0 " : "" ; // Anulados
    
            }  


            if ( strlen( $f_ini ) > 0 ) { $sent_buscar .= " AND " . $columna_fecha_buscar . " >= '" . $f_ini . "'" ; }
            if ( strlen( $f_fin ) > 0 ) { $sent_buscar .= " AND " . $columna_fecha_buscar . " <= '" . $f_fin . "'" ; }

                if ( strlen ( $descripcion ) > 0 ) {

                    $condiciones = explode(' ', $descripcion) ;

                        foreach($condiciones as $condicion) {   
                                                                
                                $sent_buscar = $sent_buscar . " AND descripcion LIKE '%" . $condicion . "%'" ;
                        }

                }

            $sent_buscar = $sent_buscar . $orden ;


            //  ***************************                 CONECTAR A BBDD                 *************************************** //
            $conexion_bd = new mysqli( $srv_server, $srv_user, $srv_pass, $srv_db) ;        // conectar...
            if (mysqli_connect_errno()) {                                                   // verificar la conexión
                    header('Location: ' . $url_error_500 ) ;
            }
            //  ***************************                 FIN CONECTAR A BBDD             *************************************** //


            // -- si corresponde, buscar los datos de la planilla que se haya solicitado 
            if ($resultados = $conexion_bd->query($sent_buscar)) {

                while ($fila = $resultados->fetch_assoc()) {
                    $filas_detalles[] = $fila;
                }

                $resultados->free();    // liberar el resultset 

                foreach ($filas_detalles as $fila) { // DETALLES del vale de provision

                    $n_caja = $fila['n_caja'];
                    $moneda = $fila['moneda'];
                    $instrumento = $fila['instrumento'];
                    $concepto = $fila['concepto'];

                }

                //file_put_contents("resultado_sentencia.json",json_encode($filas_detalles));
            } else {
                $cod_error = 2;
                $respuesta = "Error al consultar la base de datos";
            }

            if ($id_planilla_buscar > 0) {

                // $sent_planilla = "SELECT id, n_doc, f_doc, id_moneda, referente_a, observaciones ";
                // $sent_planilla .= " FROM planillas WHERE id_estado > 0 AND operacion = " . $tipo_liquidacion;
                // $sent_planilla .= " AND id_persona = " . $id_persona . " AND id = " . $id_planilla;

                $sent_planilla = "SELECT id, n_doc, f_doc, id_moneda, referente_a, observaciones ";
                $sent_planilla .= " FROM planillas WHERE id_estado > 0 AND id = " . $id_planilla_buscar;

                $datos_planilla = $conexion_bd->query($sent_planilla); // obtener el estado del registro, si los hubiere...

                $filas_planilla = $datos_planilla->fetch_assoc();

                $planilla_moneda = $filas_planilla['id_moneda'];
                $planilla_n_doc = $filas_planilla['n_doc'];
                $planilla_f_doc = $filas_planilla['f_doc'];
                $planilla_referente_a = $filas_planilla['referente_a'];
                $planilla_observaciones = $filas_planilla['observaciones'];



                $datos_planilla->free(); // liberar memoria

                $planilla_f_doc = fecha_salida_con_hora($planilla_f_doc); // formato de salida, antes de enviar como respuesta

            }

            $periodo_ini = fecha_salida_corta_sin_hora($f_ini, '/');
            $periodo_fin = fecha_salida_corta_sin_hora($f_fin, '/');

            // $_SESSION['estado_registro_caja'] = $estado_registro_caja;

        
            
            if ($ancho_hoja > 0 and $alto_hoja > 0) {
                // $orientacion = ($orientacion_hoja > 0) ? 'P' : 'L';
                $orientacion = 'L';
                $pdf = new FPDF($orientacion, 'mm', [$ancho_hoja, $alto_hoja]); // DEFAULT
            } else {
                $pdf = new PDF();
            }
            $orientacion = 'L';
            $pdf = new PDF($orientacion, 'mm', [$ancho_hoja, $alto_hoja]);

                
            $pdf->fecha_inicio = ($v_f_ini != "") ? $periodo_ini : "";
            $pdf->fecha_fin = ($v_f_fin != "") ? $periodo_fin : "";
            

            $pdf->id_cliente = $id_solicitante;
            
            $pdf->cliente = $fila['solicitante'];

            if($proceso == -1) { $pdf->proceso = "<Todos>"; }
            if($proceso ==  0) { $pdf->proceso = "Importación"; }
            if($proceso ==  1) { $pdf->proceso = "Exportación"; }
          
            if($id_estado == -1) { $pdf->estado = "<Todos>"; }
            if($id_estado ==  0) { $pdf->estado = "Abiertos"; }
            if($id_estado ==  1) { $pdf->estado = "Cerrados"; }


            switch ($id_estado) {
        
                case -1:
                    $pdf->estado = "Todos";
                     break;
                case 0:
                   $pdf->estado = "Abierto";
                    break;
                
                case 1 :
                    $pdf->estado = "Cerrado";
                    break;
        
        
                
               
            
            }

          
            $pdf->setMargen_seguro();
            $pdf->setTitulo($titulo_reporte_hoja);
            $pdf->setsub_Titulo($sub_titulo_reporte_hoja);
            $pdf->SetTitle($titulo_documento);
            $pdf->SetAuthor(utf8_decode($autor_documento));
            $pdf->SetCreator($creador_documento);
            $pdf->SetSubject($asunto_documento);

            $pdf->SetLineWidth($grosor_borde);

            $pdf->AliasNbPages();
          
            $pdf->SetAutoPageBreak(true, 10);

           
            $pdf->AddPage();

            $ancho_celda_membrete_derecha = $ancho_total - $ancho_celda_membrete - $logo_width - $margen_logo_texto - $medida_img_validar;
            $punto_x_inicial_derecha = ($ancho_total - $ancho_celda_membrete_derecha) + ($margen_logo_texto + $medida_img_validar);
            $qr_validador_x = $punto_x_inicial_derecha - ($medida_img_validar + $qr_area_vacia);
            $ancho_celda_titulo = 11;
            $via_actual = 0;
            $bordes = ($imprimir_formato > 0) ? 1 : 0;

            while ($via_actual < $reporte_cant_vias) {    // ----------------------------------------------------------------      WHILE DE VIAS               --------------------------------------------------------------

                // echo "The number is: $x <br>";

                $altura_sumar = $altura_via * $via_actual; // altura que se va a sumar a la via, si original, CERO, duplicado o triplicado ira multiplicando

                $imprimir_marca_de_agua = false;



                // DEPENDIENDO DEL ESTADO DEL REGISTRO 

                /*
                                
                                if ( $via_actual == 1 ) { $imprimir_marca_de_agua = true ; $texto_marca_de_agua = 'DUPLICADO' ; }
    
                                if ( $via_actual == 0 AND $id_estado == 0 ) { $imprimir_marca_de_agua = true ; $texto_marca_de_agua = 'ANULADO' ; }
    
                                if ( $via_actual == 0 AND $id_estado == 6 ) { $imprimir_marca_de_agua = true ; $texto_marca_de_agua = 'ENTREGADO' ; }
    
                                */




                // SI CORRESPONDE, COLOCAR MARCA DE AGUA AL DUPLICADO 


                if ($imprimir_marca_de_agua) {

                    // MARCA DE AGUA

                    $pdf->SetFont('Arial', 'B', 40);
                    // $pdf->SetTextColor(255,192,203); // ROJO CLARO
                    $pdf->SetTextColor(192, 192, 192);

                    $marca_agua_angulo = 45;
                    $marca_agua_x = 90;
                    $marca_agua_y = 100;

                    $marca_agua_y = $marca_agua_y + $altura_sumar;

                    // $this->RotatedText(70,110, $_SESSION["marca_de_agua"] ,45);

                    $texto = $texto_marca_de_agua;

                    $pdf->Rotate($marca_agua_angulo, $marca_agua_x, $marca_agua_y);
                    $pdf->Text($marca_agua_x, $marca_agua_y, $texto);
                    $pdf->Rotate(0);
                }



                if ($via_actual > 0) {

                    // ---------------------------------------------------------    CELDA DE BORDE ---------------------------------------------------------------------------------------

                    $punto_x_actual = $punto_x_inicial;
                    $ancho_celda = $ancho_total;
                    $formato_fuente = '';
                    $tamano_fuente = 7;
                    $alineacion = 'L';

                    // $siguiente_linea = 2 ; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $pdf->SetFont($tipo_fuente, $formato_fuente, $tamano_fuente); // ajustar el tamanho de fuente que se va a imprimir

                    $bordes_anterior = $bordes;

                    $bordes = 0; // DIVISION SIN BORDES

                    $alto_renglon_celda = $alto_renglon_division;
                    $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                    // $texto = '. . . . . . . . . .. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .' ; // dato a mostrar                               
                    $texto = '';

                    $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO
                    $pdf->Cell($ancho_total, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar); // SIN BORDES... !!!

                    $bordes = $bordes_anterior;
                }



                // $pdf->SetFillColor(128,128,128);
                // $pdf->SetTextColor(64,64,64);
                $pdf->SetTextColor(0, 0, 0);

                // $pdf->SetFillColor(192,192,192);

                $pdf->SetFillColor(0, 0, 0);

                $pdf->SetDrawColor(0, 0, 0);



                // ----------------------------------------------------------------      MEMBRETE               --------------------------------------------------------------







                $punto_y_actual = $punto_y_inicial + $altura_sumar + 7;
                $punto_x_actual = $punto_x_inicial + $logo_width + $margen_logo_texto;
                $tamano_fuente = $tamano_fuente_default + 1; // por ser el nombre 


                if ($via_actual == 0) {
                    $pdf->SetY($punto_y_actual);
                } // solo al principio


                // $logo_top = $logo_top + $altura_sumar ;



                // DATOS DE LA EMPRESA 

                $formato_fuente = 'B';



                $ancho_celda = 260;
                $alto_renglon_celda = $membrete_alto;
                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)
                $alineacion = 'C';
                $alineacion_numeros = 'R';





                $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                $pdf->SetX(10.5);

                $texto = '';

                $pdf->Cell($ancho_celda, 8, $texto, '', $siguiente_linea, $alineacion, $rellenar);





                // $pdf->Image('logo_tp.jpg', 16, 13, 70, 10);

                $pdf->Cell($ancho_celda, 5, $texto, '', 0, $alineacion, $rellenar);





                //INICIO - EXTRAER LOS DATOS.
                if ($resultados_busqueda = $conexion_bd->query($sent_buscar)) {

                    while ($filas_resultado = $resultados_busqueda->fetch_assoc()) {    // obtener array asociativo 

                        $num_planilla = $filas_resultado['id_cliente'];
                    }

                    $resultados_busqueda->free();    // liberar el resultset 

                }
             

                $texto = '';

                $siguiente_linea = 2; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                $pdf->SetFont($tipo_fuente, 'B', 10); // ajustar el tamanho de fuente que se va a imprimir

                $pdf->SetX(20);

                $pdf->Cell($ancho_celda, 5, utf8_decode($texto), '', $siguiente_linea, 'R', $rellenar);



                //INICIO - EXTRAER LOS DATOS.
                if ($resultados_busqueda_estandar = $conexion_bd->query($sent_buscar)) {

                    while ($filas_resultado_estandar = $resultados_busqueda_estandar->fetch_assoc()) {    // obtener array asociativo 

                        $bordes = "";

                        $pdf->SetX(10);

                        $alineacion = "C";
                        
                        $siguiente_linea = 0;

                        $alto_renglon_celda = 5.5;

                        $suma_bruto += $filas_resultado_estandar['peso_bruto'];

                        $suma_neto +=$filas_resultado_estandar['peso_neto'];

                        $pdf->SetFont($tipo_fuente, '', 7); // ajustar el tamanho de fuente que se va a imprimir


                       
                        //CRT
                        $texto = $filas_resultado_estandar['crt'];

                        $pdf->Cell(20, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);


                        //Factura
                        $texto = '001-002-0000747';
                        $pdf->Cell(25, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);


                        //Exportador
                        $texto = substr($filas_resultado_estandar['rem_nombre'], 0, 16);
                        
                        $alineacion = "L";

                        $yRemNombre = $pdf->GetY();

                        $pdf->Cell(30, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);


                        //Origen
                        $texto = substr($filas_resultado_estandar['des_nombre'], 0, 10);

                        $yDestinatario = $pdf->GetY();

                        $pdf->Cell(25, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // Destino
                        $texto = substr($filas_resultado_estandar['con_nombre'], 0, 10);

                        $pdf->Cell(25, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $SetYOrigen = $pdf->GetY();


                         // Ad. de Salida
                        $texto = substr($filas_resultado_estandar['lugar_embarque'], 0, 10); //Ad. de salida

                        $pdf->Cell(20, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // Ad. de Ingreso
                        $texto = substr($filas_resultado_estandar['lugar_entrega'], 0, 10); //Ad. de Ingr.

                        $pdf->Cell(20, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);


                         //Producto
                        $texto = substr($filas_resultado_estandar['descripcion_producto'], 0 ,10); //Producto

                        $pdf->Cell(15, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);


                        //Valor
                        $alineacion = "R";
                        
                        $texto = $filas_resultado_estandar['peso_neto'];

                        $pdf->Cell(15, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                       //TM
                        $alineacion = "R";

                        $texto = formatMoneda($filas_resultado_estandar['peso_neto'], $id_moneda);//TM

                        $pdf->Cell(15, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);    

                        
                        //A Embarcar
                        $texto = formatMoneda($filas_resultado_estandar['peso_bruto'], $id_moneda);//A embarcar

                        $pdf->Cell(15, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        
                        //Observacion
                        $alineacion = "C";
                        
                        $texto = '01/03 vencimiento de despacho';
                        //$texto = substr($filas_resultado_estandar['descripcion_producto'], 0 ,10); //Producto

                        $pdf->Cell(40, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                        

                        //% Embarcado
                        $siguiente_linea = 2;
                        $texto = '20';

                       // $texto = formatMoneda($filas_resultado_estandar['valor_crt'], $id_moneda);//valor crt(antes de 2 f)

                        $pdf->Cell(10, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                        
                        
                    }

                    $resultados_busqueda->free();    // liberar el resultset 

                }


                $bordes = "T";

                        $pdf->SetX(10);

                        $alineacion = "R";
                        
                        $siguiente_linea = 0;

                        $alto_renglon_celda = 5.5;

                        $pdf->SetFont($tipo_fuente, 'B', 7); // ajustar el tamanho de fuente que se va a imprimir


                        $texto = "";

                        $pdf->Cell(20, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(25, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(30, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(25, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(25, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(20, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(20, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(15, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(15, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        //sumas
                        $texto = formatMoneda($suma_neto, $id_moneda);
                       
                        $pdf->Cell(15, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);
  
                        $texto = formatMoneda($suma_bruto, $id_moneda);
                      
                        $pdf->Cell(15, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);
                       
                      
                      
                        $texto = "";
                      
                        $pdf->Cell(40, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $pdf->Cell(10, $alto_renglon_celda, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);    


                $pdf->Output();

                $conexion_bd->close();

                //FIN - EXTRAER LOS DATOS.


                // ----------------------------------------------------------------      CONTENIDO DOCUMENTO               --------------------------------------------------------------



                $logo_top = $punto_y_inicial + (($altura_via + $cuerpo_valores_alto + $alto_renglon_division) * $via_actual) + 1; // 1mm para NO tapar el borde
                $logo_left = $punto_x_inicial + 1; // 1 mm para no tapar el borde





                // ----------------------------------------------------------------      FIN CONTENIDO DOCUMENTO               --------------------------------------------------------------


                $via_actual++; // aumentar el conteo de vias

            }

            if (strlen($tabla_datos) > 0) {

                $tabla = $tabla_header . $tabla_datos . $tabla_footer;

                // echo $tabla ;

                $cod_error = 0;
                $respuesta = ' ' . $cantidad_filas . ' registros encontrados.';
            }
        } else { // si no habilitado

            if ($estado_habilitacion_usuario < 1) {

                $cod_error = 2;
                $respuesta = 'Error! Usuario no habilitado.';
            }

            if ($estado_habilitacion_cliente < 1) {

                $cod_error = 2;
                $respueste = 'Error! Sistema inhabilitado.';
            }
        } // fin si no habilitado 


    } else {
        echo "!"; // por si se lo llama de onda !
    }
}
