<?php
function compute_path($filepath) {
    $computed_file_path = str_replace('\\', '/', $filepath);
    if (strpos($computed_file_path, '../') != false) {
       $substr_count = substr_count($computed_file_path, '../');
        if ($substr_count > 0) {
            $computed_file_path = str_replace('../', '', $computed_file_path);
            // $computed_file_path = substr(trim($computed_file_path), 0, count(trim($computed_file_path)) - 2);
            for ($i = $substr_count; $i > 0; $i--) {
                $computed_file_path = substr($computed_file_path, 0, strrpos($computed_file_path, '/', -1));
            }
        }
    }
    return $computed_file_path.'/';
}
function compute_web_path($abspath) {
    return str_replace($_SERVER['DOCUMENT_ROOT'], 'http://'.$_SERVER['HTTP_HOST'], $abspath);
}
define('TBL_PRIFIX', 'alumni_');
define('ALUM_ABS_PATH', compute_path(__DIR__.'../'));
define('ALUM_WS_HOME', compute_web_path(ALUM_ABS_PATH));
define('ALUM_LEAVE_DOCUMENTS', 'leave-documents/');
define('CURRENT_DATE', date("Y/m/d"));
define('CURRENT_DT', date("Y-m-d h:i:s"));
define('ALUM_EVENTS', 'events/');
define('ALUM_GALLERY', 'gallery/');
define('ALUM_ANNOUN', 'announcement/');
define('ALUM_PROFILE', 'profile-images/');
define('ALUM_TEMPLATES', 'templates/');
define('ALUM_ALBUM', 'album/');
define('ALUM_ACHIEVEMENT', 'achievement/');
define('ALUM_ETEMPLATES', 'e-templates/');
define('ALUM_CONFIG', 'config/');
define('ALUM_INCLUDES', ALUM_ABS_PATH.'includes/');
define('ALUM_E_TEMP', ALUM_ABS_PATH.'e-templates/');
define('ALUM_WS_UPLOADS', ALUM_WS_HOME.'uploads/');
define('ALUM_WS_IMAGES', ALUM_WS_HOME.'images/');
define('ALUM_COMMON_UPLOADS','uploads/');
define('ALUM_AJAX', ALUM_WS_HOME.'ajax/');
define('ALUM_CSS', ALUM_WS_HOME.'css/');
define('ALUM_SCRIPT', ALUM_WS_HOME.'script/');
define('ALUM_JS', ALUM_WS_HOME.'js/');
define('ALUM_COMPANY', 'company/');
define('ALUM_RUNION', 'reunion/');
?>