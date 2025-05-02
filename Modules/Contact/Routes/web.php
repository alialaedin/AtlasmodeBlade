<?php

use Illuminate\Support\Facades\Route;
use Modules\Contact\Entities\Contact;
use Modules\Contact\Http\Controllers\Admin\ContactController as AdminContactController;
use Modules\Contact\Http\Controllers\Front\ContactController as FrontContactController;

Route::webSuperGroup('admin', function () {
  Route::patch('contacts/read', [AdminContactController::class,'read'])->name('contacts.read');
  Route::get('/contacts', [AdminContactController::class,'index'])->name('contacts.index');
  Route::patch('/contacts/answer', [AdminContactController::class,'answer'])->name('contacts.answer');
  Route::delete('/contacts/delete/{contact}', [AdminContactController::class,'destroy'])->name('contacts.destroy');
});

Route::prefix(Contact::CONTACT_URL)->name('front.contacts.')->group(function () {
  Route::get('/', [FrontContactController::class, 'index'])->name('index');
  Route::post('/', [FrontContactController::class, 'store'])->name('store');
});

Route::get(Contact::ABOUT_URL, [FrontContactController::class, 'about'])->name('front.about.index');
