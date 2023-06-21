<?php
/**
 * author: zhangyongku
 * datetime: 2019/12/8 17:49
 */

namespace Framework;

use Carbon\Carbon;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class Jwt {

    protected $header = 'authorization';
    protected $prefix = 'bearer';

    /**
     * @var Key
     */
    private $sign_key;
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var Builder
     */
    private $builder;
    /**
     * @var Sha256
     */
    private $signer;

    public function __construct(Parser $parser, Builder $builder)
    {
        $secret = env('JWT_SECRET');
        if (!$secret || $secret == 'APP_KEY') {
            $secret = env('APP_KEY', 'zhangyongku');
        }
        $this->sign_key = new Key($secret);
        $this->parser = $parser;
        $this->builder = $builder;
        $this->signer = new Sha256();
    }

    /**
     * 获取JWT
     * @param string $id ID
     * @param array $ext 扩展信息
     * @param Carbon $issuedAt 发行时间
     * @param int $ttl 过期时间
     * @return string jwt
     */
    public function getToken(string $id, array $ext = [], Carbon $issuedAt = null, int $ttl = null) : string
    {
        $issuedAt = $issuedAt ?? Carbon::now();
        $token_ttl = config('jwt.ttl', 86400);
        $ttl = $ttl ?? $token_ttl;

        $this->builder->issuedAt($issuedAt->toDateTimeImmutable())
            ->expiresAt($issuedAt->addSeconds($ttl)->toDateTimeImmutable())
            ->withClaim('id', $id);

        foreach ($ext as $key => $val) {
            $this->builder->withClaim($key, $val);
        }

        $token = $this->builder->getToken($this->signer, $this->sign_key);

        return $token->toString();
    }

    /**
     * 从头部获取匹配的JWT
     * @return mixed
     */
    public function parse()
    {
        $header = request()->header($this->header);
        if ($header && preg_match('/'.$this->prefix.'\s*(\S+)\b/i', $header, $matches)) {
            return $matches[1];
        } else {
            return false;
        }
    }

    /**
     * 检查Token并返回用户ID
     * @return bool|int
     */
    public function checkToken()
    {
        $headerToken = $this->parse();
        if (!$headerToken) {
            return false;
        }

        try {
            /** @var Token $curToken */
            $curToken = $this->parser->parse((string)$headerToken);

            $signedWith = new SignedWith($this->signer, $this->sign_key);
            $signedWith->assert($curToken);
        } catch (\Exception $e) {
            return false;
        }

        $flg = $curToken->isExpired(Carbon::now()->toDateTimeImmutable());
        if ($flg) {
            return false;
        }

        if ($curToken->claims()->has('id')) {
            $user_id = $curToken->claims()->get('id');
        } else {
            return false;
        }

        return $user_id;
    }

    /**
     * 获取扩展信息
     * @param string $jwt
     * @return DataSet
     */
    public function getClaims(string $jwt) : DataSet
    {
        $token = $this->parser->parse($jwt);
        return $token->claims();
    }
}
