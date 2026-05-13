-- REGISTRO DE MOVIMIENTOS 

INSERT INTO TRANSACCION (id_cuenta_origen, id_cuenta_destino, tipo_movimiento, monto, descripcion, estado) 
VALUES (?, ?, ?, ?, ?, ?);


-- CONFIRMACIÓN / CANCELACIÓN 
UPDATE TRANSACCION SET estado = ? WHERE id_transaccion = ?;


-- FILTRADO DE MOVIMIENTOS 

SELECT * FROM TRANSACCION WHERE tipo_movimiento = ? AND id_cuenta_origen = ?;


-- DETALLE DE OPERACIÓN 
SELECT T.*, C.numero_cuenta 
FROM TRANSACCION T 
JOIN CUENTA C ON T.id_cuenta_origen = C.id_cuenta 
WHERE T.id_transaccion = ?;


