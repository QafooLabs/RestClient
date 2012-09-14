<?php
/**
 * This file is part of the QafooLabs RestClient Component.
 *
 * @version $Revision$
 */

namespace QafooLabs\RestClient;

use \RuntimeException;
use \QafooLabs\ErrorHandler\Throwable;

/**
 * This type of exception will be thrown when the remote server returns an error
 * struct.
 *
 * @version $Revision$
 */
class RemoteException extends RuntimeException implements Throwable
{

}
