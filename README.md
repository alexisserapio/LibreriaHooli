# 📚 Librería Hooli

#### WebApp para administrar una tienda de libros en línea. Conecta una Base de Datos a un Front-end.
## Introducción
Esta Webapp desarrollada en lenguaje PHP funciona para administrar una tienda de libros virtual en donde tu puedes: 
- Visualizar en un dashboard el despliegue de todos los libros que se tienen en la librería.
- Realizar acciones CRUD dentro de la DB asociada a la librería a través de la Webapp.
- Realizar un pedido para el stock de las tiendas físicas.
- Desplegar información de asociada a los pedidos que se realizaron a través de la tienda.

## 💻 Tech Stack
- Se desarrolló en el sistema operativo **Oracle Linux** para tener una mejor gestión en las conexiones de los clientes.
- Como base de datos, se utilizó **Oracle Database**, implicando el uso de commits y la posibilidad de realizar rollbacks. 
- Se utilizó **PHP** para conectar la base de datos con el sitio.
- Se hizo uso de **HTML y CSS** para crear los estilos de la webapp.
- En el proceso de desarrollo se implemento control de versionamiento utilizando **Git** y manejo de repositorios en **Github**.

## 📲 In-app
- Al entrar al sitio, se nos muestra la página principal, donde observamos un dashboard desplegando los libros en inventario.
<img src="https://github.com/alexisserapio/LibreriaHooli/blob/main/images/1.png" alt="Captura de Pantalla sobre la app en el m贸vil y su interfaz principal" width="600" height="280">

---
- Las secciones 'Insertar Libro', 'Eliminar Libro' y 'Actualizar Stock' son muy parecidas al ser secciones CRUD.
<img src="https://github.com/alexisserapio/LibreriaHooli/blob/main/images/2.png" alt="Captura de Pantalla sobre el apartado de escaneo QR" width="600" height="280">
<img src="https://github.com/alexisserapio/LibreriaHooli/blob/main/images/3.png" alt="Captura de Pantalla sobre el apartado de escaneo QR" width="600" height="280">
<img src="https://github.com/alexisserapio/LibreriaHooli/blob/main/images/4.png" alt="Captura de Pantalla sobre el apartado de escaneo QR" width="600" height="280">

---
- Dentro de la sección "Stock por Sucursal" podemos consultar la cantidad de libros que existen en cada sucursal de acuerdo al id del libro, en esta sección se nos despliega un BLOB para observar la portada del libro.
<img src="https://github.com/alexisserapio/LibreriaHooli/blob/main/images/5.png" alt="Captura de Pantalla sobre el apartado de escaneo QR" width="600" height="280">

---
- La sección de pedidos es una que idealmente pueden acceder tanto los administradores de la librería como los clientes de dicha tienda, en ella se puede realizar un pedido de acuerdo al libro solicitado.
- Al momento de buscar dicho pedido se nos despliega una tabla con la información del libro solicitado, desplegando los BLOBs tanto de la portada de dicho libro, así como un video con una reseña para que el usuario pueda consultarlo.
<img src="https://github.com/alexisserapio/LibreriaHooli/blob/main/images/6.png" alt="Captura de Pantalla sobre el apartado de escaneo QR" width="600" height="280">
<img src="https://github.com/alexisserapio/LibreriaHooli/blob/main/images/7.png" alt="Captura de Pantalla sobre el apartado de escaneo QR" width="600" height="280">

## 📣 To-do
- Darle una mejor identidad a la marca, esto para poder crear una interfaz más intuitiva.
- Se puede hacer uso de triggers SQL para un mejor control sobre los accesos a la base de datos para atrapar errores o valores incorrectos.
- Mejorar la seguridad e implementar logs para rastrear como se hace uso de la aplicación, ya que accede directamente la base de datos.
