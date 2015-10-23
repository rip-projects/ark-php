<?php
/**
 * Based on PHP Thread Library, modified for Linux environment optimizations
 */

/**
 * Start of original PHP Thread Library headers
 */

/**
 * PHP Thread Library
 *
 * Requires PHP 5 minimum. On Windows, PHP 5.2.1 or later is recommended for optimal performance.
 *
 * Initially developed as a Windows substitute for fork() and the Semaphore extension, this library is now platform-independent.
 *
 * <b>WARNING</b>: The __FILE__ constant is unreliable within child threads. See {@link Thread::FILE()} for a thread-safe replacement.
 *
 * @author Unsigned Code Labs
 * @copyright Copyright (c) 2011 Unsigned Code Labs
 * @link http://phpthreadlib.sourceforge.net/ PHP Thread Library on SourceForge
 * @license http://phpthreadlib.sourceforge.net/docs/license.txt FreeBSD License
 * @example ../examples/example.php A simple thread demonstration
 * @package phpthreadlib
 *
 */






/**
 * The current library version.
 *
 * The format is MajorVersion.MinorVersion.SVNRevision
 *
 * @internal Devs: Be sure to update the SVN revision value before committing.
 */
define('PHPTHREADLIB_VERSION', '1.0.3');

/**
 * End of original PHP Thread Library headers
 */

/**
 * A handle used to communicate between threads.
 *
 * <b>WARNING</b>: The __FILE__ constant is unreliable within child threads. See {@link Thread::FILE()} for a thread-safe replacement.
 *
 * @example ../examples/example.php A simple thread demonstration
 * @package phpthreadlib
 */
class ThreadHandle
{
	/**
	 * No unread data (buffer is empty).
	 */
	const READY = 0;
	/**
	 * Unread data is present in the buffer, waiting to be read.
	 */
	const UNREAD = 1;
	/**
	 * The link has been disconnected, no more data is available.
	 */
	const DISCONNECTED = 2;
	/**
	 * An error occurred while attempting to determine the link status.
	 */
	const ERROR = 3;
	/**
	 * A "Resource temporarily unavailable" occurred.
	 * socket_last_error is "11" on my system
	 */
	const UNAVAILABLE = 11;
	/**
	 * Default time for "usleep"
	 */
	const USLEEP = 1000;
	
	
	
	
	/**#@+
	 * @ignore
	 */
	protected $sock, $port, $id;
	/**#@-*/
	
	
	
	/**
	 * @ignore
	 */
	protected function __construct($sockethandle, $port, $id)
	{
		$this->sock = $sockethandle;
		$this->port = $port;
		$this->id = $id;
	}
	
	
	
	
	
	/**
	 * Reports communication link status.
	 *
	 * Note that it is not possible to tell if the connection has been closed until all available data has been read.
	 *
	 * @return int {@link ThreadHandle::READY}, {@link ThreadHandle::UNREAD}, {@link ThreadHandle::DISCONNECTED}, or {@link ThreadHandle::ERROR}.
	 */
	public function status()
	{
		socket_set_nonblock($this->sock);
		/**
		 * Waits until socket_last_error is NOT ThreadHandle::UNAVAILABLE
		 */
		while (	FALSE === ($blah = @socket_recv($this->sock, $buf, 1, MSG_PEEK)) &&
				socket_last_error() == ThreadHandle::UNAVAILABLE
				)
			usleep(ThreadHandle::USLEEP);
		socket_clear_error();
		if (FALSE === ($blah = @socket_recv($this->sock, $buf, 1, MSG_PEEK)))
			return (socket_last_error() == 0) ? ThreadHandle::READY : ThreadHandle::ERROR;
		return ($buf == '') ? ThreadHandle::DISCONNECTED : ThreadHandle::UNREAD;
	}
	
