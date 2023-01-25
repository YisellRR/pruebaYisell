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



$titulo_documento = 'Semilla de Reportes';

$titulo_reporte_hoja = "Semilla de Reportes";

$sub_titulo_reporte_hoja = "Sub Titulo";

$autor_documento = 'Usuario del Sistema';
$creador_documento = 'Desarrollador';
$asunto_documento = 'Factura Nro. 001-002-002656';




// cantidad de vias de este reporte
$reporte_cant_vias = 1; // DEFAULT 
$altura_via = 140;





$margen_izquierdo = 10;
$margen_derecha = 5;

$punto_x_inicial = 5;
$punto_y_inicial = 30;

$margen_logo_texto = 0;


$tamano_hoja = 0; // 1 A4, 2 OFICIO, 3 CARTA, 4 PERSONALIZADO
$orientacion_hoja = 1; // 0 horizontal, 1 vertical

// SI HOJA ES A4
$ancho_hoja = 210;
$alto_hoja = 297;


$tamano_encabezado = array(7,50,12,23,30,30,30,15,22,18,15,13,18,18,16.5);
$tamano_tittulo = array(7,30,12,23,30,30,30,15,22,18,15,13,18,18,16.5);



// SI HOJA A4

$grosor_borde = 0.3;

$membrete_alto = 20; // el alto del recuadro del membrete, en su totalidad

$alto_renglon_formato = 6; //

$membrete_izquierda_ancho = 100;




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







// -------------------------------------------------                        EVITAR CAMBIO DE SESION ENTRE PESTANHAS                 -----------------------------------------------------------
################################################################################################################################################################################################

// luego de cargar principal.php e iniciar la sesion con session_start, verificar la sesion actual en este script y compararla con la sesion que solicita datos a este script 

$id_usuario_llamada = 0;   // esto deberia obligar al usuario a iniciar sesion, o a recargar el formulario, en caso de cambio de sesion 
$id_cliente_llamada = 0;
$errores_de_sesion = 0;

$usuario_autorizado = false;


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
if($_SESSION['id_usuario']  == 14){
    $usuario_autorizado = true;
}else{
    $usuario_autorizado = false;
   
}
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

