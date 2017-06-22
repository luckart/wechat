<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Traits;

use EasyWeChat\Exceptions\InvalidArgumentException;
use EasyWeChat\Support\InteractsWithCache;
use EasyWeChat\Support\XML;

trait WorksInSandbox
{
    use InteractsWithCache;

    /**
     * Sandbox box mode.
     *
     * @var bool
     */
    protected $inSandbox = false;

    /**
     * Sandbox sign key.
     *
     * @var string
     */
    protected $signKey;

    /**
     * @var string
     */
    protected $signKeyEndpoint = 'sandboxnew/pay/getsignkey';

    /**
     * Set sandbox mode.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function sandboxMode(bool $enabled = false)
    {
        $this->inSandbox = $enabled;

        return $this;
    }

    /**
     * Wrap API.
     *
     * @param string $resource
     *
     * @return string
     */
    protected function wrapApi($resource)
    {
        return $this->inSandbox ? "sandboxnew/{$resource}" : $resource;
    }

    /**
     * Return key to sign.
     *
     * @param string $api
     *
     * @return string
     */
    protected function getSignKey($api)
    {
        if (!$this->inSandbox || $api === $this->signKeyEndpoint) {
            return $this->app['merchant']->key;
        }

        return $this->getSandboxSignKey();
    }

    /**
     * Get sandbox sign key.
     *
     * @return string
     */
    protected function getSandboxSignKey()
    {
        if ($this->signKey || $this->signKey = $this->getCache()->get($this->getCacheKey())) {
            return $this->signKey;
        }

        return $this->signKey = $this->getSignKeyFromServer();
    }

    /**
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function getSignKeyFromServer()
    {
        $result = (array) XML::parse(
            $this->request($this->signKeyEndpoint, [], 'post', [], true)->getBody()
        );

        if ($result['return_code'] === 'SUCCESS') {
            $this->getCache()->set($this->getCacheKey(), $signKey = $result['sandbox_signkey'], 24 * 3600);

            return $signKey;
        }

        throw new InvalidArgumentException($result['return_msg']);
    }

    /**
     * @return string
     */
    private function getCacheKey()
    {
        return 'easywechat.payment.sandbox.'.$this->app['merchant']->merchant_id;
    }
}
