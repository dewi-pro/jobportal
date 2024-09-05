<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('jb_invoices', function (Blueprint $table) {
            $table->id();
            $table->morphs('reference');
            $table->string('code')->unique();
            $table->string('customer_name');
            $table->string('company_name')->nullable();
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_tax_id')->nullable();
            $table->unsignedDecimal('sub_total', 15);
            $table->unsignedDecimal('tax_amount', 15)->default(0);
            $table->unsignedDecimal('shipping_amount', 15)->default(0);
            $table->unsignedDecimal('discount_amount', 15)->default(0);
            $table->unsignedDecimal('amount', 15);
            $table->unsignedInteger('payment_id')->nullable()->index();
            $table->string('status')->index()->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('jb_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->morphs('reference');
            $table->string('name');
            $table->string('description', 400)->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('qty');
            $table->unsignedDecimal('sub_total', 15);
            $table->unsignedDecimal('tax_amount', 15)->default(0);
            $table->unsignedDecimal('discount_amount', 15)->default(0);
            $table->unsignedDecimal('amount', 15);
            $table->text('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jb_invoice_items');
        Schema::dropIfExists('jb_invoices');
    }
};
