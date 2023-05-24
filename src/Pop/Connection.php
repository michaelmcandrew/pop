<?php

namespace Civi\Pop;

/**
 * Represent the active connection to CiviCRM. Quick and dirty placeholder.
 *
 * Built on a pipe-based onnection.
 * @link https://docs.civicrm.org/dev/en/latest/framework/pipe/
 */
class Connection {

  /**
   * List of options used to establish the connection.
   *
   * @var array
   */
  private static $connectOptions;

  /**
   * The active connection
   *
   * @var \Civi\Pipe\BasicPipeClient
   */
  private static $pipe;

  /**
   * The remaining number of requests to allow in this session.
   *
   * @var int
   */
  private static $sessionLimit;

  /**
   * @param string $command
   *   The command which opens the CiviCRM pipe.
   * @param int $bufferSize
   *   The size of the largest request/response document.
   * @param int $sessionLimit
   *   The maximum number of requests to send within a given session.
   *   If exceeded, then we will disconnect/reconnect with a new session.
   */
  public static function connect(string $command = 'cv pipe vjt', int $bufferSize = 100 * 1024 * 1024, int $sessionLimit = 5000): void {
    static::$connectOptions = func_get_args();
    static::$sessionLimit = $sessionLimit;
    static::$pipe = new \Civi\Pipe\BasicPipeClient($command, $bufferSize);
    static::options(['apiCheckPermissions' => FALSE, 'bufferSize' => $bufferSize]);
  }

  /**
   * Send an APIv3 request.
   */
  public static function api3(string $entity, string $action, array $params = []): array {
    if (--static::$sessionLimit < 0) {
      static::connect(...static::$connectOptions);
    }
    return static::$pipe->call('api3', [$entity, $action, $params]);
  }

  /**
   * Send an APIv3 request.
   */
  public static function api4(string $entity, string $action, array $params = []): array {
    if (--static::$sessionLimit < 0) {
      static::connect(...static::$connectOptions);
    }
    return static::$pipe->call('api4', [$entity, $action, $params]);
  }

  public static function options(array $options): void {
    if (--static::$sessionLimit < 0) {
      static::connect(...static::$connectOptions);
    }
    static::$pipe->call('options', $options);
  }

}
