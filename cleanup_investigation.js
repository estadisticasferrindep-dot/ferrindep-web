const fs = require('fs');

const filesToDelete = [
    'verify_controller.js',
    'verify_ferrindep.js',
    'deploy_v2_strategy.js',
    'deploy_probe.js',
    'deploy_revert.js',
    'deploy_minimal.js',
    'deploy_login_fixed.js',
    'deploy_final.js',
    'deploy_debug_overwrite.js',
    'upload_opcache.js',
    'upload_cleaner.js',
    'upload_v2.js',
    'reset_opcache.php',
    'check_root.js',
    'list_ferrindep_views.js',
    'clean_views_ftp.js',
    'login_minimal.blade.php',
    'login_debug_original.blade.php',
    'login_debug_v2.blade.php',
    'login_v2.blade.php',
    'MisComprasController_verified.php',
    'MisComprasController_ferrindep.php',
    'test_verification_root.txt',
    'login_remote.blade.php',
    'index_remote.php',
    'MisComprasController_remote.php',
    'clear_cache_proxy.php',
    'list_cache.js',
    'clear_cache.js',
    'check_paths.js',
    'cleaner_v2.php'
];

filesToDelete.forEach(file => {
    try {
        if (fs.existsSync(file)) {
            fs.unlinkSync(file);
            console.log(`Deleted: ${file}`);
        }
    } catch (err) {
        console.error(`Error deleting ${file}: ${err.message}`);
    }
});
