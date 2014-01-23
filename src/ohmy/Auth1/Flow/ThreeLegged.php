<?php namespace ohmy\Auth1\Flow;

/*
 * Copyright (c) 2014, Yahoo! Inc. All rights reserved.
 * Copyrights licensed under the New BSD License.
 * See the accompanying LICENSE file for terms.
 */

use ohmy\Auth1\Flow,
    ohmy\Auth1\Model;

class ThreeLegged extends Flow {

    public function __construct(
        Model $model,
        $request_token_url,
        $authorize_url,
        $access_token_url
    ) {
        parent::__construct(
            $model,
            array(
                # first leg
                array(
                    'type'     => 'machine',
                    'url'      => $request_token_url,
                    'have'     => array(
                        'oauth_consumer_key',
                        'oauth_consumer_secret',
                        'oauth_timestamp',
                        'oauth_nonce',
                        'oauth_signature_method',
                        'oauth_version',
                        'oauth_callback'
                    ),
                    'want'     => array(
                        'oauth_token',
                        'oauth_token_secret'
                    ),
                    'params'   => array(),
                    'headers'  => array(),
                    'callback' => function($data) use($model) {
                        $model->set('oauth_token', $data['oauth_token']);
                        $_SESSION['oauth_token_secret'] = $data['oauth_token_secret'];
                        $_SESSION['oauth_callback_confirmed'] = $data['oauth_callback_confirmed'];
                    }
                ),
                # second leg
                array(
                    'type'     => 'user',
                    'url'      => $authorize_url,
                    'have'    => array(
                        'oauth_consumer_key',
                        'oauth_consumer_secret',
                        'oauth_token',
                        'oauth_token_secret',
                        'oauth_timestamp',
                        'oauth_nonce',
                        'oauth_signature_method',
                        'oauth_version',
                        'oauth_callback',
                        'oauth_callback_confirmed'
                    ),
                    'want'     => array(
                        'oauth_token',
                        'oauth_verifier'
                    ),
                    'params'   => array(),
                    'headers'  => array(),
                    'callback' => function() {}
                ),
                # third leg
                array(
                    'type'     => 'machine',
                    'url'      => $access_token_url,
                    'have'     => array(
                        'oauth_consumer_key',
                        'oauth_consumer_secret',
                        'oauth_token',
                        'oauth_token_secret',
                        'oauth_timestamp',
                        'oauth_nonce',
                        'oauth_signature_method',
                        'oauth_version',
                        'oauth_verifier'
                    ),
                    'want'     => array(
                        'nop'
                    ),
                    'params'   => array(),
                    'headers'  => array(),
                    'callback' => function() {}
                )
            )
        );
    }
}
