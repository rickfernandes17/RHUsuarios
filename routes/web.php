<?php

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FuncionarioController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('funcionarios.index');
});

Route::resource('empresas', EmpresaController::class)->only(['index', 'create', 'store']);
Route::resource('funcionarios', FuncionarioController::class)->only(['index', 'create', 'store']);

