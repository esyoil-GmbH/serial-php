<?php
namespace Serial;

class Serial {

    /** @var int $baud baud rate of the serial port */
    private $baud;
    private $dataBits;
    private $stopBits;
    private $port;

    private $portHandler;

    /**
     * Serial constructor.
     * @param $port
     * @param $baud
     * @param $dataBits
     * @param $stopBits
     */
    public function __construct($port, $baud, $dataBits, $stopBits) {
        $this->port = $port;
        $this->baud = $baud;
        $this->dataBits = $dataBits;
        $this->stopBits = $stopBits;
    }

    /**
     *
     */
    public function setup() {

        if (!file_exists($this->port)) trigger_error("Invalid Device", E_USER_ERROR);

        $command = sprintf( "stty -F %s %d cs%d", $this->port, $this->baud, $this->dataBits );
        $command .= " ignbrk -brkint -imaxbel -opost -onlcr -isig -icanon -iexten";
        $command .= " -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts";
        if ( $this->stopBits == 1 ) {
            $command .= " -cstopb";
        } else if ( $this->stopBits == 2 ) {
            $command .= " cstopb";
        }
    
        exec($command);
    }

    /**
     *
     */
    public function open() {

        $this->portHandler = fopen($this->port, "w+");
        if (!$this->portHandler) {
            trigger_error("Error while opening device", E_USER_ERROR);
            die();
        }
    }

    /**
     *
     */
    public function close() {
        fclose($this->portHandler);
    }

    /**
     * @return string $data the data received over the port
     */
    public function read() {
        switch ( func_num_args( ) ) {
          case 0: #No args, read to 0x00
            $data = stream_get_line( $this->portHandle, 0, 0x00 );
            break;
          case 1: #Length specified or read to 0x00
            $data = stream_get_line( $this->portHandle, func_get_arg( 0 ), 0x00 );
            break;
          case 2: #Length and end character specified
            $data = stream_get_line( $this->portHandle, func_get_arg( 0 ), func_get_arg( 1 ) );
            break;
          default:
            trigger_error( "Invalid argument count", E_USER_ERROR );
            break;
        }
        return $data;
    }

    /**
     *
     */
    public function write() {
        switch (func_num_args()) {
            case 1:
                fwrite($this->portHandler, func_get_arg(0) . 0x00);
                break;
            case 2:
                fwrite($this->portHandler, func_get_arg(0) . func_get_arg(1));
                break;
            default:
                trigger_error("Invalid Argument count", E_USER_ERROR);
                break;
        }
    }
}