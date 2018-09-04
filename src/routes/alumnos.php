<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

// Acción POST
$app->post('/alumno/calificacion/alta', function(Request $request, Response $response){
    $validar = new validaciones();
        
    $idMateria = $request->getParam('id_t_materias');
    $idUsuario = $request->getParam('id_t_usuarios');
    $calificacion = $request->getParam('calificacion');
    $fecha = date("Y-m-d");

    $validar->requeridos($request->getParams());
    $validar->soloNumeros($request->getParams());

    $sql = "INSERT INTO t_calificaciones (id_t_materias, id_t_usuarios, calificacion, fecha_registro) VALUES
    (:idMaterias, :idUsuarios, :calificacion, :fecha)";

    try{
        $db = new db();

        $db = $db->conectar();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':idMaterias', $idMateria);
        $stmt->bindParam(':idUsuarios', $idUsuario);
        $stmt->bindParam(':calificacion', $calificacion);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        echo '{"success": 200, "msg": "calificación registrada"}';
    } catch(PDOException $e){
        echo '{"error": 400, "msg": '.$e->getMessage().'}';
    }
});

// Acción GET
$app->get('/alumno/promedio/{id}', function(Request $request, Response $response){

    $id = $request->getAttribute('id');

    $sql = "
        SELECT 
            c.id_t_usuarios AS id_t_usuarios,
            a.nombre AS nombre,
            a.ap_paterno AS apellido,
            m.nombre AS materia,
            c.calificacion AS calificacion,
            DATE_FORMAT(c.fecha_registro, '%d/%m/%Y') AS fecha_registro
        FROM 
            t_calificaciones c
        LEFT JOIN
            t_materias m
        ON
            m.id_t_materias = c.id_t_materias
        LEFT JOIN
            t_alumnos a
        ON
            a.id_t_usuarios = c.id_t_usuarios
        WHERE
            c.id_t_usuarios = $id
    ";

    try{
        $db = new db();

        $db = $db->conectar();
        $ejecutar = $db->query($sql);
        $calificaciones = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        $calculoP = 0;
        $countMaterias = 0;
        
        foreach($calificaciones as $row) {
            $calculoP += $row->calificacion;
            $countMaterias++;
        }
        
        $calculoP = $calculoP/$countMaterias;
        $promedio = array();
        $promedio["promedio"] = $calculoP; 
        array_push($calificaciones, $promedio);
        
        echo json_encode($calificaciones);
        
    } catch(PDOException $e){
        echo '{"error": 400, "msg": '.$e->getMessage().'}';
    }
});

// Acción PUT
$app->put('/alumno/calificacion/{id}', function(Request $request, Response $response){
    $validar = new validaciones();
    
    $id = $request->getAttribute('id');
    $idMateria = $request->getParam('id_t_materias');
    $calificacion = $request->getParam('calificacion');
    $fecha = date("Y-m-d");

    $validar->requeridos($request->getParams());
    $validar->soloNumeros($request->getParams());
    
    $sql = "
        UPDATE t_calificaciones SET
            calificacion = :calificacion,
            fecha_registro = :fecha
        WHERE 
            id_t_usuarios = :id
        AND
            id_t_materias = :idMateria
    ";

    try{
        $db = new db();
        $db = $db->conectar();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':calificacion', $calificacion);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':idMateria', $idMateria);
        $stmt->execute();
        
        echo '{"success": 200, "msg": "calificación registrada"}';
    } catch(PDOException $e){
        echo '{"error": 400, "msg": '.$e->getMessage().'}';
    }
});
// Acción DELETE
$app->delete('/alumno/{id}/calificacion/{idCal}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $idCal = $request->getAttribute('idCal');
    
    $sql = "DELETE FROM t_calificaciones WHERE id_t_calificaciones = :idCal AND id_t_usuarios = :id";
    
    try{
        $db = new db();
        $db = $db->conectar();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':idCal', $idCal);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $db = null;
        
        echo '{"success": 200, "msg": "calificación registrada"}';
    } catch(PDOException $e){
        echo '{"error": 400, "msg": '.$e->getMessage().'}';
    }
});
?>