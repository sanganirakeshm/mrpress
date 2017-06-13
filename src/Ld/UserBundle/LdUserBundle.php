<?php

namespace Ld\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LdUserBundle extends Bundle
{
	public function getParent() {
		return 'FOSUserBundle';
	}
}
