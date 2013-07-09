<?php
/**
 * @file
 * Docker Remote API
 */

/**
 * Class DockerClient
 */
class DockerClient extends CurlClient {

  /**
   * DockerRemoteAPI Constructor
   *
   * @param $host string IP or hostname of the remote api server
   * @param $port int Port number of the Docker deamon
   */
  public function __construct($host, $port) {
    parent::__construct($host, $port);
  }

  /**
   * List containers
   *
   * @param bool $all 1/True/true or 0/False/false, Show all containers. Only running containers are shown by default
   * @param int $limit Show limit last created containers, include non-running ones.
   * @param null $since Show only containers created since Id, include non-running ones.
   * @param null $before Show only containers created before Id, include non-running ones.
   * @param bool $size 1/True/true or 0/False/false, Show the containers sizes
   * @return mixed
   */
  public function containers($all = FALSE, $limit = -1, $since = NULL, $before = NULL, $size = FALSE) {
    $params = array(
      'all' => $all,
      'limit' => $limit,
      'since' => $since,
      'before' => $before,
      'size' => $size
    );
    return $this->get('/containers/json', $params);
  }

  /**
   * Create new container
   *
   * @param string $hostname
   * @param string $user
   * @param int $memory
   * @param int $memory_swap
   * @param bool $attach_stdin
   * @param bool $attach_stdout
   * @param bool $attach_stderr
   * @param array $port_specs
   * @param bool $tty
   * @param bool $open_stdin
   * @param bool $stdin_once
   * @param null $env
   * @param array $cmd
   * @param null $dns
   * @param string $image
   * @param array $volumes
   * @param string $volumes_from
   * @return mixed
   */
  public function create_container($hostname = '', $user = '', $memory = 0, $memory_swap = 0, $attach_stdin = FALSE,
                                   $attach_stdout = TRUE, $attach_stderr = TRUE, $port_specs = array(), $tty = FALSE,
                                   $open_stdin = FALSE, $stdin_once = FALSE, $env = NULL, $cmd = array(), $dns = NULL,
                                   $image = 'base', $volumes = array(), $volumes_from = '') {
    $params = array(
      'Hostname' => $hostname,
      'User' => $user,
      'Memory' => $memory,
      'MemorySwap' => $memory_swap,
      'AttachStdin' => $attach_stdin,
      'AttachStdout' => $attach_stdout,
      'AttachStderr' => $attach_stderr,
      'PortSpecs' => is_array($port_specs) && count($port_specs) > 0 ? $port_specs : NULL,
      'Tty' => $tty,
      'OpenStdin' => $open_stdin,
      'StdinOnce' => $stdin_once,
      'ENV' => $env,
      'Cmd' => $cmd,
      'Dns' => is_array($dns) && count($dns) > 0 ? $dns : NULL,
      'Image' => $image,
      'Volumes' => is_array($volumes) && count($volumes) > 0 ? (object)$volumes : (object)array(),
      'VolumesFrom' => $volumes_from
    );
    return $this->post('/containers/json', $params);
  }

  /**
   * Return low-level information on the container id
   *
   * @param $id string Container id
   * @return mixed
   */
  public function inspect($id) {
    return $this->get('/containers/' . $id . '/json');
  }

  /**
   * Inspect changes on container id ‘s filesystem
   *
   * @param $id string Container id
   * @return mixed
   */
  public function changes($id) {
    return $this->get('/containers/' . $id . '/changes');
  }

  /**
   * Export the contents of container id
   *
   * @param $id string Container id
   * @return mixed
   */
  public function export($id) {
    return $this->get('/containers/' . $id . '/export');
  }

  /**
   * Start the container id
   *
   * @param $id string Container id
   * @param $host_config array the container’s host configuration (optional)
   * @return mixed
   */
  public function start($id, $host_config = NULL) {
    return $this->post('/containers/' . $id . '/start', $host_config);
  }

  /**
   * Stop the container id
   *
   * @param $id string Container id
   * @param int $time number of seconds to wait before stopping the container
   * @return mixed
   */
  public function stop($id, $time = 2) {
    return $this->post('/containers/' . $id . '/stop', array('t' => $time));
  }

