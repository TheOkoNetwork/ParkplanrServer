<?php
namespace Kunnu\OneDrive;

use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\ResponseInterface;
use Kunnu\OneDrive\Exceptions\OneDriveClientException;

class Client
{
    /**
     * OneDrive API Root URL.
     */
    const BASE_PATH = 'https://api.onedrive.com/v1.0';

    /**
     * OneDrive Default Drive
     */
    const DEFAULT_DRIVE = "me";

    /**
     * The Guzzle Client.
     *
     * @var GuzzleHttp\Client
     */
    private $guzzle;

    /**
     * OAuth2 Access Token.
     *
     * @var string
     */
    private $access_token;

    /**
     * Response Type for API Response.
     *
     * @var string
     */
    private $contentType = 'application/json';

    /**
     * Default options to send along a Request.
     *
     * @var array
     */
    private $defaultOptions = [];

    /**
     * Current Selected Drive.
     *
     * @var default
     */
    private $selectedDrive = self::DEFAULT_DRIVE;

    /**
     * Available Conflict Behaviours.
     *
     * @var array
     */
    private $allowedBehaviors = ['rename', 'fail', 'replace'];

    /**
     * Default COnflict Behaviour.
     *
     * @var string
     */
    private $defaultBehavior = 'fail';

    /**
     * The Constructor.
     *
     * @param string $access_token The Access Token
     * @param Guzzle $guzzle       The Guzzle Client Object
     */
    public function __construct($access_token, Guzzle $guzzle)
    {
        //Set the access token
        $this->setAccessToken($access_token);
        //Set the Guzzle Client
        $this->guzzle = $guzzle;
    }

    /**
     * Get the API Base Path.
     *
     * @return string API Base Path
     */
    public function getBasePath()
    {
        return self::BASE_PATH;
    }

    /**
     * Set the Default Options.
     *
     * @param array \Kunnu\OneDrive\Client
     *
     * @return array \Kunnu\OneDrive\Client
     */
    public function setDefaultOptions(array $options = array())
    {
        $this->defaultOptions = $options;

        return $this;
    }

    /**
     * Get the Default Options.
     *
     * @return string The Default Options
     */
    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    /**
     * Get the Access Token.
     *
     * @return string Access Token
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Set the Access Token.
     *
     * @param string $access_token Access Token
     *
     * @return array \Kunnu\OneDrive\Client
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;

        return $this;
    }

    /**
     * Set the Content Type.
     *
     * @param string $type 'application/json', 'application/xml'
     *
     * @return array \Kunnu\OneDrive\Client
     */
    public function setContentType($type)
    {
        $this->contentType = $type;

        return $this;
    }

    /**
     * Get the Content Type.
     *
     * @return string Content Type
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get the Authorization Header with the Access Token.
     *
     * @return array Authorization Header
     */
    protected function getAuthHeader()
    {
        return ['Authorization' => 'bearer '.$this->getAccessToken()];
    }

    /**
     * Get the Content Type Header.
     *
     * @return array Content Type Header
     */
    public function getContentTypeHeader()
    {
        return ['Content-Type' => $this->getContentType()];
    }

    /**
     * Get Default Headers.
     *
     * @return array Default Headers
     */
    protected function getDefaultHeaders()
    {
        return array_merge($this->getAuthHeader(), $this->getContentTypeHeader());
    }

    /**
     * Set the Default Conflict Behavior.
     *
     * @param string $behavior
     */
    public function setDefaultBehavior($behavior)
    {
        $this->defaultBehavior = $behavior;

        return $this;
    }

    /**
     * Get the Default Behavior.
     *
     * @return string Default Behavior
     */
    public function getDefaultBehavior()
    {
        return $this->defaultBehavior;
    }

    /**
     * Build Headers for the API Request.
     *
     * @param array $headers Additional Headers
     *
     * @return array Merged additonal and default headers
     */
    protected function buildHeaders($headers = [])
    {
        //Override the Default Response Type, if provided
        if (array_key_exists('Content-Type', $headers)) {
            $this->setContentType($headers['Content-Type']);
        }

        return array_merge($headers, $this->getDefaultHeaders());
    }

