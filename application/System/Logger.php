<?php namespace System;

/**
 * Logger class
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package System
 */
class Logger implements \Doctrine\DBAL\Logging\SQLLogger
{

    const DELIMITER = "\n---\n";

    /**
     * @var integer
     */
    protected $iLvl;

    /**
     * @var resource
     */
    protected $stream;

    /**
     * @var boolean
     */
    protected $bSingle = true;
    protected $query;
    protected $queryCount = 0;
    protected $queryTime = 0;

    public function __construct($iLvl)
    {
        $this->iLvl = (new \Engine\Validate\Common)->getLogger($iLvl, true);
        $this->stream = fopen('php://temp', 'w+');
    }

    public function __destruct()
    {
        if ($this->bSingle) {
            fclose($this->stream);
        }
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->queryCount++;
        $this->query = array(
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
            'time' => microtime()
        );
    }

    public function stopQuery()
    {
        $time = (new Converter\DateDiff)->getMkDiff($this->query['time']);
        $this->queryTime += $time;
        unset($this->query['time']);
        $this->info('Doctrine query logger', [$this->query, number_format($time, 4) . ' s']);
    }

    public function getLvl()
    {
        return $this->iLvl;
    }

    public function getStream()
    {
        $this->bSingle = false;
        return $this->stream;
    }

    public function __toString()
    {
        $this->info('Total number of queries (time)', [$this->queryCount, $this->queryTime]);
        $this->info('Memory Peak', [$this->getMemoryPeak()]);
        return stream_get_contents($this->stream, -1, 0);
    }

    public function getMemoryPeak()
    {
        $iUse = memory_get_peak_usage(true);
        if ($iUse < 1024) {
            $sMem = $iUse . ' bytes';
        } elseif ($iUse < 1048576) {
            $sMem = round($iUse / 1024, 2) . ' Kb';
        } else {
            $sMem = round($iUse / 1048576, 2) . ' Mb';
        }
        return $sMem;
    }

    public function filter(Logger $oAnotherLogger)
    {
        if ($this->iLvl === $oAnotherLogger->getLvl()) {
            fclose($this->stream);
            $this->stream = $oAnotherLogger->getStream();
            return;
        }
        $aCode = array();
        $aLogs = explode(self::DELIMITER, (string) $oAnotherLogger);
        for ($i = 0, $iSize = count($aLogs); $i < $iSize; $i++) {
            preg_match("/\|(\d){1,}\]/", $aLogs[$i], $aCode);
            if (isset($aCode[1]) && $aCode[1] <= $this->iLvl) {
                $aValues = explode($aCode[0], $aLogs[$i]);
                $this->log(
                    $aCode[1], strtok($aValues[1], "\n"), array(
                    'actual' => $this->iLvl,
                    'prev' => $oAnotherLogger->getLvl(),
                    'clone' => trim(substr($aValues[1], strpos($aValues[1], "\n")))
                    )
                );
            }
        }
    }

    public function log($iLvl, $sMessage, array $aContext = array())
    {
        if ($iLvl > $this->iLvl) {
            return;
        }

        if (!$aContext) {
            $oEx = new \Exception();
            $aContext = $oEx->getTrace();
        }

        $sLog = (new \DateTime)->format('Y-m-d H:i:s.u')
            . ' [' . \Defines\Logger::getName($iLvl)
            . "|$iLvl] <b>$sMessage</b> \n "
        //    . print_r(array_slice($aContext, 0, 3), true)// json_encode( $aContext )
            . self::DELIMITER;
        fwrite($this->stream, $sLog);
    }

    public function emergency($sMessage, array $aContext = array())
    {
        return $this->log(\Defines\Logger::EMERGENCY, $sMessage, $aContext);
    }

    public function alert($sMessage, array $aContext = array())
    {
        return $this->log(\Defines\Logger::ALERT, $sMessage, $aContext);
    }

    public function critical($sMessage, array $aContext = array())
    {
        return $this->log(\Defines\Logger::CRITICAL, $sMessage, $aContext);
    }

    public function error($sMessage, array $aContext = array())
    {
        return $this->log(\Defines\Logger::ERROR, $sMessage, $aContext);
    }

    public function warning($sMessage, array $aContext = array())
    {
        return $this->log(\Defines\Logger::WARNING, $sMessage, $aContext);
    }

    public function notice($sMessage, array $aContext = array())
    {
        return $this->log(\Defines\Logger::NOTICE, $sMessage, $aContext);
    }

    public function info($sMessage, array $aContext = array())
    {
        return $this->log(\Defines\Logger::INFO, $sMessage, $aContext);
    }

    public function debug($sMessage, array $aContext = array())
    {
        return $this->log(\Defines\Logger::DEBUG, $sMessage, $aContext);
    }
}
