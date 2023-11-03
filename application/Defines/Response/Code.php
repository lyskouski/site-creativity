<?php namespace Defines\Response;

/**
 * Define all Request Methods that IS USED in IAC
 * @see ListInterface
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines/Response
 */
class Code extends \Defines\ListAbstract
{

    /**
     * @note Continue
     * @see \System\Registry::translation()->sys( "LB_HEADER_100" );
     *
     * This means that the server has received the request headers,
     * and that the client should proceed to send the request body
     */
    const E_CONTINUE = 100;

    /**
     * @note Switching Protocols
     * @see \System\Registry::translation()->sys( "LB_HEADER_101" );
     *
     * This means the requester has asked the server to switch protocols
     * and the server is acknowledging that it will do so.
     */
    const E_SW_PROTOCOLS = 101;

    /**
     * @note Ok
     * @see \System\Registry::translation()->sys( "LB_HEADER_200" );
     *
     * Standard response for successful HTTP requests. The actual response will depend on the request method used.
     * In a GET request, the response will contain an entity corresponding to the requested resource.
     * In a POST request, the response will contain an entity describing or containing the result of the action.
     */
    const E_OK = 200;

    /**
     * @note Created
     * @see \System\Registry::translation()->sys( "LB_HEADER_201" );
     *
     * The request has been fulfilled and resulted in a new resource being created.
     */
    const E_CREATED = 201;

    /**
     * @note Accepted
     * @see \System\Registry::translation()->sys( "LB_HEADER_202" );
     *
     * The request has been accepted for processing, but the processing has not been completed.
     * The request might or might not eventually be acted upon,
     * as it might be disallowed when processing actually takes place.
     */
    const E_ACCEPTED = 202;

    /**
     * @note Non-Authoritative Information
     * @see \System\Registry::translation()->sys( "LB_HEADER_203" );
     *
     * The server successfully processed the request, but is returning information that may be from another source.
     */
    const E_NON_AUTHORITATIVE = 203;

    /**
     * @note No Content
     * @see \System\Registry::translation()->sys( "LB_HEADER_204" );
     *
     * The server successfully processed the request, but is not returning any content.
     */
    const E_NO_CONTENT = 204;

    /**
     * @note Reset Content
     * @see \System\Registry::translation()->sys( "LB_HEADER_205" );
     *
     * The server successfully processed the request, but is not returning any content. Unlike a 204 response,
     * this response requires that the requester reset the document view.
     */
    const E_RESET_CONTENT = 205;

    /**
     * @note Partial Content
     * @see \System\Registry::translation()->sys( "LB_HEADER_206" );
     *
     * The server is delivering only part of the resource (byte serving) due to a range header sent by the client.
     * The range header is used by HTTP clients to enable resuming of interrupted downloads,
     * or split a download into multiple simultaneous streams.
     */
    const E_PARTIAL_CONTENT = 206;


    /**
     * @note 300 Multiple Choices
     * @see \System\Registry::translation()->sys( "LB_HEADER_300" );
     *
     * Indicates multiple options for the resource that the client may follow.
     * @sample could be used to present different
     * - format options for video,
     * - list files with different extensions,
     * - word sense disambiguation.
     */
    const E_MULTIPLE_CHOICES = 300;

    /**
     * @note 301 Moved Permanently
     * @see \System\Registry::translation()->sys( "LB_HEADER_301" );
     *
     * This and all future requests should be directed to the given URI.
     */
    const E_MOVED = 301;

    /**
     * @note 302 Found
     * @see \System\Registry::translation()->sys( "LB_HEADER_302" );
     *
     * This is an example of industry practice contradicting the standard.
     * The HTTP/1.0 specification (RFC 1945) required the client to perform
     * a temporary redirect (the original describing phrase was "Moved Temporarily"),
     * [6] but popular browsers implemented 302 with the functionality of a 303 See Other.
     * Therefore, HTTP/1.1 added status codes 303 and 307 to distinguish between the two behaviours.
     * [7] However, some Web applications and frameworks use the 302 status code as if it were the 303.[8]
     */
    const E_GOTO = 302;


    /**
     * @note 303 See Other (since HTTP/1.1)
     * @see \System\Registry::translation()->sys( "LB_HEADER_303" );
     *
     * The response to the request can be found under another URI using a GET method.
     * When received in response to a POST (or PUT/DELETE), it should be assumed that
     * the server has received the data and the redirect should be issued with a separate GET message.
     */
    const E_OTHER_URL = 303;

    /**
     * @note 304 Not Modified (RFC 7232)
     * @see \System\Registry::translation()->sys( "LB_HEADER_304" );
     *
     * Indicates that the resource has not been modified since the version specified by
     * the request headers If-Modified-Since or If-None-Match.
     * This means that there is no need to retransmit the resource,
     * since the client still has a previously-downloaded copy.
     */
    const E_NOT_MODIFIED = 304;


