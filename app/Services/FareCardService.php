<?php

namespace App\Services;

use App\Models\CardModel;
use phpseclib4\Crypt\EC;
use phpseclib4\Crypt\EC\PublicKey;
use phpseclib4\Crypt\EC\PrivateKey;

class FareCardService
{
    private PrivateKey $ca_private_key;
    private int $client_id;
    public PrivateKey $private_key;
    public PublicKey $public_key;

    public function __construct(int $client_id){
        $this->client_id = $client_id;

        $this->ca_private_key = service("caPrivateKey");
    }

    # 4 byte header + data
    private function sshEncode($data){
        return pack("N", strlen($data)).$data;
    }

    // AI assisted
    private function signCertificate(PrivateKey $ca_key, string $card_pubkey_raw, string $cert_id, array $principals,
        int $serial, int $validAfter, int $validBefore): string{
        $cert_type = 'ssh-ed25519-cert-v01@openssh.com';
        $nonce = random_bytes(32);

        $ca_pubkey = $ca_key->getPublicKey()->toString('OpenSSH');
        [$ca_algo, $ca_pubkey_b64] = explode(' ', $ca_pubkey);
        $ca_pub_raw = base64_decode($ca_pubkey_b64, true);

        $principals_raw = '';
        foreach ($principals as $principal) {
            $principals_raw .= $this->sshEncode($principal);
        }

        $unsignedBlob =
            $this->sshEncode($cert_type) .
            $this->sshEncode($nonce) .
            $this->sshEncode($card_pubkey_raw) .
            pack('J', $serial) .                 // uint64 serial (big-endian)
            pack('N', 1) .                       // uint32 type: 1 = SSH_CERT_TYPE_USER
            $this->sshEncode($cert_id) .
            $this->sshEncode($principals_raw) .
            pack('J', $validAfter) .             // uint64 valid_after
            pack('J', $validBefore) .            // uint64 valid_before
            $this->sshEncode('') .               // critical options (puste)
            $this->sshEncode('') .               // extensions (puste)
            $this->sshEncode('') .               // reserved (puste)
            $this->sshEncode($ca_pub_raw);        // signature key (klucz publiczny CA)

        // 5. Faktyczne użycie $caKey do złożenia podpisu kryptograficznego!
        $raw_signature = $ca_key->sign($unsignedBlob);

        // Opakowanie podpisu w format OpenSSH signature blob (algorytm + surowe bajty podpisu)
        $signature_blob = $this->sshEncode($ca_algo) . $this->sshEncode($raw_signature);

        // Połączenie ciała certyfikatu z podpisem CA
        $signed_cert_blob = $unsignedBlob . $this->sshEncode($signature_blob);

        return sprintf('%s %s %s', $cert_type, base64_encode($signed_cert_blob), $cert_id);
    }

    /**
     * AI-assisted
     * Must match Gate.Client._fingerprint() on the Ticketer side:
     * sha256 of the ASCII OpenSSH line "<algo> <base64-blob>" (no comment),
     * NOT of the binary wire blob (which is what ssh-keygen's SHA256: fingerprint shows).
     */
    public function sshFingerprint(PublicKey $key): string{
        [$algo, $blob] = explode(' ', trim($key->toString('OpenSSH')));
        return hash('sha256', $algo . ' ' . $blob);
    }

    public function generateKeys(): array{
        $this->private_key = EC::createKey("Ed25519");
        $this->public_key = $this->private_key->getPublicKey();

        return [$this->private_key, $this->public_key];
    }

    public function issueCard(PublicKey $card_pub_key, int $valid_for, int $serial): string{
        $client_uuid = hash("sha256", $this->client_id.random_bytes(32));

        return $this->signCertificate(
            $this->ca_private_key,
            $card_pub_key->getEncodedCoordinates(),
            $client_uuid,
            [$client_uuid],
            $serial,
            time() - 30,
            time() + $valid_for);
    }
}