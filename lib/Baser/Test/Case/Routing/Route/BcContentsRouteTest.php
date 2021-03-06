<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Routing.Route
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcContentsRoute', 'Routing/Route');

/**
 * BcRequestFilterTest class
 *
 * @package Baser.Test.Case.Routing.Route
 * @property BcContentsRoute $BcContentsRoute
 */
class BcContentsRouteTest extends BaserTestCase {


/**
 * フィクスチャ
 * @var array
 */
	public $fixtures = [
		'baser.Routing.Route.BcContentsRoute.SiteBcContentsRoute',
		'baser.Routing.Route.BcContentsRoute.ContentBcContentsRoute',
		'baser.Default.SiteConfig',
		'baser.Default.User',
	];

/**
 * set up
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BcContentsRoute = new BcContentsRoute(
			'/',
			[],
			[]
		);
		BcSite::flash();
	}

/**
 * リバースルーティング
 * 
 * @param string $current 現在のURL
 * @param string $params URLパラメーター
 * @param string $expects 期待するURL
 * @dataProvider reverseRoutingDataProvider
 */
	public function testReverseRouting($current, $params, $expects) {
		Router::setRequestInfo($this->_getRequest($current));
		$this->assertEquals($expects, Router::url($params));
	}

	public function reverseRoutingDataProvider() {
		return [
			// ContentFolder
			['/', ['plugin' => null, 'controller' => 'content_folders', 'action' => 'view', 'entityId' => 1], '/'],
			// Page
			['/', ['plugin' => null, 'controller' => 'pages', 'action' => 'display', 'index'], '/index'],
			['/', ['plugin' => null, 'controller' => 'pages', 'action' => 'display', 'service', 'service1'], '/service/service1'],
			// Blog
			['/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => 1], '/news/'],
			['/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 2], '/news/archives/2'],
			['/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 'page' => 2, 2], '/news/archives/2/page:2'],
			['/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 'category', 'release'], '/news/archives/category/release'],
			['/', ['page' => 2], '/page:2'],
			['/news/index', ['page' => 2], '/news/index/page:2'],
		];
	}
	
/**
 * BcContentsRoute::getUrlPattern
 *
 * @param string $url URL文字列
 * @param string $expect 期待値
 * @return void
 * @dataProvider getUrlPatternDataProvider
 */
	public function testGetUrlPattern($url, $expects) {
		$this->assertEquals($expects, $this->BcContentsRoute->getUrlPattern($url));
	}

/**
 * getUrlPattern 用データプロバイダ
 *
 * @return array
 */
	public function getUrlPatternDataProvider() {
		return [
			['/news', ['/news']],
			['/news/', ['/news/', '/news/index']],
			['/news/index', ['/news/index', '/news/']],
			['/news/archives/1', ['/news/archives/1']],
			['/news/archives/index', ['/news/archives/index', '/news/archives/']]
		];
	}

/**
 * Router::parse
 *
 * @param string $url URL文字列
 * @param string $expect 期待値
 * @return void
 * @dataProvider routerParseDataProvider
 */
	public function testRouterParse($host, $ua, $url, $expects) {
		$siteUrl = Configure::read('BcEnv.siteUrl');
		Configure::write('BcEnv.siteUrl', 'http://main.com');
		if($ua) {
			$_SERVER['HTTP_USER_AGENT'] = $ua;
		}
		if($host) {
			$_SERVER['HTTP_HOST'] = $host;
		}
		Router::setRequestInfo($this->_getRequest($url));
		$this->assertEquals($expects, Router::parse($url));
		Configure::write('BcEnv.siteUrl', $siteUrl);
	}

	public function routerParseDataProvider() {
		return [
			// PC（ノーマル）
			['', '', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['index'], 'named' => []]],
			['', '', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['index'], 'named' => []]],
			['', '', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			['', '', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			['', '', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			['', '', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '1', 'pass' => [1], 'named' => []]],
			['', '', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => ['page' => 1]]],
			['', '', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '4', 'pass' => [], 'named' => []]],
			['', '', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '3', 'pass' => ['service', 'service1'], 'named' => []]],
			['', '', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// モバイル（別URL）
			['', 'SoftBank', '/m/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '4', 'pass' => ['m', 'index'], 'named' => []]],
			['', 'SoftBank', '/m/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '4', 'pass' => ['m', 'index'], 'named' => []]],
			['', 'SoftBank', '/m/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '2', 'pass' => [], 'named' => []]],
			['', 'SoftBank', '/m/news', ['plugin' => '', 'controller' => 'm', 'action' => 'news', 'pass' => [], 'named' => []]],
			['', 'SoftBank', '/m/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '2', 'pass' => [], 'named' => []]],
			['', 'SoftBank', '/m/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '2', 'pass' => [1], 'named' => []]],
			['', 'SoftBank', '/m/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '2', 'pass' => [], 'named' => ['page' => 1]]],
			['', 'SoftBank', '/m/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '11', 'pass' => [], 'named' => []]],
			['', 'SoftBank', '/m/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '10', 'pass' => ['m', 'service', 'service1'], 'named' => []]],
			['', 'SoftBank', '/m/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// スマホ（同一URL / エイリアス）
			['', 'iPhone', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['s', 'index'], 'named' => []]],
			['', 'iPhone', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['s', 'index'], 'named' => []]],
			['', 'iPhone', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			['', 'iPhone', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			['', 'iPhone', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			['', 'iPhone', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '1', 'pass' => [1], 'named' => []]],
			['', 'iPhone', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => ['page' => 1]]],
			['', 'iPhone', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '4', 'pass' => [], 'named' => []]],
			['', 'iPhone', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '3', 'pass' => ['s', 'service', 'service1'], 'named' => []]],
			['', 'iPhone', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// PC（英語）
			['', '', '/en/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '12', 'pass' => ['en', 'index'], 'named' => []]],
			['', '', '/en/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '12', 'pass' => ['en', 'index'], 'named' => []]],
			['', '', '/en/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '3', 'pass' => [], 'named' => []]],
			['', '', '/en/news', ['plugin' => '', 'controller' => 'en', 'action' => 'news', 'pass' => [], 'named' => []]],
			['', '', '/en/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '3', 'pass' => [], 'named' => []]],
			['', '', '/en/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '3', 'pass' => [1], 'named' => []]],
			['', '', '/en/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '3', 'pass' => [], 'named' => ['page' => 1]]],
			['', '', '/en/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '8', 'pass' => [], 'named' => []]],
			['', '', '/en/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '8', 'pass' => ['en', 'service', 'service1'], 'named' => []]],
			['', '', '/en/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// PC（サブドメイン）
			['sub.main.com', '', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '13', 'pass' => ['sub', 'index'], 'named' => []]],
			['sub.main.com', '', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '13', 'pass' => ['sub', 'index'], 'named' => []]],
			['sub.main.com', '', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '4', 'pass' => [], 'named' => []]],
			['sub.main.com', '', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			['sub.main.com', '', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '4', 'pass' => [], 'named' => []]],
			['sub.main.com', '', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '4', 'pass' => [1], 'named' => []]],
			['sub.main.com', '', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '4', 'pass' => [], 'named' => ['page' => 1]]],
			['sub.main.com', '', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '9', 'pass' => [], 'named' => []]],
			['sub.main.com', '', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '9', 'pass' => ['sub', 'service', 'service1'], 'named' => []]],
			['sub.main.com', '', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// PC（別ドメイン）
			['another.com', '', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '14', 'pass' => ['another.com', 'index'], 'named' => []]],
			['another.com', '', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '14', 'pass' => ['another.com', 'index'], 'named' => []]],
			['another.com', '', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '5', 'pass' => [], 'named' => []]],
			['another.com', '', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			['another.com', '', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '5', 'pass' => [], 'named' => []]],
			['another.com', '', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '5', 'pass' => [1], 'named' => []]],
			['another.com', '', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '5', 'pass' => [], 'named' => ['page' => 1]]],
			['another.com', '', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '10', 'pass' => [], 'named' => []]],
			['another.com', '', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '11', 'pass' => ['another.com', 'service', 'service1'], 'named' => []]],
			['another.com', '', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// スマホ（別ドメイン / 同一URL / 別コンテンツ）
			['another.com', 'iPhone', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '15', 'pass' => ['another.com', 's', 'index'], 'named' => []]],
			['another.com', 'iPhone', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '15', 'pass' => ['another.com', 's', 'index'], 'named' => []]],
			['another.com', 'iPhone', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '6', 'pass' => [], 'named' => []]],
			['another.com', 'iPhone', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			['another.com', 'iPhone', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '6', 'pass' => [], 'named' => []]],
			['another.com', 'iPhone', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '6', 'pass' => [1], 'named' => []]],
			['another.com', 'iPhone', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '6', 'pass' => [], 'named' => ['page' => 1]]],
			['another.com', 'iPhone', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '13', 'pass' => [], 'named' => []]],
			['another.com', 'iPhone', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '16', 'pass' => ['another.com', 's', 'service', 'service1'], 'named' => []]],
			['another.com', 'iPhone', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '2', 'pass' => [], 'named' => []]],
		];
	}

}