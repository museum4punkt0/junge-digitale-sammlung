<?php

namespace Kirby\Cms;

use Kirby\Exception\DuplicateException;
use Kirby\Exception\LogicException;
use Kirby\Exception\PermissionException;

/**
 * Extends ContentLock and holds some extra information,
 * since we have a Group-Account and Participant-IDs inside
 * that account. With the extra information we can differentiate
 * inside the Group-Account.
 */
class CustomContentLock extends ContentLock
{
	protected $curator;
	protected $curatorUUID;
	
	/**
	 * createWithCurator
	 * Creates a lock but adds the extra information of the curator (participant)
	 * @param  string $_curator
	 * @param  string $_curatorUN
	 * @return mixed
	 */
	public function createWithCurator($_curator, $_curatorUN = 'Kein Benutzername'): bool
	{
		$this->curator = $_curator;
		$this->curatorUN = $_curatorUN;
		// check if model is already locked by another user
		if (
			isset($this->data['lock']) === true &&
			$this->data['lock']['user'] !== $this->user()->id()
		) {
			$id = ContentLocks::id($this->model);
			throw new DuplicateException($id . ' is already locked');
		}

		$this->data['lock'] = [
			'user' => $this->user()->id(),
			'time' => time(),
			'curator' => $this->curator,
			'curatorUN' => $this->curatorUN,
		];

		return $this->kirby()->locks()->set($this->model, $this->data);
	}
	
	/**
	 * isBlocked
	 * Checks if the lock is blocked by this curator
	 * @param  mixed $_curator
	 * @return bool
	 */
	public function isBlocked($_curator): bool
	{
		$lock = $this->get();
		//if ($lock !== false && $lock['curator'] !== $_curator && $this->isLocked()) {
		if (isset($this->data['lock']['curator']) && $_curator !== $this->getCurator()) {
			return true;
		}

		return false;
	}
	
	/**
	 * getInfos
	 * Returns the lock array data
	 * @return array
	 */
	public function getInfos()
	{
		$data = $this->data['lock'];
		return $data;
	}
		
	/**
	 * getCurator
	 * Returns the curator ID
	 * @return string
	 */
	public function getCurator()
	{
		return $this->data['lock']['curator'];
	}
}
