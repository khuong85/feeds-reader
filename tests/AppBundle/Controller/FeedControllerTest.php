<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeedControllerTest extends WebTestCase{
	public function testIndex(){
		$client = static::createClient();
		$crawler = $client->request('POST', '/feed/');
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertContains('Feeds list', $crawler->filter('.container h1')->text());
	}

	public function testNewFeed(){
		$params = [
			'title' => 'new title',
			'description' => 'new description',
			'link' => 'new link',
			'category' => 'new category',
			'comments' => 'new comments',
			'pub_date' => date('Y/m/d H:i:s', time())
		];
		$client = static::createClient();
		$crawler = $client->request('POST', '/feed/new', $params);
		$this->assertEquals(0, $crawler->filter('html:contains("Id")')->count());
	}

	public function testDeleteFeed(){
		$client = static::createClient();
		$client->request('POST', 'feed_delete', ['id' => '1']);
		$crawler = $crawler = $client->request('POST', '/feed/1');
		$this->assertEquals(0, $crawler->filter('html:contains("Feed Detail")')->count());
	}
}