  /**
   * Restart the container id
   *
   * @param $id string Container id
   * @param int $time number of seconds to wait before restarting the container
   * @return mixed
   */
  public function restart($id, $time = 2) {
    return $this->post('/containers/' . $id . '/restart', array('t' => $time));
  }

  /**
   * Kill the container id
   *
   * @param $id string Container id
   * @return mixed
   */
  public function kill($id) {
    return $this->post('/containers/' . $id . '/kill');
  }

  /**
   * Attach to the container id
   *
   * @param $id string Container id
   * @param bool $logs return logs. Default false
   * @param bool $stream return stream. Default false
   * @param bool $stdin attach to stdin. Default false
   * @param bool $stdout attach to stdout. Default false
   * @param bool $stderr attach to stderr. Default false
   * @return mixed
   */
  public function attach($id, $logs = FALSE, $stream = FALSE, $stdin = FALSE, $stdout = FALSE, $stderr = FALSE) {
    $params = array(
      'logs' => $logs,
      'stream' => $stream,
      'stdin' => $stdin,
      'stdout' => $stdout,
      'stderr' => $stderr
    );
    return $this->post('/containers/' . $id . '/attach', $params);
  }

  /**
   * Block until container id stops, then returns the exit code
   *
   * @param $id string Container id
   * @return mixed
   */
  public function wait($id) {
    return $this->post('/containers/' . $id . '/wait');
  }

  /**
   * Remove the container id from the filesystem
   *
   * @param $id $id string Container id
   * @param $remove_volumes bool Remove the volumes associated to the container. Default false
   * @return mixed
   */
  public function remove_container($id, $remove_volumes = FALSE) {
    return $this->delete('/containers/' . $id, array('v' => $remove_volumes));
  }

  /**
   * List images
   *
   * @param bool $all Show all containers. Only running containers are shown by default
   * @param string $format json or viz
   * @return mixed
   */
  public function images($all = FALSE, $format = 'json') {
    return $this->get('/images/' . $format, array('all' => $all));
  }

  /**
   * Create an image, either by pull it from the registry or by importing it
   *
   * @param string $from_image name of the image to pull
   * @param string $from_source source to import, - means stdin
   * @param string $repo repository
   * @param string $tag tag
   * @param string $registry the registry to pull from
   * @return mixed
   */
  public function create_image($from_image = NULL, $from_source = NULL, $repo = NULL, $tag = NULL, $registry = NULL) {
    $params = array(
      'fromImage' => $from_image,
      'fromSrc' => $from_source,
      'repo' => $repo,
      'tag' => $tag,
      'registry' => $registry
    );
    return $this->post('/images/create', $params);
  }

  /**
   * Insert a file from url in the image name at path
   *
   * @param string $name image name
   * @param string $path destination path
   * @param string $url source
   * @return mixed
   */
  public function insert_image($name, $path, $url) {
    $params = array(
      'path' => $path,
      'url' => $url
    );
    return $this->post('/images/' . $name . '/insert', $params);
  }

  /**
   * Return low-level information on the image name
   *
   * @param $name image name
   * @return mixed
   */
  public function inspect_image($name) {
    return $this->get('/images/' . $name . '/json');
  }

  /**
   * Return the history of the image name
   *
   * @param $name image name
   * @return mixed
   */
  public function history($name) {
    return $this->get('/images/' . $name . '/history');
  }

  /**
   * Push the image name on the registry
   *
   * @param $name image name
   * @param string $registry
   * @return mixed
   */
  public function push($name, $registry = NULL) {
    return $this->post('/images/' . $name . '/history', array('registry' => $registry));
  }

  /**
   * @param $name image name
   * @param string $repo The repository to tag in
   * @param bool $force default false
   * @return mixed
   */
  public function tag($name, $repo = NULL, $force = FALSE) {
    $params = array(
      'repo' => $repo,
      'force' => $force
    );
    return $this->post('/images/' . $name . '/tag', $params);
  }

  /**
   * Remove the image name from the filesystem
   *
   * @param $name image name
   * @return mixed
   */
  public function remove_image($name) {
    return $this->delete('/images/' . $name);
  }

