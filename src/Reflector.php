<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper;

use function array_fill_keys;
use function array_keys;
use function array_merge;
use JetBrains\PHPStormStub\PhpStormStubsMap;

/**
 * @private
 */
final class Reflector
{
    private const MISSING_CLASSES = [
        // https://github.com/JetBrains/phpstorm-stubs/pull/600
        'UV',

        // https://github.com/JetBrains/phpstorm-stubs/pull/596
        'Crypto\Cipher',
        'Crypto\CipherException',
        'Crypto\Hash',
        'Crypto\HashException',
        'Crypto\MAC',
        'Crypto\MACException',
        'Crypto\HMAC',
        'Crypto\CMAC',
        'Crypto\KDF',
        'Crypto\KDFException',
        'Crypto\PBKDF2',
        'Crypto\PBKDF2Exception',
        'Crypto\Base64',
        'Crypto\Base64Exception',
        'Crypto\Rand',
        'Crypto\RandException',

        // https://github.com/JetBrains/phpstorm-stubs/pull/594
        'parallel\Channel',
        'parallel\Channel\Error',
        'parallel\Channel\Error\Closed',
        'parallel\Channel\Error\Existence',
        'parallel\Channel\Error\IllegalValue',
        'parallel\Error',
        'parallel\Events',
        'parallel\Events\Error',
        'parallel\Events\Error\Existence',
        'parallel\Events\Error\Timeout',
        'parallel\Events\Event',
        'parallel\Events\Event\Type',
        'parallel\Events\Input',
        'parallel\Events\Input\Error',
        'parallel\Events\Input\Error\Existence',
        'parallel\Events\Input\Error\IllegalValue',
        'parallel\Future',
        'parallel\Future\Error',
        'parallel\Future\Error\Cancelled',
        'parallel\Future\Error\Foreign',
        'parallel\Future\Error\Killed',
        'parallel\Runtime',
        'parallel\Runtime\Bootstrap',
        'parallel\Runtime\Error',
        'parallel\Runtime\Error\Bootstrap',
        'parallel\Runtime\Error\Closed',
        'parallel\Runtime\Error\IllegalFunction',
        'parallel\Runtime\Error\IllegalInstruction',
        'parallel\Runtime\Error\IllegalParameter',
        'parallel\Runtime\Error\IllegalReturn',
    ];

