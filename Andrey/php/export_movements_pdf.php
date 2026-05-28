<?php

require_once 'db.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

session_start();

if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {

    $sqlCuentas = "
        SELECT *
        FROM CUENTA
        WHERE id_usuario = ?
    ";

    $stmtCuentas = $conn->prepare($sqlCuentas);

    $stmtCuentas->execute([$id_usuario]);

    $cuentas = $stmtCuentas->fetchAll(PDO::FETCH_ASSOC);

    if (!$cuentas) {
        die("No tienes cuentas");
    }

    $ids = [];

    foreach ($cuentas as $cuenta) {

        $ids[] = $cuenta['id_cuenta'];
    }

    $placeholders =
        implode(',', array_fill(0, count($ids), '?'));

    $sqlMovimientos = "

        SELECT

            M.*,

            CO.numero_cuenta
                AS cuenta_origen,

            CD.numero_cuenta
                AS cuenta_destino

        FROM MOVIMIENTO M

        LEFT JOIN CUENTA CO
            ON M.id_cuenta_origen = CO.id_cuenta

        LEFT JOIN CUENTA CD
            ON M.id_cuenta_destino = CD.id_cuenta

        WHERE

            M.id_cuenta_origen IN ($placeholders)

            OR

            M.id_cuenta_destino IN ($placeholders)

        ORDER BY M.fecha_movimiento DESC
    ";

    $stmtMovimientos =
        $conn->prepare($sqlMovimientos);

    $stmtMovimientos->execute([
        ...$ids,
        ...$ids
    ]);

    $movimientos =
        $stmtMovimientos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Error: " . $e->getMessage());
}

$html = '

<style>

body{
    font-family: Arial;
}

h1{
    text-align:center;
    color:#6b21a8;
}

h3{
    text-align:center;
}

table{
    width:100%;
    border-collapse: collapse;
}

th{
    background:#6b21a8;
    color:white;
}

th, td{
    border:1px solid #ccc;
    padding:10px;
    font-size:12px;
    text-align:center;
}

</style>

<h1>NovaBank</h1>

<h3>Historial de Movimientos</h3>

<hr><br>

<table>

<tr>

    <th>Tipo</th>
    <th>Origen</th>
    <th>Destino</th>
    <th>Monto</th>
    <th>Fecha</th>

</tr>

';

foreach ($movimientos as $mov) {

    $html .= '

    <tr>

        <td>
            '.$mov['tipo_movimiento'].'
        </td>

        <td>
            '.($mov['cuenta_origen'] ?? '-').'
        </td>

        <td>
            '.($mov['cuenta_destino'] ?? '-').'
        </td>

        <td>
            $'.number_format($mov['monto'],2).'
        </td>

        <td>
            '.date(
                'd/m/Y h:i A',
                strtotime(
                    $mov['fecha_movimiento']
                )
            ).'
        </td>

    </tr>
    ';
}

$html .= '</table>';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream(
    "movimientos_novabank.pdf",
    ["Attachment" => true]
);

?>