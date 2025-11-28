# Sistema de Inventarios y Pagos al Crédito - Santo Pecatto

## 📋 Descripción del Proyecto

Sistema web para la gestión integral del restaurante Santo Pecatto, que incluye control de inventarios, compras, ventas, créditos y pagos. El sistema está siendo desarrollado de forma incremental, manteniendo la infraestructura existente del proyecto de biblioteca.

---

## 🗄️ Estructura de la Base de Datos

### Diagrama Entidad-Relación

El sistema está diseñado con las siguientes entidades principales y sus relaciones:

#### **1. Gestión de Usuarios y Roles**

**Rol**
- `id`: Identificador único
- `nombre`: Nombre del rol (administrador, almacenero, mesero, cliente)

**Usuario**
- `id`: Identificador único
- `nombre`: Nombre del usuario
- `correo`: Email del usuario
- `contraseña`: Contraseña encriptada
- `foto`: Foto de perfil
- **Relación**: Un usuario tiene uno o más roles (1:*)

#### **2. Gestión de Inventario**

**Marca**
- `id`: Identificador único
- `nombre`: Nombre de la marca

**Insumo**
- `id`: Identificador único
- `nombre`: Nombre del insumo
- `stock`: Cantidad actual en inventario
- `unidad_medida`: Unidad de medida (kg, litros, unidades, etc.)
- **Relación**: Pertenece a una Marca (1:*)

**Movimiento**
- `id`: Identificador único
- `tipo`: Tipo de movimiento (Ingreso, Salida, Ajuste)
- `cantidad`: Cantidad del movimiento
- `motivo`: Razón del movimiento
- `fecha_hora`: Timestamp del movimiento
- **Relaciones**: 
  - Relacionado con un Insumo (0..1:1)
  - Puede generar un Detalle_compra (0..*:1)

#### **3. Gestión de Compras**

**Proveedor**
- `id`: Identificador único
- `nombre`: Nombre del proveedor
- `detalles`: Información adicional
- `correo`: Email de contacto
- `celular`: Teléfono de contacto

**Compra**
- `id`: Identificador único
- `total`: Monto total de la compra
- `fecha_hora`: Timestamp de la compra
- **Relación**: Relacionada con un Usuario (0..*:1)

**Detalle_compra**
- `costo_unitario`: Precio por unidad
- `cantidad`: Cantidad comprada
- **Relaciones**:
  - Pertenece a una Compra (0..*:1)
  - Relacionado con un Insumo (relación con línea punteada)

#### **4. Gestión de Productos y Ventas**

**Producto**
- `id`: Identificador único
- `nombre`: Nombre del producto
- `costo`: Costo de producción
- `precio_venta`: Precio de venta al público
- `stock`: Cantidad disponible

**Mesa**
- `id`: Identificador único
- `codigo`: Código de la mesa
- `capacidad`: Número de personas

**Venta**
- `id`: Identificador único
- `total`: Monto total de la venta
- `fecha_hora`: Timestamp de la venta
- `estado`: Estado de la venta (PENDIENTE, ENTREGADO)
- `tipo_pago`: Tipo de pago (Inmediato, Crédito)
- **Relaciones**:
  - Relacionada con un Usuario (0..*:1)
  - Relacionada con una Mesa (0..*:1)
  - Puede generar un Crédito (0..1:0..1)

**Detalle_venta**
- `cantidad`: Cantidad vendida del producto
- **Relación**: Relacionado con Producto (relación con línea punteada)

#### **5. Gestión de Créditos y Pagos**

**Plan**
- `id`: Identificador único
- `nombre`: Nombre del plan
- `tasa_interes_diario`: Tasa de interés diaria aplicable
- `plazo_dias`: Plazo en días para el pago

**Credito**
- `id`: Identificador único
- `nro`: Número de crédito
- `fecha`: Fecha de creación
- `saldo_inicial`: Monto inicial del crédito
- `interes`: Intereses acumulados
- `capital`: Capital pendiente
- `cuota`: Monto de la cuota
- `saldo_final`: Saldo pendiente
- **Relaciones**:
  - Relacionado con una Venta (0..1:0..1)
  - Relacionado con un Plan (0..1:1)
  - Tiene múltiples Cuotas (1..*:1)

