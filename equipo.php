<?php

// require("fpdf.php"); // SE DEFINE EN ROATATION, donde tambien se ajusta las funciones de la marca de agua
require("rotation.php");
require("../principal.php");
require("../funciones.php");

require_once("qr/qrlib.php"); // Lib de codigo QR

$cod_error = 2;
$respuesta = "Error! No se pudo completar la solicitud";


$obj = new stdClass();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
} // PRIMERO iniciar si aun no se ha hecho....


// TMP
$validar_reporte = false;

$tipo_doc_validar = "equipo";

$nom_archivo_descargar = "equipo";

$destino_reporte = "i"; // inline, ver en el navegador utilzando el plugin si disponible

$mostrar_propietario = 1 ; // 0 - 1 

$n_o_c = 0;
// $notas = "" ;

$id_tractor = 0;
$id_carreta = 0;
$id_chofer = 0;
$id_propietario = 0;


$cargar_fotos_ficha = 1;

// VARIABLES NUEVAS.
$ancho_desc = 15;


$ancho_sub_titulo = 11;

$ancho_celda_adicionales_titulo = 14 ;
$ancho_celda_adicionales_dato = 26 ;

$ancho_celda_adicionales_vencimiento = 10 ;

$ancho_celda_espacio = 4 ;
$ancho_celda_espacio_adicionales = 1.5 ;

$ancho_celda_dato_estrecho = 14 ;
//if ($_SESSION^["cd_id_cliente"] == 5) 
$ancho_celda_dato_extenso = 33 ; 

$ancho_celda_vacia = 10;

$alto_renglon_celda_datos = 7 ;

$alto_renglon_titulos = 8 ;

$ancho_celda_dato_estrecho_anterior = $ancho_celda_dato_estrecho ; 


$cant_documentos_adicionales = 0 ;

$cant_documentos_adicionales_tractor = 0 ;
$cant_documentos_adicionales_carreta = 0 ;
$cant_documentos_adicionales_chofer = 0 ;
$cant_documentos_adicionales_propietario = 0 ;



$formato_imagen = '';


// if ( isset ( $_SESSION["id_usuario"] ) ) {  // SI SESION INICIADA....

if (isset($_POST["id"])) {
    $id = $_POST["id"];
}
if (isset($_POST["destino"])) {
    $destino_reporte = $_POST["destino"];
}
if (isset($_POST["propietario"])) {
    $mostrar_propietario = intval( $_POST["propietario"]) ;
}
if (isset($_POST["chapa"])) {
    $chapa_ = $_POST["chapa"];
}
if (isset($_POST["carreta"])) {
    $carreta_ = $_POST["carreta"];
}
if (isset($_POST["propietario"])) {
    $mostrar_propietario = intval( $_POST["propietario"]) ;
}


// mostrar_propietario

// TEMPORALMENTE PARA configurar

if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
if (isset($_GET["destino"])) {
    $destino_reporte = $_GET["destino"];
}

if (isset($_GET["propietario"])) {
    $mostrar_propietario = intval( $_GET["propietario"]);
}




