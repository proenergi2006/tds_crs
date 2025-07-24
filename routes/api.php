<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\TwoFactorController; 
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\CabangController;
use App\Http\Controllers\SatuanController; 
use App\Http\Controllers\UkuranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProdukHargaController;
use App\Http\Controllers\AttachmentHargaDasarController;
use App\Http\Controllers\ProvinsiController;
use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\VendorPoController;
use App\Http\Controllers\VendorPoProdukController;
use App\Http\Controllers\PoVerificationController;
use App\Http\Controllers\ReceiveItemController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PenawaranController;
use App\Http\Controllers\JenisProdukController;
use App\Http\Controllers\TransportirController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\VolumeController;
use App\Http\Controllers\WilayahAngkutController;
use App\Http\Controllers\MasterKapalController;
use App\Http\Controllers\OngkosKapalController;



// 1. Login
Route::post('login', [AuthController::class, 'login']);

// 2. Verifikasi 2FA
Route::post('two-factor', [AuthController::class, 'twoFactor']);

Route::get('produk-hargas/check', [ProdukHargaController::class, 'check']);

// 3. Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // a) Get current user
    Route::get('user', fn(Request $req) => $req->user());

    // b) Roles CRUD
    Route::apiResource('roles', RoleController::class);

    // c) Users CRUD
    Route::apiResource('users', UserController::class);

    // d) 2FA management
    Route::post('2fa/generate', [TwoFactorController::class,'generate']);
    Route::post('2fa/enable',   [TwoFactorController::class,'enable']);
    Route::post('2fa/disable',  [TwoFactorController::class,'disable']);

    // e) Update password
    Route::post('user/password', [ProfileController::class, 'updatePassword']);
    Route::post('/user/face', [ProfileController::class, 'updateFace']);

    // f) CRUD Cabang / Satuan / Ukuran / Produk / ProdukHarga / AttachmentHarga / Provinsi / Kabupaten / Customer / Vendor / Terminal
    Route::apiResource('cabangs', CabangController::class);
    Route::apiResource('satuans', SatuanController::class);
    Route::apiResource('ukurans', UkuranController::class);
    Route::apiResource('produks', ProdukController::class);
   
    Route::apiResource('produk-hargas', ProdukHargaController::class);
  
    Route::apiResource('attachment-harga-dasar', AttachmentHargaDasarController::class);
    Route::apiResource('provinsis', ProvinsiController::class);
    Route::apiResource('kabupatens', KabupatenController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('vendors', VendorController::class);
    Route::apiResource('terminals', TerminalController::class);

    // g) Vendor PO + detail PO
    //   – Batch‐delete dan batch‐create detail PO
    Route::delete('vendor-pos-produk/batch', [VendorPoProdukController::class, 'destroyByPo'])
        ->name('vendor-pos-produk.batch-destroy');
    Route::post('vendor-pos-produk/batch', [VendorPoProdukController::class, 'storeBatch'])
        ->name('vendor-pos-produk.batch-store');

    //   – Resource routes untuk detail PO (index, store, show, update, destroy)
    Route::apiResource('vendor-pos-produk', VendorPoProdukController::class)
         ->only(['index','store','show','update','destroy'])
         ->where(['vendor_pos_produk' => '[0-9]+']);

    //   – Resource routes untuk header PO (kecuali create/edit)
    Route::apiResource('vendor-pos', VendorPoController::class)
         ->except(['create','edit']);

    //   – Endpoint khusus “approve” PO
    Route::patch('vendor-pos/{id}/approve', [VendorPoController::class, 'approve'])
         ->name('vendor-pos.approve');

    //   – Verifikasi PO (CFO/CEO)
    Route::get('po-verification', [PoVerificationController::class, 'index']);
    Route::post('po-verification/{id}', [PoVerificationController::class, 'verify']);
    //    GET  /api/vendor-pos/{poId}/receives    → daftar receive beserta detail produk
    //    POST /api/vendor-pos/{poId}/receives    → simpan receive baru
    Route::get('vendor-pos/{poId}/receives',     [ReceiveItemController::class, 'index'])
    ->whereNumber('poId');
    Route::post('vendor-pos/{poId}/receives',    [ReceiveItemController::class, 'store'])
    ->whereNumber('poId');

    Route::get('stocks', [StockController::class, 'index']);

    Route::get('penawarans',             [PenawaranController::class, 'index']);
    Route::get('penawarans/{id}',        [PenawaranController::class, 'show']);

    // Create, update, delete penawaran
    Route::post('penawarans',            [PenawaranController::class, 'store']);
    Route::put('penawarans/{id}',        [PenawaranController::class, 'update']);
    Route::delete('penawarans/{id}',     [PenawaranController::class, 'destroy']);

    Route::apiResource('jenis-produks', JenisProdukController::class);

    Route::apiResource('transportirs', TransportirController::class);
    
    Route::apiResource('personnels', PersonnelController::class);

    Route::apiResource('volumes', VolumeController::class);

    Route::apiResource('wilayah-angkuts', WilayahAngkutController::class);

    Route::apiResource('master-kapals', MasterKapalController::class);

    Route::apiResource('ongkos-kapal', OngkosKapalController::class);
    // Logout
    Route::post('logout',[AuthController::class,'logout']);
});

// 4. Public‐facing routes (tanpa perlu login)
//    – Tampilkan detail PO (tanpa login), mis. untuk QR‐Code preview
Route::get(
    'public/vendor-pos/{id}',
    [VendorPoController::class, 'publicShow']
)->whereNumber('id');