	/**
	 * Waits for available data, or until the connection is closed.
	 *
	 * This method waits until {@link ThreadHandle::status()} is in a state <i>other than</i> {@link ThreadHandle::READY}, up to a maximum of $maxwait seconds (or forever, if $maxwait is -1)
	 *
	 * @param int $maxwait (optional) specifies the maximum number of seconds to wait, defaults to -1 (no time limit)
	 * @return int the current return value of {@link ThreadHandle::status()}.
	 */
	public function wait_idle($maxwait = -1)
	{
		if ($maxwait == -1)
		{
			while (($stat = $this->status()) == ThreadHandle::READY)
				usleep(ThreadHandle::USLEEP);
		}
		else
		{
			$endwait = time() + $maxwait;
			while (($stat = $this->status()) == ThreadHandle::READY && time() <= $endwait)
				usleep(ThreadHandle::USLEEP);
		}
		return $stat;
	}
	
	/**
	 * Receives the first data variable from the incoming queue.
	 *
	 * Note that data transferred via the {@link ThreadHandle::send()} and {@link ThreadHandle::recv()} methods is serialized.
	 * Any data will therefore retain not only its <i>value</i> but its <i>type</i>.
	 * This also allows complex structures such as arrays to be transferred.
	 *
	 * @param int $maxwait the maximum number of seconds to wait for data, defaults to -1 (no time limit). A value of 0 is valid, and will cause this method to return immediately if the buffer is empty.
	 * @return mixed the variable read, or NULL if no data was available (or the connection was aborted)
	 */
	public function recv($maxwait = -1)
	{
		return (($tmp = $this->recv_raw($maxwait)) === NULL) ? NULL : unserialize($tmp);
	}
	
	
	/**
	 * Used by {@link ThreadHandle::recv()} and others for internal (raw) communication.
	 * @ignore
	 */
	protected function recv_raw($maxwait = -1)
	{
		if ($this->wait_idle($maxwait) != ThreadHandle::UNREAD)
			return NULL;
		$r = '';
		socket_set_block($this->sock);
		while (substr($r, -1) != "\n") {
			if (FALSE === (@socket_recv($this->sock, $buf, 1, 0)))
				return NULL;
			$r .= $buf;
		}
		return base64_decode(substr($r, 0, -1));
	}
	
	
	/**
	 * Adds a data variable to the outgoing queue.
	 *
	 * Note that data transferred via the {@link ThreadHandle::send()} and {@link ThreadHandle::recv()} methods is serialized.
	 * Any data will therefore retain not only its <i>value</i> but its <i>type</i>.
	 * This also allows complex structures such as arrays to be transferred.
	 *
	 * @param string $var the variable to send
	 * @return bool TRUE on success, FALSE on error
	 */
	public function send($var)
	{
		return $this->send_raw(serialize($var));
	}
	
	
	/**
	 * Used by {@link ThreadHandle::send()} and others for internal (raw) communication.
	 * @ignore
	 */
	protected function send_raw($data)
	{
		socket_set_block($this->sock);
		$length = strlen($data = (base64_encode($data)."\n"));
		for ($written = 0; $written < $length; $written += $fwrite)
		{
			if (FALSE === ($fwrite = @socket_write($this->sock, substr($data, $written), $length - $written)))
				return FALSE;
			elseif (($written + $fwrite) < $length)
				usleep(ThreadHandle::USLEEP);
		}
		return TRUE;
	}
	
	
	/**
	 * Closes the connection.
	 *
	 * This method closes the local end of the inter-process socket between the parent and child threads, but does not terminate either thread.
	 *
	 * No more data may be transferred in either direction after calling this method.
	 *
	 * @ignore
	 */
	protected function close()
	{
		@socket_close($this->sock);
	}
	
	
	
	/**
	 * Performs necessary shutdown of a thread handle upon de-referencing.
	 */
	public function __destruct()
	{
		$this->close();
	}
	
}














