# Sistema Finora — Diagramas de Actividad UML

> **Materia:** INF412 – Sistemas de Información II  
> **Institución:** Universidad Autónoma Gabriel René Moreno (UAGRM) — Facultad Integral del Chaco (FICH)  
> **Proyecto:** Finora — Sistema de Información Web para la Gestión Comercial y Financiera de un Emprendimiento de Venta por Catálogo  
> **Docente:** Ing. Rolando Lijerón  
> **Estudiantes:** Josué Nicolás Chacae Veizaga, Maricelba Flores Aparicio, Malinda Nazareth Davila Mancilla, Midory Nayeli Toledo Moreno  
> **Fecha:** Octubre, 2026  

---

## Índice de Diagramas

1. [Diagrama 1: Macro-Proceso Comercial y Financiero (Visión Global de Negocio)](#diagrama-1-macro-proceso-comercial-y-financiero-visión-global)
2. [Diagrama 2: Captura de Pedidos Web y Reserva Atómica de Stock (CU-15 y CU-17)](#diagrama-2-captura-de-pedidos-web-y-reserva-atómica-de-stock-cu-15-y-cu-17)
3. [Diagrama 3: Confirmación Atómica de Venta, Salida de Kárdex y Crédito (CU-20 y CU-22)](#diagrama-3-confirmación-atómica-de-venta-salida-de-kárdex-y-crédito-cu-20-y-cu-22)
4. [Diagrama 4: Cobranza y Amortización en Cascada de Cuotas (CU-23)](#diagrama-4-cobranza-y-amortización-en-cascada-de-cuotas-cu-23)
5. [Diagrama 5: Abastecimiento, Conversión Multidivisa y Recepción en Kárdex (CU-08 y CU-09)](#diagrama-5-abastecimiento-conversión-multidivisa-y-recepción-en-kárdex-cu-08-y-cu-09)

---

## Diagrama 1: Macro-Proceso Comercial y Financiero (Visión Global)

### Contexto y Trazabilidad
Modela el ciclo operativo de extremo a extremo, integrando las 3 calles (*swimlanes*) de actores del sistema:
- **Cliente / Visitante Web**: Accede al catálogo público, arma el carrito y envía el pedido.
- **Consultora / Colaborador**: Gestiona las operaciones desde el panel de control.
- **Sistema Finora (Backend Laravel + Base de Datos MySQL)**: Orquesta validaciones, bloqueos pesimistas (`lockForUpdate`), kárdex y amortización de créditos.

### Código Fuente Mermaid

```mermaid
flowchart TD
    subgraph Cliente ["Cliente / Visitante Web"]
        A1([Inicio]) --> A2["Explorar catálogo público (/ o /categorias)"]
        A2 --> A3["Armar carrito y enviar pedido (POST /carrito/pedido)"]
        A3 --> A4["Confirmar derivación por WhatsApp con mensaje preformateado"]
    end

    subgraph Sistema ["Sistema Finora (Backend Laravel / MySQL)"]
        S1["Deduplicar Persona y registrar Pedido en estado PENDIENTE"]
        S2{"¿Existe stock disponible libre de reservas?"}
        S3["Asignar cantidad_reservada requerida<br/>Estado del pedido: LISTO_ENTREGA"]
        S4["Asignar reserva parcial o cero<br/>Estado: PENDIENTE_ABASTECIMIENTO"]
        S5["Verificar disponibilidad física con lockForUpdate"]
        S6["Generar movimientos SALIDA_VENTA en Kárdex"]
        S7{"¿Forma de pago?"}
        S8["Crear Pago automático al 100% (REGISTRADO)"]
        S9["Crear Crédito ACTIVO y calcular N cuotas a 30 días"]
        S10["Amortizar abono cuota por cuota en orden cronológico"]
        S11{"¿Saldo total de la venta == 0.00 Bs?"}
        S12["Actualizar Crédito a PAGADO"]
        S13["Mantener Crédito en estado ACTIVO"]
    end

    subgraph Operador ["Consultora / Colaborador"]
        O1["Recibir mensaje de WhatsApp y abrir pedido en panel"]
        O2["Presionar botón 'Reservar Stock'"]
        O3["Generar Venta en borrador desde el pedido"]
        O4["Presionar botón 'Confirmar Venta'"]
        O5["Registrar abono de cobranza en /panel/pagos"]
        O6([Fin de Ciclo Operativo])
    end

    A3 --> S1
    S1 --> A4
    A4 -.-> O1
    O1 --> O2
    O2 --> S2
    S2 -- "Sí (100% cubierto)" --> S3
    S2 -- "No (Falta stock)" --> S4
    S3 --> O3
    S4 -.->|"Tras reabastecer inventario"| O3
    O3 --> O4
    O4 --> S5
    S5 --> S6
    S6 --> S7
    S7 -- "CONTADO" --> S8
    S7 -- "CRÉDITO" --> S9
    S8 --> O6
    S9 --> O5
    O5 --> S10
    S10 --> S11
    S11 -- "Sí" --> S12
    S11 -- "No" --> S13
    S12 --> O6
    S13 --> O5
```

---

## Diagrama 2: Captura de Pedidos Web y Reserva Atómica de Stock (CU-15 y CU-17)

### Contexto y Trazabilidad
- **Controlador Web:** `app/Http/Controllers/PedidoPublicoController.php` (métodos `create` y `store`).
- **Controlador Operativo:** `app/Http/Controllers/Admin/PedidoController.php` (métodos `reservarStock` y `ejecutarReserva`).
- **Garantía Transaccional:** Se utiliza `DB::transaction` combinada con `lockForUpdate()` sobre la tabla de productos para evitar sobreventas concurrentes cuando dos clientes o administradores operan simultáneamente sobre la última unidad disponible.

### Código Fuente Mermaid

```mermaid
flowchart TD
    subgraph Cliente ["Cliente / Visitante Web"]
        P1([Inicio]) --> P2["Navegar catálogo en /categorias"]
        P2 --> P3["Agregar productos al carrito"]
        P3 --> P4["Ingresar datos de contacto: nombre, teléfono, dirección y notas"]
        P4 --> P5["Enviar solicitud: POST /carrito/pedido"]
        P6["Visualizar pantalla de confirmación con código de pedido"]
        P6 --> P7["Hacer clic en 'Confirmar por WhatsApp' (enlace wa.me/...)"]
    end

    subgraph BackendWeb ["Backend Finora (PedidoPublicoController)"]
        B1["Iniciar DB::transaction"]
        B2{"¿Existe persona por teléfono o email?"}
        B3["Asociar a Cliente existente"]
        B4["Crear nuevo registro en Personas y Clientes"]
        B5["Crear registro en Pedidos con estado PENDIENTE"]
        B6["Crear registros en DetallePedidos"]
        B7["Hacer Commit y generar URL de WhatsApp con mensaje estructurado"]
    end

    subgraph Operador ["Consultora / Colaborador"]
        U1["Visualizar pedido en /panel/pedidos/{id}"]
        U2["Hacer clic en el botón 'Reservar Stock'"]
        U3["Ver pedido actualizado con stock reservado y nuevo estado"]
        U4([Fin del Subproceso])
    end

    subgraph MotorReserva ["Motor de Reserva Atómica (PedidoController@ejecutarReserva)"]
        R1["Iniciar DB::transaction"]
        R2["Bloquear productos del pedido con lockForUpdate"]
        R3["Calcular stock disponible: Stock Físico Kárdex - Reservas Otros Pedidos"]
        R4["Asignar en DetallePedidos: cantidad_reservada = min(solicitada, disponible)"]
        R5{"¿Se cubrió el 100% de la cantidad solicitada?"}
        R6["Actualizar estado del Pedido a LISTO_ENTREGA"]
        R7["Actualizar estado del Pedido a PENDIENTE_ABASTECIMIENTO"]
        R8["Hacer Commit, liberar bloqueos y registrar auditoría"]
    end

    P5 --> B1
    B1 --> B2
    B2 -- "Sí" --> B3
    B2 -- "No" --> B4
    B3 --> B5
    B4 --> B5
    B5 --> B6
    B6 --> B7
    B7 --> P6
    P7 -. "Derivación comercial a WhatsApp" .-> U1
    U1 --> U2
    U2 --> R1
    R1 --> R2
    R2 --> R3
    R3 --> R4
    R4 --> R5
    R5 -- "Sí (Total)" --> R6
    R5 -- "No (Parcial o Cero)" --> R7
    R6 --> R8
    R7 --> R8
    R8 --> U3
    U3 --> U4
```

---

## Diagrama 3: Confirmación Atómica de Venta, Salida de Kárdex y Crédito (CU-20 y CU-22)

### Contexto y Trazabilidad
- **Controlador:** `app/Http/Controllers/Admin/VentaController.php` (método `confirmar`).
- **Modelos involucrados:** `Venta`, `DetalleVenta`, `MovimientoInventario`, `Credito`, `Cuota`, `Pago`.
- **Reglas del Negocio:**
  1. Valida que el stock físico descontando reservas de *otros* pedidos sea estrictamente suficiente.
  2. Genera registros inmutables en `movimientos_inventario` con `tipo = 'SALIDA_VENTA'` y cantidad negativa.
  3. Si la venta se derivó de un pedido, libera la reserva previa y lo marca en `LISTO_ENTREGA`.
  4. En ventas al contado, genera automáticamente el pago íntegro (`REGISTRADO`).
  5. En ventas a crédito, crea la cabecera en `creditos` y genera $N$ cuotas a 30 días, absorbiendo posibles residuos de decimales en la última cuota.

### Código Fuente Mermaid

```mermaid
flowchart TD
    subgraph Operador ["Consultora / Colaborador"]
        V1([Inicio]) --> V2["Ingresar a /panel/ventas/{id}"]
        V2 --> V3["Revisar cliente, ítems, precios, descuentos y forma de pago"]
        V3 --> V4["Presionar botón 'Confirmar Venta'"]
        V5["Visualizar venta CONFIRMADA, nota de salida de kárdex y comprobante"]
        V6([Fin del Subproceso])
    end

    subgraph TransaccionVenta ["Transacción ACID (VentaController@confirmar)"]
        T1["Iniciar DB::transaction y bloquear filas de productos con lockForUpdate"]
        T2{"¿Stock disponible físico >= Cantidad a vender?"}
        T3["Lanzar ValidationException y abortar con Rollback"]
        T4["Actualizar estado de Venta a CONFIRMADA"]
        T5["Insertar tuplas en MovimientoInventario tipo SALIDA_VENTA con cantidad negativa"]
        T6{"¿Venta asociada a un Pedido previo?"}
        T7["Liberar reservas del pedido y actualizar pedido a LISTO_ENTREGA"]
        T8{"¿Forma de pago registrada?"}
        T9["Insertar registro en Pagos al 100% con estado REGISTRADO"]
        T10["Crear registro en Creditos con estado ACTIVO"]
        T11["Generar N cuotas a 30 días calendario absorbiendo redondeos en última cuota"]
        T12["Registrar evento en tabla Auditorias y ejecutar Commit"]
    end

    V4 --> T1
    T1 --> T2
    T2 -- "No (Stock insuficiente)" --> T3
    T3 --> V2
    T2 -- "Sí (Stock garantizado)" --> T4
    T4 --> T5
    T5 --> T6
    T6 -- "Sí" --> T7
    T6 -- "No" --> T8
    T7 --> T8
    T8 -- "CONTADO" --> T9
    T8 -- "CRÉDITO" --> T10
    T10 --> T11
    T9 --> T12
    T11 --> T12
    T12 --> V5
    V5 --> V6
```

---

## Diagrama 4: Cobranza y Amortización en Cascada de Cuotas (CU-23)

### Contexto y Trazabilidad
- **Controlador:** `app/Http/Controllers/Admin/PagoController.php` (método `store`).
- **Modelos involucrados:** `Pago`, `AplicacionPago`, `Cuota`, `Credito`, `Venta`.
- **Reglas del Negocio:**
  1. El cliente entrega un importe (sea en efectivo, transferencia o QR).
  2. El sistema recupera las cuotas del crédito ordenadas por `numero ASC` y las bloquea con `lockForUpdate()`.
  3. Aplica el monto disponible cuota por cuota: las que se cubran por completo pasan a `PAGADA`; si el abono se agota a medio camino, esa cuota queda en `PARCIAL`.
  4. Crea tuplas de trazabilidad en `aplicaciones_pago` con el monto exacto asignado a cada cuota.
  5. Si el saldo restante de la venta llega a `0.00 Bs`, el crédito pasa de forma automática a estado `PAGADO`.

### Código Fuente Mermaid

```mermaid
flowchart TD
    subgraph Operador ["Consultora / Colaborador"]
        C1([Inicio]) --> C2["Ingresar a /panel/pagos/create"]
        C2 --> C3["Seleccionar venta a crédito con saldo pendiente"]
        C3 --> C4["Ingresar monto abonado, método (Efectivo/QR/Transferencia) y observación"]
        C4 --> C5["Presionar botón 'Registrar Pago'"]
        C6["Visualizar recibo, cuotas amortizadas y saldo deudor actualizado"]
        C7([Fin del Subproceso])
    end

    subgraph AlgoritmoCascada ["Algoritmo de Amortización en Cascada (PagoController@store)"]
        G1["Iniciar DB::transaction"]
        G2["Crear registro en Pagos con estado REGISTRADO"]
        G3["Obtener cuotas del crédito ordenadas por número ascendente con lockForUpdate"]
        G4["Seleccionar siguiente cuota con saldo pendiente"]
        G5["Calcular saldo individual de la cuota: Monto - Suma(AplicacionesPago previas)"]
        G6["Calcular monto a amortizar = min(MontoRestante, SaldoCuota)"]
        G7["Insertar tupla en AplicacionesPago con pago_id, cuota_id y monto_aplicado"]
        G8["Actualizar saldo restante: MontoRestante = MontoRestante - MontoAplicado"]
        G9{"¿Cuota amortizada al 100% de su valor?"}
        G10["Actualizar estado de cuota a PAGADA"]
        G11["Actualizar estado de cuota a PARCIAL"]
        G12{"¿MontoRestante > 0 y aún existen cuotas pendientes?"}
        G13{"¿Saldo total pendiente de la venta <= 0.00 Bs?"}
        G14["Actualizar cabecera de Crédito a estado PAGADO"]
        G15["Mantener cabecera de Crédito en estado ACTIVO"]
        G16["Registrar auditoría, ejecutar Commit y retornar respuesta exitosa"]
    end

    C5 --> G1
    G1 --> G2
    G2 --> G3
    G3 --> G4
    G4 --> G5
    G5 --> G6
    G6 --> G7
    G7 --> G8
    G8 --> G9
    G9 -- "Sí" --> G10
    G9 -- "No" --> G11
    G10 --> G12
    G11 --> G12
    G12 -- "Sí" --> G4
    G12 -- "No" --> G13
    G13 -- "Sí" --> G14
    G13 -- "No" --> G15
    G14 --> G16
    G15 --> G16
    G16 --> C6
    C6 --> C7
```

---

## Diagrama 5: Abastecimiento, Conversión Multidivisa y Recepción en Kárdex (CU-08 y CU-09)

### Contexto y Trazabilidad
- **Controladores:** `app/Http/Controllers/Admin/CompraController.php` y `app/Http/Controllers/TipoCambioController.php`.
- **Modelos involucrados:** `Compra`, `DetalleCompra`, `Configuracion`, `MovimientoInventario`, `Producto`.
- **Reglas del Negocio:**
  1. Permite cotizar compras en moneda extranjera (USD) convirtiendo automáticamente a Bolivianos (BOB) según el tipo de cambio oficial sincronizado con el BCB o valor de contingencia.
  2. Ciclo de estados de la orden: `BORRADOR -> SOLICITADA -> EN_CAMINO -> RECIBIDA`.
  3. Al marcarse como `RECIBIDA`, una transacción atómica genera movimientos en `movimientos_inventario` con `tipo = 'ENTRADA_COMPRA'` con cantidad positiva, actualizando el stock disponible de forma instantánea y bloqueando la compra contra futuras modificaciones.

### Código Fuente Mermaid

```mermaid
flowchart TD
    subgraph Operador ["Consultora / Colaborador"]
        M1([Inicio]) --> M2["Identificar productos con stock crítico o pedidos en PENDIENTE_ABASTECIMIENTO"]
        M2 --> M3["Ingresar a /panel/compras/create y seleccionar proveedor Natura"]
        M3 --> M4["Añadir productos e ingresar costos unitarios en USD o BOB"]
        M5["Efectuar pedido en el portal mayorista de Natura"]
        M6["Al arribar la encomienda física, abrir compra en panel y seleccionar 'RECIBIDA'"]
        M7["Verificar incremento automático en /panel/inventario y kárdex"]
        M8([Fin del Subproceso])
    end

    subgraph BackendFinora ["Sistema Finora (Compras y Kárdex)"]
        K1["Consultar tasa de cambio activa (Sincronización BCB o valor configurado)"]
        K2["Calcular costo en BOB y guardar cabecera/detalle en estado BORRADOR"]
        K3["Actualizar estado a SOLICITADA y registrar número de guía en EN_CAMINO"]
        K4["Iniciar DB::transaction tras solicitud de recepción"]
        K5["Bloquear filas de productos en MySQL con lockForUpdate"]
        K6["Insertar registros en MovimientoInventario con tipo ENTRADA_COMPRA (cantidad positiva)"]
        K7["Asentar fecha_recepcion y bloquear compra contra modificaciones posteriores"]
        K8["Actualizar estado a RECIBIDA, registrar en Auditorias y ejecutar Commit"]
    end

    M4 --> K1
    K1 --> K2
    K2 --> K3
    K3 --> M5
    M5 --> M6
    M6 --> K4
    K4 --> K5
    K5 --> K6
    K6 --> K7
    K7 --> K8
    K8 --> M7
    M7 --> M8
```

---

## Recomendaciones para la Inserción en el Documento Oficial

Para cumplir con el formato **APA 7** adoptado en el documento oficial `Finora_Documento_Original_Tablas_APA7.pdf`:

1. **Ubicación recomendada:**
   - **Capítulo II (Sección 2.3):** Reemplazar o acompañar las listas de texto de las páginas 21 a 23 con las figuras:
     - *Figura 2.1: Diagrama de Actividad — Proceso de Abastecimiento y Compra a Proveedor.*
     - *Figura 2.2: Diagrama de Actividad — Captura de Pedidos Web y Reserva Atómica de Stock.*
     - *Figura 2.3: Diagrama de Actividad — Venta, Financiamiento a Crédito y Cobranza en Cascada.*
   - **O Capítulo V (Sección 5.4):** Como nueva subsección titulada *“Diagramas de Actividad UML de los Flujos Críticos”*, justo después de los diagramas de secuencia.
2. **Formato de Figura según APA 7:**
   - **Número de figura** en negrita (ej. **Figura 2.2**).
   - **Título en cursiva** una línea abajo (ej. *Diagrama de Actividad del Proceso de Captura de Pedidos y Reserva Atómica*).
   - Imagen del diagrama centrada.
   - **Nota al pie:** *Nota.* Diagrama de actividad UML modelado con calles de responsabilidad (swimlanes) y trazabilidad directa sobre el backend Laravel y motor InnoDB de MySQL.
