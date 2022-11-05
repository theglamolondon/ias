<?php
/**
 * Created by PhpStorm.
 * User: Wilfried KOFFI
 * Date: 25/05/2022
 * Time: 15:53
 */

namespace App\Services;

use Illuminate\Support\Facades\Auth;

use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Checker;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;

trait AuthServices
{
  public function generateNewTokens() : array {
    return [
      "user" => Auth::user(),
      "access" => $this->generateAccessToken(Auth::user()->employe->service->code, Auth::user()->employe->matricule, Auth::user()->employe_id),
      "refresh" => $this->generateRefreshToken(Auth::user()->employe_id)
    ];
  }

  private static function getJwk() : JWK{
    return JWKFactory::createFromSecret(
      "abcdefghijklmnopqrst,nsddhgdhdsghsgdhgdhgdshsdgsdhjgds",       // The shared secret
      [                             // Optional additional members
        'alg' => 'HS256',
        'use' => 'sig'
      ]
    );
  }

  protected function checkClaims(array $claims){
    $claimCheckerManager = new ClaimCheckerManager(
      [
        new Checker\IssuedAtChecker(0),
        new Checker\NotBeforeChecker(0),
        new Checker\ExpirationTimeChecker(0),
        new Checker\AudienceChecker('IAS Manager'),
      ]
    );
    $claimCheckerManager->check($claims);
  }

  protected function getJwsFromToken(string $token) : JWS {

    // The serializer manager. We only use the JWS Compact Serialization Mode.
    $serializerManager = new JWSSerializerManager([ new CompactSerializer()]);

    // We try to load the token.
    return $serializerManager->unserialize($token);
  }

  protected function getIdFromRefreshToken(string $token) : int {
    $jws = $this->getJwsFromToken($token);
    $data = json_decode($jws->getPayload());
    return $data->userId;
  }

  private function getVerifier() : JWSVerifier{
    // The algorithm manager with the HS256 algorithm.
    $algorithmManager = new AlgorithmManager([new HS256()]);

    // We instantiate our JWS Verifier.
    return new JWSVerifier($algorithmManager);
  }

  protected function verifyToken(string $token) : bool {

    // Our key.
    $jwk = self::getJwk();

    $jws = $this->getJwsFromToken($token);

    $this->checkClaims(json_decode($jws->getPayload(), true));

    // We verify the signature. This method does NOT check the header.
    // The arguments are:
    // - The JWS object,
    // - The key,
    // - The index of the signature to check. See
    return $this->getVerifier()->verifyWithKey($jws, $jwk, 0);
  }

  private function initJwt() : JWSBuilder {

    $algorithmManager = new AlgorithmManager([ new HS256() ]);

    // We instantiate our JWS Builder.
    return new JWSBuilder($algorithmManager);
  }

  protected function generateAccessToken(string $service, string $matricule, int $userId) : string {
    $payload = json_encode([
      'iat' => time(),
      'nbf' => time(),
      'matricule' => $matricule,
      'exp' => time() + 3600*2,
      'iss' => "IAS API",
      'role' => $service,
      'userId' => $userId,
      'aud' => 'IAS Manager',
    ]);

    $jws = $this->initJwt()
      ->create()                      // We want to create a new JWS
      ->withPayload($payload)         // We set the payload
      ->addSignature(self::getJwk(), ['alg' => 'HS256'])  // We add a signature with a simple protected header
      ->build();

    $serializer = new CompactSerializer(); // The serializer

    return $serializer->serialize($jws, 0);
  }

  protected function generateRefreshToken(int $userId) : string {
    $payload = json_encode([
      'iat' => time(),
      'nbf' => time(),
      'exp' => time() + 3600*4,
      'iss' => "IAS API",
      'userId' => $userId,
      'aud' => 'IAS Manager',
    ]);

    $jws = $this->initJwt()
      ->create()                      // We want to create a new JWS
      ->withPayload($payload)         // We set the payload
      ->addSignature(self::getJwk(), ['alg' => 'HS256'])  // We add a signature with a simple protected header
      ->build();

    $serializer = new CompactSerializer(); // The serializer

    return $serializer->serialize($jws, 0);
  }
}