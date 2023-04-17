<?php

namespace Kirby\Cms;

use Kirby\Exception\DuplicateException;
use Kirby\Exception\LogicException;
use Kirby\Exception\PermissionException;

class CustomContentLock extends ContentLock
{
	protected $curator;
	protected $curatorUUID;

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

	public function isBlocked($_curator): bool
	{
		$lock = $this->get();
		//if ($lock !== false && $lock['curator'] !== $_curator && $this->isLocked()) {
		if (isset($this->data['lock']['curator']) && $_curator !== $this->getCurator()) {
			return true;
		}

		return false;
	}

	public function getInfos()
	{
		$data = $this->data['lock'];
		return $data;
	}

	public function getCurator()
	{
		return $this->data['lock']['curator'];
	}
}
