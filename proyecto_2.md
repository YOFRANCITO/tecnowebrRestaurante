# Sistema de inventarios y pagos al credito para el restaurante Santo Pecatto

## Casos de uso:

* CU1. Gestión de Usuarios(administrador, almacenero, mesero, gerente, cliente->SSO-RRSS).
* CU2. Gestión de Productos.
* CU3. Gestión de Marcas.
* CU4. Gestión de Inventarios(ingresos,salidas,ajustes).
* CU5. Gestión de Compras.
* CU6. Gestión de Ventas.
* CU7. Gestión de Pagos.
* CU8. Reportes y Estadísticas.

## Usuarios:

### Vista General:

* **Administrador:** tiene acceso a todo.
* **Almacenero:** tiene acceso solo a gestion de inventario, es decir puede:
    - Registrar filas en las tablas: movimiento, proveedor, compra, insumo, marca
* **Mesero:** tiene acceso solo para leer registros de ventas, es decir revisa que ordenaron los clientes y que mesa esta asignada a esa venta para poder entregarles la comida o productos, solo pueden editar el estado de la venta: del primer estado: "PENDIENTE" a "ENTREGADO". No existe opción para cancelar la venta ya que la estrategia de Negocio del restaurante es Prepago, es decir primero se paga para recibir la comida. Y no hay devoluciones.
* **Cliente:** 
    - tiene acceso a la lista de productos que ofrece el restaurante como ser platos de comida, bebidas enbotelladas, etc.
    - Puede comprar productos del restaurante (registrar una fila en la tabla Venta), indicando en que mesa se encuentra, que metodo de pago usar: Inmediato o solicitar un credito para su compra.
    
### Vista Detallada:

* **Administrador:**
    1. Dashboard con estadisticas de ventas, compras, insumos con menor stock
    2. CRUD de Marca (Marcas de los insumos. Ej: lazzaroni, coca cola, famosa, etc. La marca es opcional en los insumos)
    3. CRUD de Insumo (Insumos son todo aquello que pueden o no formar un producto o ser directamente un producto, ej: arroz, aceite, detergente, botella de coca cola de 500ml, etc.)
    4. Registro de Movimiento indicando el tipo de movimiento (Ingreso, Salida y Ajuste), cantidad, motivo, y el insumo en cuestión. No existen ediciones en movimiento, esto se realiza mediante el registro de un movimiento de tipo "Ajuste". No se pueden eliminar permanentemente los movimientos. La lista de movimientos se debe mostrar en orden de primero las fechas mas recientes.
    5. CRUD de Proveedor
    6. Registro de Compra, indicando a que proveedor se le esta comprando los insumos, indicar el costo y cantidad de cada insumo que se esta comprando. Es posible editar una compra, pero cuando esta afecta el stock de un insumo, se debe registrar un registro en movimiento indicando el ajuste, pero solo es posible mientras el stock actual del insumo lo permita, es decir si el stock actual de un insumo es = 5 y quiero editar la cantidad de ese detalle de venta de 25 a 15, es decir quiero quitar 10 del stock actual de ese insumo, el sistema no me debe dejar ya que el stock no puede ser negativo (5 - 10 = -5). Lo mismo para la eliminación, la eliminación debe insertar un registro en movimiento de tipo ajuste, y solo realizarse si el stock del insumo lo permite. La eliminación no debe ser permanente, debe ser eliminación logica. Las compras se deben mostrar en orden de fecha mas reciente.
    7. CRUD de Producto
    8. CRUD de Plan (Este es el Plan de Creditos al que puede acceder un cliente), lleva datos como nombre del Plan, tasa de interes diario (Ej: 1%), y el plazo de días que tiene el cliente para pagar el total del credito (Por ejemplo 40 días limite para pagar). Nota: La tasa de interes diario se aplica al saldo pendiente por pagar del credito.
    9. Registro de Venta, puede registrar una venta pero solo con tipo de pago inmediato. Solo los clientes que inicien sesión en el sistema pueden acceder a pagar con credito. Puede ver la lista de ventas. Las ventas no se pueden editar ni eliminar una vez realizado el pago. Porque la logica de negocio es Prepago. Las ventas se listan en orden de fecha mas reciente.
    10. CRUD de Mesa

* **Almacenero:**
    - Realiza los puntos del 1 al 6 del Administrador.
    
* **Mesero:**
    1. Puede ver una lista de ordenes (select de ventas) donde se pueden ordenar en primero las con estado='PENDIENTE', alli puede visualizar a que mesa llevar una orden. 
    2. Puede editar el estado de una Orden y establecer en 'ENTRAGADO' el estado. 
    3. No puede ni registrar una venta, ni editar, ni eliminar.
    
* **Cliente:**
    - Pueden realizar compras (insertar en la tabla venta), puede escoger el tipo de pago inmediato o credito. Si se escoge el tipo de pago credito debe escoger también el plan de credito (el plan de credito tiene un nombre, tasa de interes diario (Ej: 1%), plazo limite en dias para pagar el credito).
    - El cliente puede visualizar los creditos que tiene pendientes por pagar, y puede ver su plan de pagos correspondiente a dicho credito. El plan de pagos se genera a partir de los datos de fecha inicio, fecha fin, saldo pendiente del credito y de los datos tasa de interes diario y plazo de dias del plan de credito. Usando esos datos se generan filas para la tabla cuotas, que genera cuotas aproximadamente iguales y en que fechas pagar. Nota: El plan de pagos es solo una guia para el cliente si desea ver cuanto pagaría cada cierto tiempo para aprovechar al maximo el tiempo limite que tiene para pagar el credito.
    - El cliente tendrá una vista de "Mis pagos" donde podrá visualizar los pagos que ha realizado ordenado por fecha mas reciente.
    - Los pagos que realize el cliente para saldar el credito se registran en la tabla Pago
    - Entonces tomando en cuenta lo anterior, el cliente puede relizar pagos por adelantado a las cuotas (Se registra en la tabla pagos). O puede pagar la cantidad que guste pero siempre tomando en cuenta que del monto total que pague solo contará como pago al capital (credito) el resultado de restarle el interes. Por ejemplo si una persona tiene un credito de 500bs de capital con una tasa de interes diario de 1%, el primer dia debe pagar 5bs de intereses y 500 del capital, y si la persona decide pagar solo 4bs quedaría debiendo 1bs de intereses y el capital se mantendría en 500bs (no le alcanza para amortizar el capital), por lo que al día siguiente el interes sigue siendo 5bs y tendría que pagar 500bs de capital + 6bs de intereses. 
    
## Nota Importante:
No existen las eliminaciones reales o fisicas a nivel de base de datos, Las eliminaciones de los CRUD deben ser eliminaciones logicas. Se pueden llamar archivar y que donde se pueda archivar se muestre un boton de ver archivados y se vea la lista de los elementos archivados con la opción de desarchivar.

## Requerimientos de desarrollo

* Las vistas deben construirse usando vuejs con inertia, las vistas con Laravel blade no son validas.
* Cada pagina debe tener un contador de visitas en el pie de cada pagina.
* Las vistas deben construirse pensando en que existiran 3 temas (Adultos, Jovenes, Niños), lo cual cambiara los colores de fondo y de las letras de la aplicación, también algunos temas cambiaran el tamaño de las letras, por ejemplo el tema adultos debería aumentar el tamaño de las letras. Y también cada tema tendrá la opción de cambiar el modo (Día, Noche), el modo deberá cambiar según el horario del cliente.
* Todos las entradas o campos de texto (inputs) deberán estar validados y mostrar mensajes en español.





