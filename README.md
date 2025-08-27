
# 🚀 Guía de Instalación - Aplicación Web Local

¡Bienvenido/a! Esta guía te ayudará a configurar y ejecutar nuestra aplicación web en tu máquina local paso a paso.

## 📋 Prerrequisitos

Antes de comenzar, asegúrate de tener instalado:

1. **XAMPP** (Windows/Mac/Linux)
   - 🔗 [Descargar XAMPP](https://www.apachefriends.org/)  

2. **Editor de código** (opcional pero recomendado):
   - 🔗 [Visual Studio Code](https://code.visualstudio.com/)
   
## 📦 Paso 1: Descargar los Archivos del Proyecto

1. 📥 Descarga el archivo ZIP del proyecto
2. 📂 Extrae el contenido en una carpeta de tu preferencia
3. 🗂️ Recomendamos colocar la carpeta del proyecto en:
   - **XAMPP**: C:\xampp\htdocs\ 

## 🖥️ Paso 2: Configurar el Servidor Local

### Para XAMPP:
1. 🖱️ Abre el panel de control de XAMPP
2. ▶️ Inicia los módulos "Apache" y "MySQL"
3. ✅ Verifica que ambos muestren un fondo verde indicando que están activos

## 🗃️ Paso 3: Configurar la Base de Datos

1. 🌐 Abre tu navegador y ve a http://localhost/phpmyadmin 
2. ➕ Haz clic en "Nueva" para crear una base de datos
3. 🏷️ Nombra la base de datos: `proyecto_transportes`
4. ✅ Selecciona la codificación `utf8_general_ci`
5. 🖱️ Haz clic en "Crear"

## 📤 Paso 4: Importar la Base de Datos

1. 📂 En phpMyAdmin, selecciona la base de datos recién creada
2. 📋 Ve a la pestaña "Importar"
3. 📤 Haz clic en "Seleccionar archivo" y elige el archivo SQL incluido en el proyecto:
   - 📁 Ubicación: database/transportes.sql (o el archivo SQL proporcionado)
4. ⚙️ Deja las opciones por defecto
5. 🚀 Haz clic en "Continuar" o "Importar"

## ⚙️ Paso 5: Configurar la Conexión a la Base de Datos

1. 📁 Abre el archivo de configuración en tu editor de código:
   - 📄 config/database.php

2. 🔄 Modifica los siguientes valores con tus credenciales locales:
	$host = 'localhost';       // Dirección del servidor de base de datos
    $db_name = 'proyecto_transportes';  // Nombre de la base de datos
    $username = '';        // Nombre de usuario de la base de datos
    $password = '';            // Contraseña de la base de datos


## 🌐 Paso 6: Ejecutar la Aplicación
	
   🌍 Abre tu navegador web	

   🔍 Ve a la dirección:

   http://localhost/AppCamiones/index.php


🎉 ¡La aplicación debería cargar correctamente!

