<?php

namespace Emailclient;

class NoConnectionException extends \FuelException {
    
}

class Emailclient {

    /**
     * Instance for singleton usage.
     */
    public static $_instance = false;

    /**
     * Driver config defaults.
     */
    protected static $_defaults;
    private $config = array(
        'host' => '',
        'port' => '110',
        'username' => '',
        'password' => '',
        'folder' => 'INBOX',
        'ssl' => false
    );
    private $connection = false;

    /**
     * Email driver forge.
     *
     * @param	string|array	$setup		setup key for array defined in email.setups config or config array
     * @param	array			$config		extra config array
     */
    public static function forge($setup = null, array $config = array()) {
        if (static::$_instance === false) {
            empty($setup) and $setup = \Config::get('pop3.default_setup', 'default');
            is_string($setup) and $setup = \Config::get('pop3.setups.' . $setup, array());

            $setup = \Arr::merge(static::$_defaults, $setup);
            $config = \Arr::merge($setup, $config);

            $instance = new self($config);
            static::$_instance = &$instance;
        }
        return static::$_instance;
    }

    /**
     * Init, config loading.
     */
    public static function _init() {
        \Config::load('emailclient', true);
        static::$_defaults = \Config::get('pop3.defaults');
    }

    private function __construct($config) {
        $this->config = $config;
    }

    public function __destruct() {
        if ($this->connection !== false) {
            imap_close($this->connection, CL_EXPUNGE);
        }
    }

    public function connect() {
        $ssl = ($this->config['ssl'] == false) ? '/novalidate-cert' : '';
        $connect_str = '{' . $this->config['host'] . ':' . $this->config['port'] . '/pop3' . $ssl . '}' . $this->config['folder'];
        if (!( $this->connection = imap_open($connect_str, $this->config['username'], $this->config['password']) )) {
            throw new NoConnectionException('Could not connect to host: ' . $this->config['host']);
        }
    }

    public function messages($limit = 0) {
        $MC = imap_check($this->connection);
        $limit = ($limit == 0) ? $MC->Nmsgs : $limit;
        $range = "1:" . $limit;
        $result = array();
        $response = imap_fetch_overview($this->connection, $range);
        foreach ($response as $msg) {
            $result[$msg->msgno] = (array) $msg;
            $result[$msg->msgno]['message'] = imap_body($this->connection, $msg->msgno);
        }
        return $result;
    }

    public function delete($i) {
        imap_delete($this->connection, $i);
    }

    public function close() {
        if ($this->connection !== false) {
            imap_expunge($this->connection);
            imap_close($this->connection, CL_EXPUNGE);
        }
    }

}

