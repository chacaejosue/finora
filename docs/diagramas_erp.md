# Diagramas del Sistema ERP - Finora

Este documento contiene los dos diagramas del sistema **Finora**, construidos a partir de los modelos y migraciones reales de la base de datos:

1. **Diagrama Entidad-Relación (ERD / DER):** Estructura relacional de datos física (tablas, campos clave y cardinalidades).
2. **Diagrama de Arquitectura y Procesos ERP:** Mapa funcional por módulos y flujo operativo del negocio.

---

## 1. Diagrama Entidad-Relación (ERD / DER)

> **Archivos de imagen listos para descargar o insertar en documentos:**
> - Imagen PNG de alta resolución: [`docs/diagrama_erd_finora.png`](file:///d:/Proyectos/finora/docs/diagrama_erd_finora.png)
> - Gráfico vectorial SVG: [`docs/diagrama_erd_finora.svg`](file:///d:/Proyectos/finora/docs/diagrama_erd_finora.svg)

![Diagrama ERD Finora](diagrama_erd_finora.png)

### 1.1 Código Fuente Mermaid

```mermaid
erDiagram
    PERSONAS ||--o| USUARIOS : "es"
    PERSONAS ||--o| CLIENTES : "es"
    
    USUARIOS ||--o{ COMPRAS : "registra"
    USUARIOS ||--o{ PEDIDOS : "registra"
    USUARIOS ||--o{ PEDIDOS : "entrega (delivery)"
    USUARIOS ||--o{ VENTAS : "registra"
    USUARIOS ||--o{ PAGOS : "registra"
    USUARIOS ||--o{ MOVIMIENTOS_INVENTARIO : "registra"
    USUARIOS ||--o{ AUDITORIAS : "genera"

    LINEAS ||--o{ PRODUCTOS : "clasifica"
    CATEGORIAS ||--o{ PRODUCTOS : "agrupa"
    PROVEEDORES ||--o{ PRODUCTOS : "suministra_habitual"

    PROVEEDORES ||--o{ COMPRAS : "emite_a"
    COMPRAS ||--|{ DETALLE_COMPRAS : "contiene"
    PRODUCTOS ||--o{ DETALLE_COMPRAS : "comprado_en"
    
    CLIENTES ||--o{ PEDIDOS : "solicita"
    PEDIDOS ||--|{ DETALLE_PEDIDOS : "contiene"
    PRODUCTOS ||--o{ DETALLE_PEDIDOS : "incluido_en"

    CLIENTES ||--o{ VENTAS : "adquiere"
    PEDIDOS ||--o| VENTAS : "origina"
    VENTAS ||--|{ DETALLE_VENTAS : "contiene"
    PRODUCTOS ||--o{ DETALLE_VENTAS : "vendido_en"

    VENTAS ||--o| CREDITOS : "financiada_por"
    CREDITOS ||--|{ CUOTAS : "planifica"
    VENTAS ||--o{ PAGOS : "recibe"
    PAGOS ||--|{ APLICACIONES_PAGO : "distribuye"
    CUOTAS ||--o{ APLICACIONES_PAGO : "amortizada_con"

    PRODUCTOS ||--o{ MOVIMIENTOS_INVENTARIO : "afecta_stock"
    DETALLE_COMPRAS ||--o| MOVIMIENTOS_INVENTARIO : "origen_entrada"
    DETALLE_VENTAS ||--o| MOVIMIENTOS_INVENTARIO : "origen_salida"

    PERSONAS {
        bigint id PK
        string ci UK
        string nombres
        string primer_apellido
        string segundo_apellido
        string telefono
        string direccion
        string genero
    }

    USUARIOS {
        bigint id PK
        bigint persona_id FK
        string username UK
        string password
        string rol
        boolean estado
    }

    CLIENTES {
        bigint id PK
        bigint persona_id FK
        string codigo_cliente UK
        boolean activo
    }

    PROVEEDORES {
        bigint id PK
        string razon_social
        string nit_rut UK
        string contacto_nombre
        string telefono
        string direccion
        boolean activo
    }

    LINEAS {
        bigint id PK
        string nombre UK
        string descripcion
        boolean activa
    }

    CATEGORIAS {
        bigint id PK
        string nombre UK
        string descripcion
        boolean activa
    }

    PRODUCTOS {
        bigint id PK
        string codigo UK
        string nombre
        text descripcion
        decimal precio_costo
        decimal precio_venta
        integer stock_actual
        integer stock_minimo
        bigint proveedor_id FK
        bigint linea_id FK
        bigint categoria_id FK
        boolean activo
    }

    COMPRAS {
        bigint id PK
        string numero_factura_nota
        date fecha_compra
        decimal total
        string estado
        bigint proveedor_id FK
        bigint registrado_por_usuario_id FK
    }

    DETALLE_COMPRAS {
        bigint id PK
        bigint compra_id FK
        bigint producto_id FK
        integer cantidad
        decimal precio_unitario
        decimal subtotal
    }

    PEDIDOS {
        bigint id PK
        string codigo UK
        bigint cliente_id FK
        bigint registrado_por_usuario_id FK
        date fecha_pedido
        timestamp fecha_entrega
        bigint entregado_por_usuario_id FK
        string estado
        decimal total
        string direccion_entrega
        decimal costo_delivery
    }

    DETALLE_PEDIDOS {
        bigint id PK
        bigint pedido_id FK
        bigint producto_id FK
        integer cantidad
        decimal precio_unitario
        decimal subtotal
    }

    VENTAS {
        bigint id PK
        string numero_comprobante UK
        bigint cliente_id FK
        bigint pedido_id FK
        bigint registrado_por_usuario_id FK
        dateTime fecha_venta
        decimal subtotal
        decimal descuento
        decimal total
        string tipo_pago
        string metodo_pago
        integer numero_cuotas
        string estado
    }

    DETALLE_VENTAS {
        bigint id PK
        bigint venta_id FK
        bigint producto_id FK
        integer cantidad
        decimal precio_unitario
        decimal subtotal
    }

    CREDITOS {
        bigint id PK
        bigint venta_id FK
        decimal monto_credito
        decimal saldo_pendiente
        integer numero_cuotas
        decimal tasa_interes
        string estado
    }

    CUOTAS {
        bigint id PK
        bigint credito_id FK
        integer numero_cuota
        date fecha_vencimiento
        decimal monto_cuota
        decimal saldo_cuota
        string estado
    }

    PAGOS {
        bigint id PK
        bigint venta_id FK
        bigint registrado_por_usuario_id FK
        dateTime fecha_pago
        decimal monto
        string metodo_pago
        string referencia
    }

    APLICACIONES_PAGO {
        bigint id PK
        bigint pago_id FK
        bigint cuota_id FK
        decimal monto_aplicado
    }

    MOVIMIENTOS_INVENTARIO {
        bigint id PK
        bigint producto_id FK
        bigint registrado_por_usuario_id FK
        string tipo_movimiento
        integer cantidad
        integer stock_anterior
        integer stock_nuevo
        string motivo
        bigint detalle_compra_id FK
        bigint detalle_venta_id FK
    }

    AUDITORIAS {
        bigint id PK
        bigint usuario_id FK
        string accion
        string modulo
        string direccion_ip
        text detalles
    }

    CONFIGURACIONES {
        bigint id PK
        string clave UK
        text valor
        string descripcion
    }
```

### 1.2 Código DBML (para importar en [dbdiagram.io](https://dbdiagram.io))

```dbml
Table personas {
  id bigint [pk, increment]
  ci varchar [unique]
  nombres varchar
  primer_apellido varchar
  segundo_apellido varchar
  telefono varchar
  direccion varchar
  genero varchar
  created_at timestamp
  updated_at timestamp
}

Table usuarios {
  id bigint [pk, increment]
  persona_id bigint [unique, ref: - personas.id]
  username varchar [unique]
  password varchar
  rol varchar
  estado boolean
  created_at timestamp
  updated_at timestamp
}

Table clientes {
  id bigint [pk, increment]
  persona_id bigint [unique, ref: - personas.id]
  codigo_cliente varchar [unique]
  activo boolean
  created_at timestamp
  updated_at timestamp
}

Table proveedores {
  id bigint [pk, increment]
  razon_social varchar
  nit_rut varchar [unique]
  contacto_nombre varchar
  telefono varchar
  direccion varchar
  activo boolean
  created_at timestamp
  updated_at timestamp
}

Table lineas {
  id bigint [pk, increment]
  nombre varchar [unique]
  descripcion varchar
  activa boolean
  created_at timestamp
  updated_at timestamp
}

Table categorias {
  id bigint [pk, increment]
  nombre varchar [unique]
  descripcion varchar
  activa boolean
  created_at timestamp
  updated_at timestamp
}

Table productos {
  id bigint [pk, increment]
  codigo varchar [unique]
  nombre varchar
  descripcion text
  precio_costo decimal
  precio_venta decimal
  stock_actual int
  stock_minimo int
  proveedor_id bigint [ref: > proveedores.id]
  linea_id bigint [null, ref: > lineas.id]
  categoria_id bigint [ref: > categorias.id]
  activo boolean
  created_at timestamp
  updated_at timestamp
}

Table compras {
  id bigint [pk, increment]
  numero_factura_nota varchar
  fecha_compra date
  total decimal
  estado varchar
  proveedor_id bigint [ref: > proveedores.id]
  registrado_por_usuario_id bigint [null, ref: > usuarios.id]
  created_at timestamp
  updated_at timestamp
}

Table detalle_compras {
  id bigint [pk, increment]
  compra_id bigint [ref: > compras.id]
  producto_id bigint [ref: > productos.id]
  cantidad int
  precio_unitario decimal
  subtotal decimal
}

Table pedidos {
  id bigint [pk, increment]
  codigo varchar [unique]
  cliente_id bigint [ref: > clientes.id]
  registrado_por_usuario_id bigint [null, ref: > usuarios.id]
  fecha_pedido date
  fecha_entrega timestamp
  entregado_por_usuario_id bigint [null, ref: > usuarios.id]
  estado varchar
  total decimal
  direccion_entrega varchar
  costo_delivery decimal
  created_at timestamp
  updated_at timestamp
}

Table detalle_pedidos {
  id bigint [pk, increment]
  pedido_id bigint [ref: > pedidos.id]
  producto_id bigint [ref: > productos.id]
  cantidad int
  precio_unitario decimal
  subtotal decimal
}

Table ventas {
  id bigint [pk, increment]
  numero_comprobante varchar [unique]
  cliente_id bigint [ref: > clientes.id]
  pedido_id bigint [null, ref: - pedidos.id]
  registrado_por_usuario_id bigint [null, ref: > usuarios.id]
  fecha_venta datetime
  subtotal decimal
  descuento decimal
  total decimal
  tipo_pago varchar
  metodo_pago varchar
  numero_cuotas int
  estado varchar
  created_at timestamp
  updated_at timestamp
}

Table detalle_ventas {
  id bigint [pk, increment]
  venta_id bigint [ref: > ventas.id]
  producto_id bigint [ref: > productos.id]
  cantidad int
  precio_unitario decimal
  subtotal decimal
}

Table creditos {
  id bigint [pk, increment]
  venta_id bigint [unique, ref: - ventas.id]
  monto_credito decimal
  saldo_pendiente decimal
  numero_cuotas int
  tasa_interes decimal
  estado varchar
  created_at timestamp
  updated_at timestamp
}

Table cuotas {
  id bigint [pk, increment]
  credito_id bigint [ref: > creditos.id]
  numero_cuota int
  fecha_vencimiento date
  monto_cuota decimal
  saldo_cuota decimal
  estado varchar
  created_at timestamp
  updated_at timestamp
}

Table pagos {
  id bigint [pk, increment]
  venta_id bigint [ref: > ventas.id]
  registrado_por_usuario_id bigint [null, ref: > usuarios.id]
  fecha_pago datetime
  monto decimal
  metodo_pago varchar
  referencia varchar
  created_at timestamp
  updated_at timestamp
}

Table aplicaciones_pago {
  id bigint [pk, increment]
  pago_id bigint [ref: > pagos.id]
  cuota_id bigint [ref: > cuotas.id]
  monto_aplicado decimal
  created_at timestamp
  updated_at timestamp
}

Table movimientos_inventario {
  id bigint [pk, increment]
  producto_id bigint [ref: > productos.id]
  registrado_por_usuario_id bigint [null, ref: > usuarios.id]
  tipo_movimiento varchar
  cantidad int
  stock_anterior int
  stock_nuevo int
  motivo varchar
  detalle_compra_id bigint [null, ref: > detalle_compras.id]
  detalle_venta_id bigint [null, ref: > detalle_ventas.id]
  created_at timestamp
  updated_at timestamp
}

Table auditorias {
  id bigint [pk, increment]
  usuario_id bigint [null, ref: > usuarios.id]
  accion varchar
  modulo varchar
  direccion_ip varchar
  detalles text
  created_at timestamp
}

Table configuraciones {
  id bigint [pk, increment]
  clave varchar [unique]
  valor text
  descripcion varchar
  created_at timestamp
  updated_at timestamp
}
```

---

## 2. Diagrama de Módulos y Procesos ERP (Flujo Operativo)

> **Archivos de imagen listos para descargar o insertar en documentos:**
> - Imagen PNG de alta resolución: [`docs/diagrama_procesos_erp_finora.png`](file:///d:/Proyectos/finora/docs/diagrama_procesos_erp_finora.png)
> - Gráfico vectorial SVG: [`docs/diagrama_procesos_erp_finora.svg`](file:///d:/Proyectos/finora/docs/diagrama_procesos_erp_finora.svg)

![Diagrama Procesos ERP Finora](diagrama_procesos_erp_finora.png)

### 2.1 Código Fuente Mermaid

```mermaid
flowchart TB
    subgraph MOD_ABASTECIMIENTO["1. Módulo de Abastecimiento & Compras"]
        PROV["Proveedores"]
        COMP["Registro de Compra"]
        DET_COMP["Detalle de Compra"]
        PROV -->|"Emite factura/nota"| COMP
        COMP -->|"Genera ítems"| DET_COMP
    end

    subgraph MOD_INVENTARIO["2. Módulo de Catálogo & Inventario"]
        CATALOGO["Catálogo de Productos\n(Líneas / Categorías)"]
        STOCK["Stock en Almacén\n(Control de Mínimos)"]
        KARDEX["Movimientos de Inventario\n(Kardex: Entradas / Salidas)"]
        
        CATALOGO --> STOCK
        STOCK <--> KARDEX
    end

    subgraph MOD_VENTAS["3. Módulo Comercial & Ventas"]
        CLIENT["Cartera de Clientes"]
        PED["Gestión de Pedidos"]
        DELIVERY["Despacho & Delivery\n(Asignación y Ruta)"]
        VENTA["Facturación / Venta Directa"]
        DET_VENTA["Detalle de Venta"]

        CLIENT -->|"Solicita"| PED
        PED -->|"Envío a domicilio"| DELIVERY
        PED -->|"Liquidación"| VENTA
        CLIENT -->|"Compra en mostrador"| VENTA
        VENTA -->|"Genera ítems"| DET_VENTA
    end

    subgraph MOD_FINANZAS["4. Módulo Financiero & Cobranzas"]
        TIPO_PAGO{"¿Tipo de Pago?"}
        CONTADO["Pago de Contado"]
        CRED["Apertura de Crédito"]
        CUOTAS_PLAN["Cronograma de Cuotas"]
        CAJA["Recepción de Pagos / Caja"]
        AMORT["Amortización de Cuotas\n(Aplicación de Pago)"]

        VENTA --> TIPO_PAGO
        TIPO_PAGO -->|"Contado"| CONTADO --> CAJA
        TIPO_PAGO -->|"Crédito"| CRED --> CUOTAS_PLAN
        CAJA --> AMORT
        CUOTAS_PLAN -->|"Se salda con"| AMORT
    end

    subgraph MOD_GESTION["5. Módulo de Seguridad & Auditoría"]
        USR["Usuarios y Roles"]
        AUDIT["Bitácora de Auditoría"]
        CONF["Configuración Global"]
    end

    %% Conexiones intermodulares (Flujo ERP)
    DET_COMP -->|"(+) Entrada de Stock"| KARDEX
    DET_VENTA -->|"(-) Salida / Reserva de Stock"| KARDEX
    CATALOGO -.->|"Lista de Precios"| DET_COMP
    CATALOGO -.->|"Lista de Precios"| DET_VENTA
    USR -.->|"Registra / Supervisa"| COMP
    USR -.->|"Registra / Entrega"| PED
    USR -.->|"Cobra / Autoriza"| VENTA
    USR -.->|"Registra cobro"| CAJA
    USR -.->|"Trazabilidad"| AUDIT
```

### Resumen de los Flujos de Negocio del ERP

1. **Flujo de Abastecimiento:**
   Se registran compras asociadas a proveedores. Cada ítem de compra impacta directamente en el módulo de inventario registrando una **entrada física** en el Kardex y actualizando el stock actual y costo promedio.
2. **Flujo de Pedido a Facturación (Order-to-Cash):**
   Un cliente puede realizar un pedido (con opción de delivery y seguimiento de entrega por repartidor/usuario) o compra directa. Al concretarse la venta, se genera la salida del stock en el inventario.
3. **Flujo Financiero (Cuentas por Cobrar):**
   Las ventas pueden ser al contado o al crédito. Si es al crédito, se genera una tabla de amortización con cuotas y fechas de vencimiento. Los pagos posteriores recibidos en caja se distribuyen selectivamente a las cuotas mediante aplicaciones de pago hasta liquidar el saldo.
4. **Flujo Transversal de Control:**
   Todas las operaciones registran el usuario responsable y los eventos críticos alimentan el módulo de auditoría del sistema.

