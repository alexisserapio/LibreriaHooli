CREATE TABLESPACE tbs_volatil
DATAFILE 'volatile_datafile.dbf' SIZE 50M AUTOEXTEND ON;

CREATE TABLESPACE tbs_noVolatil
DATAFILE 'nonvolatile_datafile.dbf' SIZE 50M AUTOEXTEND ON;

--Tabla Cliente

CREATE TABLE Cliente(
    id_cliente INT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Telefono VARCHAR(20),
    Correo VARCHAR(100)
)
TABLESPACE tbs_noVolatil
STORAGE (
INITIAL 512K -- Tamaño del primer extent
NEXT 256K -- Tamaño de cada extent adicional
PCTINCREASE 0 -- Sin aumento del tamaño de extent
MINEXTENTS 1 -- Número mínimo de extents
MAXEXTENTS UNLIMITED -- Número máximo de extents (sin límite)
);

--Tabla Editorial

CREATE TABLE Editorial(
    id_editorial    INT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Pais VARCHAR(50)
)
TABLESPACE tbs_noVolatil
STORAGE (
INITIAL 512K -- Tamaño del primer extent
NEXT 256K -- Tamaño de cada extent adicional
PCTINCREASE 0 -- Sin aumento del tamaño de extent
MINEXTENTS 1 -- Número mínimo de extents
MAXEXTENTS UNLIMITED -- Número máximo de extents (sin límite)
);

--Tabla Empleados

CREATE TABLE Empleado(
    id_empleado INT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    id_sucursal INT NOT NULL,
    Puesto VARCHAR(50) NOT NULL
)
TABLESPACE tbs_noVolatil
STORAGE (
INITIAL 512K -- Tamaño del primer extent
NEXT 256K -- Tamaño de cada extent adicional
PCTINCREASE 0 -- Sin aumento del tamaño de extent
MINEXTENTS 1 -- Número mínimo de extents
MAXEXTENTS UNLIMITED -- Número máximo de extents (sin límite)
);

-- Tabla Sucursal

CREATE TABLE Sucursal(
	id_sucursal INT PRIMARY KEY,
	Nombre VARCHAR (100) NOT NULL,
	Direccion VARCHAR(200)
)
TABLESPACE tbs_noVolatil
STORAGE (
INITIAL 512K -- Tamaño del primer extent
NEXT 256K -- Tamaño de cada extent adicional
PCTINCREASE 0 -- Sin aumento del tamaño de extent
MINEXTENTS 1 -- Número mínimo de extents
MAXEXTENTS UNLIMITED -- Número máximo de extents (sin límite)
);

-- Tabla Pedido

CREATE TABLE Pedido(
    id_pedido INT PRIMARY KEY,
    id_cliente INT NOT NULL,
    Factura_pdf BLOB,
    Total NUMBER(10,2),
    Fecha_Pedido DATE
)
TABLESPACE tbs_volatil
STORAGE (
INITIAL 512K -- Tamaño del primer extent
NEXT 256K -- Tamaño de cada extent adicional
PCTINCREASE 0 -- Sin aumento del tamaño de extent
MINEXTENTS 1 -- Número mínimo de extents
MAXEXTENTS UNLIMITED -- Número máximo de extents (sin límite)
);

-- Tabla Libro

CREATE TABLE Libro(
    id_libro INT PRIMARY KEY,
    id_editorial INT NOT NULL,
    Portada BLOB,
    Precio NUMBER(10,2),
    Stock INT,
    Genero VARCHAR(50),
    Anio_Publicacion INT,
    Autor VARCHAR(100),
    Titulo VARCHAR(100)
)
TABLESPACE tbs_noVolatil
STORAGE (
INITIAL 512K -- Tamaño del primer extent
NEXT 256K -- Tamaño de cada extent adicional
PCTINCREASE 0 -- Sin aumento del tamaño de extent
MINEXTENTS 1 -- Número mínimo de extents
MAXEXTENTS UNLIMITED -- Número máximo de extents (sin límite)
);

-- Tabla Detalle_Pedido

CREATE TABLE Detalle_Pedido(
    id_detalle INT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_libro INT NOT NULL,
    Cantidad INT NOT NULL,
    precio_unitario NUMBER(10,2)
)
TABLESPACE tbs_volatil
STORAGE (
INITIAL 512K -- Tamaño del primer extent
NEXT 256K -- Tamaño de cada extent adicional
PCTINCREASE 0 -- Sin aumento del tamaño de extent
MINEXTENTS 1 -- Número mínimo de extents
MAXEXTENTS UNLIMITED -- Número máximo de extents (sin límite)
);

-- Tabla Inventario

CREATE TABLE Inventario(
    id_inventario INT PRIMARY KEY,
    id_sucursal INT NOT NULL,
    id_libro INT NOT NULL,
    Cantidad_Disponible INT NOT NULL
)
TABLESPACE tbs_volatil
STORAGE (
INITIAL 512K -- Tamaño del primer extent
NEXT 256K -- Tamaño de cada extent adicional
PCTINCREASE 0 -- Sin aumento del tamaño de extent
MINEXTENTS 1 -- Número mínimo de extents
MAXEXTENTS UNLIMITED -- Número máximo de extents (sin límite)
);

-----------------------------------------
-- Añadir las relaciones entre tablas
-----------------------------------------

ALTER TABLE Empleado ADD CONSTRAINT fk_sucursal
FOREIGN KEY (id_sucursal) REFERENCES Sucursal(id_sucursal);

ALTER TABLE Pedido ADD CONSTRAINT fk_cliente
FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente);

ALTER TABLE Libro ADD CONSTRAINT fk_editorial
FOREIGN KEY (id_editorial) REFERENCES Editorial(id_editorial);

ALTER TABLE Detalle_Pedido ADD CONSTRAINT fk_pedido
FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido);

ALTER TABLE Detalle_Pedido ADD CONSTRAINT fk_libro
FOREIGN KEY (id_libro) REFERENCES Libro(id_libro);

ALTER TABLE Inventario ADD CONSTRAINT fk_sucursal_inv
FOREIGN KEY (id_sucursal) REFERENCES Sucursal(id_sucursal);

ALTER TABLE Inventario ADD CONSTRAINT fk_libro_inv
FOREIGN KEY (id_libro) REFERENCES Libro(id_libro);