    /**
     * @note 307 Temporary Redirect (since HTTP/1.1)
     * @see \System\Registry::translation()->sys( "LB_HEADER_307" );
     *
     * In this case, the request should be repeated with another URI;
     * however, future requests should still use the original URI.
     * In contrast to how 302 was historically implemented,
     * the request method is not allowed to be changed when reissuing
     * the original request. For instance,
     * a POST request should be repeated using another POST request.[12]
     */
    const E_REDIRECT = 307;


    /**
     * @note 400 Bad Request
     * @see \System\Registry::translation()->sys( "LB_HEADER_400" );
     *
     * The server cannot or will not process the request due to something
     * that is perceived to be a client error
     */
    const E_BAD_REQUEST = 400;

    /**
     * @note 401 Unauthorized (RFC 7235)
     * @see \System\Registry::translation()->sys( "LB_HEADER_401" );
     *
     * Similar to 403 Forbidden, but specifically for use
     * when authentication is required and has failed or has not yet been provided.
     * The response must include a WWW-Authenticate header field containing
     * a challenge applicable to the requested resource.
     * @see Basic access authentication and Digest access authentication.
     */
    const E_UNAUTHORIZED = 401;


    /**
     * @note 402 Payment Required
     * @see \System\Registry::translation()->sys( "LB_HEADER_402" );
     *
     * The original intention was that this code might be used
     * as part of some form of digital cash or micropayment scheme,
     * but that has not happened, and this code is not usually used.
     */
    const E_PAYMENT_REQUIRED = 402;

    /**
     * @note 403 Forbidden
     * @see \System\Registry::translation()->sys( "LB_HEADER_403" );
     *
     * The request was a valid request, but the server is refusing to respond to it.
     * Unlike a 401 Unauthorized response, authenticating will make no difference.
     */
    const E_FORBIDDEN = 403;

    /**
     * @note 404 Not Found
     * @see \System\Registry::translation()->sys( "LB_HEADER_404" );
     *
     * The requested resource could not be found
     * but may be available again in the future.
     * Subsequent requests by the client are permissible.
     */
    const E_NOT_FOUND = 404;

    /**
     * @note 405 Method Not Allowed
     * @see \System\Registry::translation()->sys( "LB_HEADER_405" );
     *
     * A request was made of a resource using a request method not supported by that resource
     */
    const E_NOT_ALLOWED = 405;

    /**
     * @note 406 Not Acceptable
     * @see \System\Registry::translation()->sys( "LB_HEADER_406" );
     *
     * The requested resource is only capable of generating content
     * not acceptable according to the Accept headers sent in the request.
     */
    const E_NOT_ACCEPTABLE = 406;

    /**
     * @note 409 Conflict
     * @see \System\Registry::translation()->sys( "LB_HEADER_409" );
     *
     * Indicates that the request could not be processed because
     * of conflict in the request, such as an edit conflict in the case of multiple updates.
     */
    const E_CONFLICT = 409;

    /**
     * @note 410 Gone
     * @see \System\Registry::translation()->sys( "LB_HEADER_410" );
     *
     * Indicates that the resource requested is no longer available
     * and will not be available again.
     */
    const E_DELETED = 410;

    /**
     * @note 417 Expectation Failed
     * @see \System\Registry::translation()->sys( "LB_HEADER_417" );
     *
     * The server cannot meet the requirements of the Expect request-header field.
     */
    const E_FAILED = 417;

    /**
     * @note 419 Authentication Timeout (not in RFC 2616)
     * @see \System\Registry::translation()->sys( "LB_HEADER_419" );
     *
     * Not a part of the HTTP standard,
     * 419 Authentication Timeout denotes that previously valid authentication has expired.
     * It is used as an alternative to 401 Unauthorized in order to differentiate
     * from otherwise authenticated clients being denied access to specific server resources.
     */
    const E_AUTH_TIMEOUT = 419;

    /**
     * @note 423 Locked (WebDAV; RFC 4918)
     * @see \System\Registry::translation()->sys( "LB_HEADER_423" );
     *
     * The resource that is being accessed is locked.
     */
    const E_LOCKED = 423;

    /**
     * @note 429 Too Many Requests (RFC 6585)
     * @see \System\Registry::translation()->sys( "LB_HEADER_429" );
     *
     * The user has sent too many requests in a given amount of time.
     * Intended for use with rate limiting schemes.
     */
    const E_MANY_REQUESTS = 429;


    /**
     * @note 500 Internal Server Error
     * @see \System\Registry::translation()->sys( "LB_HEADER_500" );
     *
     * A generic error message, given when an unexpected
     * condition was encountered and no more specific message is suitable.
     */
    const E_FATAL = 500;

    /**
     * @note 503 Service Unavailable
     * @see \System\Registry::translation()->sys( "LB_HEADER_503" );
     *
     * The server is currently unavailable (because it is overloaded or down for maintenance).
     * Generally, this is a temporary state.
     */
    const E_UNAVAILABLE = 503;

    /**
     * Get header translation
     *
     * @param integer $code
     * @param string $language - see \Defines\Language
     * @return string
     */
    public static function getHeader($code, $language = \Defines\Language::EN)
    {
        return \System\Registry::translation()->sys("LB_HEADER_{$code}", $language);
    }

    public static function getDefault()
    {
        return self::E_OK;
    }
}
