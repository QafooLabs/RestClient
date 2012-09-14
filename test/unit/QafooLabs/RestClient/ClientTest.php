<?php
/**
 * This file is part of the QafooLabs RestClient Component.
 *
 * @version $Revision$
 */

namespace QafooLabs\RestClient;

/**
 * Test case for the REST client class.
 *
 * @version $Revision$
 * @covers \QafooLabs\RestClient\Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $server;

    /**
     * @var string
     */
    private $path;

    /**
     * Configures the temporary server location and the rest service path.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->server = 'file://' . sys_get_temp_dir();
        $this->path   = '/' . uniqid( 'rest_client_' );
    }

    /**
     * Removes temporary resources created by a test.
     *
     * @return void
     */
    protected function tearDown()
    {
        if ( file_exists( $this->server . $this->path ) )
        {
            unlink( $this->server . $this->path );
        }
        parent::tearDown();
    }

    /**
     * Returns a mocked instance of {@link \QafooLabs\RestClient\Client} where
     * only the <b>request()</b> method is mocked.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getClient()
    {
        return $this->getMock(
            '\QafooLabs\RestClient\Client',
            array( 'request' ),
            array( 'http://example.com' )
        );
    }

    /**
     * testDeleteDelegatesWithExpectedMethodToRequest
     *
     * @return void
     */
    public function testDeleteDelegatesWithExpectedMethodToRequest()
    {
        $client = $this->getClient();
        $client->expects( $this->once() )
            ->method( 'request' )
            ->with(
                $this->equalTo( Client::DELETE ),
                $this->equalTo( '/test' ),
                $this->equalTo( 42 )
            );

        $client->delete( '/test', 42 );
    }

    /**
     * testGetDelegatesWithExpectedMethodToRequest
     *
     * @return void
     */
    public function testGetDelegatesWithExpectedMethodToRequest()
    {
        $client = $this->getClient();
        $client->expects( $this->once() )
            ->method( 'request' )
            ->with(
                $this->equalTo( Client::GET ),
                $this->equalTo( '/test' ),
                $this->equalTo( null )
            );

        $client->get( '/test' );
    }

    /**
     * testPostDelegatesWithExpectedMethodToRequest
     *
     * @return void
     */
    public function testPostDelegatesWithExpectedMethodToRequest()
    {
        $client = $this->getClient();
        $client->expects( $this->once() )
            ->method( 'request' )
            ->with(
                $this->equalTo( Client::POST ),
                $this->equalTo( '/test' ),
                $this->equalTo( array( 'foo' => 'bar' ) )
            );

        $client->post( '/test', array( 'foo' => 'bar' ) );
    }

    /**
     * testPutDelegatesWithExpectedMethodToRequest
     *
     * @return void
     */
    public function testPutDelegatesWithExpectedMethodToRequest()
    {
        $client = $this->getClient();
        $client->expects( $this->once() )
            ->method( 'request' )
            ->with(
                $this->equalTo( Client::PUT ),
                $this->equalTo( '/test' ),
                $this->equalTo( true )
            );

        $client->put( '/test', true );
    }

    /**
     * testGetAppendsQueryStringForNonEmptyParameterArray
     *
     * @return void
     */
    public function testGetAppendsQueryStringForNonEmptyParameterArray()
    {
        $client = $this->getClient();
        $client->expects( $this->once() )
            ->method( 'request' )
            ->with(
                $this->equalTo( Client::GET ),
                $this->equalTo( '/test?foo=42&bar=23' ),
                $this->equalTo( null )
            );

        $client->get( '/test', array( 'foo' => 42, 'bar' => 23 ) );
    }

    /**
     * testGetNotAppendsQueryStringForEmptyParameterArray
     *
     * @return void
     */
    public function testGetNotAppendsQueryStringForEmptyParameterArray()
    {
        $client = $this->getClient();
        $client->expects( $this->once() )
            ->method( 'request' )
            ->with(
                $this->equalTo( Client::GET ),
                $this->equalTo( '/test' ),
                $this->equalTo( null )
            );

        $client->get( '/test', array() );
    }

    /**
     * Writes data to the temporary test path.
     *
     * @param mixed $data Temporary test data
     * @return void
     */
    private function writeTestPath( $data )
    {
        file_put_contents( $this->server . $this->path, json_encode( $data ) );
    }

    /**
     * testRequestReturnsExpectedResult
     *
     * @return void
     */
    public function testRequestReturnsExpectedResult()
    {
        $expected      = new \stdClass();
        $expected->foo = 42;
        $expected->bar = 23;

        $this->writeTestPath( $expected );

        $client = new Client( $this->server );
        $actual = $client->request( 'GET', $this->path );

        $this->assertEquals( $expected, $actual );
    }

    /**
     * testRequestThrowsExpectedExceptionWhenResponseContainsErrorFlag
     *
     * @return void
     * @covers \QafooLabs\RestClient\RemoteException
     * @expectedException \QafooLabs\RestClient\RemoteException
     */
    public function testRequestThrowsExpectedExceptionWhenResponseContainsErrorFlag()
    {
        $this->writeTestPath( array( 'error' => true, 'type' => 42, 'message' => 'A message' ) );

        $client = new Client( $this->server );
        $client->request( 'GET', $this->path );
    }

    /**
     * Returns the test suite with all tests declared in this class.
     *
     * @return \PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        return new PHPUnit_Framework_TestSuite( __CLASS__ );
    }
}
