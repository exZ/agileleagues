<?php

App::uses('TestUtils', 'Lib');

class BadgeTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateBadges();
		$this->utils->generateBadgeRequisites();
		$this->utils->generateActivityRequisites();
	}

	public function testAllFromOwner() {
		$this->assertEquals(4, count($this->utils->Badge->allFromOwner(GAME_MASTER_ID_1)));
		$this->assertEquals(0, count($this->utils->Badge->allFromOwner(GAME_MASTER_ID_2)));
	}

	public function testAllFromOwnerById() {
		$this->assertEquals(4, count($this->utils->Badge->allFromOwnerById(GAME_MASTER_ID_1)));
		$this->assertEquals(0, count($this->utils->Badge->allFromOwnerById(GAME_MASTER_ID_2)));
	}
	
	public function testAllFromDomainById(){
		$domain = $this->utils->Domain->find('first', array('order' => 'Domain.id'));
		$domainId = $domain['Domain']['id'];
		$badges = $this->utils->Badge->allFromDomainById($domainId);
		$this->assertNotEmpty($badges);

		foreach ($badges as $id => $badge) {
			$this->assertEquals($badge['Badge']['id'], $id);
			$this->assertEquals($badge['Badge']['domain_id'], $domainId);
		}
	}

	public function testSimpleFromDomain(){
		$domain = $this->utils->Domain->find('first', array('order' => 'Domain.id'));
		$domainId = $domain['Domain']['id'];
		$badges = $this->utils->Badge->simpleFromDomain($domainId);
		$this->assertNotEmpty($badges);

		foreach ($badges as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testClaimSuccess() {
		$this->utils->generateBadgeLogs();
		$this->utils->generateLogs();

		$this->utils->Badge->recursive = 1;
		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = PLAYER_ID_1;
		// Insere as informações na tabela de resumo, 
		// pois os logs não foram revisados chamando _review()
		$this->utils->ActivityRequisiteSummary->updateAll(
			array('ActivityRequisiteSummary.times' => 1, 'ActivityRequisiteSummary.player_id' => $playerId), 
			array('ActivityRequisiteSummary.id >' => 0)
		);

		$this->utils->BadgeLog->query('DELETE FROM badge_log WHERE player_id = ? AND badge_id = ?', array($playerId, $badgeId));
		$this->utils->Badge->claim($playerId, $badgeId);
		$this->assertNotEmpty($this->utils->BadgeLog->findByPlayerIdAndBadgeId($playerId, $badgeId));
	}

	public function testClaimWrongPlayerType() {
		$this->utils->generateBadgeLogs();
		$this->utils->generateLogs();

		$this->utils->Badge->recursive = 1;
		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = GAME_MASTER_ID_1;

		$this->utils->BadgeLog->query('DELETE FROM badge_log WHERE player_id = ? AND badge_id = ?', array($playerId, $badgeId));
		try {
			$this->utils->Badge->claim($playerId, $badgeId);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Badge not compatible with player type.', $ex->getMessage());
		}
	}

	public function testClaimAlreadyClaimed() {
		$this->utils->generateBadgeLogs();
		$this->utils->generateLogs();

		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = PLAYER_ID_1;
		try {
			$this->utils->Badge->claim($playerId, $badgeId);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Badge already claimed.', $ex->getMessage());
		}
	}

	public function testClaimBadgeNotFound() {
		$playerId = PLAYER_ID_1;
		try {
			$this->utils->Badge->claim($playerId, 0);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Badge not found.', $ex->getMessage());
		}
	}


	public function testClaimFailureNoActivities() {
		$this->utils->generateBadgeLogs();

		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = PLAYER_ID_2;

		$this->utils->BadgeLog->query('DELETE FROM badge_log WHERE player_id = ? AND badge_id = ?', array($playerId, $badgeId));

		try {
			$this->utils->Badge->claim($playerId, $badgeId);
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('You lack the necessary activities to claim this badge.', $ex->getMessage());
		}
		$this->assertEmpty($this->utils->BadgeLog->findByPlayerIdAndBadgeId($playerId, $badgeId));
	}


	public function testClaimFailureNoBadges() {
		$this->utils->generateBadgeLogs();
		$this->utils->generateLogs();

		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = PLAYER_ID_2;

		$this->utils->BadgeLog->query('DELETE FROM badge_log');

		try {
			$this->utils->Badge->claim($playerId, $badgeId);
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('You lack the necessary badge requisites to claim this badge.', $ex->getMessage());
		}
		$this->assertEmpty($this->utils->BadgeLog->findByPlayerIdAndBadgeId($playerId, $badgeId));
	}

}