**Cuota**
- `id`: Identificador único
- `nro`: Número de cuota
- `fecha`: Fecha de vencimiento
- `saldo_inicial`: Saldo al inicio
- `interes`: Interés de la cuota
- `capital`: Capital amortizado
- `cuota`: Monto de la cuota
- `saldo_final`: Saldo después del pago
- **Relación**: Pertenece a un Crédito (1..*:1)

**Pago**
- `id`: Identificador único
- `monto`: Monto del pago
- `fecha`: Fecha del pago
- **Relación**: Relacionado con un Crédito (0..1:1)

### Reglas de Negocio Importantes

> [!IMPORTANT]
> **Detalle de Compra**: Genera una fila en la tabla Movimiento cuando se registra una compra

> [!IMPORTANT]
> **Producto**: El stock se actualiza manualmente (no automáticamente)

> [!WARNING]
> **Métodos de Pago**: El sistema NO contempla métodos de pago específicos de momento (efectivo, tarjeta, etc.)

> [!NOTE]
> **Plan de Crédito**: La tasa de interés diario se aplica al saldo pendiente del capital del crédito (total de la venta)

---

## ✅ Avance Actual del Proyecto

### **CU1: Gestión de Usuarios - COMPLETADO ✅**

#### Funcionalidades Implementadas

##### 1. **Sistema de Roles y Permisos**
- ✅ 4 roles del restaurante creados:
  - **Administrador**: Acceso completo al sistema
  - **Almacenero**: Gestión de inventario y compras
  - **Mesero**: Gestión de órdenes
  - **Cliente**: Compras y gestión de créditos
- ✅ 70+ permisos específicos definidos
- ✅ Asignación automática de permisos por rol

##### 2. **CRUD de Usuarios**
- ✅ Listar usuarios con paginación
- ✅ Búsqueda por nombre o email
- ✅ Filtro por rol
- ✅ Crear nuevos usuarios
- ✅ Editar usuarios existentes
- ✅ Archivar usuarios (eliminación lógica)
- ✅ Validaciones en español

##### 3. **Sistema de Temas**
- ✅ 3 temas implementados:
  - **Claro** (☀️): Tema profesional claro
  - **Oscuro** (🌙): Tema oscuro para reducir fatiga visual
  - **Niños** (🎨): Tema colorido con fuente aumentada (18px)
- ✅ Variables CSS para fácil personalización
- ✅ Selector de tema en sidebar
- ✅ Persistencia del tema seleccionado

##### 4. **Contador de Visitas**
- ✅ Tabla `page_visits` en base de datos
- ✅ Modelo `PageVisit` con métodos helper
- ✅ Contador automático en todas las vistas del restaurante
- ✅ Visualización en pie de página

##### 5. **Middleware y Seguridad**
- ✅ Middleware `CheckRestaurantRole` para protección por roles
- ✅ Rutas protegidas con autenticación y verificación de roles
- ✅ Prevención de auto-eliminación de usuarios

##### 6. **Interfaz de Usuario**
- ✅ Vistas Vue.js + Inertia:
  - `Index.vue`: Lista de usuarios
  - `Create.vue`: Formulario de creación
  - `Edit.vue`: Formulario de edición
- ✅ Componente `PageFooter` reutilizable
- ✅ Integración con sistema de temas
- ✅ Diseño responsive

#### Archivos Creados

**Base de Datos:**
- `database/migrations/2025_11_24_012353_create_page_visits_table.php`
- `database/seeders/RestaurantRolesSeeder.php`

**Modelos:**
- `app/Models/PageVisit.php`

**Middleware:**
- `app/Http/Middleware/CheckRestaurantRole.php`

**Controladores:**
- `app/Http/Controllers/Admin/RestaurantUserController.php`

**Vistas:**
- `resources/js/Pages/Restaurant/Users/Index.vue`
- `resources/js/Pages/Restaurant/Users/Create.vue`
- `resources/js/Pages/Restaurant/Users/Edit.vue`
- `resources/js/Components/PageFooter.vue`

**Estilos:**
- `resources/css/theme.css`

**Configuración:**
- `bootstrap/app.php` (middleware registrado)
- `routes/web.php` (rutas agregadas)
- `resources/js/Layouts/AppLayout.vue` (enlace en menú)

---

## 🚧 Pendiente de Implementación

### **CU2: Gestión de Productos**
- [ ] CRUD de productos (nombre, costo, precio_venta, stock)
- [ ] Control de stock manual
- [ ] Búsqueda y filtros de productos
- [ ] Validaciones de disponibilidad

