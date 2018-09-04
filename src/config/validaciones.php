<?php
    class validaciones {
        function requeridos($parametro = array()) {
            $msj = array();
            $response = array();

            foreach ($parametro as $key => $value) {
                if(empty($value)) {
                    $msj[$key] = 'Dato requerido';
                }
            }

            if(count($msj) > 0) {
                $response["error"] = "412";
                $response["msg"] = $msj;

                echo json_encode($response);
                exit();
            }

        }

        function soloNumeros($parametro = array()) {
            $msj = array();
            $response = array();
            
            foreach ($parametro as $key => $value) {
                if(!is_numeric($value)) {
                    $msj[$key] = 'El dato debe contener un valor numérico';
                }
            }

            if(count($msj) > 0) {
                $response["error"] = "412";
                $response["msg"] = $msj;
                
                echo json_encode($response);
                exit();
            }

        }
    }
?>