    private const MISSING_FUNCTIONS = [
        // https://github.com/JetBrains/phpstorm-stubs/pull/613
        'sapi_windows_vt100_support',

        // https://github.com/JetBrains/phpstorm-stubs/pull/600
        'uv_unref',
        'uv_last_error',
        'uv_err_name',
        'uv_strerror',
        'uv_update_time',
        'uv_ref',
        'uv_run',
        'uv_run_once',
        'uv_loop_delete',
        'uv_now',
        'uv_tcp_bind',
        'uv_tcp_bind6',
        'uv_write',
        'uv_write2',
        'uv_tcp_nodelay',
        'uv_accept',
        'uv_shutdown',
        'uv_close',
        'uv_read_start',
        'uv_read2_start',
        'uv_read_stop',
        'uv_ip4_addr',
        'uv_ip6_addr',
        'uv_listen',
        'uv_tcp_connect',
        'uv_tcp_connect6',
        'uv_timer_init',
        'uv_timer_start',
        'uv_timer_stop',
        'uv_timer_again',
        'uv_timer_set_repeat',
        'uv_timer_get_repeat',
        'uv_idle_init',
        'uv_idle_start',
        'uv_idle_stop',
        'uv_getaddrinfo',
        'uv_tcp_init',
        'uv_default_loop',
        'uv_loop_new',
        'uv_udp_init',
        'uv_udp_bind',
        'uv_udp_bind6',
        'uv_udp_recv_start',
        'uv_udp_recv_stop',
        'uv_udp_set_membership',
        'uv_udp_set_multicast_loop',
        'uv_udp_set_multicast_ttl',
        'uv_udp_set_broadcast',
        'uv_udp_send',
        'uv_udp_send6',
        'uv_is_active',
        'uv_is_readable',
        'uv_is_writable',
        'uv_walk',
        'uv_guess_handle',
        'uv_handle_type',
        'uv_pipe_init',
        'uv_pipe_open',
        'uv_pipe_bind',
        'uv_pipe_connect',
        'uv_pipe_pending_instances',
        'uv_ares_init_options',
        'ares_gethostbyname',
        'uv_loadavg',
        'uv_uptime',
        'uv_get_free_memory',
        'uv_get_total_memory',
        'uv_hrtime',
        'uv_exepath',
        'uv_cpu_info',
        'uv_interface_addresses',
        'uv_stdio_new',
        'uv_spawn',
        'uv_process_kill',
        'uv_kill',
        'uv_chdir',
        'uv_rwlock_init',
        'uv_rwlock_rdlock',
        'uv_rwlock_tryrdlock',
        'uv_rwlock_rdunlock',
        'uv_rwlock_wrlock',
        'uv_rwlock_trywrlock',
        'uv_rwlock_wrunlock',
        'uv_mutex_init',
        'uv_mutex_lock',
        'uv_mutex_trylock',
        'uv_sem_init',
        'uv_sem_post',
        'uv_sem_wait',
        'uv_sem_trywait',
        'uv_prepare_init',
        'uv_prepare_start',
        'uv_prepare_stop',
        'uv_check_init',
        'uv_check_start',
        'uv_check_stop',
        'uv_async_init',
        'uv_async_send',
        'uv_queue_work',
        'uv_fs_open',
        'uv_fs_read',
        'uv_fs_close',
        'uv_fs_write',
        'uv_fs_fsync',
        'uv_fs_fdatasync',
        'uv_fs_ftruncate',
        'uv_fs_mkdir',
        'uv_fs_rmdir',
        'uv_fs_unlink',
        'uv_fs_rename',
        'uv_fs_utime',
        'uv_fs_futime',
        'uv_fs_chmod',
        'uv_fs_fchmod',
        'uv_fs_chown',
        'uv_fs_fchown',
        'uv_fs_link',
        'uv_fs_symlink',
        'uv_fs_readlink',
        'uv_fs_stat',
        'uv_fs_lstat',
        'uv_fs_fstat',
        'uv_fs_readdir',
        'uv_fs_sendfile',
        'uv_fs_event_init',
        'uv_tty_init',
        'uv_tty_get_winsize',
        'uv_tty_set_mode',
        'uv_tty_reset_mode',
        'uv_tcp_getsockname',
        'uv_tcp_getpeername',
        'uv_udp_getsockname',
        'uv_resident_set_memory',
        'uv_ip4_name',
        'uv_ip6_name',
        'uv_poll_init',
        'uv_poll_start',
        'uv_poll_stop',
        'uv_fs_poll_init',
        'uv_fs_poll_start',
        'uv_fs_poll_stop',
        'uv_stop',
        'uv_signal_stop',

        // https://github.com/JetBrains/phpstorm-stubs/pull/594
        'parallel\bootstrap',
        'parallel\run',

        // https://youtrack.jetbrains.com/issue/WI-47038
        'pcov\collect',
        'pcov\start',
        'pcov\stop',
        'pcov\clear',
        'pcov\waiting',
        'pcov\memory',
    ];

    private const MISSING_CONSTANTS = [
        'STDIN',
        'STDOUT',
        'STDERR',

        // https://youtrack.jetbrains.com/issue/WI-47038
        'pcov\all',
        'pcov\inclusive',
        'pcov\exclusive',
    ];

    private static $CLASSES;

    private static $FUNCTIONS;

    private static $CONSTANTS;

    /**
     * @param array<string,string>|null $symbols
     * @param array<string,string>      $source
     * @param string[]                  $missingSymbols
     */
    private static function initSymbolList(?array &$symbols, array $source, array $missingSymbols): void
    {
        if (null !== $symbols) {
            return;
        }

        $symbols = array_fill_keys(
            array_merge(
                array_keys($source),
                $missingSymbols
            ),
            true
        );
    }

    public function __construct()
    {
        self::initSymbolList(self::$CLASSES, PhpStormStubsMap::CLASSES, self::MISSING_CLASSES);
        self::initSymbolList(self::$FUNCTIONS, PhpStormStubsMap::FUNCTIONS, self::MISSING_FUNCTIONS);
        self::initSymbolList(self::$CONSTANTS, PhpStormStubsMap::CONSTANTS, self::MISSING_CONSTANTS);
    }

    public function isClassInternal(string $name): bool
    {
        return isset(self::$CLASSES[$name]);
    }

    public function isFunctionInternal(string $name): bool
    {
        return isset(self::$FUNCTIONS[$name]);
    }

    public function isConstantInternal(string $name): bool
    {
        return isset(self::$CONSTANTS[$name]);
    }
}
