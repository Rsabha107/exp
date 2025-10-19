<?php

use App\Http\Controllers\Sps\Admin\StorageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Setting\EventController;
use App\Http\Controllers\GeneralSettings\AttachmentController;
use App\Http\Controllers\GeneralSettings\CompanyController;
use App\Http\Controllers\Sps\Admin\DashboardController;
use App\Http\Controllers\Sps\User\UserController as AdminUserController;
use App\Http\Controllers\AdminController as AuthAdminController;
use App\Http\Controllers\Setting\AppSettingController;
use App\Http\Controllers\Setting\CategoryController;
use App\Http\Controllers\TaskController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\Setting\VenueController;
use App\Http\Controllers\Setting\LocationController;
use App\Http\Controllers\Setting\StorageTypeController;
use App\Http\Controllers\Setting\TodoStatusController;
use App\Http\Controllers\Sps\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Sps\AuditLog\AuditLogController;
use App\Http\Controllers\Sps\Customer\ProfileController;
use App\Http\Controllers\Sps\VenueAdmin\DashboardController as VenueAdminDashboardController;
use App\Http\Controllers\Sps\VenueAdmin\StorageController as VenueAdminStorageController;
use App\Http\Controllers\Sps\Operator\StorageController as OperatorStorageController;
use App\Http\Controllers\Sps\VenueAdmin\TaskController as VenueAdminTaskController;
use App\Http\Controllers\UtilController;
use Barryvdh\DomPDF\ServiceProvider;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Audit;

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

// Route::get('/', function () {
//     if (auth()->check()) {
//         if (auth()->user()->hasRole('SuperAdmin')) {
//             return redirect()->route('sps.admin');
//         } elseif (auth()->user()->hasRole('VenueAdmin')) {
//             Log::info('Redirecting to sps.venue.admin');
//             return redirect()->route('sps.venue.admin');
//         } elseif (auth()->user()->hasRole('Operator')) {
//             Log::info('Redirecting to sps.operator');
//             return redirect()->route('sps.operator');
//         } else {
//             return redirect()->route('index');
//         }
//     } else {
//         return redirect()->route('index');
//     }
// })->name('home');

Route::get('/', function () {

    Log::info('In home route');
    Log::info('User authenticated: ' . (auth()->check() ? 'yes' : 'no'));
    if (!auth()->check()) {
        Log::info('User is not authenticated');
        return redirect()->route('login');
    }

    $roleRoutes = [
        'SuperAdmin' => 'sps.admin',
        'VenueAdmin'   => 'sps.venue.admin',
        'Operator'   => 'sps.operator',
    ];

    foreach ($roleRoutes as $role => $route) {
        if (auth()->user()->hasRole($role)) {
            Log::info("Redirecting to $route for role $role");
            return redirect()->route($route);
        }
    }

    // Auth::guard('web')->logout();
    // $request->session()->invalidate();
    // $request->session()->regenerateToken();
    // $tenantId = config('services.microsoft.tenant_id'); // from .env
    // $redirectUri = urlencode(route('home')); // or any route you want after logout
    // $microsoftLogoutUrl = Socialite::driver('microsoft')->getLogoutUrl(route('login')); // Replace 'azure' with your Microsoft Socialite driver name if different, and 'login' with your desired redirect URI after Microsoft logout.
    // return redirect($microsoftLogoutUrl);
    abort(403, 'Unauthorized role');
})->name('home');

Route::get('/index', [ProfileController::class, 'index'])->name('index');
Route::get('/spectator', [ProfileController::class, 'spectator'])->name('spectator');
Route::post('/visitors/store', [ProfileController::class, 'store'])->name('visitor.store');
Route::get('/sps/customer/visitor', function () {
    return view('sps.customer.visitor');
})->name('sps.customer.visitor');
Route::get('/sps/customer/confirmation/{token}', [ProfileController::class, 'confirmation'])->name('sps.customer.confirmation');