/**
 * A child thread.
 *
 * This extension of {@link ThreadHandle} governs instantiation of new threads and provides the parent with additional methods for managing the child thread.
 *
 * For documentation of thread communication methods available to threads, refer to the {@link ThreadHandle parent class}.
 *
 * <b>WARNING</b>: The __FILE__ constant is unreliable within child threads. See {@link Thread::FILE()} for a thread-safe replacement.
 *
 * @example ../examples/example.php A simple thread demonstration
 * @package phpthreadlib
 */
class Thread extends ThreadHandle
{
	/**
	 * Maximum and minimum port numbers
	 */
	const PORT_MAX = 65000;
	const PORT_MIN = 12000;
	/**
	 * How many times to try finding unused port
	 */
	const SOCKET_BIND_TRIES = 1000;	

	/**#@+
	 * @ignore
	 */
	static protected $defaultport = NULL;
	static protected $init_timeout = 5;
	static protected $threadfilename = '';
	
	// Tested in PHP 5.2.7: if you lose the process handle (it goes out of context) the app will hang until the child process terminates, so we use $phandle to store it instead
	protected $phandle;
	/**#@-*/
	
	
	
	
	/**
	 * Spawns a new thread.
	 *
	 * @param callback $entrypoint the entrypoint of the new thread, specified as a PHP callback value. Must be a string "functionName" or array("className", "methodName").
	 *
	 *  o The method/function referred to by $entrypoint must be defined IN the calling file, unless $filename is explicitly specified.
	 *  o The callback function itself must take a single parameter, a {@link ThreadHandle}, used to communicate with the parent thread.
	 *  o <b>WARNING</b>: The __FILE__ constant is unreliable within child threads. See {@link Thread::FILE()} for a thread-safe replacement.
	 *
	 * @param string $filename (optional) the path and filename of the PHP script that defines the entrypoint given in $entrypoint. If NULL, the filename will be that of the calling script.
	 * @param int $maxwait (optional) the number of seconds to wait for a signal from the child process before failing, defaults to 5 seconds. Use -1 to remove the time limit (wait indefinitely)
	 */
	public function __construct($entrypoint, $filename = NULL, $maxwait = 5)
	{
		$this->launch($entrypoint, $filename, $maxwait, 'Thread::run');
	}
	
	
		
	/**
	 * Calls {@link Thread::stop()} when the parent de-references the last handle to a child thread, to prevent orphaned threads.
	 */
	public function __destruct()
	{
		$this->stop();
	}
	
		
	
	/**
	 * Terminate a child thread.
	 *
	 * Note: It is generally better practice to arrange some signal upon which receiving the child thread will exit gracefully.
	 *   If a child thread is terminated using this method, it will be killed immediately, without being given a chance to
	 *   perform any shutdown routines.
	 *
	 * @param int $signal (optional) indicates the POSIX signal to use to terminate the child. Defaults to 15 (SIGTERM).
	 * Use 9 to send SIGKILL instead. This parameter is ignored on non-POSIX-compliant platforms.
	 */
	public function stop($signal = 15)
	{
		$proc_status = NULL;
		$this->close();
		@posix_kill($this->phandle, (int)$signal);
		pcntl_waitpid($this->phandle, $proc_status);
		return;
	}
	
	
	
	
	/**
	 * Initializes the current thread process.
	 *
	 * @ignore
	 */
	static public function run($port, $id)
	{
		if (strlen($port) != 5 || !ctype_digit($port))
			die();
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if (FALSE === @socket_connect($sock, '127.0.0.1', $port))
			die();
		$handle = new ThreadHandle($sock, $port, $id);
		if (	!$handle->send($id) ||
				$handle->recv(self::$init_timeout) != 'ack' ||
				($entrypoint = $handle->recv(self::$init_timeout)) === NULL ||
				(self::$threadfilename = $handle->recv(self::$init_timeout)) === NULL ||
				!$handle->send('ack')
				)
		{	$handle->close();
			die();
		}
		call_user_func($entrypoint, $handle);
		$handle->stop();
		exit();	// just in case all else fails
	}
	
	
	
	
	/**
	 * A thread-safe replacement for the __FILE__ constant.
	 *
	 * Due to limitations in the threading implementation, the __FILE__ constant may give erroneous values when referenced from within a child thread process.
	 * Use this method as a replacement for __FILE__ when developing threaded scripts.
	 *
	 * <code>
	 * <?php
	 * // Don't do this (it doesn't work in child threads)
	 * $MyScript = __FILE__;
	 *
	 * // Do this instead (works in any script, anywhere)
	 * $MyScript = Thread::FILE();
	 * ?>
	 * </code>
	 *
	 * @return string the filename of the currently executing script.
	 */
	static public function FILE()
	{
		return self::backtrace_file(0);
	}
	
	
	/**
	 * Retrives the filename from the call $x hops back ($x hops from the CALLER)
	 * @ignore
	 */
	static protected function backtrace_file($x)
	{
		// Must increment $x because we must account for the additional trace of self::backtrace_file() itself
		$arr = debug_backtrace();
		$ret = $arr[$x + 1]['file'];
		return (substr($ret, 0, 17) == 'Command line code') ? self::$threadfilename : $ret;
	}
	
	
	
