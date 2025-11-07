<?php
require_once '../conection/conectionBD.php'; 

class classAdmin ()
{
    public function inserirDados($data_nascimento,$nome,$email,$senha,$cpf,$tipo)
    {

        $sql = 'INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?, "admin")';
        $stmt = mysqli_prepare($con, $sql);

        mysqli_stmt_bind_param($stmt, 'sssss', $data_nascimento, $nome, $email, $hash, $cpf);

        return mysqli_stmt_execute($stmt);
    }
}

?>