  /**
   * Search for an image in the docker index
   *
   * @param string $term search term
   * @return mixed
   */
  public function search($term) {
    return $this->get('/images/search', array('term' => $term));
  }

  /**
   * Build an image from Dockerfile via stdin
   *
   * @param string $tag tag to be applied to the resulting image in case of success
   * @return mixed
   */
  public function build($tag) {
    return $this->post('/build', array('tag' => $tag));
  }

  /**
   * Get the default username and email
   *
   * @param string $username
   * @param $password
   * @param $email
   * @return mixed
   */
  public function auth($username, $password, $email) {
    $params = array(
      'username' => $username,
      'password' => $password,
      'email' => $email
    );
    return $this->post('/auth', $params);
  }

  /**
   * Display system-wide information
   *
   * @return mixed
   */
  public function info() {
    return $this->get('/info');
  }

  /**
   * Show the docker version information
   *
   * @return mixed
   */
  public function version() {
    return $this->get('/version');
  }

  /**
   * @param string $container source container
   * @param string $repo repository
   * @param string $tag tag
   * @param string $message commit message
   * @param string $author author (eg. “John Hannibal Smith <hannibal@a-team.com>”)
   * @param array $run config automatically applied when the image is run. (ex: {“Cmd”: [“cat”, “/world”], “PortSpecs”:[“22”]})
   * @return mixed
   */
  public function commit($container, $repo, $tag, $message, $author, $run) {
    $params = array(
      'container' => $container,
      'repo' => $repo,
      'tag' => $tag,
      'message' => $message,
      'author' => $author,
      'run' => $run
    );
    return $this->post('/commit', $params);
  }
}

/**
 * Class CurlClient
 */
class CurlClient {
  /**
   * @var string Remote API protocol
   */
  private $protocol;

  /**
   * @var string Remote API IP or hostname
   */
  private $host;

  /**
   * @var int Remote API port
   */
  private $port;

  /**
   * @var string Base URI
   */
  private $base_uri;

  /**
   * DockerRestClient constructor
   *
   * @param $host string IP or hostname of the remote api server
   * @param $port int Port number of the Docker deamon
   * @param $protocol string http or https
   */
  public function __construct($host, $port, $protocol = 'http') {
    $this->host = $host;
    $this->port = $port;
    $this->protocol = $protocol;
    $this->base_uri = $protocol . '://' . $this->host . ':' . $this->port;
  }

  /**
   * GET
   *
   * @param $uri
   * @param array $get key/value array of GET vars
   * @param array $options CURL options
   * @return mixed
   */
  public function get($uri, array $get = NULL, array $options = array()) {
    $uri = $this->base_uri . $uri;
    $defaults = array(
      CURLOPT_URL => $uri . (strpos($uri, '?') === FALSE ? '?' : '') . http_build_query($get),
      CURLOPT_HEADER => 0,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_TIMEOUT => 4
    );
    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if (!$result = curl_exec($ch)) {
      trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return json_decode($result);
  }

  /**
   * POST
   *
   * @param $uri
   * @param array $post key/value array of POST vars
   * @param array $options CURL options
   * @return mixed
   */
  public function post($uri, array $post = NULL, array $options = array()) {
    $uri = $this->base_uri . $uri;
    $defaults = array(
      CURLOPT_POST => 1,
      CURLOPT_HEADER => 0,
      CURLOPT_URL => $uri,
      CURLOPT_FRESH_CONNECT => 1,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_FORBID_REUSE => 1,
      CURLOPT_TIMEOUT => 4,
      CURLOPT_POSTFIELDS => http_build_query($post)
    );
    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if ( ! $result = curl_exec($ch)) {
      trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return json_decode($result);
  }

  /**
   * DELETE
   *
   * @param $uri
   * @param array $get
   * @param array $options
   * @return mixed
   */
  public function delete($uri, array $get = NULL, array $options = array()) {
    $options += array(CURLOPT_CUSTOMREQUEST => "DELETE");
    return $this->get($uri, $get, $options);
  }
}