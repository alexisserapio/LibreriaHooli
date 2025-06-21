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
- Al moverse a la secci贸n "Generador de vCards QR" se nos solicita conceder el permiso para acceder a los contactos.
- Colocamos en el formulario la informaci贸n que queremos que contenga la vCard.
<img src="https://github.com/alexisserapio/AppQR/blob/master/images/3.png" alt="Captura de Pantalla sobre el apartado de generar QR" width="450" height="330">

---
- Despliegue del c贸digo QR generado.
<img src="https://github.com/alexisserapio/AppQR/blob/master/images/4.png" alt="Captura de Pantalla sobre el QR generado" width="150" height="330">

## 馃摚 To-do
- Modificar las restricciones puestas dentro del lector QR para poder recibir todo tipo de c贸digos QR.
- Procurar mejorar el dise帽o de la interfaz para tener un dise帽o m谩s moderno y actualizado.
- Implementar la generaci贸n de c贸digos QR de todo tipo para que el usuario pueda utilizar estos QR en procesos personales.
