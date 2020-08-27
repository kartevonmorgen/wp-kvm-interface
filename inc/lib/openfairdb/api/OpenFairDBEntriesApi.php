<?php
/**
 */

/**
 * OpenFairDBEntriesApi Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class OpenFairDBEntriesApi extends AbstractOpenFairDBApi
{
 /**
  * Operation entriesIdsGet
  *
  * Get multiple entries
  *
  * @param  array ids (required)
  *
  * @throws OpenFairDBApiException on non-2xx response
  * @throws InvalidArgumentException
  * @return array of WPInitiative
  */
  public function entriesGet($ids)
  {
    $request = $this->entriesGetRequest($ids);

    $response = $this->client->send($request);
    $statusCode = $response->getStatusCode();
    if ($statusCode < 200 || $statusCode > 299) 
    {
      throw $this->createException($request, $response);
    }

    $responseBody = $response->getBody();
    $wpInitiativen = array();
    $entryArr = json_decode($responseBody, true);
    foreach($entryArr as $entry)
    {
      $wpInitiative = $this->createInitiative($entry);
      array_push( $wpInitiativen, $wpInitiative);
    }
    return $wpInitiativen;
  }

  /**
   * Create request for operation 'entriesIdsGet'
   *
   * @param  array $ids (required)
   *
   * @throws InvalidArgumentException
   * @return RequestInterface
   */
  protected function entriesGetRequest($ids)
  {
    // verify the required parameter 'ids' is set
    if (empty($ids)) 
    {
      throw new InvalidArgumentException(
        'Missing the required parameter $ids'.
        ' when calling entriesIdsGet');
    }

    $resourcePath = '/entries/'. implode(',', $ids);

    $headers = array();
    $headers['Accept'] = 'application/json';

    return $this->getRequest('GET',
                             $resourcePath, 
                             $headers);       
  }

  public function entriesPut($wpInitiative, 
                             $id, 
                             $version)
  {
    $body = $this->createBodyWithVersion($wpInitiative, 
                                     $version);
    $body['id'] = $id;
    $request = $this->entriesPutRequest($body, $id);

    $response = $this->client->send($request);
    $statusCode = $response->getStatusCode();
    if ($statusCode < 200 || $statusCode > 299) 
    {
      throw $this->createException($request, $response);
    }
  }

  public function entriesPutAsync($wpInitiative, 
                                  $id, 
                                  $version)
  {
    $body = $this->createBodyWithVersion($wpInitiative,
                                         $version);
    $body['id'] = $id;
    $request = $this->entriesPutRequest($body, $id);
    $this->client->sendAsync($request);
  }
    
  protected function entriesPutRequest($body, $id)
  {
    // verify the required parameter 'body' is set
    if (empty($body)) 
    {
      throw new InvalidArgumentException(
                'Missing the required parameter $body' .
                ' when calling entriesIdPut');
    }
    
    // verify the required parameter 'id' is set
    if (empty($id)) 
    {
      throw new InvalidArgumentException(
                'Missing the required parameter $id' .
                ' when calling entriesIdPut');
    }

    $resourcePath = '/entries/' . $id;

    $headers = array();
    $headers['Content-Type'] = 'application/json';

    return $this->getRequest('PUT',
                             $resourcePath, 
                             $headers, 
                             true,
                             array(),
                             $body);
  }

  /**
   * Operation entriesPost
   *
   * Create an entry
   *
   * @param  \Swagger\Client\Model\Entry $body body (required)
   *
   * @throws OpenFairDBApiException on non-2xx response
   * @throws InvalidArgumentException
   * @return string
   */
  public function entriesPost($wpInitiative)
  {
    $body = $this->createBody($wpInitiative);
    $request = $this->entriesPostRequest($body);

    $response = $this->client->send($request);
    $statusCode = $response->getStatusCode();
    if ($statusCode < 200 || $statusCode > 299) 
    {
      throw $this->createException($request, $response);
    }
    return json_decode($response->getBody());
  }

  /**
   * Operation entriesPostAsync
   *
   * Create an entry
   *
   * @param  $wpInitiative (required)
   *
   * @throws InvalidArgumentException
   * @return ResponseInterface
   */
  public function entriesPostAsync($wpInitiative)
  {
    $body = $this->createBody($wpInitiative);
    $request = $this->entriesPostRequest($body);
    return $this->client->sendAsync($request);     
  }

  /**
   * Create request for operation 'entriesPost'
   *
   * @param  \Swagger\Client\Model\Entry $body (required)
   *
   * @throws \InvalidArgumentException
   * @return \GuzzleHttp\Psr7\Request
   */
  protected function entriesPostRequest($body)
  {
    // verify the required parameter 'body' is set
    if (empty($body)) 
    {
      throw new InvalidArgumentException(
        'Missing the required parameter $body when calling entriesPost');
    }

    $resourcePath = '/entries';
    $headers = array();
    $headers['Content-Type'] = 'application/json';
    return $this->getRequest('POST',
                             $resourcePath, 
                             $headers, 
                             true,
                             array(),
                             $body);
  }

  /**
   * Operation searchGet
   *
   * Search for entries
   *
   * @param  string $bbox Bounding Box (optional)
   * @param  string $categories Comma-separated list of category identifiers. We currently use the following two: 
   *  -Initiative (non-commercial): &#x60;2cd00bebec0c48ba9db761da48678134&#x60; 
      -Company (commercial): &#x60;77b3c33a92554bcf8e8c2c86cedd6f6f&#x60; 
   * @param  string $text text (optional)
   * @param  \Swagger\Client\Model\IdList $ids ids (optional)
   * @param  \Swagger\Client\Model\TagList $tags tags (optional)
   * @param  \Swagger\Client\Model\ReviewStatusList $status status (optional)
   * @param  int $limit Maximum number of items to return or implicit/unlimited if unspecified. (optional)
   *
   * @throws OpenFairDBApiException on non-2xx response
   * @throws InvalidArgumentException
   * @return WPInitiative[] 
   */
  public function searchGet($bbox = null, 
                            $categories = null, 
                            $text = null, 
                            $ids = null, 
                            $tags = null, 
                            $status = null, 
                            $limit = null)
  {
    $request = $this->searchGetRequest($bbox, $categories, $text, $ids, $tags, $status, $limit);

    $response = $this->client->send($request, $options);
    $statusCode = $response->getStatusCode();
    if ($statusCode < 200 || $statusCode > 299) 
    {
      throw $this->createException($request, $response);
    }
    $responseBody = $response->getBody();

    $body = json_decode($responseBody, true);
    $wpInitiativeArr = $body['visible'];
    $wpInitiativen = array();
    foreach($wpInitiativeArr as $wpInitiativeData)
    {
      $wpInitiative = $this->createInitiative($wpInitiativeData);
      array_push( $wpInitiativen, $wpInitiative);
    }
    return $wpInitiativen;
  }

  /**
   * Create request for operation 'searchGet'
   *
   * @param  string $bbox Bounding Box (optional)
   * @param  string $categories Comma-separated list of category identifiers. We currently use the following two: - Initiative (non-commercial): &#x60;2cd00bebec0c48ba9db761da48678134&#x60; - Company (commercial): &#x60;77b3c33a92554bcf8e8c2c86cedd6f6f&#x60; (optional)
   * @param  string $text (optional)
   * @param  \Swagger\Client\Model\IdList $ids (optional)
   * @param  \Swagger\Client\Model\TagList $tags (optional)
   * @param  \Swagger\Client\Model\ReviewStatusList $status (optional)
   * @param  int $limit Maximum number of items to return or implicit/unlimited if unspecified. (optional)
   *
   * @throws InvalidArgumentException
   * @return RequestInterface
   */
  protected function searchGetRequest($bbox = null, 
                                      $categories = null, 
                                      $text = null, 
                                      $ids = null, 
                                      $tags = null, 
                                      $status = null, 
                                      $limit = null)
  {
    $resourcePath = '/search';
    $queryParams = [];

    $formParams = [];
    $headerParams = [];
    $httpBody = '';
    $multipart = false;

    // query params
    if ($bbox !== null) 
    {
      $queryParams['bbox'] = $this->toQueryValue($bbox);
    }

    // query params
    if ($categories !== null) 
    {
      $queryParams['categories'] = $this->toQueryValue($categories);
    }

    // query params
    if ($text !== null) 
    {
      $queryParams['text'] = $this->toQueryValue($text);
    }

    // query params
    if ($ids !== null) 
    {
      $queryParams['ids'] = $this->toQueryValue($ids);
    }
    
    // query params
    if ($tags !== null) 
    {
      $queryParams['tags'] = $this->toQueryValue($tags);
    }

    // query params
    if ($status !== null) 
    {
      $queryParams['status'] = $this->toQueryValue($status);
    }

    // query params
    if ($limit !== null) 
    {
      $queryParams['limit'] = $this->toQueryValue($limit);
    }

    // body params
    $headers = array();
    $headers['Accept'] = 'application/json';
    return $this->getRequest('GET',
                             $resourcePath, 
                             $headers, 
                             false, 
                             $queryParams);       
  }

  private function createInitiative($body)
  {
    $wpInitiative = new WPInitiative();
    $wpInitiative->set_id( $body['id'] );
    $wpInitiative->set_name( $body['title'] );
    $wpInitiative->set_description( $body['description'] );

    $wpLocHelper = new WPLocationHelper();
    $wpLocation = new WPLocation();
    $wpLocation->set_name( $body['title']);
    if(!empty($body['street']))
    {
      $wpLocHelper->set_address($wpLocation, $body['street'] );
    }
    if(!empty($body['zip']))
    {
      $wpLocation->set_zip( $body['zip'] );
    }
    if(!empty($body['city']))
    {
      $wpLocation->set_city( $body['city'] );
    }
    $wpLocation->set_lon( $body['lng'] );
    $wpLocation->set_lat( $body['lat'] );

    $wpInitiative->set_location($wpLocation);

    if(!empty($body['tags']))
    {
      foreach($body['tags'] as $tag)
      {
        $wpTag = new WPTag($tag, $tag);
        $wpInitiative->add_tag($wpTag);
      }
    }


    if( !empty( $body['categories'] ))
    {
      foreach( $body['categories'] as $cat)
      {
        if( $cat == '77b3c33a92554bcf8e8c2c86cedd6f6f' )
        {
          $wpInitiative->set_company(true);
          break;
        }
        if( $cat == '2cd00bebec0c48ba9db761da48678134' )
        {
          $wpInitiative->set_company(false);
          break;
        }
      }
    }

    // TODO: Fill elements füther

    if(!empty($body['version']))
    {
      $wpInitiative->set_kvm_version( $body['version'] );
    }
    return $wpInitiative;
  }

  private function createBodyWithVersion($wpInitiative, $version)
  {
    $body = $this->createBody($wpInitiative);
    $body['version'] = intval($version);
    return $body;
  }

  private function createBody($wpInitiative)
  {
    $body = array();
    $body['title'] = $wpInitiative->get_name();
    $body['description'] = $wpInitiative->get_description();
    $body['telephone'] = $wpInitiative->get_contact_phone();
    $body['email'] = $wpInitiative->get_contact_email();
    $body['homepage'] = $wpInitiative->get_contact_website();

    if( $wpInitiative->is_company())
    {
      $body['categories'] = 
        array('77b3c33a92554bcf8e8c2c86cedd6f6f');
    }
    else
    {
      $body['categories'] = 
        array('2cd00bebec0c48ba9db761da48678134');
    }

    $tags = array();
    foreach($wpInitiative->get_tags() as $wpTag)
    {
      array_push($tags, $wpTag->get_slug());
    }

    $fixed_tag = get_option('kvm_fixed_tag');
    if(!empty($fixed_tag))
    {
      array_push($tags, $fixed_tag);
    }

    $body['tags'] = $tags;

    $wpLocation = $wpInitiative->get_location();
    if(!empty($wpLocation))
    {
      $wpLocH = new WPLocationHelper();
      $address = $wpLocH->get_address($wpLocation);
      
      if(!empty($address))
      {
        $body['street'] = $address;
      }
      
      if(!empty($wpLocation->get_zip()))
      {
        $body['zip'] = $wpLocation->get_zip();
      }
      
      if(!empty($wpLocation->get_city()))
      {
        $body['city'] = $wpLocation->get_city();
      }
      
      if(!empty($wpLocation->get_country_code()))
      {
        $body['country'] = $wpLocation->get_country_code();
      }
      
      if(!empty($wpLocation->get_state()))
      {
        $body['state'] = $wpLocation->get_state();
      }

      if(!empty($wpLocation->get_lat()) 
         && !empty($wpLocation->get_lon()))
      {
        $body['lat'] = doubleval($wpLocation->get_lat());
        $body['lng'] = doubleval($wpLocation->get_lon());
      }
    }
    $body['license'] = 'CC0-1.0';
    return $body;
  }
}
