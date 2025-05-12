<?php
//
//
//--------------------------------------------------
// Esta linea es la que añado para comprobar 
//
//
//------------------------------------------------------
/*
Implementar aquí la función que realiza la autenticación de usuario (solo debe haber una función).
La función debe:
- Recibir por parámetro la conexión a la base de datos (no debe crearse una nueva conexión en su interior)
- Recibir por parámet
*/
function autenticarUsuario($pdo, $login, $password) {
    $sql = "SELECT id, hash_contraseña FROM usuarios WHERE login = :login";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($fila) {
            // Construir el hash de la contraseña ingresada
            $password_hash = hash('sha256', strrev($login) . 'TEST' . SALTEADO);
            var_dump($login);  // Verifica el login
            var_dump($password);  // Verifica la contraseña
            var_dump($password_hash);  // Verifica el hash generado
            var_dump($fila['hash_contraseña']);
            if ($password_hash === $fila['hash_contraseña']) {
                return [
                    'estado' => LOGIN_OK,
                    'id_usuario' => $fila['id'],
                    'login' => $login
                ];
            }
            return ['estado' => LOGIN_ERR]; // Contraseña incorrecta
        }

        return ['estado' => LOGIN_ERR]; // Usuario no encontrado

    } catch (PDOException $e) {
        return ['estado' => LOGIN_FAIL_DB, 'error' => $e->getMessage()];
    }
}


function obtenerMascotasPorTipo(PDO $pdo, array $tipos):array|int
{
    if (empty($tipos)) {        
        return -1;
    }    

    try {
        $tipos=array_values($tipos); //Reindexamos el array (para recorrerlo con bucle for/count)
        $SQLPART=implode (" OR ",array_fill(0,count($tipos),'tipo=?'));
        $query = "SELECT id, nombre, tipo FROM mascotas WHERE ($SQLPART) AND publica = \"si\"";
        $stmt = $pdo->prepare($query);
        for ($i=1;$i<=count($tipos);$i++)
        {
            $stmt->bindValue($i,$tipos[$i-1]);
        }        
        $mascotas = $stmt->execute()?$stmt->fetchAll(PDO::FETCH_ASSOC):false;
        return $mascotas ? $mascotas : -1;

    } catch (PDOException $e) {
        return -2;
    }
}
?>
