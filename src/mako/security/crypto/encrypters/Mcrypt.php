<?php

/**
 * @copyright  Frederic G. Østby
 * @license    http://www.makoframework.com/license
 */

namespace mako\security\crypto\encrypters;

use \mako\security\crypto\padders\PadderInterface;

/**
 * MCrypt encrypter.
 *
 * @author  Frederic G. Østby
 */

class MCrypt extends \mako\security\crypto\encrypters\Encrypter implements \mako\security\crypto\encrypters\EncrypterInterface
{
	//---------------------------------------------
	// Class properties
	//---------------------------------------------

	/**
	 * Key used to encrypt/decrypt string.
	 *
	 * @var string
	 */
	
	protected $key;

	/**
	 * Padder instance.
	 * 
	 * @var \mako\security\crypto\padders\PadderInterface
	 */

	protected $padder;

	/**
	 * The cipher method to use for encryption.
	 *
	 * @var int
	 */
	
	protected $cipher;
	
	/**
	 * Encryption mode.
	 *
	 * @var int
	 */
	
	protected $mode;

	/**
	 * Key size.
	 * 
	 * @var int
	 */

	protected $keySize;
	
	/**
	 * Initialization vector size.
	 *
	 * @var string
	 */
	
	protected $ivSize;

	//---------------------------------------------
	// Class constructor, destructor etc ...
	//---------------------------------------------

	/**
	 * Constructor.
	 * 
	 * @access  public
	 * @param   string                                         $key     Encryption key
	 * @param   \mako\security\crypto\padders\PadderInterface  $padder  Padder instance
	 * @param   int                                            $cipher  (optional) Cipher
	 * @param   int                                            $mode    (optional) Mode
	 */

	public function __construct($key, PadderInterface $padder, $cipher = null, $mode = null)
	{
		$this->key = $key;

		$this->padder = $padder;

		$this->cipher = $cipher ?: MCRYPT_RIJNDAEL_256;

		$this->mode = $mode ?: MCRYPT_MODE_ECB;

		$this->keySize = mcrypt_get_key_size($this->cipher, $this->mode);

		$this->ivSize = mcrypt_get_iv_size($this->cipher, $this->mode);
	}

	//---------------------------------------------
	// Class methods
	//---------------------------------------------

	/**
	 * Encrypts string.
	 *
	 * @access  public
	 * @param   string  $string  String to encrypt
	 * @return  string
	 */
	
	public function encrypt($string)
	{
		$blockSize = mcrypt_get_block_size($this->cipher, $this->mode);

		$string = $this->padder->addPadding($string, $blockSize);

		$iv = mcrypt_create_iv($this->ivSize, MCRYPT_DEV_URANDOM);

		$key = $this->deriveKey($this->key, $iv, $this->keySize);
		
		return base64_encode($iv . mcrypt_encrypt($this->cipher, $key, $string, $this->mode, $iv));
	}

	/**
	 * Decrypts string.
	 *
	 * @access  public
	 * @param   string          $string  String to decrypt
	 * @return  string|boolean
	 */
	
	public function decrypt($string)
	{
		$string = base64_decode($string, true);
		
		if($string === false)
		{
			return false;
		}
		
		$iv = substr($string, 0, $this->ivSize);

		$key = $this->deriveKey($this->key, $iv, $this->keySize);
		
		$string = substr($string, $this->ivSize);

		return $this->padder->stripPadding(mcrypt_decrypt($this->cipher, $key, $string, $this->mode, $iv));
	}
}