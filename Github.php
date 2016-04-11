<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 4/6/16
 * Time: 9:44 PM
 */
class Github {

    const ENDPOINT_URL  = 'https://api.github.com/search/repositories';

    const SORT_FORKS    = 'forks';
    const SORT_STARS    = 'stars';
    const SORT_UPDATED  = 'updated';

    const LANG_DEFAULT  = 'php';
    const STARS_DEFAULT = 0;
    const MAX_PER_PAGE  = 100;
    const DEFAULT_PER_PAGE = 30;

    public $username    = '';
    public $access_tkn  = '';
    public $sort;
    public $min_stars;
    public $per_page;
    public $language;
    public $response;


    protected $headers;
    protected $query;
    protected $status;
    protected $error;
    /**
     * Github constructor.
     */
    public function __construct()
    {
        if(!isset($this->sort))         $this->sort = self::SORT_STARS;
        if(!isset($this->language))     $this->language = self::LANG_DEFAULT;
        if(!isset($this->min_stars))    $this->min_stars = self::STARS_DEFAULT;


        if(!isset($this->per_page)){
            $this->per_page = self::DEFAULT_PER_PAGE;
        }elseif($this->per_page > self::MAX_PER_PAGE){
            $this->per_page = self::MAX_PER_PAGE;
        }
    }

    /**
     * @param $projects
     *
     * @return Generator
     */
    public static function iterate($projects){
        foreach($projects as $p){
            yield $p;
        }
    }

    /**
     * @param $page
     */
    public function get_projects()
    {
        $this->query = ($this->sort == self::SORT_STARS) ? $this->sort .':>'.$this->min_stars : '';
        if(empty($this->language)) $this->query.=' language:'.$this->language;

        if ($ch = curl_init(self::ENDPOINT_URL . '?q='.$this->query.'&sort='.$this->sort.'&per_page='.$this->per_page)) {

            $ua_str = ($this->username) ? $this->username : 'Victr';

            $setopt_arr = array(
                CURLOPT_HTTPGET         => true,
                CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
                CURLOPT_USERAGENT       => $ua_str,
                CURLOPT_ENCODING        => '',
                CURLOPT_HEADER          => true,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_HTTPHEADER      => array('Accept:application/json'),
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_CONNECTTIMEOUT  => 8,
                CURLOPT_TIMEOUT         => 20
            );

            if ($this->username && $this->access_tkn) {
                $setopt_arr[CURLOPT_USERPWD] = $this->username.':'.$this->access_tkn;
            }

            curl_setopt_array($ch, $setopt_arr);

            $return     = curl_exec($ch);
            $hdr_size   = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $hdr_str    = substr($return, 0, $hdr_size);

            $this->status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->response = substr($return, $hdr_size);
            $this->headers  = $this->get_headers($hdr_str);

            if(curl_errno($ch)){
                $this->error = 'Curl error ['.curl_errno($ch).']: '.curl_error($ch);
                trigger_error($this->error, E_USER_NOTICE);
            }
            curl_close($ch);
        }else{
            $no_init = 'Curl failed to init.';
            trigger_error($no_init, E_USER_NOTICE);
        }
    }

    /**
     * @param $header_str
     *
     * @return array
     */
    protected static function get_headers($header_str){

        $hrows = explode(PHP_EOL, $header_str);
        $hrows = array_filter($hrows, 'trim');
        $headers = array();
        $i = 0;
        foreach($hrows as $hr){
            $colonpos = strpos($hr, ':');
            $key = ($colonpos !== false)
                ? substr($hr, 0, $colonpos)
                : (int) $i++;
            $headers[$key] = ($colonpos !== false)
                ? trim(substr($hr, $colonpos+1))
                : $hr;
        }
        $j = 0;
        foreach((array)$headers as $key => $val){
            $vals = explode(';', $val);
            if(count($vals) >= 2){
                unset($headers[$key]);
                foreach($vals as $v){
                    $equalpos = strpos($v, '=');
                    $vkey = ($equalpos !== false)
                        ? trim(substr($v, 0, $equalpos))
                        : (int)$j++;
                    $headers[$key][$vkey] = ($equalpos !== false)
                        ? trim(substr($v, $equalpos+1))
                        : $v;
                }
            }
        }
       return $headers;
    }

}
