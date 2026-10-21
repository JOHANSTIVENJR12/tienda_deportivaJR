USE tienda_deportiva_jr;
-- Cuentas ficticias: ana@example.com y bruno@example.com / DemoJR2026!
-- Este hash se generó con password_hash(), nunca mediante cifrado reversible.
INSERT INTO usuarios (id,nombre,email,password) VALUES
(1,'Ana Demo','ana@example.com','$2y$10$SgV4cEVzFPVFqRGR02Orqu.7kFNk..ywWdElWpws/cKGUcm3r3L7C'),
(2,'Bruno Demo','bruno@example.com','$2y$10$SgV4cEVzFPVFqRGR02Orqu.7kFNk..ywWdElWpws/cKGUcm3r3L7C');
INSERT INTO direcciones (usuario_id,calle,ciudad,cp,tipo,principal) VALUES
(1,'Calle Ejemplo 10, 2 A','Madrid','28001','entrega',1),
(1,'Avenida Prueba 25','Madrid','28002','facturación',1),
(2,'Calle Otra Cuenta 8','Valencia','46001','entrega',1);
INSERT INTO pedidos (id,usuario_id,nombre,email,calle,ciudad,cp,total,estado,token) VALUES
(1,1,'Ana Demo','ana@example.com','Calle Ejemplo 10, 2 A','Madrid','28001',45.00,'entregado',REPEAT('a',64)),
(2,2,'Bruno Demo','bruno@example.com','Calle Otra Cuenta 8','Valencia','46001',120.00,'pendiente',REPEAT('b',64)),
(3,NULL,'Invitado Demo','invitado@example.com','Plaza Ficticia 1','Sevilla','41001',8.00,'pendiente',REPEAT('c',64));
INSERT INTO pedido_lineas (pedido_id,producto_id,producto,precio_unitario,cantidad) VALUES
(1,1,'Camiseta Deportiva roja',30.00,1),(1,5,'Medias Deportivas',15.00,1),
(2,3,'Nike Superfly',120.00,1),(3,6,'Banda Elástica',8.00,1);