	/**
	 * This is protected so that it can only be called by {@link Thread::__construct()}, we don't want user-code tinkering with $bootstrapper
	 * @ignore
	 */
	protected function launch($entrypoint, $filename, $maxwait, $bootstrapper)
	{
		if (self::$defaultport === NULL) self::$defaultport = mt_rand(self::PORT_MIN, self::PORT_MAX);
		$id = mt_rand(10000, 99999);
		$proc_status=NULL;
		
		$listen = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$socket_bind_tries=0;
		while (	FALSE === @socket_bind($listen, '127.0.0.1', self::$defaultport) &&
				$socket_bind_tries++ < self::SOCKET_BIND_TRIES
				)
			self::$defaultport = mt_rand(self::PORT_MIN, self::PORT_MAX);
		if ($socket_bind_tries == self::SOCKET_BIND_TRIES)
			throw new Exception('Unable to bind listening socket.');
		socket_listen($listen, 1);
		
		if ($filename === NULL)	// must be (1) below, as this is a protected method and will always be called by an internal library method, so we jump from 0 to 1
			$filename = self::backtrace_file(1);
		
		$filename = realpath($filename);
		$thisfile = realpath(Thread::FILE());
		
		if (!function_exists("pcntl_fork"))
			throw new Exception('pcntl_fork not available.');
		if (($proc=pcntl_fork()) == -1)
			throw new Exception('Unable to start process.');
		elseif ($proc == 0)
		{
			/**
			 * Child thread
			 */
			socket_close($listen);
			self::run(self::$defaultport,$id);
			die();
		}

		if ($maxwait == -1)
			$sock = socket_accept($listen);
		else
		{
			socket_set_nonblock($listen);
			$endwait = time() + $maxwait;
			while (FALSE === ($sock = @socket_accept($listen)) && time() <= $endwait)
				usleep(ThreadHandle::USLEEP);
		}
		socket_close($listen);
		
		if ($sock === FALSE)
			throw new Exception('Unable to contact child.');
		$this->phandle = $proc;
		$this->sock = $sock;
		$this->port = self::$defaultport;
		$this->id = $id;
		
		if (	$this->recv(self::$init_timeout) != $id ||
				!$this->send('ack') ||
				!$this->send($entrypoint) ||
				!$this->send($filename) ||
				$this->recv(self::$init_timeout) != 'ack'
				)
		{
			$this->close();
			@posix_kill($proc);
			pcntl_waitpid($proc , $proc_status);
			throw new Exception('Error communicating with child.');
		}
	}
	
	
}

















?>
