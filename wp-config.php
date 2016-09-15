<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'philinkz_db');

/** Имя пользователя MySQL */
define('DB_USER', 'philinkz_elnur');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'cmsJoomla156;;');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '~^P+MNN|LKcf.4AJ8ZyQn6T{%-PlR-If]EU#F)~o[PuYXd U:[Xg!OSX);91rh}Q');
define('SECURE_AUTH_KEY',  'qqq9+(+$0V)pw{}v<#8r<M,]QOdEaj0cq+k:A*ynI]*:`ij~2eIoY]Z,3+*kN6u ');
define('LOGGED_IN_KEY',    'pjay_yx.,my2rSfa%20H{HwJRN^0:{ZLCl rP1.4vp;Y}8QzIc4@,m}?89kH>ZgN');
define('NONCE_KEY',        'AfVEM;gPfXox7>C_[)CkVajE{o0{c3e^K0I80V1W4_O+oO)lFg5F{0:7>X):QHyN');
define('AUTH_SALT',        'DWnIl0PGg7>Wz+/c |-FYBqv;{orAbIlR}NblI-4MRo~ 1GqG[M=m7eo:wovu}>d');
define('SECURE_AUTH_SALT', '40 h;,F;QB[r-SxkvS6EEV6oDVkJxA*[X7)MO+,/H>Rn<?#{D BAD+N*JqiDZnz?');
define('LOGGED_IN_SALT',   'IU; ?nKp5GQ9N!7U6+{j9[glQLXh;hMj!uZS_SFX~v1Y6~UfUZF=fS3yB[^-rI;Y');
define('NONCE_SALT',       'n*TAQiXA<]OxZ~;jWigCd[Bv7ZPsgEvp+B%w3e|mfx0:.qeQEz1#/6rf>g!Sv3OV');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
