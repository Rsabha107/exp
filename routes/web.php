<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Setting\EventController;
use App\Http\Controllers\GeneralSettings\AttachmentController;
use App\Http\Controllers\GeneralSettings\CompanyController;
// use App\Http\Controllers\Sps\Admin\DashboardController;
use App\Http\Controllers\Chl\Admin\UserController as AdminUserController;
use App\Http\Controllers\Chl\Auth\AdminController as AuthAdminController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\Chl\Admin\ReportController;
use App\Http\Controllers\Setting\AppSettingController;
use App\Http\Controllers\Setting\CategoryController;

use App\Http\Controllers\Setting\VenueController;
use App\Http\Controllers\Setting\PermissionVenueEventController;

use App\Http\Controllers\Chl\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Chl\VenueAdmin\ReportController as VenueAdminReportController;
use App\Http\Controllers\Security\ActivityAuditController;
use App\Http\Controllers\Chl\VenueAdmin\TaskController as VenueAdminTaskController;
use App\Http\Controllers\UtilController;
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

Route::get('/', function () {

    Log::info('In home route');
    Log::info('User authenticated: ' . (auth()->check() ? 'yes' : 'no'));
    if (!auth()->check()) {
        Log::info('User is not authenticated');
        return redirect()->route('login');
    }

    $roleRoutes = [
        'SuperAdmin' => 'chl.admin.tasks.report',
        'VenueAdmin'   => 'chl.venue.admin.tasks.index',
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

Route::controller(MicrosoftController::class)->group(function () {
    Route::get('auth/microsoft', 'redirectToMicrosoft')->name('auth.microsoft');
    Route::get('auth/microsoft/callback', 'handleMicrosoftCallback');
});

Route::group(['middleware' => 'prevent-back-history', 'XssSanitizer'], function () {
    // SPS MANAGEMENT ******************************************************************** Admin All Route
    // 'roles:admin',

    Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin|VenueAdmin', 'prevent-back-history', 'auth.session'])->group(function () {
        Route::controller(AdminUserController::class)->group(function () {
            Route::get('/sps/users/profile', 'profile')->name('chl.users.profile');
            Route::post('/sps/users/profile/update', 'update')->name('chl.users.profile.update');
            Route::post('/sps/users/profile/password/update', 'updatePassword')->name('chl.users.profile.password.update');
        });
    });

    Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin', 'prevent-back-history', 'auth.session'])->group(function () {
        // Route::controller(DashboardController::class)->group(function () {
        //     Route::get('/sps/admin/dashboard', 'dashboard')->name('chl.admin.dashboard');
        // });
        Route::get('/auth/ms-signup', [AuthAdminController::class, 'msSignUp'])->name('auth.ms.signup');
        Route::post('/signup/ms/store', [AdminUserController::class, 'msStore'])->name('auth.ms.store');

        Route::controller(AdminTaskController::class)->group(function () {
            Route::get('/chl/admin/tasks', 'index')->name('chl.admin.tasks.index');
            Route::get('/chl/admin/tasks/create', 'create')->name('chl.admin.tasks.create');
            Route::post('/chl/admin/tasks/store', 'store')->name('chl.admin.tasks.store');
            Route::get('/chl/admin/tasks/{id}/edit', 'edit')->name('chl.admin.tasks.edit');
            Route::post('/update-task-item', 'update')->name('chl.admin.tasks.update');
            // Route::delete('/chl/admin/tasks/{id}', 'destroy')->name('chl.admin.tasks.destroy');
            Route::delete('/chl/admin/tasks/delete/{id}',  'delete')->name('chl.admin.tasks.delete');

            Route::post('/chl/admin/tasks/copy-to-lead', 'copyToLead')
                ->name('chl.admin.tasks.copyToLead');

            Route::get('/tasks/admin/orders/{id}/switch', 'switch')->name('tasks.admin.orders.switch');


            // Toggle complete/incomplete
            Route::patch('/chl/admin/tasks/{task}/toggle', 'toggle')->name('chl.admin.tasks.toggle');
        });

        Route::controller(ReportController::class)->group(function () {
            Route::get('/chl/admin/tasks/report', 'index')->name('chl.admin.tasks.report');
            Route::get('/chl/admin/tasks/report/list', 'list')->name('chl.admin.tasks.report.list');
            Route::get('/chl/admin/tasks/show/exp/{id}', 'showPdf')->name('chl.admin.tasks.show.exp');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('setting/categories', 'index')->name('setting.category.index');
            Route::get('setting/categories/list', 'list')->name('setting.category.list');
            Route::get('setting/categories/create', 'create')->name('setting.category.create');
            Route::post('setting/categories/store', 'store')->name('setting.category.store');
            Route::get('setting/categories/{id}/edit', 'edit')->name('setting.category.edit');
            Route::put('setting/categories/{id}', 'update')->name('setting.category.update');
            // Route::get('/sps/category/visitor/mv/get/{id}', 'getVisitorResultView')->name('chl.operator.visitor.mv.get');
            Route::delete('setting/categories/{id}', 'delete')->name('setting.category.delete');
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

        //Permisssion venue event
        Route::controller(PermissionVenueEventController::class)->group(function () {
            Route::get('/setting/per_venue_event', 'index')->name('setting.per_venue_event');
            Route::get('/setting/per_venue_event/list', 'list')->name('setting.per_venue_event.list');
            Route::get('/setting/per_venue_event/get/{id}', 'get')->name('setting.per_venue_event.get');
            Route::post('setting/per_venue_event/update', 'update')->name('setting.per_venue_event.update');
            Route::delete('/setting/per_venue_event/delete/{id}', 'delete')->name('setting.per_venue_event.delete');
            Route::post('/setting/per_venue_event/store', 'store')->name('setting.per_venue_event.store');
            Route::get('/setting/per_venue_event/mv/get/{id}', 'getEventView')->name('setting.per_venue_event.get.mv');
            // Route::get('/cms/setting/event/file/{file}', 'getPrivateFile')->name('cms.setting.event.file');
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
            Route::get('/sps/admin/users/profile', 'profile')->name('chl.admin.users.profile');
            Route::post('/sps/admin/users/profile/update', 'update')->name('chl.admin.users.profile.update');
            Route::post('/sps/admin/users/profile/password/update', 'updatePassword')->name('chl.admin.users.profile.password.update');
            // Route::get('/sps/admin/users/invite-user', 'showForm')->name('chl.admin.users.invite.form');
            // Route::post('/admin/invite-user', 'sendInvite')->name('chl.admin.users.invite.send');
        });

        // General Settings MANAGEMENT ******************************************************************** Admin All Route
        // company Routes
        Route::controller(CompanyController::class)->group(function () {
            Route::get('/general/settings/company/', 'index')->name('general.settings.company');
            Route::post('/general/settings/update', 'update')->name('general.settings.update');
        });
    });

    Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:VenueAdmin', 'prevent-back-history', 'auth.session'])->group(function () {
        // Route::controller(VenueAdminDashboardController::class)->group(function () {
        //     Route::get('/sps/venue-admin/dashboard', 'dashboard')->name('chl.venue.admin.dashboard');
        // });

        Route::controller(VenueAdminReportController::class)->group(function () {
            Route::get('/chl/venue-admin/tasks/report', 'index')->name('chl.venue.admin.tasks.report');
            Route::get('/chl/venue-admin/tasks/report/list', 'list')->name('chl.venue.admin.tasks.report.list');
            Route::get('/chl/venue-admin/tasks/show/pdf/{id}', 'showPdf')->name('chl.venue.admin.tasks.show.pdf');
            Route::get('/chl/venue-admin/tasks/show/exp/{id}', 'showExp')->name('chl.venue.admin.tasks.show.exp');
        });

        Route::controller(VenueAdminTaskController::class)->group(function () {
            Route::get('/chl/venue-admin/tasks', 'index')->name('chl.venue.admin.tasks.index');
            Route::get('/chl/venue-admin/tasks/create', 'create')->name('chl.venue.admin.tasks.create');
            Route::post('/chl/venue-admin/tasks/store', 'store')->name('chl.venue.admin.tasks.store');
            Route::get('/chl/venue-admin/tasks/{id}/edit', 'edit')->name('chl.venue.admin.tasks.edit');
            Route::put('/chl/venue-admin/tasks/{id}', 'update')->name('chl.venue.admin.tasks.update');
            Route::delete('/chl/venue-admin/tasks/{id}', 'destroy')->name('chl.venue.admin.tasks.destroy');

            Route::get('/chl/venue-admin/orders/{id}/switch', 'switch')->name('chl.venue.admin.orders.switch');
            Route::get('/chl/venue-admin/venue/{id}/switch', 'venueSwitch')->name('chl.venue.admin.venue.switch');

            Route::post('/chl/venue-admin/tasks/comment', 'saveComment')->name('chl.venue.admin.tasks.comment');
            Route::delete('tasks/comment/delete', 'deleteComment')->name('chl.admin.tasks.comment.delete');


            // Toggle complete/incomplete
            Route::post('/chl/venue-admin/tasks/{task}/toggle', 'toggle')->name('chl.venue.admin.tasks.toggle');

            Route::get('/chl/venue-admin/tasks/export/pdf/{id}', 'exportPdf')->name('chl.venue.admin.tasks.export.pdf');
            // Route::get('/chl/pdf/download', 'preview')->name('chl.pdf.download');
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

        // Admin pick
        Route::get('/chl/admin/pick', function () {
            return view('/chl/admin/pick');
        })
            ->name('chl.admin.pick')
            ->middleware('role:SuperAdmin');

        Route::post('/chl/admin/events/switch', [AdminTaskController::class, 'pickEvent'])
            ->name('chl.admin.event.switch')
            ->middleware('role:SuperAdmin');

        // Venue Admin pick
        Route::get('/chl/venue-admin/pick', function () {
            return view('/chl/venue-admin/pick');
        })
            ->name('chl.venue.admin.pick')
            ->middleware('role:VenueAdmin');

        Route::post('/chl/venue-admin/events/switch', [VenueAdminTaskController::class, 'pickEvent'])
            ->name('chl.venue.admin.event.switch')
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

        Route::controller(ActivityAuditController::class)->group(function () {
            Route::get('/sec/audit', 'index')->name('sec.audit');
            Route::get('/sec/audit/list', 'list')->name('sec.audit.list');
        });
    }); //
    // Route::get('/run-migration', function () {
    //     Artisan::call('optimize:clear');

    //     Artisan::call('migrate:refresh --seed');
    //     return "Migration executed successfully";
    // });
});
