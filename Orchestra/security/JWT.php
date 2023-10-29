<?php

namespace Orchestra\security;

class JWT{

    public static function generate_jwt($headers, $payload, $secret = 'secret') {
        $headers_encoded = self::base64url_encode(json_encode($headers));

        $payload_encoded = self::base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = self::base64url_encode($signature);

        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";

        return $jwt;
    }

    public static function decode($token, $secretKey) {
        $tokenParts = explode('.', $token);
    
        if (count($tokenParts) !== 3) {
            // The token should have three parts: header, payload, and signature
            return false;
        }
    
        list($header, $payload, $signature) = $tokenParts;
    
        // Decode the header and payload JSON
        $decodedHeader = json_decode(base64_decode($header), true);
        $decodedPayload = json_decode(base64_decode($payload), true);
    
        if (!$decodedHeader || !$decodedPayload) {
            // Invalid JSON in header or payload
            return false;
        }
    
        // Verify the signature
        $expectedSignature = hash_hmac('sha256', "$header.$payload", $secretKey, true);
        $decodedSignature = base64_decode($signature);
    
        if ($expectedSignature !== $decodedSignature) {
            // Signature verification failed
            return false;
        }
    
        // Token is valid, return the decoded payload
        return $decodedPayload;
    }

    public static function base64url_encode($str) {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    public static function guidv4(): string
    {

        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}