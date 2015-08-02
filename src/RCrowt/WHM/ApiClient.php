<?php
namespace RCrowt\WHM;

/**
 * WHM Api Client.
 *
 * @package RCrowt\WHM
 */
class ApiClient
{
    // Account Search Methods
    const ACCOUNT_SEARCH_DOMAIN = 'domain';
    const ACCOUNT_SEARCH_OWNER = 'owner';
    const ACCOUNT_SEARCH_USER = 'user';
    const ACCOUNT_SEARCH_IP = 'ip';
    const ACCOUNT_SEARCH_PACKAGE = 'package';

    /**
     * @var string API Username
     */
    protected $username;

    /**
     * @var string API Access Hash
     */
    protected $access_hash;

    /**
     * @var string API Host
     */
    public $host;

    /**
     * @var string API Protocol (http|https)
     */
    public $protocol;

    /**
     * @var integer API Port Number.
     */
    public $port;

    /**
     * @var null|ApiClient\CPanelPackage[] Cache of Packages.
     */
    private $_cache_packages;

    /**
     * @param $username string API Username
     * @param $access_hash string API Access Hash
     * @param string $host string API Host
     * @param bool $https Is this a secure connection?
     */
    function __construct($username, $access_hash, $host = 'localhost', $https = true)
    {
        $this->username = $username;
        $this->access_hash = preg_replace("'(\r|\n)'", "", $access_hash);
        $this->host = $host;
        $this->protocol = ($https ? 'https' : 'http');
        $this->port = ($https ? 2087 : 2086);
    }

    /**
     * Do a simple API Call to WHM.
     * @param $path string API Function
     * @param array $params Optional GET Parameters
     * @param bool $is_json Is this a JSON response?
     * @return mixed|array|object
     * @throws ApiException
     * @see https://documentation.cpanel.net/display/SDK/Guide+to+WHM+API+1
     */
    public function doApiCall($path, $params = [], $is_json = true)
    {

        $path = $path . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_URL, "{$this->protocol}://{$this->host}:{$this->port}/json-api/{$path}");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: WHM {$this->username}:{$this->access_hash}"]);
        $data = curl_exec($ch);

        // Throw an exception on Error.
        if (curl_errno($ch)) throw new ApiException(curl_error($ch), curl_errno($ch));

        // Close the connection and return the data.
        curl_close($ch);

        if ($is_json) return json_decode($data);
        else return $data;
    }

    /**
     * Get an account by Domain, Username and more.
     * @param $query string Domain/Username
     * @param string $search_type (Default: Domain Name)
     * @return ApiClient\CPanelAccount
     * @throws UserException
     * @throws \Exception
     */
    public function getAccount($query, $search_type = self::ACCOUNT_SEARCH_DOMAIN)
    {
        $out = $this->getAccountList($query, $search_type);
        if (count($out) == 1) return reset($out);
        else throw new UserException('Account not found');
    }

    /**
     * Get a list of Accounts
     * @param null|string $search Optional search parameters.
     * @param null|string $search_type Optional search type.
     * @return ApiClient\CPanelAccount[]
     * @throws ApiException
     * @throws \Exception
     */
    public function getAccountList($search = null, $search_type = null)
    {
        if (!in_array($search_type, [null, self::ACCOUNT_SEARCH_DOMAIN, self::ACCOUNT_SEARCH_IP, self::ACCOUNT_SEARCH_OWNER, self::ACCOUNT_SEARCH_PACKAGE, self::ACCOUNT_SEARCH_USER]))
            throw new \Exception('Invalid $search_type');

        $data = $this->doApiCall('listaccts', [
            'search' => $search,
            'searchtype' => $search_type,
        ]);
        return $this->_dataToObject($data->acct, ApiClient\CPanelAccount::class);
    }

    /**
     * Get a WHM Package by name.
     * @param $package_name
     * @return ApiClient\CPanelPackage
     * @throws UserException
     */
    public function getPackage($package_name)
    {
        // Cache package list if not cached.
        if ($this->_cache_packages == null) $this->_cache_packages = $this->getPackageList();

        // Find package and return.
        foreach ($this->_cache_packages as $pack)
            if ($pack->getName() == $package_name) return $pack;

        // Return Error
        throw new UserException('Package not found: ' . $package_name);

    }

    /**
     * Get a list of WHM Packages.
     * @return ApiClient\CPanelPackage
     * @throws ApiException
     * @throws \Exception
     */
    public function getPackageList()
    {
        $data = $this->doApiCall('listpkgs');
        if (property_exists($data, 'package')) return $this->_dataToObject($data->package, ApiClient\CPanelPackage::class);
        else return [];
    }

    /**
     * Convert an array of Data to and array of objects of the specified class.
     * @param array $data
     * @param $class
     * @throws \Exception
     * @return array
     */
    private function _dataToObject(array $data, $class)
    {
        if (!is_a($class, ApiClient\CPanelObject::class, true)) throw new \Exception($class . ' must implement ' . ApiClient\CPanelObject::class);

        $out = [];
        foreach ($data as $d) $out[] = new $class($d, $this);
        return $out;
    }

}