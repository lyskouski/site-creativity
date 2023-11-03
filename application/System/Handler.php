<?php namespace System;

/**
 * Application behaviour
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package System
 */
class Handler
{

    /**
     * Change PHP params in errors section
     */
    public function __construct ()
    {
        // ...

    }

    /**
     * Display HTML response in case of shutdown by \Error\TextInterface
     * @link http://php.net/manual/en/function.memory-get-peak-usage.php
     * @link http://php.net/manual/en/function.sys-getloadavg.php
     */
    public function catchShutdown ()
    {
        //echo '<pre>', Registry::logger();
        // memory_get_peak_usage( true );
        // sys_getloadavg();
    }

    /**
     * Disable XDEBUG mode for LIVE and user exceptions
     *
     * @param \Exception $oError
     */
    public function catchException(\Exception $oError)
    {
        $iErrorLvl = \Defines\Logger::ERROR;
        if ($oError instanceof \Error\TextInterface) {
            $iErrorLvl = $oError->getCode();
            $sFile = '';
            $iLine = 0;
            if (headers_sent($sFile, $iLine)) {
                $aBacktrace = array(
                    'file' => $sFile,
                    'line' => $iLine,
                    'trace' => $oError->getTrace()
                );
                Registry::logger()->emergency("Headers issue. Plotting error: {$oError->getMessage()}", $aBacktrace);
            }
            $oError->plotErrorPage();
        }
        Registry::logger()->log($iErrorLvl, $oError->getMessage(), $oError->getTrace());
    }

    /**
     * Catch application code errors
     *
     * @param integer $iNumber
     * @param string $sMessage
     * @param string $sFile
     * @param integer $iFileLine
     * @return null
     */
    public function catchError ( $iNumber, $sMessage, $sFile, $iFileLine )
    {
        $aBacktrace = array(
            'file' => $sFile,
            'line' => $iFileLine,
            'error_type' => $iNumber
        );
        switch ( true )
        {
            case E_RECOVERABLE_ERROR === $iNumber:
                $sError = 'Application internal error';
                Registry::logger()->emergency( "($iNumber) $sMessage", $aBacktrace );
                throw new \Error\Application( $sError );

            case in_array( $iNumber, array( E_ERROR, E_COMPILE_ERROR, E_COMPILE_WARNING, E_NOTICE ) ):
                Registry::logger()->error( $sMessage, $aBacktrace );
                break;

            case E_USER_WARNING === $iNumber:
                Registry::logger()->warning( $sMessage, $aBacktrace );
                break;

            case in_array( $iNumber, array( E_USER_NOTICE, E_DEPRECATED, E_STRICT ) ):
                Registry::logger()->notice( $sMessage, $aBacktrace );
                break;

            default:
                Registry::logger()->debug( "($iNumber) $sMessage", $aBacktrace );
        }
        // To avoid PHP internal error handler execution
        return;

    }

}