if ($id > 0) {  // SI SESION INICIADA....
    // if ( isset ( $_GET["id"] ) ) {  // SI SESION INICIADA....

    require('../conn/conexion.php');

    //  ***************************                 CONECTAR A BBDD                 *************************************** //

    $conexion_bd = new mysqli($srv_server, $srv_user, $srv_pass, $srv_db);        // conectar...

    if (mysqli_connect_errno()) {                                                   // verificar la conexion
        header('Location: ' . $url_error_500);
    }

    //  ***************************                 FIN CONECTAR A BBDD             *************************************** //


    $este_archivo = $_SERVER['PHP_SELF'];

    $ruta = urlDirectorioArchivo($este_archivo);

    $_SESSION["marca_de_agua"] = ""; // RESET !
    $_SESSION["d_n_doc"] = 0;
    $_SESSION["d_fecha_doc"] = "";
    $_SESSION["d_n_contrato"] = "";

    $_SESSION["txt_mail_html"] = "";
    $_SESSION["asunto_mail"] = "";
    $_SESSION["nom_adjunto_mail"] = "";

    $_SESSION["archivo_str"] = ""; // EVENTUALMENTE, enviar el PDF como string ( base64 ) ;
    // $_SESSION["destinatarios_cc"] = "" ; // Borrar de los eventuales envios anteriores
    $destinatarios_cc = "";

    // preparar la firma
    $firma = "";
    $firma_mail_html = $_SESSION["firma_mail_html"];
    $firma_mail_html_css = $_SESSION["firma_mail_html_css"];

    // INICIALMENTE, imprimir o enviar el PDF
    $imprimir_pdf = true;  // ---------------------------------------------        IMPRIMIR O ENVIAR EL DOCUMENTO EN PDF 
    // INICIALMENTE, preparar el texto HTML para el envio del correo
    $preparar_html = true;  // ---------------------------------------------       PREPARAR TEXTO HTML PAR ENVIO DE CORREO 



    $ancho_total = $reporte_vertical_ancho; // principal
    $tamano_fuente_default = $tamano_fuente_reportes;
    $tamano_fuente_titulo = $tamano_fuente_default;

    $ancho_celda_default = 90;
    $ancho_celda_membrete = 90;
    $ancho_celda_membrete_derecha = 44;

    $tipo_fuente = $fuente_reportes;

    // DIMENSIONES DEL LOGO ---------------------------------------------------------------------------------------------------------------------
    $_SESSION["tipo_reporte"] = 0; // 0 VALES 1 INFORMES cargara en conf_reportes.php
    require('../conf_reportes.php');


   
    

    $url_fotos = $_SESSION["url_fotos"];
    $cod_cliente = strtolower($_SESSION["cd_cod_cliente"]);
    $id_cliente = $_SESSION["cd_id_cliente"];
    $nom_usuario = strtolower($_SESSION["nom_usuario"]);
    $porcentaje_fotos = $_SESSION["porcentaje_fotos"];

    $firma_mail_jpg = $_SESSION["firma_mail_jpg"];



    $logo_top = $_SESSION["logo_vales_top"];
    $logo_left = $_SESSION["logo_vales_left"];
    $logo_width = $_SESSION["logo_vales_width"];
    $logo_height = $_SESSION["logo_vales_height"];

    $membrete_top = $_SESSION["membrete_vales_top"];
    $membrete_left = $_SESSION["membrete_vales_left"];
    $membrete_width = $_SESSION["membrete_vales_width"];
    $membrete_height = $_SESSION["membrete_vales_height"];


    




    // BUSCAR DATOS DEL DOCUMENTO A IMPRIMIR 

    $sql = "SELECT * FROM v_vehiculos WHERE id_cliente = " . $id_cliente . " AND id = " . $id;
    // $sql = "SELECT * FROM v_vehiculos WHERE id_cliente = " . $id_cliente . " AND id IN(" . $chapa_ .','. $carreta_ . ")";

    // echo $sql ;

    if ($resultados = $conexion_bd->query($sql)) {

        while ($fila = $resultados->fetch_assoc()) {    // obtener array asociativo 

            $id_tractor = intval($fila["id"]);

            $n_o_c = intval($fila["n_doc"]);
            $num_doc = $fila["num_doc"];

            $f_doc = fecha_salida_sin_hora($fila['f_doc'], '/'); // $f_doc = fecha_salida_sin_hora($fecha) ;
            $f_reg = fecha_salida_sin_hora($fila['f_reg']); // $f_doc = fecha_salida_sin_hora($fecha) ;

            $milisegundos_reg = fecha_milisegundos($fila['f_reg']); // $f_reg = milisegundos del registro($f_reg) ; 
            $ano_reg = fecha_ano($fila['f_reg']); // $f_reg = ano del registro ;  

            $n_contrato = $fila["n_contrato"];

            //Tractor.
            $tractor_tipo_vehiculo = $fila["tractor_tipo_vehiculo"];
            $chapa_tractor = $fila["chapa_tractor"];
            $tractor_chassis = $fila["chassis"];
            $tractor_marca = $fila["marca"];
            $tractor_modelo = $fila["modelo"];
            $tractor_color = $fila["color"];
            $tractor_ano = $fila["ano"];
            $tractor_ejes = $fila["ejes"];
            $tractor_tara = $fila["tara"];
            $tractor_capacidad = $fila["capacidad"];
            $tractor_flota = $fila["flota"];
            $tractor_combustible = $fila["tipo_combustible"];
            $tractor_tanque = $fila["capacidad_tanque"];
            $tractor_promedio = $fila["consumo_promedio"];

            $tractor_alto = $fila["alto"];
            $tractor_ancho = $fila["ancho"];
            $tractor_largo = $fila["largo"];

            $tractor_caracteristicas_individuales = $fila["caracteristicas_individuales"];

            // $tractor_capacidad = $fila["capacidad"];

            // Carreta.
            $id_carreta = intval($fila["id_carreta"]);
            $chapa_carreta = $fila["chapa_carreta"];
            $carreta_marca = $fila["carreta_marca"];
            $carreta_ano = $fila['carreta_ano'];
            $carreta_color = $fila["carreta_color"];
            $carreta_modelo = $fila["carreta_modelo"];
            $carreta_tipo_vehiculo = $fila["carreta_tipo_vehiculo"];
            $carreta_ejes = $fila['carreta_ejes'];
            $carreta_tara = $fila['carreta_tara'];
            $carreta_volumen = $fila['carreta_volumen'];
            $carreta_codigo = $fila['carreta_codigo'];
            $carreta_flota = $fila['carreta_flota'];

            $carreta_alto = $fila["carreta_alto"];
            $carreta_ancho = $fila["carreta_ancho"];
            $carreta_largo = $fila["carreta_largo"];

            $carreta_caracteristicas_individuales = $fila["carreta_caracteristicas_individuales"];

            $carreta_chassis = $fila["carreta_chassis"];

            // combinacion 
            $tara_combinada = $fila["tara_combinada"];
            $combinacion = $fila["combinacion"];
            $bruto_max = $fila["bruto_max"];
            $neto_max = $fila["neto_max"];

           if($fila["id_tipo"] == 0) {

            $carreta_capacidad = $fila['capacidad'];

           }
            

            //Chofer.
            $id_chofer = intval($fila["id_chofer"]);
            $chofer = $fila["chofer"];
            $chofer_nombre = $fila["nombre_chofer"];
            $chofer_t_doc_corto = $fila["chofer_t_doc_corto"];
            $chofer_documento = $fila["chofer_documento"];
            $chofer_f_nac = $fila["chofer_f_nac"];
            $chofer_telefono = $fila["chofer_telefono"];
            $chofer_direccion = $fila["chofer_direccion"];

            //Propietario
            $id_propietario = intval($fila["id_propietario"]);
            $propietario_nombre = $fila["propietario_nombre"];
            $propietario_t_doc_corto = $fila["propietario_t_doc_corto"];
            $propietario_doc = $fila["propietario_documento"];
            $propietario_telefono = $fila["propietario_telefono"];
            $propietario_direccion = $fila["propietario_direccion"];

            $observaciones = $fila["observaciones"];
            $usuario = $fila["usuario"];


            $estado = $fila["estado"];

            $id_estado = $fila["id_estado"];

            
        }
              
        $resultados->free();    // liberar el resultset 


    } else {

        $cod_error = 2;
        $respuesta = "Error al consultar la base de datos";
    }



    $sql_carreta = "SELECT capacidad FROM v_vehiculos WHERE id_cliente = " . $id_cliente . " AND id = " . $carreta_;

    if ($resultados_carreta = $conexion_bd->query($sql_carreta)) {

        while ($fila_carreta = $resultados_carreta->fetch_assoc()) {    // obtener array asociativo 

           if($fila_carreta["id_tipo"] == 0) {

                $carreta_capacidad = $fila_carreta['capacidad'];

           }
            
        }
              
        $resultados_carreta->free();    // liberar el resultset 

    }

    // FIN BUSCAR DATOS DEL DOCUMENTO A IMPRIMIR


    // quitar los puntos de subir carpeta, pues aqui no funcionan 

    $url_logo = urlServidor() . $carpeta_raiz . str_replace( "../", "", $_SESSION["url_logo_reportes"] );

    $_SESSION["url_logo"] = $url_logo;

    if($_SESSION["cd_id_cliente"]==15){

        if ($_SESSION["logo_vales_personalizado"]) {  // si tiene logo personalizado buscar nomas en las carpeta de logos 

            //$url_logo = $_SESSION["url_logo_vales"];
    
           

                 $url_logo = urlServidor() . $carpeta_raiz . $_SESSION["url_logo_vales"];
    
            $_SESSION["url_logo"] = $url_logo;
    
        }else{
            
            $url_logo = urlServidor() . $carpeta_raiz . $_SESSION["url_logo_vales"];
    
            $_SESSION["url_logo"] = $url_logo;
    
        }

    }

    




    if (!isset($_SESSION["fuente_reportes"])) {
        $_SESSION["fuente_reportes"] = $fuente_reportes;
    }


    // cantidad de vias de este reporte
    $reporte_cant_vias = 1; // DEFAULT 
    $altura_via = 140;
    $top_qr_validador = 110;



    if ($id_tractor > 0) { // SI existe el tractor


            // CONSULTA DOCUMENTOS ADICIONALES ////////////////////////////////////////////////////////////////////////////////////////////////////////////

                /* id, id_cliente, id_tipo_documentacion, tipo_documentacion, id_t_doc, t_doc_corto, t_doc_largo, permanente, id_registro, titular, 
                persona_id, persona_nombre, persona_t_doc_corto, persona_t_doc_largo, persona_documento, vehiculo_id, vehiculo_chapa, vehiculo_marca, 
                vehiculo_ano, vehiculo_chassis, referencia, f_doc, f_venc, observaciones, id_estado, dias_diferencia_venc, estado_vigencia, 
                dias_a_vencer, dias_vencido, ficha, mostrar_en_ficha, f_reg, id_sesion, ip, usuario, descripcion

                */

                $columnas_doc_adicionales = 'id, t_doc_corto, t_doc_largo, permanente, persona_id, vehiculo_id, referencia, f_doc, f_venc, orden, id_estado, id_cliente, ficha, vigente ' ;

                    $vista = "v_documentos_adicionales" ;

                    $sent_doc_adicionales = "SELECT " . $columnas_doc_adicionales . " FROM ( " ;

                        $sent_doc_adicionales .= " SELECT " . $columnas_doc_adicionales . " FROM " . $vista . " WHERE vehiculo_id = " . $id_tractor  ;

                            if ( $id_carreta > 0 ) {

                                $sent_doc_adicionales .= " UNION ALL " ;

                                $sent_doc_adicionales .= " SELECT " . $columnas_doc_adicionales . " FROM " . $vista . " WHERE vehiculo_id = " . $id_carreta  ;

                            }

                            if ( $id_chofer > 0 ) {

                                $sent_doc_adicionales .= " UNION ALL " ;

                                $sent_doc_adicionales .= " SELECT " . $columnas_doc_adicionales . " FROM " . $vista . " WHERE persona_id = " . $id_chofer  ;

                            }

                            if ( $id_propietario > 0 ) {

                                $sent_doc_adicionales .= " UNION ALL " ;

                                $sent_doc_adicionales .= " SELECT " . $columnas_doc_adicionales . " FROM " . $vista . " WHERE persona_id = " . $id_propietario  ;

                            }


                    $sent_doc_adicionales .= " ) AS datos_temporales " ;

                    $sent_doc_adicionales .= " WHERE id_estado = 1 AND ficha = 1 AND vigente = 1 " ; // id_estado 1 SOLO para los vigentes, NO dados de baja


                    $sent_doc_adicionales .= " ORDER BY orden, t_doc_corto " ;


                    
                    
                    /*
                    
                    echo $sent_doc_adicionales ;

                    exit ;

                    */

                    


                    if ($resultados = $conexion_bd->query($sent_doc_adicionales)) {  
                                
                        while ( $fila_adicionales = $resultados->fetch_assoc() ){
                            $filas_adicionales[] = $fila_adicionales ;
                        }                              

                        $resultados->free();    // liberar el resultset 

                            foreach( $filas_adicionales as $fila_adicional ) { // DETALLES del vale de provision

                                $cant_documentos_adicionales++ ; // SOLO PARA CONTAR

                                    // en el orden esta la forma de organizar ... 0 tractor, 1 carreta, 2 chofer 

                                    $orden_doc_adicional = $fila_adicional['orden'] ;
                                    $persona_id = $fila_adicional['persona_id'] ;

                                    if ( $orden_doc_adicional == 0 ) { $cant_documentos_adicionales_tractor++ ; }
                                    if ( $orden_doc_adicional == 1 ) { $cant_documentos_adicionales_carreta++ ; }
                                    if ( $orden_doc_adicional == 2 AND $persona_id == $id_chofer ) { $cant_documentos_adicionales_chofer++ ; }
                                    if ( $orden_doc_adicional == 2 AND $persona_id == $id_propietario ) { $cant_documentos_adicionales_propietario++ ; }

                            }

                    }



            // FIN CONSULTA DOCUMENTOS ADICIONALES ////////////////////////////////////////////////////////////////////////////////////////////////////////////


        // AJUSTAR VALORES A IMPRIMIR ************************************************************************************************************************

        $fecha_doc = $f_doc;

        // EL CODIGO A VALIDAR SERA AAAA-N_DOC-MS ( anho, n_doc, milisegundos del registro )

        // $ano_reg = $f_reg->format("Y");
        // $milisegundos_reg = substr ( $f_reg->format("u"), 0, 3 ) ;

        $cod_validar = $ano_reg . $chapa_tractor . '-' . $id;

        $url_generador = $ruta . $generador_qr . "?d=" . $_SESSION["cd_ruc"] . "t=" . $tipo_doc_validar . "&c=" . $cod_validar; // RUC, tipo_doc, y codigo


        
        $tipo_comprobante = utf8_decode("Ficha de Vehículos");
        $titulo_documento = $tipo_comprobante . " " . $chapa_tractor;

        $asunto = $tipo_comprobante . " " . $chapa_tractor . " " . $chofer;

        $tractor = $chapa_tractor;
        $carreta = $chapa_carreta;

        $nombre_chofer = $chofer; // para HTML
        $doc_chofer = $chofer_documento; // para HTML



        $chofer_telefono = $chofer_telefono;

        $usuario = $usuario;

        
        

        $descripcion_tractor = $tractor_marca . " " . $tractor_modelo . " " . $tractor_color;
        $descripcion_carreta = $carreta_marca . " " . $carreta_color." ".$carreta_ejes;



        /*
            if ( $estado == 'a' ) { $_SESSION["marca_de_agua"] = "ANULADO" ; }
            if ( $estado == 'e' ) { $_SESSION["marca_de_agua"] = "ENTREGADO" ; }
            */

        if ($id_estado == 0) {
            $_SESSION["marca_de_agua"] = "ANULADO";
        }
        if ($id_estado == 1) {
            $_SESSION["marca_de_agua"] = "SIN AUTORIZACION";
        }
        if ($id_estado == 6) {
            $_SESSION["marca_de_agua"] = "ENTREGADO";
        }

        $nom_archivo_descargar = $nom_archivo_descargar . "_" . strtolower($chapa_tractor) . ".pdf";



        // agregar a variables de sesion los encabezados y pie del documento "d_" hace referencia al DOCUMENTO ***********************************************
        $_SESSION["d_n_doc"] = $num_doc;
        $_SESSION["d_fecha_doc"] = $fecha_doc;
        $_SESSION["d_n_contrato"] = utf8_decode($n_contrato);


        // *******************************************************************              TEXTO HTML MAIL                 *************************************

        if ($preparar_html) {

            $asunto_mail = utf8_decode("FICHA ") . $chapa_tractor;
            $nom_adjunto_mail = "Equipo: " . $chapa_tractor . ".pdf";

            $_SESSION["asunto_mail"] = $asunto_mail ;
            $_SESSION["nom_adjunto_mail"] = $nom_adjunto_mail ;


            //  *********************************            !!!!!!!!!!!!!!! ATENCION !!!!!!!!!!                ******************************************//
            //  definir a quienes enviar una copia, SOLO EN OC Y VD, actualmente

            // $destinatarios_cc = $_SESSION["cc_o_c_enviadas"];

            $destinatarios_cc = '' ;

            // FIRMA DEFAULT
            $firma = $firma . '<div class="fila"><div id="firma_mensaje">';
            $firma = $firma . '<h2>' . $_SESSION["nombre_usuario"] . '</h2>';
            $firma = $firma . '<p><strong>' . $_SESSION["cargo_usuario"] . '</strong></p>';
            $firma = $firma . '<p>' . $_SESSION["cd_direccion"] . '</p>';
            $firma = $firma . '<p>' . $_SESSION["cd_telefono"] . '</p>';
            $firma = $firma . '</div></div>';


            // SI HAY FIRMA HTML // ANTES DE CREAR EL HTML
            if (strlen($firma_mail_jpg) > 0) {
                $firma_mail_jpg_str = base64_encode(file_POST_contents($firma_mail_jpg));
                $firma = '<img src="data:image/jpeg;base64,' . $firma_mail_jpg_str . '" alt="Firma" />';
            }


            $txt = '';

            $txt .= '<!DOCTYPE html>';
            $txt .= '<html lang="es">';
            $txt .= '<head>';
            $txt .= '<meta name="language" content="ES">';
            $txt .= '<title>Equipo ' . $chapa_tractor . " - " . $chapa_carreta . '</title>';
            $txt .= '<style>';

            $txt .= $css_vales; // CSS VALES 

            if (strlen($firma_mail_html) > 0) {

                $firma = $firma_mail_html; // CARGAR LA FIRMA EN ESTA VARIABLE

                $txt .= $firma_mail_html_css;
            }

            // datos del camion, segun la chapa

            $datos_vehiculo = $chapa_tractor ; 

            if ( strlen( $chapa_carreta ) > 0 ) { $datos_vehiculo .= " - " . $chapa_carreta ; }

            $txt .= '</style>';
            $txt .= '</head>';
            $txt .= '<body>';
            $txt .= '<div id="cuerpo">';
            $txt .= '<div class="fila"><p><strong>' . htmlentities($_SESSION["cd_razon_social"]) . '</strong> remite a Ud. los datos del veh&iacute;culo <strong>' . $datos_vehiculo .'</strong></p></div>';


            $txt .= '<div class="notas"></div>'; // ESPACIO DESTINADOA LAS NOTAS QUE SE AGREGUEN AL MOMENTO DE ENVIAR EL MENSAJE


            //COMIENZO TRACTOR......................................................................................
            $txt .= '<div class="fila"><h3>Tractor</h3></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Chapa</div><div class="valor"><strong>' . htmlentities($chapa_tractor) . '</strong></div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Chassis</div><div class="valor">' . htmlentities($tractor_chassis) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Marca</div><div class="valor">' . htmlentities($tractor_marca) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Color</div><div class="valor">' . htmlentities($tractor_color) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Año</div><div class="valor"><strong>' . htmlentities($tractor_ano) . '</strong></div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Tara</div><div class="valor">' . htmlentities($tractor_tara) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Cap.carga</div><div class="valor">' . htmlentities($tractor_capacidad) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Combustible</div><div class="valor">' . htmlentities($tractor_combustible) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Cap.tanque</div><div class="valor">' . htmlentities($tractor_tanque) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Modelo</div><div class="valor">' . htmlentities($tractor_modelo) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Ejes</div><div class="valor">' . htmlentities($tractor_ejes) . '</div></div>';

            // $txt .= '<div class="fila"><div class="etiqueta">Peso habilitado por DINATRAN</div><div class="valor">' . formatMoneda($tractor_capacidad, 1) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">';
            //FIN TRACTOR......................................................................................



            //COMIENZO CARRETA......................................................................................
            $txt .= '<div class="fila"><h3>Carreta</h3></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Chapa</div><div class="valor"><strong>' . htmlentities($chapa_carreta) . '</strong></div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Chassis</div><div class="valor">' . htmlentities($tractor_chassis) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Marca</div><div class="valor">' . htmlentities($carreta_marca) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Color</div><div class="valor">' . htmlentities($carreta_color) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">';
            //FIN CARRETA......................................................................................



            //COMIENZO CHOFER......................................................................................
            $txt .= '<div class="fila"><h3>Chofer</h3></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Nombre</div><div class="valor"><strong>' . htmlentities($chofer) . '</strong></div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Documento</div><div class="valor">' . htmlentities($chofer_documento) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Telefono</div><div class="valor">' . htmlentities($chofer_telefono) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">Direccionr</div><div class="valor">' . htmlentities($chofer_direccion) . '</div></div>';

            $txt .= '<div class="fila"><div class="etiqueta">';
            //FIN CHOFER......................................................................................


            if ( $mostrar_propietario > 0 ) { 

                //COMIENZO PROPIETARIO......................................................................................
                $txt .= '<div class="fila"><h3>Propietario</h3></div>';

                $txt .= '<div class="fila"><div class="etiqueta">Nombre</div><div class="valor"><strong>' . htmlentities($propietario_nombre) . '</strong></div></div>';

                $txt .= '<div class="fila"><div class="etiqueta">Documento</div><div class="valor">' . htmlentities($propietario_doc) . '</div></div>';

                $txt .= '<div class="fila"><div class="etiqueta">Telefono</div><div class="valor">' . htmlentities($propietario_telefono) . '</div></div>';

                $txt .= '<div class="fila"><div class="etiqueta">Direccion</div><div class="valor">' . htmlentities($propietario_direccion) . '</div></div>';

                $txt .= '<div class="fila"><div class="etiqueta">';
                //FIN PROPIETARIO......................................................................................

            }


            
            $txt .= '<div class="fila"><p>Saludos Cordiales</p></div>';
            $txt .= $firma;
            $txt .= '<div class="fila"><div class="ecologia"><p>' . $mensaje_ecologia . '</p></div></div>';
            // $txt .= '<div class="fila"><div class="derecha" id="validar"><p><a href="' . $url_validar_doc . '" target="_blank" title="Validar el origen de este mensaje y su contenido...">Comprobar el origen de este mensaje en ' . $app_url . '</a></p></div></div>';
            $txt .= '<div class="confidencialidad"></div>'; // ESPACIO DESTINADO A LAS NOTAS DESCARGO DE CONFIDENCIALIDAD DEL MENSAJE
            $txt .= '</div>';
            $txt .= '</body>';
            $txt .= '</html>';


            $txt_mail_html = $txt; //ELIMINANDO O COMENTANDO ESTA VARIABLE, EL CORREO NO VA FUNCIONAR EN (REGISTRO DE EQUIPOS).


            //FIN OBSERVACION......................................................................................
        }



        // $_SESSION["txt_mail_html_tmp"] = $txt_mail_html ;

        // *******************************************************************              FIN TEXTO HTML MAIL             *************************************



        if ($imprimir_pdf) { //    *****************************************               SI IMPRIMIR PDF                 ************************************

            // INICIO ENCABEZADO ********************************************************************************************************************************    

            class PDF extends PDF_Rotate
            {
                function Header()
                {
                }

                // Page footer
                function Footer()
                {
                    // Position at 1.5 cm from bottom
                    // $this->SetY(-15);
                    // Arial italic 8
                    // $this->SetFont($_SESSION["fuente_reportes"],"I",8);
                    // Page number
                    // $this->Cell(0,10,"Page ".$this->PageNo()."/{nb}",0,0,"C");
                }

                function RotatedText($x, $y, $txt, $angle)
                {
                    //Text rotated around its origin
                    $this->Rotate($angle, $x, $y);
                    $this->Text($x, $y, $txt);
                    $this->Rotate(0);
                }
            }


            // FIN ENCABEZADO ********************************************************************************************************************************



            // Instanciation of inherited class
            $pdf = new PDF();

            $pdf->SetTitle($titulo_documento);
            $pdf->SetAuthor(utf8_decode($_SESSION["cd_razon_social"] . ' - ' . $usuario . " Impreso por " . $_SESSION["nom_usuario"]));
            $pdf->SetCreator($app_name . ' - ' . $app_url);
            $pdf->SetSubject($asunto);

            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetAutoPageBreak(false, 6);







            // #########################################################################################################################################################################################
            // #########################################################################################################################################################################################

            //                                                                          INICIO FORMATO REPORTE 

            // #########################################################################################################################################################################################
            // #########################################################################################################################################################################################


            $bordes = 0; // BORDES EN CADA CELDA  // SOLO PARA VERIFICAR FORMATO PARA DISENHAR


            $rellenar = false;


            $margen_izquierdo = 10;
            $margen_derecha = 10;

            $margen_logo_texto = 3;

            $punto_x_inicial = 15;
            $punto_y_inicial = 15;

            // logo personalizado 1/4 // comentar esta linea...
            // $ancho_celda_membrete_derecha = $ancho_total - $ancho_celda_membrete - $logo_width - $margen_logo_texto - $medida_img_validar; 

            // logo personalizado 2/4 // remplazar por esta linea
            if ( $membrete_width > 0 ) { $ancho_celda_membrete_derecha = $ancho_total - $ancho_celda_membrete - $logo_width - $margen_logo_texto - $medida_img_validar; }

            
            $punto_x_inicial_derecha = ($ancho_total - $ancho_celda_membrete_derecha) + ($margen_logo_texto + $medida_img_validar);

            $qr_validador_x = $punto_x_inicial_derecha - ($medida_img_validar + $qr_area_vacia);

            $ancho_celda_titulo = 18;

            $via_actual = 0;




            while ($via_actual < $reporte_cant_vias) {    // ----------------------------------------------------------------      WHILE DE VIAS               --------------------------------------------------------------

                // echo "The number is: $x <br>";

                $altura_sumar = $altura_via * $via_actual; // altura que se va a sumar a la via, si original, CERO, duplicado o triplicado ira multiplicando

                $imprimir_marca_de_agua = false;

                $existe_foto_tractor = false;
                $existe_foto_carreta = false;
                $existe_foto_chofer = false;


                // DEPENDIENDO DEL ESTADO DEL REGISTRO 

                if ($via_actual == 1) {
                    $imprimir_marca_de_agua = true;
                    $texto_marca_de_agua = 'DUPLICADO';
                }

                if ($via_actual == 0 and $id_estado == 0) {
                    $imprimir_marca_de_agua = true;
                    $texto_marca_de_agua = 'ANULADO';
                }

                if ($via_actual == 0 and $id_estado == 6) {
                    $imprimir_marca_de_agua = true;
                    $texto_marca_de_agua = 'ENTREGADO';
                }






                


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



                if ($via_actual == 1) {

                    // ---------------------------------------------------------    CELDA DE BORDE ---------------------------------------------------------------------------------------

                    $punto_x_actual = $punto_x_inicial;
                    $ancho_celda = $ancho_total;
                    $formato_fuente = '';
                    $tamano_fuente = 7;
                    $alineacion = 'L';
                    $siguiente_linea = 2; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // VARIABLE NUEVAS.
                    $debajo = 2;


                    $pdf->SetFont($tipo_fuente, $formato_fuente, $tamano_fuente); // ajustar el tamanho de fuente que se va a imprimir

                    $alto_renglon_celda = 4;
                    $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                    $texto = '';

                    $pdf->Cell($ancho_total, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar); // AUXILIAR PARA ESPACIADO


                    $texto = '. . . . . . . . . .. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .'; // dato a mostrar                               

                    // $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO
                    $pdf->Cell($ancho_total, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar); // SIN BORDES... !!!


                }



                // $pdf->SetFillColor(128,128,128);
                $pdf->SetTextColor(64, 64, 64);
                $pdf->SetFillColor(192, 192, 192);
                $pdf->SetDrawColor(100, 100, 100);



                // logo personalizado 3/4 // DESDE LA SIGUIENTE LINEA....
                // ----------------------------------------------------------------      MEMBRETE               --------------------------------------------------------------

                    $logo_top = $logo_top + $altura_sumar;


                    if($_SESSION["cd_id_cliente"] == 69) {
                        $logo_top = 2;
                        $logo_left = 18;
                    }



                    // logo personalizado ...
                    if ( $_SESSION["logo_vales_personalizado"] ) {

                        $pdf->Image($_SESSION["url_logo"], $logo_left, $logo_top, $logo_width ); // L, T, W, H // altura ajusta automaticamente

                    } else {

                        $pdf->Image($_SESSION["url_logo"], $logo_left, $logo_top, $logo_width, $logo_height); // L, T, W, H

                    }
                    // fin logo personalizado ...

                    // DATOS DE LA EMPRESA 

                    $formato_fuente = 'B';

                    $punto_y_actual = $punto_y_inicial + $altura_sumar;
                    $punto_x_actual = $punto_x_inicial + $logo_width + $margen_logo_texto;
                    $tamano_fuente = $tamano_fuente_default + 1; // por ser el nombre 

                    $ancho_celda = $ancho_celda_membrete;
                    $alto_renglon_celda = 5.5;
                    $siguiente_linea = 2; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)
                    $alineacion = 'L';

                    $texto = ( $membrete_width > 0 ) ? utf8_decode( $_SESSION["cd_razon_social"] ) : '' ; // dato a mostrar

                    // organizar segun variables 
                    $pdf->SetY($punto_y_actual);
                    $pdf->SetX($punto_x_actual);
                    $pdf->SetFont($tipo_fuente, $formato_fuente, $tamano_fuente); // ajustar el tamanho de fuente que se va a imprimir

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    // los demas datos 

                    $formato_fuente = '';
                    $tamano_fuente = $tamano_fuente_default - 2; // por ser el nombre 
                    $alto_renglon_celda = 3;

                    $texto = ( $membrete_width > 0 ) ? utf8_decode( $_SESSION["cd_t_doc_corto"] . ' ' . $_SESSION["cd_ruc"] ) : '' ; // dato a mostrar

                    $pdf->SetFont($tipo_fuente, $formato_fuente, $tamano_fuente); // ajustar el tamanho de fuente que se va a imprimir

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $texto = ( $membrete_width > 0 ) ? utf8_decode( $_SESSION["cd_direccion"] . ' ' . $_SESSION["cd_ciudad"] . ' ' . $_SESSION["cd_cod_pais"] ) : '' ; // dato a mostrar

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $texto = ( $membrete_width > 0 ) ? utf8_decode( 'Tel.: ' . $_SESSION["cd_telefono"] ) : '' ; // dato a mostrar

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $texto = ( $membrete_width > 0 ) ? utf8_decode( 'E-mail: ' . $_SESSION["cd_mail"] ) : '' ; // dato a mostrar

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                // $tipo_comprobante

                // logo personalizado 4/4 // HASTA LA LINEA ANTERIOR....



                $formato_fuente = 'B';

                $punto_y_actual = $punto_y_inicial + $altura_sumar;
                $punto_x_actual = $punto_x_inicial_derecha;
                $tamano_fuente = $tamano_fuente_default; // por ser el nombre 

                $ancho_celda = $ancho_celda_membrete_derecha;
                $alto_renglon_celda = 5.5;
                $siguiente_linea = 2; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)
                $alineacion = 'C';

                $texto = strtoupper( $tipo_comprobante ); // dato a mostrar

                // organizar segun variables 
                $pdf->SetY($punto_y_actual);
                $pdf->SetX($punto_x_actual);
                $pdf->SetFont($tipo_fuente, $formato_fuente, $tamano_fuente); // ajustar el tamanho de fuente que se va a imprimir

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                $formato_fuente = '';

                $alineacion = 'L';

                $tamano_fuente = $tamano_fuente_default; // por ser el nombre 

                $pdf->SetFont($tipo_fuente, $formato_fuente, $tamano_fuente); // ajustar el tamanho de fuente que se va a imprimir

                $ancho_celda = $ancho_celda_membrete_derecha / 2;
                $alto_renglon_celda = 4.5;
                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                $texto = utf8_decode(''); // dato a mostrar

                $alineacion = 'L';

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                $alineacion = 'R';

                $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                $texto = $num_doc; // dato a mostrar

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                $punto_x_actual = $punto_x_inicial_derecha;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                $alineacion = 'L';

                $texto = utf8_decode(''); // dato a mostrar

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                $siguiente_linea = 2; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                $alineacion = 'R';

                $texto = ""; // dato a mostrar

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                $punto_x_actual = $punto_x_inicial_derecha;
                $tamano_fuente = $tamano_fuente_default - 2; // por ser el nombre 

                $pdf->SetFont($tipo_fuente, $formato_fuente, $tamano_fuente); // ajustar el tamanho de fuente que se va a imprimir
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO


                $siguiente_linea = 2; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                $alineacion = 'C';

                $ancho_celda = $ancho_celda_membrete_derecha;

                // $texto = utf8_decode( 'VALIDO POR 3 DIAS' ) ; // dato a mostrar
                $texto = '';

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                // ---------------------------------------------------------    CELDA DE BORDE ---------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $alto_renglon_celda = 1;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO                                
                $texto = ''; // dato a mostrar
                $pdf->Cell($ancho_total, $alto_renglon_celda, $texto, 'T', $siguiente_linea, $alineacion, $rellenar);






                // AL FINAL CODIGO QR 

                if ($validar_reporte) {

                    // $x = round ( ( $ancho_total / 2 ) - ( $medida_img_validar / 2 ) ) + $margen_col_ini + 6 ;

                    $qr_validador_y = $qr_validador_y + $altura_sumar;

                    // $qr_validador_x = $ancho_total - $qr_validador_x ; // al principio

                    $pdf->Image($url_generador, $qr_validador_x, $qr_validador_y, $medida_img_validar, $medida_img_validar, "png"); // CASI ARBITRARIAMENTE COLOCO a la derecha

                }



                // ----------------------------------------------------------------      FIN MEMBRETE               --------------------------------------------------------------




                











                // SIGUIENTE LINEA .......................................................................................................


                $punto_y_inicial_tractor = 0 ;
                $punto_y_inicial_carreta = 0 ;
                $punto_y_inicial_chofer = 0 ;
                $punto_y_inicial_propietario = 0 ;

                $margen_y_foto = 2.5 ;



                // TRACTOR .......................................................................................................


                // 
                $ancho_celda = $ancho_total;
                $alto_renglon_celda = 8;
                $bordes = 'B';
                $alineacion = 'L';
                $tamano_fuente = 7;

                $pdf->SetX(15);
                $pdf->SetFont('Arial', 'B', $tamano_fuente); // Tipo de fuente

                $texto = utf8_decode( "Tractor" ) ;

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                //


                $bordes = 0 ; // TMP

                $alto_renglon_celda = $alto_renglon_celda_datos ;

                $pdf->SetFont('Arial', '', $tamano_fuente); // Tipo de fuente               

                $punto_y_inicial_tractor = $pdf->GetY() ; // FIJAR para datos de documentacion adicional





                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Chapa:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $texto = utf8_decode( $chapa_tractor ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Año:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $tractor_ano, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<







                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Marca:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $texto = utf8_decode( $tractor_marca ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Ejes:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $tractor_ejes, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Tipo:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                   // $texto = utf8_decode( $tractor_tipo_vehiculo ); // DATO  
                    $texto = substr(utf8_decode($tractor_tipo_vehiculo),0 ,25 ); // DATO                                                                               

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Tara:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $tractor_tara, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Modelo:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $texto = substr(utf8_decode($tractor_modelo),0 ,25 ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Capacidad:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $tractor_capacidad, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Chassis:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $texto = utf8_decode( $tractor_chassis ); // DATO   
                  
                   // $texto = substr(utf8_decode($tractor_chassis),0 ,19 ); // DATO      

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Largo:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $tractor_largo, 2 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<





                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Color:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $texto = utf8_decode( $tractor_color ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Tanque:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $tractor_tanque, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<






                // ---- LINEA -----------------------------------------------------------------------------------------------------------------


                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Flota:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso + $ancho_celda_espacio + $ancho_sub_titulo + $ancho_celda_dato_estrecho ;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = utf8_decode( $tractor_flota ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                // ---- LINEA -----------------------------------------------------------------------------------------------------------------


                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo + 8 ; // EN ESTE CASO
                    $alineacion = 'L';

                    $texto = utf8_decode('Características:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso + $ancho_celda_espacio + $ancho_sub_titulo + $ancho_celda_dato_estrecho ;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = utf8_decode( $tractor_caracteristicas_individuales ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<








                // SIGUIENTE LINEA .......................................................................................................
                // INICIO CONTENIDO CARRETA ..............................................................................................

                // Carreta

                $ancho_celda = $ancho_total;
                $alto_renglon_celda = $alto_renglon_titulos;
                $borde = "B";
                $alineacion = 'L';

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO 
                $pdf->SetFont('Arial','B',$tamano_fuente); // Tipo de fuente

                $texto = utf8_decode('Carreta'); // SUB TITULO

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $borde, $siguiente_linea, $alineacion, $rellenar);





                $alto_renglon_celda = $alto_renglon_celda_datos ;

                $pdf->SetFont('Arial', '', $tamano_fuente); // Tipo de fuente               

                $punto_y_inicial_carreta = $pdf->GetY() ; // FIJAR para datos de documentacion adicional





                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Chapa:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $texto = utf8_decode( $chapa_carreta ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Año:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $carreta_ano, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<







                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Marca:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $texto = utf8_decode( $carreta_marca ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Ejes:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $carreta_ejes, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Tipo:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    //$texto = utf8_decode( $carreta_tipo_vehiculo ); // DATO
                    $texto = substr(utf8_decode($carreta_tipo_vehiculo),0 ,22 ); // DATO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Tara:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $carreta_tara, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Modelo:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    //$texto = utf8_decode( $carreta_modelo ); // DATO   
                    $texto = substr(utf8_decode($carreta_modelo),0 ,25 ); // DATO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Volumen:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $carreta_volumen, 2 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Chassis:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                    //if ($_SESSION[if])
                    $ancho_celda = $ancho_celda_dato_extenso ;

                    $texto = utf8_decode( $carreta_chassis ); // DATO      
                  // $texto = substr(utf8_decode($carreta_chassis),0 ,25 ); // DATO                                                                   

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Largo:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $carreta_largo, 2 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Color:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $texto = utf8_decode( $carreta_color ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    // COLUMNA DE DATOS 2
                
                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Alto:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $carreta_alto, 2 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<






                // ---- LINEA -----------------------------------------------------------------------------------------------------------------


                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Flota:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso ;

                    $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = utf8_decode( $carreta_flota ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    // ESPACIO 
                    $ancho_celda = $ancho_celda_espacio;
                    $texto = '' ;
                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_sub_titulo;

                    $texto = utf8_decode('Capacidad:'); // SUB TITULO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, "", $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_estrecho;
                    $alineacion = 'R';

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = formatMoneda ( $carreta_capacidad, 1 ); // DATO 

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, "", $siguiente_linea, $alineacion, $rellenar);
                    

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                // ---- LINEA -----------------------------------------------------------------------------------------------------------------


                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo + 8 ; // EN ESTE CASO
                    $alineacion = 'L';

                    $texto = utf8_decode('Características:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso + $ancho_celda_espacio + $ancho_sub_titulo + $ancho_celda_dato_estrecho ;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = utf8_decode( $carreta_caracteristicas_individuales ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<






                // si el equipo se conforma de 2 chapas .....

                if ( strlen( $chapa_tractor ) > 0 AND strlen( $chapa_carreta ) > 0 ) {


                    // SIGUIENTE LINEA .......................................................................................................
                    // INICIO CONTENIDO COMBINACION ..............................................................................................

                    // Equipo

                    $ancho_celda = $ancho_total;
                    $alto_renglon_celda = $alto_renglon_titulos;
                    $borde = "B";
                    $alineacion = 'L';

                    $punto_x_actual = $punto_x_inicial;
                    $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO 
                    $pdf->SetFont('Arial','B',$tamano_fuente); // Tipo de fuente

                    $texto = utf8_decode('Combinación del Equipo'); // SUB TITULO

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $borde, $siguiente_linea, $alineacion, $rellenar);




                    $alto_renglon_celda = $alto_renglon_celda_datos ;

                    $pdf->SetFont('Arial', '', $tamano_fuente); // Tipo de fuente


                    
                    // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                    $punto_x_actual = $punto_x_inicial;
                    $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                    $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)


                        // COLUMNA DE DATOS 1

                        $ancho_celda = $ancho_sub_titulo;
                        $alineacion = 'L';

                        $texto = utf8_decode('Tipo:'); // SUB TITULO                                                                             

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $ancho_celda = $ancho_celda_dato_extenso;

                        $texto = $combinacion; // DATO                                                                       

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // ESPACIO 
                        $ancho_celda = $ancho_celda_espacio;
                        $texto = '' ;
                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                        
                        // COLUMNA DE DATOS 1

                        $ancho_celda = $ancho_sub_titulo;
                        $alineacion = 'L';

                        $texto = utf8_decode('Largo:'); // SUB TITULO                                                                             

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $ancho_celda = $ancho_celda_dato_estrecho;
                        $alineacion = 'R';

                        $texto = formatMoneda( ( $tractor_largo + $carreta_largo ), 2 ); // DATO                                                                       

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // ESPACIO 
                        $ancho_celda = $ancho_celda_espacio;
                        $texto = '' ;
                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                        
                        // COLUMNA DE DATOS 1

                        $ancho_celda = $ancho_sub_titulo;
                        $alineacion = 'L';

                        $texto = utf8_decode('Ejes:'); // SUB TITULO                                                                             

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $ancho_celda = $ancho_celda_dato_estrecho;
                        $alineacion = 'R';

                        $texto = formatMoneda( ( $tractor_ejes + $carreta_ejes ), 1 ); // DATO                                                                       

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // ESPACIO 
                        $ancho_celda = $ancho_celda_espacio;
                        $texto = '' ;
                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // COLUMNA DE DATOS 1

                        $ancho_celda = $ancho_sub_titulo;
                        $alineacion = 'L';

                        $texto = utf8_decode('Tara:'); // SUB TITULO                                                                             

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $ancho_celda = $ancho_celda_dato_estrecho;
                        $alineacion = 'R';

                        $texto = formatMoneda( $tara_combinada, 1 ); // DATO                                                                           

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // ESPACIO 
                        $ancho_celda = $ancho_celda_espacio;
                        $texto = '' ;
                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // COLUMNA DE DATOS 1

                        $ancho_celda = $ancho_sub_titulo;
                        $alineacion = 'L';

                        $texto = utf8_decode('Bruto Máx:'); // SUB TITULO                                                                             

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $ancho_celda = $ancho_celda_dato_estrecho;
                        $alineacion = 'R';

                        $texto = formatMoneda( $bruto_max, 1 ); // DATO                                                                           

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                        


                        // ESPACIO 
                        $ancho_celda = $ancho_celda_espacio;
                        $texto = '' ;
                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                        // COLUMNA DE DATOS 1

                        $ancho_celda = $ancho_sub_titulo;
                        $alineacion = 'L';

                        $texto = utf8_decode('Neto Máx:'); // SUB TITULO                                                                             

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                        $ancho_celda = $ancho_celda_dato_estrecho;
                        $alineacion = 'R';

                        $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                        $texto = formatMoneda( $neto_max, 1 ); // DATO                                                                       

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                    

                    // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


                }




                // VACIO MIENTRAS, PARA MEJORAR EL ESPACIADO 

                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = ''; // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = ''; // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<









                // SIGUIENTE LINEA .......................................................................................................
                // INICIO CONTENIDO CHOFER ..............................................................................................

                // Chofer

                $ancho_celda = $ancho_total;
                $alto_renglon_celda = $alto_renglon_titulos;
                $borde = "B";
                $alineacion = 'L';

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO 
                $pdf->SetFont('Arial','B',$tamano_fuente); // Tipo de fuente

                $texto = utf8_decode('Chofer'); // SUB TITULO

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $borde, $siguiente_linea, $alineacion, $rellenar);





                $alto_renglon_celda = $alto_renglon_celda_datos ;

                $pdf->SetFont('Arial', '', $tamano_fuente); // Tipo de fuente               

                $punto_y_inicial_chofer = $pdf->GetY() ; // FIJAR para datos de documentacion adicional





                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Nombre:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = utf8_decode( $chofer ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode( $chofer_t_doc_corto . ":" ); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = utf8_decode( $chofer_documento ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = utf8_decode('Teléfono:'); // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = utf8_decode( $chofer_telefono ); // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



                // VACIO, POR SI ACASO 

                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = ''; // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = ''; // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<





                // VACIO, POR SI ACASO 

                // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    // COLUMNA DE DATOS 1

                    $ancho_celda = $ancho_sub_titulo;
                    $alineacion = 'L';

                    $texto = ''; // SUB TITULO                                                                             

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                    $ancho_celda = $ancho_celda_dato_extenso;

                    $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                    $texto = ''; // DATO                                                                           

                    $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                

                // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<









                if ( $mostrar_propietario > 0 ) {

                        // SIGUIENTE LINEA .......................................................................................................
                        // INICIO CONTENIDO CHOFER ..............................................................................................

                        // Propietario

                        $ancho_celda = $ancho_total;
                        $alto_renglon_celda = $alto_renglon_titulos;
                        $borde = "B";
                        $alineacion = 'L';

                        $punto_x_actual = $punto_x_inicial;
                        $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO 
                        $pdf->SetFont('Arial','B',$tamano_fuente); // Tipo de fuente

                        $texto = utf8_decode('Propietario'); // SUB TITULO

                        $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $borde, $siguiente_linea, $alineacion, $rellenar);





                        $alto_renglon_celda = $alto_renglon_celda_datos ;

                        $pdf->SetFont('Arial', '', $tamano_fuente); // Tipo de fuente               

                        $punto_y_inicial_propietario = $pdf->GetY() ; // FIJAR para datos de documentacion adicional





                        // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                        $punto_x_actual = $punto_x_inicial;
                        $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                        $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            // COLUMNA DE DATOS 1

                            $ancho_celda = $ancho_sub_titulo;
                            $alineacion = 'L';

                            $texto = utf8_decode('Nombre:'); // SUB TITULO                                                                             

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                            $ancho_celda = $ancho_celda_dato_extenso;

                            $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            $texto = utf8_decode( $propietario_nombre ); // DATO                                                                           

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                        

                        // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



                        // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                        $punto_x_actual = $punto_x_inicial;
                        $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                        $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            // COLUMNA DE DATOS 1

                            $ancho_celda = $ancho_sub_titulo;
                            $alineacion = 'L';

                            $texto = utf8_decode( $propietario_t_doc_corto . ":" ); // SUB TITULO                                                                             

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                            $ancho_celda = $ancho_celda_dato_extenso;

                            $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            $texto = utf8_decode( $propietario_doc ); // DATO                                                                           

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                        

                        // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



                        // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                        $punto_x_actual = $punto_x_inicial;
                        $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                        $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            // COLUMNA DE DATOS 1

                            $ancho_celda = $ancho_sub_titulo;
                            $alineacion = 'L';

                            $texto = utf8_decode('Teléfono:'); // SUB TITULO                                                                             

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                            $ancho_celda = $ancho_celda_dato_extenso;

                            $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            $texto = utf8_decode( $propietario_telefono ); // DATO                                                                           

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                        

                        // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                        // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                        $punto_x_actual = $punto_x_inicial;
                        $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                        $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            // COLUMNA DE DATOS 1

                            $ancho_celda = $ancho_sub_titulo;
                            $alineacion = 'L';

                            $texto = utf8_decode('Dirección:'); // SUB TITULO                                                                             

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                            $ancho_celda = $ancho_celda_dato_extenso;

                            $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            $texto = utf8_decode( $propietario_direccion ); // DATO                                                                           

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);
                        

                        // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


                }
                












                // DATOS DE DOCUMENTOS ADICIONALES 


                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO


                if ( $cant_documentos_adicionales > 0 ) {

                    // $bordes = 1 ; // TMP

                    $pos_y_tractor = 0 ;
                    $pos_y_carreta = 0 ;
                    $pos_y_chofer = 0 ;
                    $pos_y_propietario = 0 ;

                    
                    foreach( $filas_adicionales as $fila_adicional ) { // DETALLES 

                            // en el orden esta la forma de organizar ... 0 tractor, 1 carreta, 2 chofer o propietario

                            $orden_doc_adicional = $fila_adicional['orden'] ;
                            $persona_id = $fila_adicional['persona_id'] ;

                            if ( $orden_doc_adicional == 0 AND $pos_y_tractor == 0 ) { $pos_y_tractor = $punto_y_inicial_tractor ; $pdf->SetY( $pos_y_tractor ) ; }
                            if ( $orden_doc_adicional == 1 AND $pos_y_carreta == 0 ) { $pos_y_carreta = $punto_y_inicial_carreta ; $pdf->SetY( $pos_y_carreta ) ; }
                            if ( $orden_doc_adicional == 2 AND $persona_id == $id_chofer AND $pos_y_chofer == 0 ) { $pos_y_chofer = $punto_y_inicial_chofer ; $pdf->SetY( $pos_y_chofer ) ; }
                            if ( $orden_doc_adicional == 2 AND $persona_id == $id_propietario AND $pos_y_propietario == 0 ) { $pos_y_propietario = $punto_y_inicial_propietario ; $pdf->SetY( $pos_y_propietario ) ; }

                            // SI HAYA CORRESPONDIDO REINICIAR LA FILA

                            $punto_x_actual = $punto_x_inicial + $ancho_celda_titulo + $ancho_celda_dato_extenso + ( $ancho_celda_espacio * 2 ) + $ancho_celda_titulo + $ancho_celda_espacio_adicionales ;
                            $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO

                            $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            $permanente = intval( $fila_adicional['permanente'] ) ;
                            $fecha_venc = $fila_adicional['f_venc'] ;

                            // ---- LINEA -----------------------------------------------------------------------------------------------------------------

                            $ancho_celda = $ancho_celda_adicionales_titulo;
                            $alineacion = 'L';

                            $texto = utf8_decode( $fila_adicional['t_doc_corto'] ); // SUB TITULO                                                                             

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                            $ancho_celda = $ancho_celda_adicionales_dato;
                            $alineacion = 'R';

                            $texto = utf8_decode( $fila_adicional['referencia'] ); // DATO                                                                           

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                            // ESPACIO 
                            $ancho_celda = $ancho_celda_espacio_adicionales;
                            $texto = '' ;
                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                            $ancho_celda = $ancho_celda_adicionales_vencimiento;
                            $alineacion = 'L';

                            $texto = ( $permanente < 1 ) ? 'Venc.:' : '' ; // SUB TITULO 

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);


                            $alineacion = 'R';

                            $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                            $texto = ( $permanente < 1 AND strlen( $fecha_venc ) > 0 ) ? fecha_salida_corta_sin_hora( $fecha_venc, '/' ) : '' ; // SUB TITULO                                                                         

                            $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);



                            // ---- FIN LINEA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




                    }





                } // FIN si documentos adicionales 












                



                


                

















                //    *****************************************               IMAGENES DE REFERENCIA                 *****************************************

                if ($_SESSION["adjuntar_fotos_vales"] == 1 and $cargar_fotos_ficha > 0) { // adjuntar al final del vale ( ver en login_p.php al iniciar sesion )

                    // $x_inicial = 15 ;
                    $foto_x = $ancho_total;

                    $altura_foto_sumar = 50;

                    // $foto_y = 150 ;
                    // $foto_y = 66 ;
                    $foto_y = 78; // Mover imagen arriba 0 abajo. empezar con 72 
                    $foto_y_inicial = $foto_y;
                    $veces = 0;
                    $extension_fotos = 0;
                    $margen_fotos = 2;
                    $tipo_foto = "v"; // vehiculo o persona

                    $total_fotos_adjuntar = 3;
                    $fotos_impresas = 0;

                    while ($veces < $total_fotos_adjuntar) {

                        $respuesta_imagen = ''; // RESET

                        $foto_y = $foto_y_inicial + ($altura_foto_sumar * $veces);

                        switch ($veces) {
                            case 0:
                                // $foto_y = 45;
                                $foto_y = $punto_y_inicial_tractor ;
                                $codigo_buscar = strtolower(quitarEspacios($chapa_tractor));
                                break;
                            case 1:
                                // $foto_y = 110;
                                $foto_y = $punto_y_inicial_carreta ;
                                $codigo_buscar = strtolower(quitarEspacios($chapa_carreta));
                                break;
                            case 2:
                                // $foto_y = 168;
                                $foto_y = $punto_y_inicial_chofer ;
                                $codigo_buscar = strtolower(quitarEspacios($doc_chofer));
                                break;
                        }


                        $foto_y = $foto_y + $margen_y_foto;


                        // buscar las fotos /////////////////////////////////////////////////////////////////////////////////////////////////

                        if (strlen($codigo_buscar) > 0) {

                            // ------------------ PARAMETROS 

                            $fields = array(                        // definir los campos a enviar al buscador de fotos 
                                'p' => $porcentaje_fotos,
                                'e' => $cod_cliente,
                                'u' => $nom_usuario,
                                't' => $tipo_foto,
                                'c' => $codigo_buscar,
                                'f' => $formato_imagen
                            );
                            // ------------------ MONTAR CADENA DE PARAMETROS 

                            $fields_string = ""; // CREAR un objeto para cargar las variables

                            foreach ($fields as $key => $value) {
                                $fields_string .= $key . '=' . $value . '&';
                            } //url-ify the data for the POST
                            $fields_string = rtrim($fields_string, '&');

                            // ------------------ BUSCAR VIA CURL - POST

                            $useragent = $_SERVER['HTTP_USER_AGENT'];

                            // $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';

                            if (isset($_COOKIE['PHPSESSID'])) { // si hay comun 

                                $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
                            }

                            if (isset($_COOKIE['__Secure-PHPSESSID'])) { // si hay comun 

                                $strCookie = '__Secure-PHPSESSID=' . $_COOKIE['__Secure-PHPSESSID'] . '; path=/';
                            }


                            session_write_close();


                            $ch = curl_init($url_fotos);
                            curl_setopt($ch, CURLOPT_URL, $url_fotos);

                            // curl_setopt($ch, CURLOPT_POST, count($fields));
                            // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

                            curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
                            curl_setopt($ch, CURLOPT_COOKIE, $strCookie);

                            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
                            curl_setopt($ch, CURLOPT_POST, count($fields));
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

                            // no enviar los header, y asi evitar imprimirlos en este script, obteniendo solo el body de la respuesta curl
                            curl_setopt($ch, CURLOPT_HEADER, 0); // NO HEADERS            
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            $respuesta_imagen = curl_exec($ch); // EJECUTAR CURL para obtener los resultados

                            curl_close($ch);
                        }   // FIN buscar las fotos /////////////////////////////////////////////////////////////////////////////////////////////////


                        if (strlen($respuesta_imagen) > 0) {

                            $imagen = imagecreatefromstring($respuesta_imagen);

                            $foto_str = 'data://text/plain;base64,' . base64_encode($respuesta_imagen);

                            $ancho_foto = imagesx($imagen);
                            $alto_foto = imagesy($imagen);

                            if ($ancho_foto > $alto_foto) {
                                $ancho = 40;
                                $alto = round(($alto_foto * $ancho) / $ancho_foto); // $alto = 60 ; // la altura sera relativa a la imagen en cuestion 
                            } else {
                                $ancho = 30;
                                $alto = round(($ancho_foto * $ancho) / $alto_foto); // $alto = 82 ; // el anchosera relativo a la imagen en cuestion 
                            }

                            /*
                                                            if ( $fotos_impresas % 2 == 0 ) { // si la cantidad de veces es par alinear a la izquierda, caso contrario a la derecha
                                                                $foto_x = $punto_x_inicial ; // reset para abajo
                                                            } else {
                                                                $foto_x = ( $ancho_total + $punto_x_inicial ) - $ancho ;
                                                            }
                                                            */

                            $extension_fotos = $extension_fotos + $ancho; // ancho de la foto 

                            if ($fotos_impresas > 0) {
                                $extension_fotos = $extension_fotos + $margen_fotos;
                            }

                            $foto_x = ($ancho_total + $punto_x_inicial) - $ancho; // menos el ancho de la foto actual

                            // $foto_x = 15 ;

                            $alto = 0; // segun el manual, calcula automatico si valor del altura es CERO

                            $pdf->Image($foto_str, $foto_x, $foto_y, $ancho, $alto, "jpg"); // colocar imagen en el PDF

                            imagedestroy($imagen); // limpiar ...

                            switch ($veces) {
                                case 0:
                                    $existe_foto_tractor = true;
                                    break;
                                case 1:
                                    $existe_foto_carreta = true;
                                    break;
                                case 2:
                                    $existe_foto_chofer = true;
                                    break;
                            }

                            $fotos_impresas++; // aumenta el numero de fotos impresas...

                        } // fin si existe tractor ...

                        $veces++; // aumenta el numero de veces, igual si no se encontro la foto...


                        /*
                                        
                                        if ( $fotos_impresas > 1 ){
                                            // $foto_y = 220 ;
                                            $foto_y = 90 ; 
                                        }

                                        */

                        if ($veces > 1) {
                            $tipo_foto = "p"; // a partir del segundo se busca persona ( chofer )
                        }
                    } // FIN WHILE fotos 


                } // fin si incluir imagenes 

                //    *****************************************               FIN IMAGENES DE REFERENCIA                 *****************************************


















                











                // ENVIAR POSICION AL PIE DEL DOCUMENTO 

                $pos_y_actual = 284 ;


                $pdf->SetY($pos_y_actual) ;


                $bordes = 'T' ;

                


                // SIGUIENTE LINEA .......................................................................................................

                $tamano_fuente = 7;

                $alto_renglon_celda = 7;

                // SIGUIENTE LINEA .......................................................................................................
                $punto_x_actual = $punto_x_inicial;
                $pdf->SetX($punto_x_actual); // ENVIARLO AL PRINCIPIO 

                $alineacion = 'L';
                $ancho_celda = 12;
                $siguiente_linea = 0; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)


                $texto = utf8_decode('Impreso el '); // dato a mostrar

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                $ancho_celda = 20;

                $texto = $f_doc;

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);

                // SIGUIENTE LINEA .......................................................................................................

                $siguiente_linea = 1; // 0 right (derecha), 1 beggining next line (comienzo de la siguiente linea), 2 below (debajo)

                $alineacion = 'R';

                $ancho_celda = 148;

                $texto = $app_url; // dato a mostrar

                $pdf->Cell($ancho_celda, $alto_renglon_celda, $texto, $bordes, $siguiente_linea, $alineacion, $rellenar);








                // ----------------------------------------------------------------      FIN CONTENIDO DOCUMENTO               --------------------------------------------------------------


                $via_actual++; // aumentar el conteo de vias




            } // ---------------------------------------------------------------------          FIN DEL WHILE DE VIAS               --------------------------------------------------------------






            // #########################################################################################################################################################################################
            // #########################################################################################################################################################################################

            //                                                                          FIN FORMATO REPORTE 

            // #########################################################################################################################################################################################
            // #########################################################################################################################################################################################















            // SEGUN EL DESTINO seleccionado ...

            if ($destino_reporte == "i") {
                $pdf->Output();
            } // ver en linea...

            if ($destino_reporte == "d") {
                $pdf->Output("D", $nom_archivo_descargar);
            }

            if ($destino_reporte == "s") {
                $archivo_str = $pdf->Output("S", $nom_archivo_descargar);
            }
        } //    *****************************************               FIN SI IMPRIMIR PDF                 *****************************************



        // RESPONDER A LA LLAMADA POST !
        if ($destino_reporte == "s") {

            $obj->cod_error = 0;
            $obj->destinatarios_cc = $destinatarios_cc;
            $obj->asunto_mail = utf8_decode($asunto_mail);
            $obj->txt_mail_html = $txt_mail_html;
            $obj->nom_adjunto_mail = $nom_adjunto_mail;
            $obj->archivo_str = base64_encode($archivo_str);

            $json = json_encode($obj);

            echo ($json);
        }
    } else {

        $cod_error = 2;
        $respuesta = "No se ha encontrado el equipo solicitado";

        // preparar la respuesta ....

        $obj->cod_error = $cod_error;
        $obj->respuesta = $respuesta;

        $json = json_encode($obj);

        echo ($json);
    }
} else { // FIN SI SESION INICIADA....

    // preparar la respuesta ....

    $obj->cod_error = $cod_error;
    $obj->respuesta = $respuesta;

    $json = json_encode($obj);

    echo ($json);
}