Route::group(['middleware' => 'prevent-back-history', 'XssSanitizer'], function () {
    // SPS MANAGEMENT ******************************************************************** Admin All Route
    // 'roles:admin',

    Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin|VenueAdmin|Operator', 'prevent-back-history', 'auth.session'])->group(function () {
        Route::controller(AdminUserController::class)->group(function () {
            Route::get('/sps/users/profile', 'profile')->name('sps.users.profile');
            Route::post('/sps/users/profile/update', 'update')->name('sps.users.profile.update');
            Route::post('/sps/users/profile/password/update', 'updatePassword')->name('sps.users.profile.password.update');
        });
    });

    Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin', 'prevent-back-history', 'auth.session'])->group(function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/sps/admin/dashboard', 'dashboard')->name('sps.admin.dashboard');
        });

        Route::controller(AdminTaskController::class)->group(function () {
            Route::get('/sps/admin/tasks', 'index')->name('sps.admin.tasks.index');
            Route::get('/sps/admin/tasks/create', 'create')->name('sps.admin.tasks.create');
            Route::post('/sps/admin/tasks/store', 'store')->name('sps.admin.tasks.store');
            Route::get('/sps/admin/tasks/{id}/edit', 'edit')->name('sps.admin.tasks.edit');
            Route::put('/sps/admin/tasks/{id}', 'update')->name('sps.admin.tasks.update');
            Route::delete('/sps/admin/tasks/{id}', 'destroy')->name('sps.admin.tasks.destroy');
            Route::post('/sps/admin/tasks/{task}/assign', 'assignTaskToVenue')->name('sps.admin.tasks.assign');

            Route::post('/sps/admin/tasks/copy-to-lead', 'copyToLead')
                ->name('sps.admin.tasks.copyToLead');

            Route::get('/sps/admin/orders/{id}/switch', 'switch')->name('sps.admin.orders.switch');


            // Toggle complete/incomplete
            Route::patch('/sps/admin/tasks/{task}/toggle', 'toggle')->name('sps.admin.tasks.toggle');
            // Refresh only task list (ul)
            Route::get('/sps/admin/tasks/list/refresh', 'tasksList')->name('sps.admin.tasks.tasksList');
        });



        Route::controller(CategoryController::class)->group(function () {
            Route::get('setting/categories', 'index')->name('setting.category.index');
            Route::get('setting/categories/list', 'list')->name('setting.category.list');
            Route::get('setting/categories/create', 'create')->name('setting.category.create');
            Route::post('setting/categories/store', 'store')->name('setting.category.store');
            Route::get('setting/categories/{id}/edit', 'edit')->name('setting.category.edit');
            Route::put('setting/categories/{id}', 'update')->name('setting.category.update');
            // Route::get('/sps/category/visitor/mv/get/{id}', 'getVisitorResultView')->name('sps.operator.visitor.mv.get');
            Route::delete('setting/categories/{id}', 'delete')->name('setting.category.delete');
        });

        Route::controller(StorageController::class)->group(function () {
            Route::get('/sps/admin', 'index')->name('sps.admin');
            Route::get('/sps/admin/list', 'list')->name('sps.admin.list');
            Route::get('/sps/admin/create', 'create')->name('sps.admin.create');
            Route::post('/sps/admin/visitor/store', 'store')->name('sps.admin.visitor.store');
            Route::post('/sps/admin/item/store', 'ItemStore')->name('sps.admin.item.store');
            Route::get('/sps/admin/item/mv/edit/{id}', 'getItemDescriptionView')->name('sps.admin.item.mv.edit');
            Route::get('/sps/admin/visitor/mv/get/{id}', 'getVisitorResultView')->name('sps.admin.visitor.mv.get');
            Route::get('/sps/admin/find', 'find')->name('sps.admin.find');
            Route::post('/sps/admin/find', 'get')->name('sps.admin.get');
            Route::delete('/sps/admin/visitor/delete/{id}', 'deleteVisitor')->name('sps.admin.visitor.delete');
            // update status routes
            Route::post('/sps/admin/item/status/update', 'updateStatus')->name('sps.admin.item.status.update');
            Route::get('/sps/admin/item/status/edit/{id}', 'editStatus')->name('sps.admin.item.status.edit');
            // update inline fields
            Route::post('/sps/admin/item/update-field/{id}', 'updateField')->name('sps.admin.item.update.field');

            Route::get('/sps/admin/orders/{id}/switch', 'switch')->name('sps.admin.orders.switch');
            Route::get('/sps/admin', 'index')->name('sps.admin');

            // // check-in and check-out routes
            // Route::post('/sps/admin/update-status', 'updateCheckInStatus')->name('sps.admin.profile.update.status');
            // check-in and check-out routes
            Route::post('/sps/admin/update-status', 'updateCheckInStatus')->name('sps.admin.profile.update.status');
            Route::post('/sps/admin/update-item-status', 'updateCheckInItemStatus')->name('sps.admin.item.update.status');
        });

        // Venue
        Route::controller(VenueController::class)->group(function () {
            Route::get('/setting/venue', 'index')->name('setting.venue');
            Route::get('/setting/venue/list', 'list')->name('setting.venue.list');
            Route::get('/setting/venue/get/{id}', 'get')->name('setting.venue.get');
            Route::post('setting/venue/update', 'update')->name('setting.venue.update');
            Route::delete('/setting/venue/delete/{id}', 'delete')->name('setting.venue.delete');
            Route::post('/setting/venue/store', 'store')->name('setting.venue.store');
        });

        //Event
        Route::controller(EventController::class)->group(function () {
            Route::get('/setting/event', 'index')->name('setting.event');
            Route::get('/setting/event/list', 'list')->name('setting.event.list');
            Route::get('/setting/event/get/{id}', 'get')->name('setting.event.get');
            Route::post('setting/event/update', 'update')->name('setting.event.update');
            Route::delete('/setting/event/delete/{id}', 'delete')->name('setting.event.delete');
            Route::post('/setting/event/store', 'store')->name('setting.event.store');
            Route::get('/setting/event/mv/get/{id}', 'getEventView')->name('setting.event.get.mv');
            // Route::get('/cms/setting/event/file/{file}', 'getPrivateFile')->name('cms.setting.event.file');
        });

        // Location
        Route::controller(LocationController::class)->group(function () {
            Route::get('/setting/location', 'index')->name('setting.location');
            Route::get('/setting/location/list', 'list')->name('setting.location.list');
            Route::get('/setting/location/get/{id}', 'get')->name('setting.location.get');
            Route::post('setting/location/update', 'update')->name('setting.location.update');
            Route::delete('/setting/location/delete/{id}', 'delete')->name('setting.location.delete');
            Route::post('/setting/location/store', 'store')->name('setting.location.store');
        });

        // Storage Type
        Route::controller(StorageTypeController::class)->group(function () {
            Route::get('/setting/storage-type', 'index')->name('setting.storage.type');
            Route::get('/setting/storage-type/list', 'list')->name('setting.storage.type.list');
            Route::get('/setting/storage-type/get/{id}', 'get')->name('setting.storage.type.get');
            Route::post('setting/storage-type/update', 'update')->name('setting.storage.type.update');
            Route::delete('/setting/storage-type/delete/{id}', 'delete')->name('setting.storage.type.delete');
            Route::post('/setting/storage-type/store', 'store')->name('setting.storage.type.store');
        });

        //Application Setting
        Route::controller(AppSettingController::class)->group(function () {
            Route::get('/setting/application', 'index')->name('setting.application');
            Route::get('/setting/application/list', 'list')->name('setting.application.list');
            Route::get('/setting/application/get/{id}', 'get')->name('setting.application.get');
            Route::post('setting/application/update', 'update')->name('setting.application.update');
            Route::delete('/setting/application/delete/{id}', 'delete')->name('setting.application.delete');
            Route::post('/setting/application/store', 'store')->name('setting.application.store');
        });

        Route::controller(AdminUserController::class)->group(function () {
            Route::get('/sps/admin/users/profile', 'profile')->name('sps.admin.users.profile');
            Route::post('/sps/admin/users/profile/update', 'update')->name('sps.admin.users.profile.update');
            Route::post('/sps/admin/users/profile/password/update', 'updatePassword')->name('sps.admin.users.profile.password.update');
            // Route::get('/sps/admin/users/invite-user', 'showForm')->name('sps.admin.users.invite.form');
            // Route::post('/admin/invite-user', 'sendInvite')->name('sps.admin.users.invite.send');
        });

        // General Settings MANAGEMENT ******************************************************************** Admin All Route
        // company Routes
        Route::controller(CompanyController::class)->group(function () {
            Route::get('/general/settings/company/', 'index')->name('general.settings.company');
            Route::post('/general/settings/update', 'update')->name('general.settings.update');
        });
    });

    Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:VenueAdmin', 'prevent-back-history', 'auth.session'])->group(function () {
        Route::controller(VenueAdminDashboardController::class)->group(function () {
            Route::get('/sps/venue-admin/dashboard', 'dashboard')->name('sps.venue.admin.dashboard');
        });

        Route::controller(VenueAdminTaskController::class)->group(function () {
            Route::get('/sps/venue-admin/tasks', 'index')->name('sps.venue.admin.tasks.index');
            Route::get('/sps/venue-admin/tasks/create', 'create')->name('sps.venue.admin.tasks.create');
            Route::post('/sps/venue-admin/tasks/store', 'store')->name('sps.venue.admin.tasks.store');
            Route::get('/sps/venue-admin/tasks/{id}/edit', 'edit')->name('sps.venue.admin.tasks.edit');
            Route::put('/sps/venue-admin/tasks/{id}', 'update')->name('sps.venue.admin.tasks.update');
            Route::delete('/sps/venue-admin/tasks/{id}', 'destroy')->name('sps.venue.admin.tasks.destroy');

            Route::get('admin/orders/{id}/switch', 'switch')->name('admin.orders.switch');


            Route::post('/sps/venue-admin/tasks/comment', 'saveComment')->name('sps.venue.admin.tasks.comment');
            Route::post('tasks/comment/delete', 'deleteComment')->name('venue.admin.tasks.comment.delete');


            // Toggle complete/incomplete
            Route::post('/sps/venue-admin/tasks/{task}/toggle', 'toggle')->name('sps.venue.admin.tasks.toggle');

            Route::get('/sps/venue-admin/tasks/export/pdf', 'exportPdf')->name('sps.venue.admin.tasks.export.pdf');
            Route::get('/sps/pdf/download', 'preview')->name('sps.pdf.download');
        });

        Route::controller(VenueAdminStorageController::class)->group(function () {
            Route::get('/sps/venue-admin', 'index')->name('sps.venue.admin');
            Route::get('/sps/venue-admin/list', 'list')->name('sps.venue.admin.list');
            Route::get('/sps/venue-admin/create', 'create')->name('sps.venue.admin.create');
            Route::post('/sps/venue-admin/visitor/store', 'store')->name('sps.venue.admin.visitor.store');
            Route::post('/sps/venue-admin/item/store', 'ItemStore')->name('sps.venue.admin.item.store');
            Route::get('/sps/venue-admin/item/mv/edit/{id}', 'getItemDescriptionView')->name('sps.venue.admin.item.mv.edit');
            Route::get('/sps/venue-admin/visitor/mv/get/{id}', 'getVisitorResultView')->name('sps.venue.admin.visitor.mv.get');
            Route::get('/sps/venue-admin/find', 'find')->name('sps.venue.admin.find');
            Route::post('/sps/venue-admin/get', 'get')->name('sps.venue.admin.get');
            Route::delete('/sps/venue-admin/visitor/delete/{id}', 'deleteVisitor')->name('sps.venue.admin.visitor.delete');
            // update status routes
            Route::post('/sps/venue-admin/item/status/update', 'updateStatus')->name('sps.venue.admin.item.status.update');
            Route::get('/sps/venue-admin/item/status/edit/{id}', 'editStatus')->name('sps.venue.admin.item.status.edit');
            // update inline fields
            Route::post('/sps/venue-admin/item/update-field/{id}', 'updateField')->name('sps.venue.admin.item.update.field');

            Route::get('/sps/venue-admin/orders/{id}/switch', 'switch')->name('sps.venue.admin.orders.switch');
            Route::get('/sps/venue-admin/venue/{id}/switch', 'venueSwitch')->name('sps.venue.admin.venue.switch');

            // check-in and check-out routes
            Route::post('/sps/venue-admin/update-status', 'updateCheckInStatus')->name('sps.venue.admin.profile.update.status');
            Route::post('/sps/venue-admin/update-item-status', 'updateCheckInItemStatus')->name('sps.venue.admin.item.update.status');
        });
    });

    Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:Operator', 'prevent-back-history', 'auth.session'])->group(function () {

        Route::controller(OperatorStorageController::class)->group(function () {
            Route::get('/sps/operator', 'index')->name('sps.operator');
            Route::get('/sps/operator/list', 'list')->name('sps.operator.list');
            Route::get('/sps/operator/create', 'create')->name('sps.operator.create');
            Route::post('/sps/operator/visitor/store', 'store')->name('sps.operator.visitor.store');
            Route::post('/sps/operator/item/store', 'ItemStore')->name('sps.operator.item.store');
            Route::get('/sps/operator/item/mv/edit/{id}', 'getItemDescriptionView')->name('sps.operator.item.mv.edit');
            Route::get('/sps/operator/visitor/mv/get/{id}', 'getVisitorResultView')->name('sps.operator.visitor.mv.get');
            Route::get('/sps/operator/find', 'find')->name('sps.operator.find');

            Route::get('/sps/operator/find_direct/{id}', 'find_direct')->name('sps.operator.find_direct');
            Route::post('/sps/operator/item/update-storage', 'updateStorage')->name('sps.operator.item.update.storage');


            Route::post('/sps/operator/get', 'get')->name('sps.operator.get');
            Route::delete('/sps/operator/visitor/delete/{id}', 'deleteVisitor')->name('sps.operator.visitor.delete');
            // update status routes
            Route::post('/sps/operator/item/status/update', 'updateStatus')->name('sps.operator.item.status.update');
            Route::get('/sps/operator/item/status/edit/{id}', 'editStatus')->name('sps.operator.item.status.edit');
            // update inline fields
            Route::post('/sps/operator/item/update-field/{id}', 'updateField')->name('sps.operator.item.update.field');

            //Routes handeling for QR scanning (operator using mobile device) 
            Route::get('/sps/operator/visitor/mv/get/m/{id}', 'getVisitorResultMobileView')->name('sps.operator.visitor.mv.get.mobile');
            Route::get('/sps/operator/find/m/{id}', 'findm')->name('sps.operator.find.mobile');

            Route::get('/sps/operator/orders/{id}/switch', 'switch')->name('sps.operator.orders.switch');
            Route::get('/sps/operator/venue/{id}/switch', 'venueSwitch')->name('sps.operator.venue.switch');

            // check-in and check-out routes
            Route::post('/sps/operator/update-status', 'updateCheckInStatus')->name('sps.operator.profile.update.status');
            Route::post('/sps/operator/update-item-status', 'updateCheckInItemStatus')->name('sps.operator.item.update.status');
        });
    });
});

