# 🍽️ Sistema de Inventarios y Pagos al Crédito - Restaurante Santo Pecatto

Sistema web completo para gestión de restaurante desarrollado con Laravel 11, Inertia.js y Vue 3. Incluye gestión de usuarios, inventario, ventas, créditos con planes de pago, y sistema de temas personalizado con accesibilidad.

---

## 📋 Tabla de Contenidos

- [Características Principales](#características-principales)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Casos de Uso Implementados](#casos-de-uso-implementados)
- [Roles y Permisos](#roles-y-permisos)
- [Base de Datos](#base-de-datos)
- [Sistema de Temas](#sistema-de-temas)
- [Buscador Global](#buscador-global)
- [Instalación](#instalación)
- [Configuración](#configuración)

---

## ✨ Características Principales

### 🎨 Sistema de Temas Dinámico
- **5 Temas Disponibles**: Light, Dark, Kids (Niños), Young (Jóvenes), Adult (Adultos)
- **Modo Automático**: Cambia entre día/noche según hora local del cliente
- **Fuentes Personalizadas**: Cada tema tiene su propia tipografía
  - Kids: Fredoka (redondeada, divertida)
  - Young: Poppins (moderna)
  - Adult: Merriweather (serif, profesional)
- **Accesibilidad**: 4 tamaños de fuente y 3 niveles de contraste
- **Persistencia**: Preferencias guardadas en localStorage

### 🔍 Buscador Global Inteligente
- **Búsqueda Universal**: Busca en navegación y entidades del sistema
- **Navegación Rápida**: Acceso directo a cualquier sección (Ctrl+K)
- **Keywords Inteligentes**: Palabras clave por acción y rol
- **Sin Acentos**: Búsqueda insensible a acentos usando PostgreSQL unaccent
- **Permisos Respetados**: Solo muestra resultados accesibles por rol

### 📊 Contador de Visitas
- **Tracking por Página**: Registra cada visita a cada página
- **Pie de Página**: Muestra contador en cada vista
- **Dashboard**: Estadísticas de páginas más visitadas

### 🖼️ Gestión de Imágenes con Cloudinary
- **Fotos de Perfil**: Para usuarios del sistema
- **Imágenes de Productos**: Catálogo visual
- **Preview en Tiempo Real**: Vista previa antes de subir

### 🔒 Eliminación Lógica (Soft Deletes)
- **No hay eliminaciones físicas**: Todos los registros se archivan
- **Opción de Desarchivar**: Recuperación de registros archivados
- **Historial Completo**: Mantiene integridad de datos

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 11**: Framework PHP
- **PostgreSQL**: Base de datos relacional
- **Spatie Laravel Permission**: Gestión de roles y permisos
- **Cloudinary**: Almacenamiento de imágenes en la nube

### Frontend
- **Vue 3**: Framework JavaScript reactivo
- **Inertia.js**: SPA sin necesidad de API
- **Tailwind CSS**: Framework CSS utility-first
- **Vite**: Build tool moderno

### Autenticación
- **Laravel Jetstream**: Autenticación y gestión de usuarios
- **Laravel Sanctum**: API tokens
- **Laravel Fortify**: Backend de autenticación

---

## 📝 Casos de Uso Implementados

### CU1: Gestión de Usuarios
**Actores**: Administrador  
**Descripción**: CRUD completo de usuarios del sistema con roles

**Funcionalidades**:
- ✅ Crear, editar, eliminar (lógico) usuarios
- ✅ Asignar roles: Administrador, Almacenero, Mesero, Cliente
- ✅ Subir foto de perfil con Cloudinary
- ✅ Soft deletes (archivar/desarchivar)
- ✅ Validaciones en español

### CU2: Gestión de Productos
**Actores**: Administrador  
**Descripción**: Administración del catálogo de productos del restaurante

**Funcionalidades**:
- ✅ CRUD de productos (platos, bebidas, etc.)
- ✅ Gestión de stock
- ✅ Precios de costo y venta
- ✅ Imágenes de productos
- ✅ Soft deletes

### CU3: Gestión de Marcas
**Actores**: Administrador, Almacenero  
**Descripción**: Administración de marcas de insumos

**Funcionalidades**:
- ✅ CRUD de marcas (Coca Cola, Lazzaroni, Famosa, etc.)
- ✅ Relación con insumos (opcional)
- ✅ Soft deletes

### CU4: Gestión de Inventarios
**Actores**: Administrador, Almacenero  
**Descripción**: Control de inventario con movimientos de entrada, salida y ajuste

**Funcionalidades**:
- ✅ CRUD de insumos (arroz, aceite, botellas, etc.)
- ✅ Registro de movimientos (Ingreso, Salida, Ajuste)
- ✅ Stock actual y unidad de medida
- ✅ Movimientos NO editables (solo ajustes)
- ✅ Validación de stock (no puede ser negativo)
- ✅ Lista ordenada por fecha más reciente

### CU5: Gestión de Compras
**Actores**: Administrador, Almacenero  
**Descripción**: Registro de compras a proveedores con control de stock

**Funcionalidades**:
- ✅ CRUD de proveedores (nombre, NIT, teléfono, dirección)
- ✅ Registro de compras con detalle de insumos
- ✅ Edición de compras con ajuste automático de stock
- ✅ Validación: solo si stock lo permite
- ✅ Eliminación lógica con ajuste de movimientos
- ✅ Lista ordenada por fecha más reciente

### CU6: Gestión de Ventas
**Actores**: Administrador (pago inmediato), Mesero (ver/editar estado), Cliente (crear con crédito)  
**Descripción**: Sistema de ventas con prepago y asignación de mesas

**Funcionalidades**:
- ✅ Registro de ventas con selección de productos
- ✅ Asignación de mesa
- ✅ Tipos de pago: Inmediato o Crédito
- ✅ Cálculo automático de totales
- ✅ Estados: PENDIENTE → ENTREGADO
- ✅ Mesero solo puede cambiar estado a ENTREGADO
- ✅ Cliente puede pagar con crédito (selecciona plan)
- ✅ NO se pueden editar/eliminar ventas pagadas (prepago)
- ✅ Lista ordenada por fecha más reciente

### CU7: Gestión de Créditos y Pagos
**Actores**: Administrador, Cliente  
**Descripción**: Sistema de créditos con planes de pago e interés diario

**Funcionalidades Créditos**:
- ✅ CRUD de planes de crédito (nombre, tasa interés diario, plazo días)
- ✅ Generación automática de crédito al comprar con plan
- ✅ Cálculo de interés diario sobre saldo pendiente
- ✅ Generación de plan de pagos (cuotas sugeridas)
- ✅ Cliente ve sus créditos pendientes

**Funcionalidades Pagos**:
- ✅ Cliente registra pagos de cualquier monto
- ✅ Cálculo automático: Pago = Interés + Amortización Capital
- ✅ Si pago < interés: se acumula deuda de interés
- ✅ Actualización automática de saldo pendiente
- ✅ Historial de pagos ordenado por fecha
- ✅ Vista "Mis Pagos" para cliente

**Ejemplo de Interés**:
- Crédito: 500 Bs, Tasa: 1% diario
- Día 1: Interés = 5 Bs, Capital = 500 Bs
- Si paga 4 Bs: Deuda interés = 1 Bs, Capital = 500 Bs
- Día 2: Interés = 6 Bs (5 + 1 deuda), Capital = 500 Bs

### CU8: Reportes y Estadísticas
**Actores**: Administrador, Almacenero  
**Descripción**: Dashboard con métricas y estadísticas del sistema

**Funcionalidades**:
- ✅ Estadísticas de ventas
- ✅ Estadísticas de compras
- ✅ Insumos con menor stock
- ✅ Productos más vendidos
- ✅ Páginas más visitadas
- ✅ Gráficos y visualizaciones

---

## 👥 Roles y Permisos

### 🔴 Administrador
**Acceso Total al Sistema**

**Permisos**:
- ✅ Dashboard con todas las estadísticas
- ✅ CRUD Usuarios
- ✅ CRUD Productos
- ✅ CRUD Mesas
- ✅ CRUD Marcas
- ✅ CRUD Insumos
- ✅ Registro de Movimientos
- ✅ CRUD Proveedores
- ✅ CRUD Compras
- ✅ Registro de Ventas (solo pago inmediato)
- ✅ CRUD Planes de Crédito
- ✅ Ver todos los créditos y pagos

**Navegación**:
```
Dashboard → Usuarios → Productos → Mesas → Marcas → Insumos → Movimientos → 
Proveedores → Compras → Ventas → Planes → Créditos → Pagos → Reportes
```

### 🟡 Almacenero
**Gestión de Inventario y Compras**

**Permisos**:
- ✅ Dashboard (inventario)
- ✅ CRUD Marcas
- ✅ CRUD Insumos
- ✅ Registro de Movimientos
- ✅ CRUD Proveedores
- ✅ CRUD Compras
- ❌ NO gestiona usuarios, ventas, créditos

**Navegación**:
```
Dashboard → Marcas → Insumos → Movimientos → Proveedores → Compras → Reportes
```

### 🟢 Mesero
**Gestión de Órdenes**

**Permisos**:
- ✅ Ver lista de órdenes (ventas)
- ✅ Filtrar por estado PENDIENTE
- ✅ Ver mesa asignada a cada orden
- ✅ Cambiar estado a ENTREGADO
- ❌ NO puede crear ventas
- ❌ NO puede editar productos
- ❌ NO puede eliminar ventas

**Navegación**:
```
Órdenes → Ver Órdenes Pendientes → Marcar como Entregado
```

### 🔵 Cliente
**Compras y Créditos**

**Permisos**:
- ✅ Ver catálogo de productos
- ✅ Crear pedidos/ventas
- ✅ Seleccionar mesa
- ✅ Pago inmediato o crédito
- ✅ Seleccionar plan de crédito
- ✅ Ver mis créditos pendientes
- ✅ Ver plan de pagos sugerido
- ✅ Registrar pagos
- ✅ Ver historial de mis pagos
- ❌ NO ve otros usuarios
- ❌ NO edita productos
- ❌ NO ve ventas de otros

**Navegación**:
```
Productos → Nuevo Pedido → Mis Créditos → Plan de Pagos → Mis Pagos
```

---

## 🗄️ Base de Datos

### Tablas Principales

#### `users`
```sql
- id (PK)
- name
- email (unique)
- password
- profile_photo_path
- timestamps
- deleted_at
```

#### `productos`
```sql
- id (PK)
- nombre (unique)
- costo
- precio_venta
- imagen_url
- stock
- timestamps
- deleted_at
```

#### `marcas`
```sql
- id (PK)
- nombre (unique)
- timestamps
- deleted_at
```

#### `insumos`
```sql
- id (PK)
- nombre
- stock
- unidad_medida
- marca_id (FK → marcas, nullable)
- timestamps
- deleted_at
```

#### `movimientos`
```sql
- id (PK)
- insumo_id (FK → insumos)
- tipo (INGRESO, SALIDA, AJUSTE)
- cantidad
- motivo
- fecha
- timestamps
```

#### `proveedores`
```sql
- id (PK)
- nombre
- nit (unique)
- telefono
- direccion
- timestamps
- deleted_at
```

#### `compras`
```sql
- id (PK)
- proveedor_id (FK → proveedores)
- total
- fecha
- timestamps
- deleted_at
```

#### `detalle_compra`
```sql
- id (PK)
- compra_id (FK → compras)
- insumo_id (FK → insumos)
- cantidad
- costo_unitario
- subtotal
- timestamps
```

#### `mesas`
```sql
- id (PK)
- codigo (unique)
- capacidad
- timestamps
- deleted_at
```

#### `ventas`
```sql
- id (PK)
- user_id (FK → users)
- mesa_id (FK → mesas)
- total
- tipo_pago (INMEDIATO, CREDITO)
- estado (PENDIENTE, ENTREGADO)
- timestamps
- deleted_at
```

#### `detalle_venta`
```sql
- id (PK)
- venta_id (FK → ventas)
- producto_id (FK → productos)
- cantidad
- precio_unitario
- subtotal
- timestamps
```

#### `planes`
```sql
- id (PK)
- nombre
- tasa_interes_diario (decimal, ej: 1.00 = 1%)
- plazo_dias
- timestamps
- deleted_at
```

#### `creditos`
```sql
- id (PK)
- user_id (FK → users)
- venta_id (FK → ventas)
- plan_id (FK → planes)
- monto_total
- saldo_pendiente
- fecha_inicio
- fecha_limite
- estado (ACTIVO, PAGADO, VENCIDO)
- timestamps
```

#### `cuotas` (Plan de Pagos Sugerido)
```sql
- id (PK)
- credito_id (FK → creditos)
- numero_cuota
- monto_sugerido
- fecha_sugerida
- timestamps
```

#### `pagos`
```sql
- id (PK)
- credito_id (FK → creditos)
- monto
- fecha_pago
- timestamps
```

#### `page_visits`
```sql
- id (PK)
- user_id (FK → users)
- page_name
- visited_at
- timestamps
```

### Diagrama de Relaciones

```
users (1) ──── (N) ventas
users (1) ──── (N) creditos
mesas (1) ──── (N) ventas
ventas (1) ──── (N) detalle_venta
ventas (1) ──── (1) creditos
productos (1) ──── (N) detalle_venta
planes (1) ──── (N) creditos
creditos (1) ──── (N) cuotas
creditos (1) ──── (N) pagos
marcas (1) ──── (N) insumos
insumos (1) ──── (N) movimientos
proveedores (1) ──── (N) compras
compras (1) ──── (N) detalle_compra
insumos (1) ──── (N) detalle_compra
```

---

## 🎨 Sistema de Temas

### Temas Disponibles

#### 🌞 Light (Claro)
- **Colores**: Blanco, azul índigo
- **Fuente**: Sistema por defecto
- **Uso**: Tema estándar para trabajo diurno

#### 🌙 Dark (Oscuro)
- **Colores**: Gris oscuro, negro
- **Fuente**: Sistema por defecto
- **Uso**: Reducción de fatiga visual nocturna

#### 🎈 Kids (Niños)
- **Colores**: Rosa, turquesa, gradientes vibrantes
- **Fuente**: Fredoka (redondeada, 17px)
- **Efectos**: Puntos decorativos, animaciones
- **Uso**: Interfaz amigable para niños

#### 🎮 Young (Jóvenes)
- **Colores**: Púrpura, rosa, gradientes modernos
- **Fuente**: Poppins (moderna, 16px)
- **Efectos**: Pulso animado en sidebar
- **Uso**: Diseño dinámico para jóvenes

#### 💼 Adult (Adultos)
- **Colores**: Gris, azul oscuro, tonos profesionales
- **Fuente**: Merriweather (serif, 15px) - **Tamaño aumentado para mejor legibilidad**
- **Efectos**: Minimalista, elegante
- **Uso**: Ambiente profesional

### Modo Automático Día/Noche
- **6:00 AM - 6:00 PM**: Tema Light
- **6:00 PM - 6:00 AM**: Tema Dark
- **Actualización**: Cada minuto según hora local del cliente

### Accesibilidad

**Tamaños de Fuente**:
- Pequeño (14px)
- Normal (16px)
- Grande (18px)
- Extra Grande (20px)

**Niveles de Contraste**:
- Normal
- Alto
- Muy Alto

---

## 🔍 Buscador Global

### Características

**Búsqueda en**:
- 🧭 Navegación del menú (acciones, páginas)
- 👥 Usuarios (solo admin)
- 📦 Productos (solo admin, almacenero)
- 💰 Ventas (según rol)
- 🛍️ Mis Pedidos (cliente)

**Funcionalidades**:
- **Sin Acentos**: "cafe" encuentra "Café Americano"
- **Case Insensitive**: "CAFE", "cafe", "Cafe" funcionan igual
- **Keywords**: Búsqueda por palabras clave (crear, gestión, mis, etc.)
- **Navegación Rápida**: Ctrl+K / Cmd+K para abrir
- **Teclado**: ↑↓ navegar, Enter seleccionar, Esc cerrar
- **Permisos**: Solo muestra resultados accesibles

### Ejemplos de Búsqueda

**Administrador**:
- "crear" → Crear Usuario, Crear Producto, Crear Mesa, Nueva Venta
- "usuarios" → Gestión de Usuarios, [Usuarios de BD]
- "reportes" → Dashboard, Reportes

**Cliente**:
- "mis" → Mis Pedidos, Mis Créditos, Mis Pagos
- "pedidos" → Mis Pedidos, [Mis ventas]

**Mesero**:
- "venta" → Gestión de Ventas (solo ver)
- "orden" → Ver Órdenes

---

## 🚀 Instalación

### Requisitos Previos
- PHP 8.2 o superior
- Composer
- Node.js 18 o superior
- PostgreSQL 14 o superior
- Cuenta de Cloudinary (para imágenes)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd ProyectoBibliotecaTecnoWeb
```

2. **Instalar dependencias PHP**
```bash
composer install
```

3. **Instalar dependencias JavaScript**
```bash
npm install
```

4. **Configurar variables de entorno**
```bash
cp .env.example .env
```

5. **Generar key de aplicación**
```bash
php artisan key:generate
```

6. **Configurar base de datos**
Editar `.env` con tus credenciales de PostgreSQL

7. **Habilitar extensión unaccent en PostgreSQL**
```bash
php artisan tinker --execute="DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent;');"
```

8. **Ejecutar migraciones**
```bash
php artisan migrate
```

9. **Ejecutar seeders**
```bash
php artisan db:seed
```

10. **Compilar assets**
```bash
npm run dev
```

11. **Iniciar servidor**
```bash
php artisan serve
```

---

## ⚙️ Configuración

### Usuarios de Prueba

Después de ejecutar los seeders:

| Email | Contraseña | Rol |
|-------|-----------|-----|
| admin@restaurant.com | password | Administrador |
| almacenero@restaurant.com | password | Almacenero |
| mesero@restaurant.com | password | Mesero |
| cliente@restaurant.com | password | Cliente |

### Atajos de Teclado

- `Ctrl+K` / `Cmd+K`: Abrir buscador global
- `↑` / `↓`: Navegar resultados
- `Enter`: Seleccionar resultado
- `Esc`: Cerrar buscador

---

## 📄 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

---

## 👨‍💻 Desarrolladores

Proyecto desarrollado como parte del curso de Tecnología Web - Universidad Católica Boliviana.

**Restaurante**: Santo Pecatto

---

## 📞 Soporte

Para reportar bugs o solicitar features, por favor crear un issue en el repositorio.
