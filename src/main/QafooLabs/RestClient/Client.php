<?php
/**
 * This file is part of the QafooLabs RestClient Component.
 *
 * @version $Revision$
 */

namespace QafooLabs\RestClient\Rest;

/**
 * Helper class that provides some basic REST functionality.
 *
 * This class provides helper methods for common HTTP request methods like
 * <em>DELETE</em>, <em>GET</em>, <em>POST</em> and <em>PUT</em>.
 *
 * <code>
 * $client = new Client( 'http://example.com' );
 * $client->get( '/objects' );
 * $client->put( '/objects', $obj );
 * $client->post( '/objects', $obj );
 * $client->delete( '/objects/42' );
 * </code>
 *
 * The ctor of this class expects the remote REST server as argument. This
 * includes host/ip, port and protocol.
 *
 * @version $Revision$
 */
class Client
{
    /**
     * Wrapped HTTP request methods.
     */
    const DELETE = 'DELETE',
          GET    = 'GET',
          POST   = 'POST',
          PUT    = 'PUT';

    /**
     * Optional default headers for each request.
     *
     * @var string
     */
    private $header = '';

    /**
     * The remote REST server location.
     *
     * @var string
     */
    private $server;

    /**
     * Constructs a new REST client instance for the given <b>$server</b>.
     *
     * @param string $server Remote server location. Must include the used protocol.
     */
    public function __construct( $server )
    {
        $url = parse_url( rtrim( $server, '/' ) );
        $url += array(
            'scheme' => 'http',
            'host'   => null,
            'port'   => null,
            'user'   => null,
            'pass'   => null,
            'path'   => null,
        );

        if ( $url['user'] || $url['pass'] )
        {
            $this->header = 'Authorization: Basic ' .
                             base64_encode( "{$url['user']}:{$url['pass']}" ) . "\r\n";
        }

        $this->server = $url['scheme'] . '://' . $url['host'];
        if ( $url['port'] )
        {
            $this->server .= ':' . $url['port'];
        }
        $this->server .= $url['path'];
    }

    /**
     * Execute a HTTP DELETE request to the remote server
     *
     * Returns the parse JSON result from the remote server.
     *
     * @param string $path
     * @param mixed $body
     * @return mixed
     */
    public function delete( $path, $body = null )
    {
        return $this->request( self::DELETE, $path, $body );
    }

    /**
     * Execute a HTTP GET request to the remote server
     *
     * Returns the parse JSON result from the remote server.
     *
     * @param string $path
     * @param array $query
     * @param mixed $body
     * @return mixed
     */
    public function get( $path, array $query = null, $body = null )
    {
        if ( $query )
        {
            $path .= '?' . http_build_query( $query );
        }
        return $this->request( self::GET, $path, $body );
    }

    /**
     * Execute a HTTP POST request to the remote server
     *
     * Returns the parse JSON result from the remote server.
     *
     * @param string $path
     * @param mixed $body
     * @return mixed
     */
    public function post( $path, $body = null )
    {
        return $this->request( self::POST, $path, $body );
    }

    /**
     * Execute a HTTP PUT request to the remote server
     *
     * Returns the parse JSON result from the remote server.
     *
     * @param string $path
     * @param mixed $body
     * @return mixed
     */
    public function put( $path, $body = null )
    {
        return $this->request( self::PUT, $path, $body );
    }

    /**
     * Execute a HTTP request to the remote server
     *
     * Returns the parse JSON result from the remote server.
     *
     * @param string $method
     * @param string $path
     * @param mixed $body
     * @return mixed
     */
    public function request( $method, $path, $body = null )
    {
        if ( false === is_null( $body ) && false === is_string( $body ) )
        {
            $body = json_encode( $body );
        }

        $data = json_decode( file_get_contents(
            $this->server . $path,
            false,
            stream_context_create(
                array(
                    'http' => array(
                        'method'        => $method,
                        'content'       => $body,
                        'ignore_errors' => true,
                        'header'        => $this->header .
                                           "Content-type: application/json\r\n" .
                                           "Accept: application/json;q=1.0",
                    ),
                )
            )
        ) );

        if ( isset( $data->error ) )
        {
            throw new RemoteException( "HTTP Error {$data->type}: {$data->message}", $data->type );
        }

        return $data;
    }
}