// echo $_SESSION = $_SESSION["url_logo"];
if($usuario_autorizado ){

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






                public $id_cliente;
                public $id_usuario;
                public $id_rubro;
                public $id_responsable;

                public $pedido;
                public $fecha_inicio;
                public $fecha_fin;
                public $cliente;
                public $flota;
                public $usuario;
                public $chofer;
                public $rubro;
                public $responsable;

                
            

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


                public function getMargen_top(){
                    return $this->margen_top;
                }
            
                public function setMargen_top($margen_top){
                    $this->margen_top = $margen_top;
                }
            
                public function getMargen_izquierdo(){
                    return $this->margen_izquierdo;
                }
            
                public function setMargen_izquierdo($margen_izquierdo){
                    $this->margen_izquierdo = $margen_izquierdo;
                }
            
                public function getMargen_derecho(){
                    return $this->margen_derecho;
                }
            
                public function setMargen_derecho($margen_derecho){
                    $this->margen_derecho = $margen_derecho;
                }



                public function getPos_x_inicial(){
                    return $this->pos_x_inicial;
                }
            
                public function setPos_x_inicial($pos_x_inicial){
                    $this->pos_x_inicial = $pos_x_inicial;
                }
            
                public function getPos_y_inicial(){
                    return $this->pos_y_inicial;
                }
            
                public function setPos_y_inicial($pos_y_inicial){
                    $this->pos_y_inicial = $pos_y_inicial;
                }
            
                public function getPosicion_x_condiciones(){
                    return $this->posicion_x_condiciones;
                }
            
                public function setPosicion_x_condiciones($posicion_x_condiciones){
                    $this->posicion_x_condiciones = $posicion_x_condiciones;
                }
            
                public function getPosicion_y_condición(){
                    return $this->posicion_y_condición;
                }
            
                public function setPosicion_y_condición($posicion_y_condición){
                    $this->posicion_y_condición = $posicion_y_condición;
                }

                public function get_pos_Titulo(){
                    return $this->titulo;
                }
            
                public function set_pos_Titulo($titulo){
                    $this->titulo = $titulo;
                }

            
                public function getTitulo(){
                    return $this->titulo;
                }
            
                public function setTitulo($titulo){
                    $this->titulo = $titulo;
                }
            
                public function getSub_titulo(){
                    return $this->sub_titulo;
                }
            
                public function setSub_titulo($sub_titulo){
                    $this->sub_titulo = $sub_titulo;
                }

                function Header() {

                    $tipo_fuente = "arial";
                    $logo_top = $_SESSION["logo_vales_top"];
                    $logo_left = $_SESSION["logo_vales_left"];
                    // $logo_width = $_SESSION["logo_vales_width"];
                    // $logo_height = $_SESSION["logo_vales_height"];
                    $logo_width = 15;
                    $logo_height = 10;

                    $logo_left = $this->margen_izquierdo;
                    $logo_top = $this->margen_top;
    
                    $bordes = 0;

                    $pos_x_inicial = 10;
    
                    $pos_y_inicial = 10;

                    $ancho_logo_reportes = 20;
    
                    $alto_logo_reportes = 0;
    
    
    
                    $posicion_x_condiciones = $this->GetPageWidth() / 2;
                    $posicion_y_condiciones = 20;

                    
                    // Logo
                    $this->Image($_SESSION["url_logo"], $logo_left, $logo_top, $ancho_logo_reportes, $alto_logo_reportes); // L, T, W, EE



                   
                   $posicion_titulo = $this->margen_izquierdo ;
                
                    $this->SetY($this->margen_top);

                    $ancho_celda_titulo = $this->GetPageWidth()-($this->margen_derecho + $this->margen_izquierdo);

                    $this->SetX($posicion_titulo);

                    $ancho_celda = $ancho_celda_titulo;

                    $tamaño_margen_seguro = $ancho_celda_titulo;

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
                        ($this->periodo != NULL) ? $this->periodo: "<Todos>",
                        
                        "Cliente:",
                        ($this->id_cliente > 0) ? substr($this->cliente, 0, 15) : "<Todos>",
                        
                        "Flota:",
                        $this->flota,
                       
                        "Usuario:",
                        ($this->id_usuario > 0) ? $this->usuario : "<Todos>" ,

                        "Rubro:",
                        ($this->id_rubro > 0) ? $this->rubro : "<Todos>",

                        "Responsable:",
                        ($this->id_responsable > 0) ? $this->responsable : "<Todos>",

                    );

                    $tamaños_enca = array(
                        12,
                        22,
                        12,
                        30,
                        12,
                        12,
                        12,
                        18,
                        12,
                        20,
                        15,
                        20
                    );
                    $cantidad = count($encabezados_busqueda);
                


                    $siguiente_linea = 0;
                    $bordes = "";
                    $alineacion = "C";

                    for ($i=0; $i < count($tamaños_enca); $i++) { 
                        $suma_anchos = $tamaños_enca[$i];
                    }

                    

                    

                     
                    $this->SetY($posicion_y_condiciones);

                    $this->SetX($posicion_x_condiciones - 115);
                    
                    for ($i=0; $i < $cantidad; $i++) {
                        $this->SetFont($tipo_fuente, '', 7); // ajustar el tamanho de fuente que se va a imprimir

                        $texto = $encabezados_busqueda[$i];

                        $ancho_celda = $tamaños_enca[$i];   

                        if($i == $cantidad -1){

                            $siguiente_linea = 1;

                        }

                        
                        if($i%2==1) {
                            
                            $this->Cell($ancho_celda, 10, utf8_decode($texto), $bordes, $siguiente_linea, "L", $rellenar);
                            
                        } else {
                            
                            $this->Cell($ancho_celda, 10, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                        }
                        

                    }

                    $encabezados_tabla = array(
                        "N°",               //0
                        "Fecha",            //1
                        "Tractor",         //2
                        "Chofer",          //3
                        "Cliente",          //4          
                        "Origen",           //5
                        "Destino",          //6
                        "Tipo Carga",         //7
                        "Remisión",            //8
                        "Cant. Salida",           //9
                        "Kg.Falt.",          //14
                        "F. Carga",        //12
                        "Cant. Llegada",        //11
                        "F. Descarga",        //13
                        // "U. Med",           //10
                        "Estado",        //15
                        // "V.Pagar",           //16
                    );

                    $tamaños_enca = array(
                        7,
                        30,
                        12,
                        23,
                        30,
                        30,
                        30,
                        15,
                        22,
                        18,
                        15,
                        13,
                        18,
                        18,
                        16.5
                    );
                

                    $cantidad = count($encabezados_tabla);
            
                    $siguiente_linea = 0;
                    $bordes  = 1;
                    $alineacion = "C";

                    $this->SetFont($tipo_fuente, '', 7); 
                   
                
                    $this->SetX($pos_x_inicial);

                    for ($i=0; $i < $cantidad; $i++) { 

                        $this->SetFont($tipo_fuente, '', 7); // ajustar el tamanho de fuente que se va a imprimir

                        $texto = $encabezados_tabla[$i];

                        $ancho_celda = $tamaño_margen_seguro / $cantidad;

                        if($i == $cantidad -1){

                            $siguiente_linea = 1;

                        }
                        
                        $this->Cell($ancho_celda, 5, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                    }

                   // $this->Write(10, "Ancho Celda enca ".$ancho_celda);

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
                // $nivel_usuario = intval ( $_SESSION["nivel_usuario"] ) ;
                $nivel_usuario = $nivel_actual_del_usuario ; // a cada consulta, verificar el nivel del usuario
                                            
                $fletes_fecha_hora = $_SESSION["fletes_fecha_hora"] ; // validar si mostrar la columna HORA
    
                $f_ini = '' ;
                $f_fin = '' ;
                $id_tipo_carga = 0 ;
                $id_estado = 0 ;
    
                $id_responsable = 0;
                $descripcion = '' ;
    
                $id_pedido = NULL ;
    
                $estado = -2 ;
    
                $tercero = -1 ;
                $id_usuario = 0 ;
                $id_rubro = 0 ;
    
                    if ( isset ( $_POST["f_ini"] ) ) { $f_ini = limpiar_texto( $_POST["f_ini"] ) ; }
                    if ( isset ( $_POST["f_fin"] ) ) { $f_fin = limpiar_texto( $_POST["f_fin"] ) ; }
                                            
                    if ( isset ( $_POST["tipo_carga"] ) ) { $id_tipo_carga = intval( $_POST["tipo_carga"] ) ; }
                    if ( isset ( $_POST["estado"] ) ) { $id_estado = intval( $_POST["estado"] ) ; }
    
                    if ( isset ( $_POST["tipo_tramo"] ) ) { $tipo_tramo = intval( $_POST["tipo_tramo"] ) ; }
                    if ( isset ( $_POST["n_pedido"] ) ) { $id_pedido = intval( $_POST["n_pedido"] ) ; }
                    if ( isset ( $_POST["solicitante"] ) ) { $id_solicitante = intval( $_POST["solicitante"] ) ; }
    
                    if ( isset ( $_POST["chapa_tractor"] ) ) { $id_tractor = intval( $_POST["chapa_tractor"] ) ; }
                    if ( isset ( $_POST["chofer"] ) ) { $id_chofer = intval( $_POST["chofer"] ) ; }
    
                    if ( isset ( $_POST["responsable"] ) ) { $id_responsable = intval( $_POST["responsable"] ) ; }
    
                    if ( isset ( $_POST["estado"] ) ) { $estado = intval( $_POST["estado"] ) ; }
    
                    if ( isset ( $_POST["tercero"] ) ) { $tercero = intval( $_POST["tercero"] ) ; }
                    if ( isset ( $_POST["usuario"] ) ) { $id_usuario = intval( $_POST["usuario"] ) ; }
                    if ( isset ( $_POST["rubro_carga"] ) ) { $id_rubro = intval( $_POST["rubro_carga"] ) ; }
    
                    if ( isset ( $_POST["descripcion"] ) ) { $descripcion = limpiar_texto( $_POST["descripcion"] ) ; }
    
    
                    // AJUSTAR FECHAS DE BUSQUEDA 1/2 sobre corregir fechas 
    
                    // $columna_fecha_buscar = 'f_doc' ; // SI tiene hora, SI no... fecha_doc 
    
    
                    // AJUSTAR FECHAS DE BUSQUEDA 1/2 sobre corregir fechas 
    
                    $columna_fecha_buscar = ( $fletes_fecha_hora > 0 ) ? 'f_doc' : 'fecha_doc' ; // SI tiene hora, SI no... fecha_doc 
    
    
                    if ( strlen( $f_ini . $f_fin ) > 0 ) { // si uno de ellos tienen algo cargado ...
    
                        if ( $f_ini == $f_fin ) { // y son iguales, y tienen hora establecida
    
                            if ( strpos ( $f_ini, ' ' ) > 0 ) { // si son iguales, pues, analizando uno ya sabremos si ambos tienen hora...
    
                                // igualar las fechas...
                                $fecha_tmp = explode(' ', $f_ini) ;
                                // $f_ini = $fecha_tmp[0] ;
                                $f_ini = fecha_a_formato_mysql($fecha_tmp[0]) ;
    
                                // ahora, redondear la fecha de fin
                                $fecha_tmp = explode(' ', $f_fin) ;
                                // $f_fin = $fecha_tmp[0] ;
                                $f_fin = fecha_a_formato_mysql($fecha_tmp[0]) ;
    
                                $columna_fecha_buscar = 'fecha_doc' ; // BUSCAR EN ESTA COLUMNA ... !
    
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
    
                        $columnas_opcionales = '' ;
    
                        $titulo_columna_responsable = '' ;
                        $titulo_columna_responsable_nombre_fantasia = '' ;
    
                        $titulo_columna_precio = 'Precio' ; // Default si solo flota propia
                        $titulo_columna_moneda = 'Moneda' ;
                        $titulo_columna_valor = 'Cobrar' ;
                        $titulo_columna_planilla = 'Planilla' ;
    
                                                
    
    
    
                    $orden = " ORDER BY f_doc, n_doc, solicitante_nombre, id_estado " ;
                                       
                    // flete cuando descarga sera estado 6 
    
                    $sent_buscar = "SELECT * FROM v_ordenes_carga WHERE id_estado > 5 AND id_cliente = " . $id_cliente ; // id_estado > 1 (que hayan sido entregados)
    
                                                $sent_buscar .= ( $id_tipo_carga > 0 ) ? " AND id_tipo_carga = " . $id_tipo_carga : "" ;
                                            
    
                                                $sent_buscar .= ( $tipo_tramo > 0 ) ? " AND id_pedido > 0 " : "" ;
                                                $sent_buscar .= ( $id_pedido > 0 ) ? " AND id_pedido = " . $id_pedido : "" ;
                                                $sent_buscar .= ( $id_solicitante > 0 ) ? " AND id_solicitante = " . $id_solicitante : "" ;
    
                                                $sent_buscar .= ( $id_tractor > 0 ) ? " AND id_tractor = " . $id_tractor : "" ;
                                                $sent_buscar .= ( $id_chofer > 0 ) ? " AND id_chofer = " . $id_chofer : "" ;
    
                                                $sent_buscar .= ( $id_responsable > 0 ) ? " AND id_responsable = " . $id_responsable : "" ;
    
    
    
                                                if ( $estado > -2 ) {
    
                                                    $sent_buscar .= ( $estado == -1 ) ? " AND id_estado > 0 " : "" ; // NO NULOS
        
                                                    $sent_buscar .= ( $estado == 0 ) ? " AND id_estado = 0 " : "" ; // Anulados
        
                                                    $sent_buscar .= ( $estado == 1 ) ? " AND id_planilla IS NULL " : "" ; // A Liquidar
        
                                                    $sent_buscar .= ( $estado == 2 ) ? " AND id_planilla > 0 " : "" ; // Liquidados
                                                    
                                                }  
    
    
                                                $sent_buscar .= ( $id_usuario > 0 ) ? " AND id_usuario = " . $id_usuario : "" ; // usuario que ha emitido la OC 
    
                                                $sent_buscar .= ( $tercero > -1 ) ? " AND tercero = " . $tercero : "" ; // si es tercero
    
                                                $sent_buscar .= ( $id_rubro > 0 ) ? " AND id_rubro = " . $id_rubro : "" ; // si es tercero
    
    
                                                    // columna fechas 2/2 
                                                    if ( strlen( $f_ini ) > 0 ) { $sent_buscar .= " AND " . $columna_fecha_buscar . " >= '" . $f_ini . "'" ; }
                                                    if ( strlen( $f_fin ) > 0 ) { $sent_buscar .= " AND " . $columna_fecha_buscar . " <= '" . $f_fin . "'" ; }
                                                    // fin columna fechas 
    
                                                        if ( strlen ( $descripcion ) > 0 ) {
    
                                                            $condiciones = explode(' ', $descripcion) ;
    
                                                                foreach($condiciones as $condicion) {   
                                                                    
                                                                        $sent_buscar = $sent_buscar . " AND descripcion LIKE '%" . $condicion . "%'" ;
                                                                }
    
                                                        }
    
                                            $sent_buscar = $sent_buscar . $orden ;
    
                                            // echo 'sent: ' . $sent_buscar ;
    
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
                } else {
                    $cod_error = 2;
                    $respuesta = "Error al consultar la base de datos";
                }
    
               
    
                $periodo_ini = fecha_salida_corta_sin_hora($f_ini, '/');
                $periodo_fin = fecha_salida_corta_sin_hora($f_fin, '/');
                
               
    
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
                $pdf->id_usuario = $id_usuario;
                $pdf->id_rubro = $id_rubro;
                $pdf->id_responsable = $id_responsable;
    
                $pdf->responsable = $fila['responsable_nombre'];
                $pdf->cliente = $fila['solicitante_nombre'];
                $pdf->usuario = $fila['usuario'];
                $pdf->rubro = $fila['rubro_carga'];
                if($tercero == -1) {$pdf->flota = "<Todos>"; }
                if($tercero ==  0) {$pdf->flota = "Propia"; }
                if($tercero ==  1) {$pdf->flota = "Terceros"; }
    
                switch ($id_estado) {
    
                   
                    case -2:
                        $pdf->estado = "Todos";
                         break;
                    case -1:
                        $pdf->estado = "No Nulos";
                         break;
    
                    case 0:
                    $pdf->estado = "Todos";
                        break;
                        
                    case 1:
                        $pdf->estado = "Liquidados";
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
                $pdf->AddPage();
                $pdf->SetAutoPageBreak(true, 10);
    
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
    
                    $pdf->SetX($pdf->getMargen_izquierdo());
    

                    $encabezados_tabla = array(
                        "N°",               //0
                        "Fecha",            //1
                        "Tractor",         //2
                        "Chofer",          //3
                        "Cliente",          //4          
                        "Origen",           //5
                        "Destino",          //6
                        "Tipo Carga",         //7
                        "Remisión",            //8
                       "",           //9
                        "Kg.Falt.",          //14
                        "F. Carga",        //12
                        "",        //11
                        "F. Descarga",        //13
                        // "U. Med",           //10
                        "Estado",        //15
                        // "V.Pagar",           //16
                    );

                 
                

                    $cantidad = count($encabezados_tabla);
            
                    $siguiente_linea = 0;
                    $bordes  = "T";
                    $alineacion = "C";

                    $pdf->SetFont($tipo_fuente, '', 7); 

                    $ancho_celda_titulo = $pdf->GetPageWidth()-($pdf->getMargen_derecho() + $pdf->getMargen_izquierdo());

                    $pdf->SetX($pdf->getMargen_izquierdo());

                    

                    $tamaño_margen_seguro = $ancho_celda_titulo;
                   
                    $ancho_celda = $tamaño_margen_seguro / $cantidad;
                    //INICIO - EXTRAER LOS DATOS.
                    if ($resultados_busqueda = $conexion_bd->query($sent_buscar)) {
    
                        while ($filas_resultado = $resultados_busqueda->fetch_assoc()) {    // obtener array asociativo 
    
                            $num_planilla = $filas_resultado['id_cliente'];
                        }

                        
    
                        $resultados_busqueda->free();    // liberar el resultset 
    
                    }
                    //FIN - EXTRAER LOS DATOS.
    
    
    
                    
    
                    
                    //INICIO - EXTRAER LOS DATOS.
                    if ($resultados_busqueda_estandar = $conexion_bd->query($sent_buscar)) {

                        while ($fila = $resultados->fetch_assoc()) {
                            $filas_detalles[] = $fila;
                        }
                        
                        for($i = 0; $i < count($encabezados_tabla); $i++) {

                            $pdf->SetX($punto_x_inicial);

                            $siguiente_linea = ( $i == (count($encabezados_tabla) - 1) ) ? 0 : 2;

                            $pdf->Cell($tamano_tittulo[$i], 5, $tamano_tittulo[$i], 1, $siguiente_linea, $rellenar);
                            
                        }
                        
                        //file_put_contents("consulta.json",json_encode($filas_resultado_estandar));
    
                        $resultados_busqueda->free();    // liberar el resultset 
    
                    }
    
    
                    $bordes = 0;

                    $pdf->SetX($pdf->getMargen_izquierdo());
    

                    $encabezados_tabla = array(
                        "",               //0
                        "",            //1
                        "",         //2
                        "",          //3
                        "",          //4          
                        "",           //5
                        "",          //6
                        " ",         //7
                        "",            //8
                        formatMoneda($suma_cant_salida, $id_moneda),           //9
                        "",          //14
                        "",        //12
                        formatMoneda($suma_cant_llegada, $id_moneda),        //11
                        "",        //13
                        // "U. Med",           //10
                        "",        //15
                        // "V.Pagar",           //16
                    );

                 
                    $bordes = "T";

                    $cantidad = count($encabezados_tabla);
            
                    $siguiente_linea = 0;
                  
                    $alineacion = "C";

                    $pdf->SetFont($tipo_fuente, '', 7); 

                    $ancho_celda_titulo = $pdf->GetPageWidth()-($pdf->getMargen_derecho() + $pdf->getMargen_izquierdo());

                    $pdf->SetX($pdf->getMargen_izquierdo());

                    $ancho_celda = $ancho_celda_titulo;

                    $tamaño_margen_seguro = $ancho_celda_titulo;
                   
                
                 

                    for ($i=0; $i < $cantidad; $i++) { 

                        $pdf->SetFont($tipo_fuente, '', 7); // ajustar el tamanho de fuente que se va a imprimir

                        $texto = $encabezados_tabla[$i];

                        $ancho_celda = $tamaño_margen_seguro / $cantidad;



                        if($i == $cantidad -1){

                            $siguiente_linea = 1;

                        }

                        $texto_limpio = str_replace(".","",$texto);

                        if(is_numeric($texto_limpio)==true){

                            $alineacion = "R";

                        }else{
                            $alineacion = "L";
                        }
                        
                        $pdf->Cell($ancho_celda, 5, utf8_decode($texto), $bordes, $siguiente_linea, $alineacion, $rellenar);

                    }

                 

                
    
                        
    
                    // if( $id_cliente == 15 ) {
    
                        $sent_buscar = "SELECT * FROM v_pedidos WHERE id_cliente = " .$id_cliente . " AND id = " . $id_pedido;
    
                        if ($resultados = $conexion_bd->query($sent_buscar)) {
    
                            while ($fila = $resultados->fetch_assoc()) {
                                $filas_detalles[] = $fila;
                            }

                            
    
                            $resultados->free();    // liberar el resultset 

    
                            foreach ($filas_detalles as $fila) { // DETALLES del vale de provision
    
                                $cantidad = $fila['cantidad'];
                                $cant_cargado = $fila['cantidad_retirada'];
    
                            }
                        } 
    
                        $bordes = 1 ;
    
                        $ancho_resumen_titulo = 22 ;
                        $ancho_resumen_cantidad = 12 ;
                        $ancho_resumen_valor = 22 ;
    
                        $alto_renglon_celda = 6 ;
    
                        // $resumen_titulos = 'Total lote,Total cargado,Total a cargar,Pendiente' ;
                        $resumen_titulos = 'Total lote,Total cargado,Total a cargar' ;
    
    
                        $pdf->SetX( $pos_x_inicial );
    
                        $siguiente_linea = 0;
    
    
                        $titulos = explode( ',', $resumen_titulos ) ;
    
                        $pos_y_inicial_resumen = $pdf->GetY();
    
                        $z = 0 ; // contar cada grupo de dato
                        $y = 0 ;
    
                        $pos_x_resumen = $pos_x_inicial + 10;
    
                        $pdf->SetX( $pos_x_resumen );
    
                        for ( $w = 0; $w < count( $titulos ); $w++ ){
    
    
                            $res_cantidad = '' ;
                            $res_valor = '' ;
    
    
                            switch ($w) {
                                case 0:
                                    $res_cantidad = $cantidad_flete; $res_valor = $suma_valor_flete; break;
                                case 1:
                                    $res_cantidad = $cantidad_combustible; $res_valor = $suma_combustible; break;
                                case 2:
                                    $res_cantidad = $cantidad_viatico; $res_valor = $suma_viatico; break;
                                case 3:
                                    $res_cantidad = $cantidad_faltante; $res_valor = $suma_faltante; break;
                                case 4:
                                    $res_cantidad = $cantidad_anticipo; $res_valor = $suma_anticipo; break;
                                case 5:
                                    $res_cantidad = $cantidad_debito; $res_valor = $suma_debito; break;
                                case 6:
                                    $res_cantidad = $cantidad_credito; $res_valor = $suma_credito; break;
                                case 7:
                                    $res_cantidad = $cantidad_otros; $res_valor = $suma_otros; break;
                            }
    
    
                            if ( $w == 0 || $w == 4 ) { 
                                $res_valor = $res_valor; 
                            } else {
                                $res_valor = 0 - $res_valor ; // negativo (restar)
                            }
                            
    
                            $res_valor = formatMoneda( $res_valor, $planilla_id_moneda );
    
    
                            $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)
    
    
                            if ( $y == 4 ) { 
                                
                                $y = 0 ; 
    
                                $pdf->SetY( $pos_y_inicial_resumen );
    
                                $pos_x_resumen = $pos_x_resumen + $ancho_resumen_titulo + $ancho_resumen_cantidad + $ancho_resumen_valor ;                        
                            
                            } // ... luego
    
                            
                            if ( $z == 2 ) { 
                                
                                // $y++;
    
                                $z = 0 ; 
                                
                                // $pdf->SetX( $pos_x_resumen );
                            
                            } // ... luego  
    
    
                            
                            $pdf->SetX( $pos_x_resumen );
    
                            
    
                            $alineacion = 'L' ;
    
                            $ancho_celda = $ancho_resumen_titulo ;
    
                            $texto = utf8_decode( $titulos[$w] ) ;
    
                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
    
                            
                            
                            $alineacion = 'R' ;
    
                            
                            $ancho_celda = $ancho_resumen_cantidad ;
    
                            $texto = "" ;
    
                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
    
    
                            $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)
    
    
                            $ancho_celda = $ancho_resumen_valor ;
    
                            $texto = "" ;
    
                            if($w == 0) { $texto = formatMoneda($cantidad, 1) ;}
    
                            if($w == 1) { $texto = formatMoneda($cant_cargado, $id_moneda) ;}
    
                            $total_cargar = ($cantidad - $cant_cargado);
    
                            if($w == 2) { $texto = formatMoneda($total_cargar, $id_moneda) ;}
    
                            // if($w == 3) { $texto = formatMoneda(($cantidad - $total_cargar ), $id_moneda) ;}
    
                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
    
    
                            $y++;
                            $z++;
    
                        }
                    // }
    
    
                    $pdf->Output();
    
                    $conexion_bd->close();
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

   
}else{
    header("Location:https://maxflet.app/inicio.php");
    //echo $id_usuario_llamada;
}

