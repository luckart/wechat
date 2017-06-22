<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Coupon;

use EasyWeChat\Applications\Payment\BaseClient;

/**
 * Class Client.
 *
 * @author tianyong90 <412039588@qq.com>
 */
class Client extends BaseClient
{
    /**
     * send a cash coupon.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function send(array $params)
    {
        $params['openid_count'] = 1;

        return $this->safeRequest('mmpaymkttransfers/send_coupon', $params);
    }

    /**
     * query a coupon stock.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function queryStock(array $params)
    {
        return $this->safeRequest('mmpaymkttransfers/query_coupon_stock', $params);
    }

    /**
     * query a info of coupon.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function query(array $params)
    {
        return $this->safeRequest('mmpaymkttransfers/querycouponsinfo', $params);
    }

    /**
     * {@inheritdoc}.
     */
    protected function extra(): array
    {
        return [
            'mch_id' => $this->app['merchant']->merchant_id,
            'appid' => $this->app['merchant']->app_id,
        ];
    }
}
