<?php
class Loquela
{
  /**
   * The api key, to get one go to http://loque.la/signup/
   * @var string the long api key generated when you create an account.
   */
  private $_apikey = null;

  /**
   * The V1 query URL.
   * @var unknown
   */
  const V1_URL = 'http://api.loque.la/v1/';
  const VERSION_STR = 'V1';

  public function __construct( $apikey )
  {
    //  save the key
    $this->_apikey = $apikey;

    if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) )
    {
      throw new Exception( 'Curl is not installed!' );
    }
  }

  /**
   * Detect one or more item of text.
   * @param string|array $text
   * @return json the decoded json response.
   */
  public function Detect( $text )
  {
    //  get the contents
    //  the request.
    $data = array( 'q' => $text, 'key' => $this->_apikey );
    $jsonContent = $this->GetWithCurl($data);

    // if we are here we didn't throw.
    $content = json_decode($jsonContent);

    //  return the decoded json
    return $content;
  }

  public function Status()
  {
    $data = array( 'get' => 'status', 'key' => $this->_apikey );
    $jsonContent = $this->GetWithCurl($data);

    // if we are here we didn't throw.
    $content = json_decode($jsonContent);

    //  return the decoded json
    return $content;
  }

  /**
   * Get the data using curl
   * @param array $data the formated data we want to request.
   * @throws string if there was an error with the detected text.
   * @return string the raw text response from the site.
   */
  private function GetWithCurl( $data )
  {
    // timeout is short
    $timeout = 10;

    // init curl
    $handle = curl_init();

    $args = array(
        CURLOPT_URL => self::V1_URL,
        CURLOPT_HTTPHEADER => array( "Content-Type: application/x-www-form-urlencoded",
                                    "Accept-Encoding: gzip, deflate",
                                    "User-Agent: Loque_la_" . self::VERSION_STR
                                  ),
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_USERAGENT      => "User-Agent: Loque_la_" . self::VERSION_STR,
        CURLOPT_RETURNTRANSFER => true
    );

    //  set the option
    curl_setopt_array($handle, $args);

    // get the data
    $ret = curl_exec($handle);

    if ($ret === false)
    {
      $e = curl_error($handle);
      curl_close($ch);
      throw $e;
    }

    // close the handle
    curl_close($handle);

    // return whatever we found.
    return $ret;
  }
}
?>