<?php

return [
    'name' => 'Invoices',
    'create' => 'New invoice',
    'edit' => 'Edit invoice',
    'print' => 'Print Invoice',
    'download' => 'Download Invoice',
    'heading' => 'Invoice',
    'invoice_for_job' => '',
    'table' => [
        'code' => 'Code',
        'amount' => 'Amount',
    ],
    'detail' => [
        'invoice_for' => 'Invoice For',
        'invoice_to' => 'Invoice To',
        'tax_id' => 'Tax ID',
        'code' => 'Invoice Code',
        'issue_at' => 'Issue At',
        'description' => 'Description',
        'qty' => 'Qty',
        'amount' => 'Amount',
        'discount' => 'Discount',
        'grand_total' => 'Grand Total',
        'shipping_fee' => 'Shipping Fee',
        'sub_total' => 'Sub Total',
        'tax' => 'Tax',
        'total' => 'Total',
    ],
    'total_amount' => 'Total Amount',
    'payment_info' => 'Payment Info',
    'payment_method' => 'Payment Method',
    'payment_status' => 'Payment Status',
    'variables' => [
        'invoice_all' => 'Invoice information from database, ex: invoice.code, invoice.amount, ...',
        'logo_full_path' => 'The site logo with full url',
        'company_logo_full_path' => 'The company logo of invoice with full URL',
        'tax_id' => 'Company tax ID',
        'payment_method' => 'Payment method',
        'payment_status' => 'Payment status',
        'payment_description' => 'Payment description',
        'settings' => [
            'using_custom_font_for_invoice' => 'Check site is using custom font for invoice or not',
            'font_family' => 'The font family of invoice template',
            'enable_invoice_stamp' => 'Check have enabled the invoice stamp',
            'company_name_for_invoicing' => 'The company name of invoice',
            'company_address_for_invoicing' => 'The company address of invoice',
            'company_email_for_invoicing' => 'The company email of invoice',
            'company_phone_for_invoicing' => 'The company phone number of invoice',
        ],
    ],
];