// ****************** ADMIN *********************
Route::group(['middleware' => 'prevent-back-history'], function () {
    // Add User
    // Route::get('/mds/auth/signup', [AuthAdminController::class, 'signUp'])->name('auth.signup');
    // Route::post('/signup/store', [UserController::class, 'store'])->name('admin.signup.store');

    Route::middleware(['auth', 'prevent-back-history'])->group(function () {
        Route::get('auth/otp', [AuthAdminController::class, 'showOtp'])->name('otp.get');
        Route::post('verify-otp', [AuthAdminController::class, 'verifyOtpAndLogin'])->name('auth.otp.post');
        Route::get('auth/resend', [AuthAdminController::class, 'resendOTP'])->name('otp.resend.get');

        //used to show images in private folder
        Route::get('/doc/{file}', [UtilController::class, 'showImage'])->name('a');

        /*************************************** Play ground */
        // Route::get('/a/{GlobalAttachment}', [UtilController::class, 'serve'])->name('a');
        Route::get('/doc/{file}', [UtilController::class, 'showImage'])->name('a');
        Route::get('/a', function () {
            return response()->file(storage_path('app/private/users/502828276250308124600avatar-2.png'));
        })->name('b');
        /*************************************** End Play ground */

        // Venue Admin pick
        Route::get('/sps/venue-admin/pick', function () {
            return view('/sps/venue-admin/pick');
        })
            ->name('sps.venue.admin.pick')
            ->middleware('role:VenueAdmin');

        Route::post('/sps/venue-admin/events/switch', [VenueAdminStorageController::class, 'pickEvent'])
            ->name('sps.venue.admin.event.switch')
            ->middleware('role:VenueAdmin');

        // Route::get('/users', [UserController::class, 'index'])->name('users.index');
        // Route::get('/mds/users/profile', [UserController::class, 'profile'])->name('mds.users.profile');
    });

    require __DIR__ . '/auth.php';

    // file manager routes
    Route::middleware(['auth', 'otp', 'XssSanitizer', 'role:SuperAdmin|Procurement|Contractor|Customer|Agency', 'prevent-back-history', 'auth.session'])->group(function () {
        Route::controller(AttachmentController::class)->group(function () {
            Route::post('file/store', 'store')->name('file.store');
            Route::get('/global/files/list/{id?}', 'list')->name('global.files.list')->middleware('permission:employee.file.list');
            // Route::get('/global/files/list/{project?}', 'list')->name('global.files.list')->middleware('permission:employee.file.list');
            Route::get('/global/file/serve/{file}', 'serve')->name('global.file.serve');
            Route::delete('/global/files/delete/{id}', 'delete')->name('global.files.delete');
        });
    });

    Route::middleware(['prevent-back-history'])->group(function () {
        // Route::get('/tracki/auth/login', [AdminController::class, 'login'])->name('tracki.auth.login')->middleware('prevent-back-history');
        // Route::get('/mds/auth/login', [AuthAdminController::class, 'login'])
        //     ->name('mds.auth.login')
        //     ->middleware('prevent-back-history');

        Route::get('/sps/auth/forgot', [AdminController::class, 'forgotPassword'])->name('auth.forgot');
        Route::post('forget-password', [AdminController::class, 'submitForgetPasswordForm'])->name('forgot.password.post');
        // Route::get('mds/auth/reset/{token}', [AuthAdminController::class, 'showResetPasswordForm'])->name('mds.auth.reset');
        Route::get('auth/first/reset/{token}', [AuthAdminController::class, 'showResetPasswordForm'])->name('auth.first.reset');
        Route::post('reset-first-time-password', [AdminController::class, 'resetFirstPassword'])->name('reset.first.password.post');
        Route::post('reset-password', [AdminController::class, 'submitResetPasswordForm'])->name('reset.password.post');

        // Route::get('/send-mail/nb', [SendMailController::class, 'newBookingEmail']);
        // Route::get('/send-mail', [SendMailController::class, 'index']);
        // Route::get('/send-mail2', [SendMailController::class, 'sendTaskAssignmentEmail']);
    });

    // HR Security Settings all routes
    Route::middleware(['auth', 'otp', 'XssSanitizer', 'role:SuperAdmin', 'prevent-back-history', 'auth.session'])->group(function () {
        Route::controller(RoleController::class)->group(function () {
            //Admin User
            Route::get('/sec/adminuser/list', 'listAdminUser')->name('sec.adminuser.list');
            Route::post('updateadminuser', 'updateAdminUser')->name('sec.adminuser.update');
            Route::post('createadminuser', 'createAdminUser')->name('sec.adminuser.store');
            Route::get('/sec/adminuser/{id}/edit', 'editAdminUser')->name('sec.adminuser.edit');
            Route::get('/sec/adminuser/{id}/delete', 'deleteAdminUser')->name('sec.adminuser.delete');
            Route::get('/sec/adminuser/create', 'addAdminUser')->name('sec.adminuser.create');
            Route::get('/sec/adminuser/add2', 'addAdminUser2')->name('sec.adminuser.add2');

            // Roles
            Route::get('/sec/roles/add', function () {
                return view('/sec/roles/add');
            })->name('sec.roles.add');
            Route::get('/sec/roles/roles/list', 'listRole')->name('sec.roles.list');
            Route::post('updaterole', 'updateRole')->name('sec.roles.update');
            Route::post('createrole', 'createRole')->name('sec.roles.create');
            Route::get('/sec/roles/{id}/edit', 'editRole')->name('sec.roles.edit');
            Route::get('/sec/roles/{id}/delete', 'deleteRole')->name('sec.roles.delete');

            // group
            Route::get('/sec/groups/add', function () {
                return view('/sec/groups/add');
            })->name('sec.groups.add');
            Route::get('/sec/groups/list', 'listGroup')->name('sec.groups.list');
            Route::post('updategroup', 'updateGroup')->name('sec.groups.update');
            Route::post('creategroup', 'createGroup')->name('sec.groups.create');
            Route::get('/sec/groups/{id}/edit', 'editGroup')->name('sec.groups.edit');
            Route::get('/sec/groups/{id}/delete', 'deleteGroup')->name('sec.groups.delete');

            // Permission
            Route::get('/sec/permissions/list', 'listPermission')->name('sec.perm.list');
            Route::post('updatepermission', 'updatePermission')->name('sec.perm.update');
            Route::post('createpermission', 'createPermission')->name('sec.perm.create');
            Route::get('/sec/perm/{id}/edit', 'editPermission')->name('sec.perm.edit');
            Route::get('/sec/perm/{id}/delete', 'deletePermission')->name('sec.perm.delete');
            Route::get('/sec/permissions/add', 'addPermission')->name('sec.perm.add');

            Route::get('/sec/perm/import', 'ImportPermission')->name('sec.perm.import');
            Route::post('importnow', 'ImportNowPermission')->name('sec.perm.import.now');

            // Roles in Permission
            Route::get('/sec/rolesetup/list', 'listRolePermission')->name('sec.rolesetup.list');
            Route::post('updaterolesetup', 'updateRolePermission')->name('sec.rolesetup.update');
            Route::post('createrolesetup', 'createRolePermission')->name('sec.rolesetup.create');
            Route::get('/sec/rolesetup/{id}/edit', 'editRolePermission')->name('sec.rolesetup.edit');
            Route::get('/sec/rolesetup/{id}/delete', 'deleteRolePermission')->name('sec.rolesetup.delete');
            Route::get('/sec/rolesetup/add', 'addRolePermission')->name('sec.rolesetup.add');
        }); //

        Route::controller(AuditLogController::class)->group(function () {
            Route::get('/sec/log', 'index')->name('sec.log');
            Route::get('/sec/log/list', 'list')->name('sec.log.list');
            Route::get('/sec/log/get/{id}', 'get')->name('sec.log.get');
        });
    }); //
    // Route::get('/run-migration', function () {
    //     Artisan::call('optimize:clear');

    //     Artisan::call('migrate:refresh --seed');
    //     return "Migration executed successfully";
    // });
});