### **CU3: Gestión de Marcas e Insumos**
- [ ] CRUD de marcas (id, nombre)
- [ ] CRUD de insumos (nombre, stock, unidad_medida)
- [ ] Relación marca-insumo
- [ ] Búsqueda y filtros

### **CU4: Gestión de Movimientos de Inventario**
- [ ] Registro de movimientos (Ingreso, Salida, Ajuste)
- [ ] Campos: tipo, cantidad, motivo, fecha_hora
- [ ] Relación con insumos
- [ ] Historial de movimientos por insumo
- [ ] Actualización automática de stock de insumos
- [ ] Validación de stock antes de salidas

### **CU5: Gestión de Proveedores y Compras**
- [ ] CRUD de proveedores (nombre, detalles, correo, celular)
- [ ] Registro de compras (total, fecha_hora)
- [ ] Detalle de compras (costo_unitario, cantidad)
- [ ] Relación compra-usuario
- [ ] Generación automática de movimiento de ingreso al registrar compra
- [ ] Actualización automática de stock de insumos

### **CU6: Gestión de Mesas y Ventas**
- [ ] CRUD de mesas (codigo, capacidad)
- [ ] Registro de ventas (total, fecha_hora, estado, tipo_pago)
- [ ] Detalle de venta por producto (cantidad)
- [ ] Estados de venta: PENDIENTE, ENTREGADO
- [ ] Tipos de pago: Inmediato, Crédito
- [ ] Selección de mesa para la venta
- [ ] Vista para meseros (actualizar estado de órdenes)
- [ ] Vista para clientes (realizar compras)
- [ ] Validación de stock de productos antes de vender

### **CU7: Gestión de Planes, Créditos y Pagos**
- [ ] CRUD de planes de crédito (nombre, tasa_interes_diario, plazo_dias)
- [ ] Creación automática de crédito al vender a crédito
- [ ] Campos de crédito: nro, fecha, saldo_inicial, interes, capital, cuota, saldo_final
- [ ] Generación automática de cuotas según el plan
- [ ] Campos de cuota: nro, fecha, saldo_inicial, interes, capital, cuota, saldo_final
- [ ] Cálculo de intereses diarios sobre saldo pendiente
- [ ] Registro de pagos (monto, fecha)
- [ ] Amortización de capital e intereses al registrar pago
- [ ] Actualización de saldo del crédito
- [ ] Historial de pagos del cliente
- [ ] Vista de créditos pendientes por cliente

### **CU8: Reportes y Estadísticas**
- [ ] Dashboard con métricas clave
- [ ] Reporte de ventas por período
- [ ] Reporte de compras por período
- [ ] Insumos con stock bajo
- [ ] Productos más vendidos
- [ ] Estadísticas de créditos (total prestado, total recuperado, mora)
- [ ] Gráficas y visualizaciones

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **Framework**: Laravel 11.x
- **Base de Datos**: PostgreSQL
- **Autenticación**: Laravel Jetstream
- **Permisos**: Spatie Laravel Permission

### Frontend
- **Framework**: Vue.js 3
- **Routing**: Inertia.js
- **Estilos**: CSS Variables + Tailwind CSS
- **Build**: Vite

### Características Especiales
- Sistema de temas dinámico
- Contador de visitas por página
- Validaciones en español
- Eliminación lógica (soft deletes)
- Diseño responsive

---

## 📦 Instalación y Configuración

### Requisitos Previos
- PHP 8.2 o superior
- Composer
- Node.js 18 o superior
- PostgreSQL 14 o superior

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone <url-del-repositorio>
cd ProyectoBibliotecaTecnoWeb
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
```

4. **Configurar variables de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con tus credenciales de base de datos:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

5. **Ejecutar migraciones**
```bash
php artisan migrate
```

6. **Ejecutar seeders**
```bash
# Seeder de roles del restaurante
php artisan db:seed --class=RestaurantRolesSeeder

