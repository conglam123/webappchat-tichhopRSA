<?php
class RSA
{
    private  $privateKey;
    private $publicKey;
    public function __construct()
    {
        if (file_exists(dirname(__FILE__) . "/private.key") && file_exists(dirname(__FILE__) . "/public.key")) {
            $this->privateKey = file_get_contents(dirname(__FILE__) . "/private.key");
            $this->publicKey = file_get_contents(dirname(__FILE__) . "/public.key");
        } else {
            // Config RSA
            $config = array(
                "config" => dirname(__FILE__) . '/openssl.cnf',
                "digest_alg" => "sha512",
                "private_key_bits" => 2048,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            );

            // Create the private and public key
            $res = openssl_pkey_new($config);
            if ($res === false) die('Failed to generate key pair.' . "\n");
            if (!openssl_pkey_export($res, $this->privateKey, null, $config)) die('Failed to retrieve private key.' . "\n");

            // Extract the private key from $res to $privKey
            openssl_pkey_export($res, $this->privateKey, null, $config);
            file_put_contents("../RSA/private.key", $this->privateKey);

            // Extract the public key from $res to $pubKey
            $pubKey = openssl_pkey_get_details($res);
            $this->publicKey = $pubKey["key"];
            file_put_contents("../RSA/public.key", $this->publicKey);
        }
    }
    public function encrypt($data)
    {
        if (openssl_public_encrypt($data, $encrypted, $this->publicKey))
            $data = base64_encode($encrypted);
        else
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');
        return $data;
    }

    public function decrypt($data)
    {
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $this->privateKey))
            $data = $decrypted;
        else
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');
        return $data;
    }

    // public function encryptImage($data){
    //     $path = $data;
    //     $type = pathinfo($path, PATHINFO_EXTENSION);
    //     $data=file_get_contents($path);
    //     $base64 = 'data:image/' . $type . ';base64,' .base64_encode($data);
    //     return $base64;
    // }
}
