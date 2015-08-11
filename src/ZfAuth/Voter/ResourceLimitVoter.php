<?php


namespace Aeris\ZfAuth\Voter;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Aeris\Fn;

class ResourceLimitVoter implements VoterInterface {

	/** @var string FQCN */
	protected $supportedClass;

	/** @var callable */
	protected $_supportsResource;

	/** @var callable */
	protected $_getLimit;

	/** @var callable */
	protected $_getCount;

	/**
	 * ResourceLimitVoter constructor.
	 */
	public function __construct(array $config = []) {
		$requiredConfig = ['supportedClass', 'limit', 'count'];
		$config = array_replace([
			// only 'supportedClass' is required
			'supportsResource' => Fn\always()
		], $config);

		foreach ($requiredConfig as $key) {
			if (!isset($config[$key])) {
				throw new \InvalidArgumentException("ResourceLimitVoter `$key` config missing.");
			}
		}

		$this->supportedClass = $config['supportedClass'];
		$this->_supportsResource = $config['supportsResource'];
		$this->_getLimit = $config['limit'];
		$this->_getCount = $config['count'];
	}



	public function supportsAttribute($attribute) {
		return $attribute === 'create';
	}

	public function supportsClass($class) {
		return $class === $this->supportedClass || is_a($class, $this->supportedClass, true);
	}

	public function vote(TokenInterface $token, $object, array $attributes) {
		$isSupported = $this->supportsClass(get_class($object)) &&
			Fn\all($attributes, [$this, 'supportsAttribute']) &&
			call_user_func($this->_supportsResource, $object);

		if (!$isSupported) {
			return self::ACCESS_ABSTAIN;
		}

		$user = $token->getUser();
		$limit = call_user_func($this->_getLimit, $user);
		$count = call_user_func($this->_getCount, $user);

		if ($limit === -1) {
			return self::ACCESS_GRANTED;
		}

		return $count >= $limit ? self::ACCESS_DENIED : self::ACCESS_GRANTED;
	}
}