SELECT
    T.*,
    (
        CASE
            WHEN PRG.Codigo_Cum IS NOT NULL THEN PRG.Precio
            WHEN T.Precio_Venta_Factura IS NOT NULL THEN T.Precio_Venta_Factura
            ELSE 0
        END
    ) AS Precio_Venta_Factura,
    IF(
        PRG.Codigo_Cum IS NOT NULL,
        "Si",
        "No"
    ) AS Regulado
FROM
    (
        SELECT
            CONCAT_WS(
                " ",
                p.Nombre_Comercial,
                p.Presentacion,
                p.Concentracion,
                " (",
                p.Principio_Activo,
                ") ",
                p.Cantidad,
                p.Unidad_Medida
            ) AS Nombre,
            0 AS Costo_unitario,
            PD.Lote AS Lote,
            IFNULL(
                i.Id_Inventario,
                INV.Id_Inventario_Nuevo
            ) AS Id_Inventario,
            PD.Cum AS Cum,
            p.Invima AS Invima,
            IFNULL(
                i.Fecha_Vencimiento,
                INV.Fecha_Vencimiento
            ) AS Fecha_Vencimiento,
            p.Laboratorio_Generico AS Laboratorio_Generico,
            p.Laboratorio_Comercial AS Laboratorio_Comercial,
            p.Presentacion AS Presentacion,
            PD.Cantidad_Formulada AS Cantidad,
            PD.Id_Producto_Dispensacion,
            p.Gravado AS Gravado,
            p.Id_Producto,
            REPLACE (p.Codigo_Cum, "-", "") AS Cum_Medicamento,
            "0" AS Descuento,
            IF(
                p.Gravado = "Si"
                AND  '.$factura['IdClienteFactura'].' != 830074184,
                0.19,
                0
            ) AS Impuesto,
            "0" AS Subtotal,
            IFNULL(PNP.Precio, 0) AS Precio_Venta_Factura,
            0 AS Precio,
            0 AS Iva,
            0 AS Total_Descuento,
            PRG.Codigo_Cum,
            "" as Cum_Homologo,
            "" AS Id_Producto_Hom,
            0 as Precio_Homologo,
            "" as Detalle_Homologo,
            PNP.Id_Producto_Contrato,
            IF(
                PNP.Id_Producto_Contrato IS NULL,
                1,
                0
            ) AS Registrar
        FROM
            Producto_Dispensacion AS PD
            INNER JOIN Producto p ON p.Id_Producto = PD.Id_Producto
            LEFT JOIN Inventario_Viejo i ON i.Id_Inventario = PD.Id_Inventario
            LEFT JOIN Inventario_Nuevo INV ON INV.Id_Inventario_Nuevo = PD.Id_Inventario_Nuevo
            LEFT JOIN Precio_Regulado PRG ON PD.Cum = PRG.Codigo_Cum
            LEFT JOIN(
                SELECT
                    PNP.*
                FROM
                    Producto_Contrato PNP
                    INNER JOIN Contrato LPN ON LPN.Id_Contrato = PNP.Id_Contrato
                WHERE
                     LPN.Tipo_Contrato = "Eps"
                    AND LPN.Id_Cliente = '.$nombre["Nit"].'

            ) PNP ON PD.Cum = PNP.Cum           
        WHERE
            PD.Id_Dispensacion =  '.$id.'
    ) T
    LEFT JOIN(
        SELECT
            Precio,
            Codigo_Cum,
            REPLACE (Codigo_Cum, "-", "") AS Cum
        FROM
            Precio_Regulado
        GROUP BY
            Codigo_Cum
    ) PRG ON T.Codigo_Cum = PRG.Cum