<?php
/**
 * User: chc
 * Date: 13-6-26
 * Time: 上午11:17
 */

class Test {
    /**
     * 图片显示函数
     * @param $req
     * @param $res
     */
    function index($req, $res) {
        $e = $req->head('charset');

        $res->head('charset', 'utf8');
        $res->send('hello world');
    }
}