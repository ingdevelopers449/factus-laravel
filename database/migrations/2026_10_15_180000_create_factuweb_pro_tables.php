<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. EMPRESA
        Schema::dropIfExists('empresa');
        Schema::create('empresa', function (Blueprint $table) {
            $table->id('id_empresa');
            $table->string('nit', 30)->unique('uk_empresa_nit');
            $table->string('razon_social', 200);
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('logo_url', 500)->nullable();
        });

        // 2. ROLES
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('code', 30)->unique();
                $table->string('name', 100);
            });
        }

        // 3. TABLA USUARIOS DESCARTADA: Se utiliza la tabla nativa de Laravel 'users'
        Schema::dropIfExists('usuarios');

        // 4. CATEGORIAS
        Schema::dropIfExists('categorias');
        Schema::create('categorias', function (Blueprint $table) {
            $table->id('id_categoria');
            $table->string('nombre_categoria', 150);
            $table->text('descripcion')->nullable();
            $table->string('estado', 30)->default('activo');
            $table->timestamp('fecha_creacion')->nullable()->useCurrent();
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrent()->useCurrentOnUpdate();
        });

        // 5. PRODUCTOS
        Schema::dropIfExists('productos');
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_producto');
            $table->unsignedBigInteger('id_categoria');
            $table->string('codigo_barras', 100)->nullable()->unique('uk_productos_codigo_barras');
            $table->string('nombre_producto', 200);
            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->decimal('precio_venta', 12, 2)->default(0);
            $table->integer('stock_actual')->default(0);
            $table->string('estado_producto', 30)->default('activo');
            $table->decimal('tarifa_iva', 5, 2)->default(0);
            $table->string('imagen_url', 500)->nullable();

            $table->index('nombre_producto', 'idx_productos_nombre');

            $table->foreign('id_categoria', 'fk_productos_categoria')
                ->references('id_categoria')
                ->on('categorias')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 6. CLIENTES
        Schema::dropIfExists('clientes');
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('id_cliente');
            $table->string('identificacion', 50);
            $table->string('nombre_razon_social', 200);
            $table->string('email', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 30)->nullable();

            $table->index('identificacion', 'idx_clientes_identificacion');
        });

        // 7. RESOLUCIONES DIAN
        Schema::dropIfExists('resolucion_dian');
        Schema::create('resolucion_dian', function (Blueprint $table) {
            $table->id('id_resolucion');
            $table->unsignedBigInteger('id_empresa');
            $table->string('numero_resolucion', 100);
            $table->date('fecha_vigencia');
            $table->string('prefijo', 20);
            $table->unsignedBigInteger('rango_inicial');
            $table->unsignedBigInteger('rango_final');
            $table->unsignedBigInteger('contador_actual')->default(0);
            $table->string('estado', 30)->default('activa');

            $table->unique(['id_empresa', 'numero_resolucion'], 'uk_resolucion_empresa_numero');

            $table->foreign('id_empresa', 'fk_resolucion_empresa')
                ->references('id_empresa')
                ->on('empresa')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 8. FACTURAS
        Schema::dropIfExists('facturas');
        Schema::create('facturas', function (Blueprint $table) {
            $table->id('id_factura');
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_resolucion');
            $table->string('prefijo_resolucion', 20);
            $table->unsignedBigInteger('consecutivo');
            $table->dateTime('fecha_emision')->useCurrent();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_usuario');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('total_iva', 14, 2)->default(0);
            $table->decimal('total_pagar', 14, 2)->default(0);
            $table->decimal('porcentaje_comision_aplicado', 5, 2)->default(0);
            $table->string('cufe', 255)->nullable();
            $table->text('codigo_qr')->nullable();
            $table->string('estado_dian', 50)->default('pendiente');
            $table->text('motivo_rechazo')->nullable();
            $table->dateTime('fecha_validacion_dian')->nullable();

            $table->unique(['id_empresa', 'prefijo_resolucion', 'consecutivo'], 'uk_facturas_empresa_numero');
            $table->index('fecha_emision', 'idx_facturas_fecha');
            $table->index('estado_dian', 'idx_facturas_estado_dian');

            $table->foreign('id_empresa', 'fk_facturas_empresa')
                ->references('id_empresa')
                ->on('empresa')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_resolucion', 'fk_facturas_resolucion')
                ->references('id_resolucion')
                ->on('resolucion_dian')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_cliente', 'fk_facturas_cliente')
                ->references('id_cliente')
                ->on('clientes')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_usuario', 'fk_facturas_usuario')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 9. VENTAS BORRADOR
        Schema::dropIfExists('ventas_borrador');
        Schema::create('ventas_borrador', function (Blueprint $table) {
            $table->id('id_borrador');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_cliente');
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_usuario', 'fk_borrador_usuario')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_cliente', 'fk_borrador_cliente')
                ->references('id_cliente')
                ->on('clientes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 10. DETALLE FACTURA
        Schema::dropIfExists('detalle_factura');
        Schema::create('detalle_factura', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_factura');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario_venta', 12, 2);
            $table->decimal('precio_unitario_costo', 12, 2);
            $table->decimal('subtotal_linea', 14, 2);

            $table->foreign('id_factura', 'fk_detalle_factura_factura')
                ->references('id_factura')
                ->on('facturas')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_producto', 'fk_detalle_factura_producto')
                ->references('id_producto')
                ->on('productos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 11. DETALLE BORRADOR
        Schema::dropIfExists('detalle_borrador');
        Schema::create('detalle_borrador', function (Blueprint $table) {
            $table->id('id_detalle_borrador');
            $table->unsignedBigInteger('id_borrador');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedInteger('cantidad');

            $table->foreign('id_borrador', 'fk_detalle_borrador_venta')
                ->references('id_borrador')
                ->on('ventas_borrador')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_producto', 'fk_detalle_borrador_producto')
                ->references('id_producto')
                ->on('productos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('detalle_borrador');
        Schema::dropIfExists('detalle_factura');
        Schema::dropIfExists('ventas_borrador');
        Schema::dropIfExists('facturas');
        Schema::dropIfExists('resolucion_dian');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('empresa');

        Schema::enableForeignKeyConstraints();
    }
};