    /**
     * Build URL for the Request.
     *
     * @param string $path Relative API path or endpoint
     *
     * @return string The Full URL
     */
    protected function buildUrl($path = '')
    {
        $path = urlencode($path);

        return $this->getBasePath().$path;
    }

    /**
     * Build Options.
     *
     * @param array $options Additional Options
     *
     * @return array Merged Additional Options
     */
    protected function buildOptions($options)
    {
        return array_merge($options, $this->getDefaultOptions());
    }

    /**
     * Make Request to the API using Guzzle.
     *
     * @param string                          $method  Method Type [GET|POST|PUT|DELETE]
     * @param null|string|UriInterface        $uri     URI for the Request
     * @param array                           $params  Options to send along the request
     * @param string|resource|StreamInterface $body    Message Body
     * @param array                           $headers Headers for the message
     *
     * @throws Exception
     *
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function makeRequest($method, $uri, $options = [], $body = null, $headers = [])
    {
        //Build headers
        $headers = $this->buildHeaders($headers);

        //Create a new Request Object
        $request = new Request($method, $uri, $headers, $body);

        //Build Options
        $options = $this->buildOptions($options);

        try {
            //Send the Request
            return $this->guzzle->send($request, $options);
        } catch (\Exception $e) {
            var_dump($e);
            exit();
        }
    }

    /**
     * Decode the Response.
     *
     * @param string|\Psr\Http\Message\ResponseInterface $response Response object or string to decode
     *
     * @return string
     */
    protected function decodeResponse($response)
    {
        $body = $response;
        if ($response instanceof ResponseInterface) {
            $body = $response->getBody();
        }

        return json_decode((string) $body);
    }

    /**
     * Get Drive Path.
     *
     * @param string $drive_id ID of the Drive
     *
     * @return string Drive Path
     */
    protected function getDrivePath($drive_id = null)
    {
        $drive_id = is_null($drive_id) ? $this->getSelectedDrive() : $drive_id;

        return "/drives/{$drive_id}";
    }

    /**
     * Select a Drive to perform operations on.
     *
     * @param string $drive Drive ID
     *
     * @return \Kunnu\OneDrive\Client
     */
    public function selectDrive($drive_id)
    {
        if (!empty($drive_id)) {
            $this->selectedDrive = $drive_id;
        }

        return $this;
    }

    /**
     * Get the Seleted Drive.
     *
     * @return string Selected Drive ID
     */
    protected function getSelectedDrive()
    {
        return $this->selectedDrive;
    }

