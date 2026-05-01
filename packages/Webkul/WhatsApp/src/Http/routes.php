<?php

use Illuminate\Support\Facades\Route;
use Webkul\WhatsApp\Http\Controllers\WhatsAppController;

Route::group(['middleware' => ['api']], function () {
    Route::match(['get', 'post'], 'whatsapp/webhook', [WhatsAppController::class, 'handleWebhook'])->name('whatsapp.webhook');
});

Route::group(['middleware' => ['web', 'admin']], function () {
    Route::get('admin/whatsapp/chats', [WhatsAppController::class, 'index'])->name('admin.whatsapp.index');
    
    // Chat API
    Route::get('admin/whatsapp/api/conversations', [WhatsAppController::class, 'getConversations'])->name('admin.whatsapp.api.conversations');
    Route::get('admin/whatsapp/api/messages/{id}', [WhatsAppController::class, 'getMessages'])->name('admin.whatsapp.api.messages');
    Route::post('admin/whatsapp/api/send', [WhatsAppController::class, 'sendMessage'])->name('admin.whatsapp.api.send');
    
    // Qualification API
    Route::get('admin/whatsapp/api/qualification/{id}', [WhatsAppController::class, 'getQualification'])->name('admin.whatsapp.api.qualification');
});