# Seeder general (usuarios de prueba)
php artisan db:seed
```

7. **Asignar rol de administrador**
```bash
php assign_role.php
```

8. **Compilar assets**
```bash
npm run dev
```

9. **Iniciar servidor de desarrollo**
```bash
php artisan serve
```

---

## 👤 Usuarios de Prueba

### Usuario Administrador del Restaurante
- **Email**: `daniel@gmail.com`
- **Contraseña**: `12345678`
- **Roles**: `admin` (biblioteca) + `administrador` (restaurante)

### Otros Usuarios Disponibles
- **Email**: `administrador@gmail.com` - Contraseña: `12345678` - Rol: `administrativo`
- **Email**: `docente@gmail.com` - Contraseña: `12345678` - Rol: `docente`
- **Email**: `micaelcardona@gmail.com` - Contraseña: `12345678` - Rol: `estudiante`

---

## 🗺️ Rutas del Sistema de Restaurante

### Gestión de Usuarios (Solo Administrador)
- `GET /restaurant/users` - Lista de usuarios
- `GET /restaurant/users/create` - Formulario de creación
- `POST /restaurant/users` - Guardar nuevo usuario
- `GET /restaurant/users/{id}/edit` - Formulario de edición
- `PUT /restaurant/users/{id}` - Actualizar usuario
- `DELETE /restaurant/users/{id}` - Archivar usuario

---

## 📊 Estructura de Roles y Permisos

### Administrador
- ✅ Acceso completo a todos los módulos
- ✅ Gestión de usuarios
- Dashboard, Marcas, Insumos, Movimientos, Proveedores, Compras, Productos, Planes de Crédito, Ventas, Mesas

### Almacenero
- ✅ Dashboard
- ✅ Marcas, Insumos, Movimientos, Proveedores, Compras

### Mesero
- ✅ Ver órdenes
- ✅ Actualizar estado de órdenes (PENDIENTE → ENTREGADO)

### Cliente
- ✅ Ver productos
- ✅ Crear ventas (inmediato o crédito)
- ✅ Ver créditos y plan de pagos
- ✅ Realizar y ver pagos

---

## 🎨 Sistema de Temas

### Temas Disponibles

#### Tema Claro (☀️)
- Fondo: Blanco/Gris claro
- Texto: Negro/Gris oscuro
- Tamaño de fuente: 16px

#### Tema Oscuro (🌙)
- Fondo: Negro/Gris oscuro
- Texto: Blanco/Gris claro
- Tamaño de fuente: 16px

#### Tema Niños (🎨)
- Fondo: Amarillo/Naranja claro
- Texto: Marrón oscuro
- Tamaño de fuente: 18px (aumentado)

### Cambiar Tema
1. Hacer clic en el selector de tema en el sidebar
2. Seleccionar el tema deseado
3. El tema se guarda automáticamente en localStorage

---

## 📈 Próximos Pasos

### Fase 2: Gestión de Inventario (CU2-CU5)
1. Implementar CRUD de Marcas
2. Implementar CRUD de Productos
3. Implementar gestión de Insumos
4. Implementar Movimientos de inventario
5. Implementar gestión de Proveedores y Compras

### Fase 3: Gestión de Ventas y Pagos (CU6-CU7)
1. Implementar registro de Ventas
2. Implementar gestión de Mesas
3. Implementar sistema de Créditos
4. Implementar cálculo de intereses
5. Implementar gestión de Pagos

### Fase 4: Reportes y Dashboard (CU8)
1. Crear dashboard con estadísticas
2. Implementar reportes de ventas
3. Implementar reportes de compras
4. Implementar alertas de stock
5. Implementar gráficas y visualizaciones

---

## 🐛 Problemas Conocidos

- Ninguno reportado hasta el momento

---

## 📝 Notas Importantes

### Enfoque Híbrido
El proyecto utiliza un **enfoque híbrido** que mantiene el sistema de biblioteca existente mientras se desarrolla el sistema de restaurante. Ambos sistemas coexisten sin interferir entre sí.

### Eliminación Lógica
Todas las eliminaciones en el sistema son **lógicas** (soft deletes), no físicas. Los registros se marcan como archivados pero permanecen en la base de datos.

### Validaciones
Todas las validaciones están configuradas para mostrar mensajes en **español**.

### Contador de Visitas
El contador de visitas se incrementa automáticamente en cada vista del sistema de restaurante usando el modelo `PageVisit`.

---

## 📞 Soporte

Para preguntas o problemas, contactar al equipo de desarrollo.

---

## 📄 Licencia

Este proyecto es privado y confidencial.

---

**Última actualización**: 24 de noviembre de 2025  
**Versión**: 0.1.0 (CU1 completado)