    /**
     * List Drives.
     *
     * @param array $params Additional Query Parameters
     *
     * @return object
     */
    public function listDrives($params = array())
    {
        $uri = $this->buildUrl('/drives');

        $response = $this->makeRequest('GET', $uri, ['query' => $params]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Get Drive MetaData.
     *
     * @param null|string $drive_id ID of the Drive to fetch. Null for Default Drive.
     * @param array       $params   Additional Query Parameters
     *
     * @return object
     */
    public function getDrive($drive_id = null, $params = array())
    {
        $path = $this->getDrivePath($drive_id);
        $uri = $this->buildUrl($path);

        $response = $this->makeRequest('GET', $uri, ['query' => $params]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Get the Default Drive.
     *
     * @param array $params Additional Query Parameters
     *
     * @return object
     */
    public function getDefaultDrive($params = array())
    {
        return $this->getDrive(self::DEFAULT_DRIVE, $params);
    }

    /**
     * Get Drive Root.
     *
     * @param null|string $drive_id ID of the Drive to fetch. Null for Default Drive.
     * @param array       $params   Additional Query Parameters
     *
     * @return object
     */
    public function getDriveRoot($drive_id = null, $params = array())
    {
        $path = $this->getDrivePath($drive_id);
        $path = "{$path}/root";
        $uri = $this->buildUrl($path);

        $response = $this->makeRequest('GET', $uri, ['query' => $params]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * List Children of the specified Item ID.
     *
     * @param null|string $item_id ID of the Item to list children of.
     * @param array       $params  Additional Query Parameters
     *
     * @return object
     */
    public function listChildren($item_id = null, $params = array())
    {
        $path = is_null($item_id) ? '/root' : "/items/{$item_id}";
        $path = $this->getDrivePath()."{$path}/children";
        $uri = $this->buildUrl($path);

        $response = $this->makeRequest('GET', $uri, ['query' => $params]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Get an Item by ID.
     *
     * @param string $item_id      ID of the Item
     * @param bool   $withChildren Get the Item along with it's children
     * @param array  $params       Additional Query Params
     *
     * @throws OneDriveClientException
     *
     * @return object
     */
    public function getItem($item_id, $withChildren = false, $params = array())
    {
        if ($item_id == '') {
            throw new OneDriveClientException('Valid item ID is Required!');
        }
        $path = $this->getDrivePath()."/items/{$item_id}";
        $uri = $this->buildUrl($path);

        if ($withChildren) {
            //User has passed an expand param
            if (array_key_exists('expand', $params)) {
                //Expand doesn't contain children param
                if ((!strpos($params['expand'], 'children'))) {
                    //Append children param into expand
                    $params['expand'] = "{$params['expand']},children";
                }
            } else {
                //No expand param given
                $params['expand'] = 'children';
            }
        }

        $response = $this->makeRequest('GET', $uri, ['query' => $params]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Search.
     *
     * @param string Search Query
     * @param null|string $item_id ID of the Item(Folder) to search inside of.
     * @param array       $params  Additional Query Parameters
     *
     * @return object
     */
    public function search($query, $item_id = null, $params = array())
    {
        $path = is_null($item_id) ? '/root' : "/items/{$item_id}";
        $path = $this->getDrivePath()."{$path}/view.search";
        $uri = $this->buildUrl($path);

        $params['q'] = stripslashes(trim($query));

        $response = $this->makeRequest('GET', $uri, ['query' => $params]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Get Thumbnails of an Item.
     *
     * @param string $item_id ID of the Item
     * @param array  $params  Additional Query Params
     *
     * @throws OneDriveClientException
     *
     * @return object
     */
    public function getItemThumbnails($item_id, $params = array())
    {
        if ($item_id == '') {
            throw new OneDriveClientException('Valid item ID is Required!');
        }
        $path = $this->getDrivePath()."/items/{$item_id}/thumbnails";
        $uri = $this->buildUrl($path);

        $response = $this->makeRequest('GET', $uri, ['query' => $params]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Get a Single Thumbnail of an Item.
     *
     * @param string $item_id      ID of the Item
     * @param string $thumbnail_id ID of the thumbnail
     * @param array  $params       Additional Query Params
     *
     *  @throws OneDriveClientException
     *
     * @return object
     */
    public function getItemThumbnail($item_id, $thumbnail_id = '0', $params = array())
    {
        if ($item_id == '') {
            throw new OneDriveClientException('Valid item ID is Required!');
        }

        $path = $this->getDrivePath()."/items/{$item_id}/thumbnails/{$thumbnail_id}";
        $uri = $this->buildUrl($path);

        $response = $this->makeRequest('GET', $uri, ['query' => $params]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Download a given File.
     *
     * @param mixed $file File Location or Resource
     *
     * @return resource
     */
    protected function downloadFile($file)
    {
        if (!is_resource($file)) {
            $file = fopen($file, 'r');
        }

        $stream = new Stream($file);

        $downloadedFile = fopen('php://temp', 'w+');

        if ($downloadedFile === false) {
            throw new \Exception('Error when saving the downloaded file');
        }

        while (!$stream->eof()) {
            $writeResult = fwrite($downloadedFile, $stream->read(8000));
            if ($writeResult === false) {
                throw new \Exception('Error when saving the downloaded file');
            }
        }

        $stream->close();
        rewind($downloadedFile);

        return $downloadedFile;
    }

    /**
     * Get a File's Content.
     *
     * @param mixed $file File Location or Resource
     *
     * @return string File Contents
     */
    protected function getFileContents($file)
    {
        if (!is_resource($file)) {
            $file = fopen($file, 'r');
        }
        $stream = new Stream($file);
        $output = $stream->getContents();
        $stream->close();

        return $output;
    }

    /**
     * Download an Item.
     *
     * @param string $item_id ID of the Item to download
     * @param array  $params  Additional Query Params
     *
     * @return string Downloaded content
     */
    public function downloadItem($item_id, $params = array())
    {
        $item = $this->getItem($item_id);
        $downloadUrl = $item->{'@content.downloadUrl'};
        $downloadedFile = $this->downloadFile($downloadUrl);

        return $downloadedFile;
    }

    /**
     * Validate whether a given behavior is a valid Conflict Behavior.
     *
     * @param string $conflictBehavior Behavior to validate
     *
     * @throws OneDriveClientException
     *
     * @return bool
     */
    protected function validateConflictBehavior($conflictBehavior)
    {
        $exists = in_array($conflictBehavior, $this->allowedBehaviors);

        if (!$exists) {
            throw new OneDriveClientException('Please enter a valid conflict behavior');
        }

        return true;
    }

    /**
     * Create a Folder Item.
     *
     * @param string $title     Name of the Folder
     * @param string $parent_id ID of the Parent Folder. Empty for drive root.
     * @param string $behavior  Conflict Behavior
     * @param array  $params    Additional Query Parameters
     *
     * @return string Created Folder Item
     */
    public function createFolder($title, $parent_id = null, $behavior = null, $params = array())
    {
        $behavior = is_null($behavior) ? $this->getDefaultBehavior() : $behavior;

        //Drive Path
        $path = $this->getDrivePath();

        //If the parent id is not provided, use the drive root
        if (is_null($parent_id)) {
            $path .= '/root/children';
        } else {
            $path .= "/items/{$parent_id}/children";
        }

        $uri = $this->buildUrl($path);

        //Validate Conflict Behavior
        $this->validateConflictBehavior($behavior);

        $body = ['name' => $title, '@name.conflictBehavior' => $behavior, 'folder' => new \StdClass()];

        //Json Encode Body
        $body = json_encode($body);

        $response = $this->makeRequest('POST', $uri, ['query' => $params, 'body' => $body]);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Create a Multipart Body.
     *
     * @param array  $metadata Metadata of the file
     * @param string $content  Request Content
     * @param string $mimeType Mimetype of the Content
     *
     * @return string Multipart Request Body
     */
    protected function createMultipartBody($metadata, $content, $mimeType)
    {
        $this->boundary = mt_rand();
        $this->boundary = str_replace('"', '', $this->boundary);
        $contentType = 'multipart/related; boundary='.$this->boundary;
        $related = "--$this->boundary\r\n";
        $related .= "Content-ID: <metadata>\r\n";
        $related .= "Content-Type: application/json\r\n";
        $related .= "\r\n".json_encode($metadata)."\r\n";
        $related .= "--$this->boundary\r\n";
        $related .= "Content-ID: <content>\r\n";
        $related .= "Content-Type: $mimeType\r\n";
        $related .= "\r\n".$content."\r\n";
        $related .= "--$this->boundary--";

        return $related;
    }

    /**
     * Upload file.
     *
     * @param string $file      File Location/Path
     * @param string $title     File Name
     * @param string $parent_id ID of the Parent Folder. Empty for drive root.
     * @param string $behavior  Conflict Behavior
     *
     * @throws OneDriveClientException
     *
     * @return object Created File Item
     */
    public function uploadFile($file, $title = null, $parent_id = null, $behavior = null)
    {
        if (!file_exists($file)) {
            throw new OneDriveClientException("File doesn't exist!");
        }

        if (is_null($title)) {
            $title = basename($file);
        }

        $behavior = is_null($behavior) ? $this->getDefaultBehavior() : $behavior;

        //Drive Path
        $path = $this->getDrivePath();

        //If the parent id is not provided, use the drive root
        if (is_null($parent_id)) {
            $path .= '/root/children';
        } else {
            $path .= "/items/{$parent_id}/children";
        }

        $uri = $this->buildUrl($path);

        //Validate Conflict Behavior
        $this->validateConflictBehavior($behavior);

        $metadata = [
        'name' => $title,
        '@name.conflictBehavior' => $behavior,
        'file' => new \StdClass(),
        '@content.sourceUrl' => 'cid:content', ];

        $content = $this->getFileContents($file);
        $mimeType = mime_content_type($file);

        $body = $this->createMultipartBody($metadata, $content, $mimeType);

        $defaultContentType = $this->getContentType();
        $this->setContentType("multipart/related; boundary={$this->boundary}");

        $response = $this->makeRequest('POST', $uri, [], $body);
        $responseContent = $this->decodeResponse($response);

        $this->setContentType($defaultContentType);

        return $responseContent;
    }

    /**
     * Create file.
     *
     * @param string $title     File Name
     * @param string $contents  File Contents
     * @param string $parent_id ID of the Parent Folder. Empty for drive root.
     * @param string $behavior  Conflict Behavior
     *
     * @return object Created File Item
     */
    public function createFile($title, $contents, $parent_id = null, $behavior = null)
    {
        $behavior = is_null($behavior) ? $this->getDefaultBehavior() : $behavior;

        //Drive Path
        $path = $this->getDrivePath();

        //If the parent id is not provided, use the drive root
        if (is_null($parent_id)) {
            $path .= "/root/children/{$title}/content";
        } else {
            $path .= "/items/{$parent_id}/children/{$title}/content";
        }

        $uri = $this->buildUrl($path);

        //Validate Conflict Behavior
        $this->validateConflictBehavior($behavior);

        $response = $this->makeRequest('PUT', $uri, [], $contents);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Update Metadata of an Item.
     *
     * @param string $item_id  ID of the item
     * @param array  $metadata Metadata to update
     *
     * @return object Updated Item
     */
    public function updateMeta($item_id, array $metadata)
    {
        //Drive Path
        $path = $this->getDrivePath()."/items/{$item_id}";

        $uri = $this->buildUrl($path);

        //Json Encode Body
        $body = json_encode($metadata);

        $response = $this->makeRequest('PATCH', $uri, [], $body);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Move an Item to a new Location.
     *
     * @param string $item_id   ID of the item
     * @param array  $parent_id ID of the parent folder to move the item to
     *
     * @return object Updated Item
     */
    public function move($item_id, $parent_id)
    {
        //Drive Path
        $path = $this->getDrivePath()."/items/{$item_id}";

        $uri = $this->buildUrl($path);

        $metadata = array('parentReference' => array('id' => $parent_id));

        //Json Encode Body
        $body = json_encode($metadata);

        $response = $this->makeRequest('PATCH', $uri, [], $body);
        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Move an Item to a new Location.
     *
     * @param string $item_id   ID of the item
     * @param array  $parent_id ID of the parent folder to copy the item to
     * @param string $name      The new name for the copy. If not provided, the original name will be used.
     *
     * @throws OneDriveClientException
     *
     * @return object Copied Item
     */
    public function copy($item_id, $parent_id, $name = null)
    {
        //Drive Path
        $path = $this->getDrivePath()."/items/{$item_id}/action.copy";

        $uri = $this->buildUrl($path);

        $metadata = array('parentReference' => array('id' => $parent_id));

        if (!is_null($name)) {
            $metadata['name'] = $name;
        }

        //Json Encode Body
        $body = json_encode($metadata);

        //Submit an Async Copy Job
        $copyJob = $this->makeRequest('POST', $uri, [], $body, ['Prefer' => 'respond-async']);
        //Fetch the Job Status URL
        $jobStatusUrl = $copyJob->getHeader('Location');

        $newItem = null;
        $jobStatus = null;

        $jobCompleted = false;

        //While the Job is not completed,
        //keep inquiring the job status url for the job status
        while (!$jobCompleted) {
            //Fetch Job Status
            $jobStatus = $this->makeRequest('GET', $jobStatusUrl[0]);
            //Get the status code
            $statusCode = $jobStatus->getStatusCode();

            //If the Copying Job was completed
            if ($statusCode == '303' || $statusCode == '200') {
                //Mark Job as Completed
                $jobCompleted = true;
                //Set the Response
                $newItem = $this->decodeResponse($jobStatus);
            } else {
                //Decode the Status
                $status = $this->decodeResponse($jobStatus);
                //If the Job Failed
                if ($status->status === 'failed') {
                    throw new OneDriveClientException('API error when copying the file');
                }
                //wait some time until the next status check
                sleep(0.5);
            }
        }

        return $newItem;
    }

    /**
     * Delete an Item.
     *
     * @param string $item_id ID of the item
     *
     * @return string|bool If deleted, ID of the item is returned, else false
     */
    public function delete($item_id)
    {
        //Drive Path
        $path = $this->getDrivePath()."/items/{$item_id}";

        $uri = $this->buildUrl($path);

        $response = $this->makeRequest('DELETE', $uri);
        $responseContent = (object) array('id' => $item_id);

        return $responseContent;
    }

    /**
     * Create Sharing Link.
     *
     * @param string $item_id ID of the Item
     * @param string $type    The type of link to create: view|edit
     *
     * @return object Created Link
     */
    public function createShareLink($item_id, $type = 'view')
    {
        //Drive Path
        $path = $this->getDrivePath()."/items/{$item_id}/action.createLink";

        $uri = $this->buildUrl($path);

        $body = json_encode(array('type' => $type));

        $response = $this->makeRequest('POST', $uri, [], $body);

        $responseContent = $this->decodeResponse($response);

        return $responseContent;
    }

    /**
     * Move an Item to a new Location.
     *
     * @param string $url   Url to download content from
     * @param string $name  The new name for the copy. If not provided, the original name will be used.
     * @param array  $parent_id ID of the parent folder to copy the item to
     *
     * @throws OneDriveClientException
     *
     * @return object Downloaded Item
     */
    public function uploadFromUrl($url, $name = null, $parent_id = null)
    {
        //Drive Path
        $path = $this->getDrivePath();

        //If the parent id is not provided, use the drive root
        if (is_null($parent_id)) {
            $path .= '/root/children';
        } else {
            $path .= "/items/{$parent_id}/children";
        }

        $uri = $this->buildUrl($path);

        $metadata = array();

        $metadata["@content.sourceUrl"] = $url;
        $metadata['file'] = new \StdClass();

        if (!is_null($name)) {
            $metadata['name'] = $name;
        }

        //Json Encode Body
        $body = json_encode($metadata);

        //Submit an Async Download Job
        $downloadJob = $this->makeRequest('POST', $uri, [], $body, ['Prefer' => 'respond-async']);
        //Fetch the Job Status URL
        $jobStatusUrl = $downloadJob->getHeader('Location');

        $newItem = null;
        $jobStatus = null;

        $jobCompleted = false;

        //While the Job is not completed,
        //keep inquiring the job status url for the job status
        while (!$jobCompleted) {
            //Fetch Job Status
            $jobStatus = $this->makeRequest('GET', $jobStatusUrl[0]);
            //Get the status code
            $statusCode = $jobStatus->getStatusCode();

            //If the Downloading Job was completed
            if ($statusCode == '303' || $statusCode == '200') {
                //Mark Job as Completed
                $jobCompleted = true;
                //Set the Response
                $newItem = $this->decodeResponse($jobStatus);
            } else {
                //Decode the Status
                $status = $this->decodeResponse($jobStatus);
                //If the Job Failed
                if ($status->status === 'failed') {
                    throw new OneDriveClientException('API error when downloading the file');
                }
                //wait some time until the next status check
                sleep(0.5);
            }
        }

        return $newItem;
    }
}
