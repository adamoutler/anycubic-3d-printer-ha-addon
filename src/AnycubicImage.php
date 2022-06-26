<?php
namespace anycubic;
/**
 * This class is used to hold metadata and content of an image from an
 * Anycubic 3D printer transferred over uart_wifi protocol.
 */
class AnycubicImage {
    /**
     * Name of the file.
     */
    private $__filename = "";
    /**
     * Image Width.
     */
    private $__width = 0;
    /**
     * Image height.
     */
    private $__height = 0;
    /**
     * Image Contents;
     */
    private $__string_contents = "";
    /**
     * Count of the pixels in the image.
     */
    private $__pixels = 0;
    /**
     * The contents of the image in hex format.
     */
    private $__int_content = [];
    /**
     * image validation
     */
    private $__valid_image = false;

    /**
     * Public constructor.  The image generation process starts when metadata is
     * received. So, when creating this object, we require metadata.
     * @param string metadata a comma delmited value of the getPreview1 result.
     */
    public function __construct($metadata) {
        $this->setMetadata($metadata);
    }

    /**
     * parse 'getPreview1,144.pwmb,224,168,180,end' into appropriate variables.
     */
    public function setMetadata(String $image_metadata_string) {
        $vars = explode(',', $image_metadata_string);
        $this->__filename = $vars[1];
        $this->__width = intval($vars[2]);
        $this->__height = intval($vars[3]);
        $this->__pixels = intval($this->__width * $this->__height);
    }

    /**
     * get the number of bytes expected to receive from the printer to represent
     * the image.
     * @return int the total number of bytes expected.
     */
    public function getExpectedBytes() {
        return $this->__pixels * 2;
    }

    /**
     * Gets the expected number of pixels.
     * @return int the number of pixels.
     */
    public function getExpectedPixels() {
        return $this->__pixels;
    }

    /**
     * Put raw data from socket into the contents
     */
    public function setContents(String $contents) {

        $this->__string_contents = $contents;
        $this->__valid_image = (strlen($this->__string_contents) / 2 > ($this->__width * $this->__height));
        $int_array = unpack('S*', $contents);
        foreach ($int_array as $i) {
            array_push($this->__int_content, $i);
        }
    }

    /**
     * Get the contents of the image.
     * @return string the contents in string format.
     */
    public function getContents() {
        return $this->__string_contents;
    }

    /**
     * Get the name of the image. Note this is internal name, not display name.
     * @return string The the name of the image.
     */
    public function getFilename() {
        return $this->__filename;
    }

    /**
     * Validation checks are performed on the Telnet stream when image is received.  This
     * represents those checks.
     * @return boolean true if the number of received bytes matches the expected bytes.
     */
    public function isValid() {
        return $this->__valid_image;
    }

    /**
     * Converts an RGB565 pixel to standard RGB format.
     */
    private function __get_RGB888_from_RGB565_int($color565) {
        /* DO NOT TOUCH UNDER ANY CIRCUMSTANCES */
        $r = ($color565&0x001f) << 3;
        $g = (($color565 >> 5)&0x003f) << 2;
        $b = (($color565 >> 11)&0x001f) << 3;
        $rgb_888_Color = ($r << 16) | ($g << 8) | $b;
        return $rgb_888_Color;
    }

    /**
     * Get the GD Image.
     *
     * TODO: when i document the return GdImage it comes out as a namespace object
     * returns GdImage a standard image representation used for exporting.
     */
    public function getImage() {

        $byte_array = $this->__int_content;
        $im = imagecreatetruecolor($this->__width, $this->__height);
        $bla = imagecolorallocate($im, 0, 0, 0);
        $y = 0;
        $charArray = [];

        for ($pixel = 0; $pixel < sizeof($byte_array); $pixel++) {
            $rgb565 = $byte_array[$pixel];
            array_push($charArray, $rgb565);
            $x = floor($pixel % $this->__width);
            $y = floor($pixel / $this->__width);
            $color = $this->__get_RGB888_from_RGB565_int($rgb565);

            imagesetpixel($im, $x, $y, $color);
        }
        imagecolortransparent($im, 18528);
        return $im;
    }

    /**
     * Serializes this AnycubicImage.  This is useful for testing.
     * @return string the string representation of this object in serial format.
     */

    public function serialize() {
        return serialize($this);
    }

    /**
     * Unserialize a string representation of this object. This is useful for testing
     *
     * @param string $serialized_data An AnycubicImage in string format
     * @return AnycubicImage A new instance of this object based on the provided data.
     */
    public static function unSerialize($serialized_data) {
        return unserialize($serialized_data);
    }
}