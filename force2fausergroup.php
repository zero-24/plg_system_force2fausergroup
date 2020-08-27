<?php
/**
 * Force2faUsergroup Plugin
 *
 * @copyright  Copyright (C) 2020 Tobias Zulauf All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Response\JsonResponse;

/**
 * Plugin class for Fetch Metadata
 *
 * @since  1.0
 */
class PlgUserForce2faUsergroup extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    CMSApplication
	 * @since  1.0
	 */
	protected $app;

	/**
	 * Reject cross-origin requests to protect from CSRF, XSSI, and other bugs
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onUserAfterLogin()
	{
		// Skip guests
		if (Factory::getUser()->guest)
		{
			return;
		}


	}

	/**
	 * Checks if 2fa needs to be enforced
	 * if so returns true, else returns false
	 *
	 * Based on: https://github.com/joomla/joomla-cms/blob/4.0.0-beta3/libraries/src/Application/CMSApplication.php#L1163-L1173
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 *
	 * @throws \Exception
	 */
	protected function isTwoFactorAuthenticationRequired(): bool
	{
		$userId = $this->getIdentity()->id;

		if (!$userId)
		{
			return false;
		}

		// Check session if user has set up 2fa
		if ($this->getSession()->has('has2fa'))
		{
			return false;
		}

		$enforce2faOptions = ComponentHelper::getComponent('com_users')->getParams()->get('enforce_2fa_options', 0);

		if ($enforce2faOptions == 0 || !$enforce2faOptions)
		{
			return false;
		}

		if (!PluginHelper::isEnabled('twofactorauth'))
		{
			return false;
		}

		$pluginsSiteEnable          = false;
		$pluginsAdministratorEnable = false;
		$pluginOptions              = PluginHelper::getPlugin('twofactorauth');

		// Sets and checks pluginOptions for Site and Administrator view depending on if any 2fa plugin is enabled for that view
		array_walk($pluginOptions,
			static function ($pluginOption) use (&$pluginsSiteEnable, &$pluginsAdministratorEnable)
			{
				$option  = new Registry($pluginOption->params);
				$section = $option->get('section', 3);

				switch ($section)
				{
					case 1:
						$pluginsSiteEnable = true;
						break;
					case 2:
						$pluginsAdministratorEnable = true;
						break;
					case 3:
					default:
						$pluginsAdministratorEnable = true;
						$pluginsSiteEnable          = true;
				}
			}
		);

		if ($pluginsSiteEnable && $this->isClient('site'))
		{
			if (\in_array($enforce2faOptions, [1, 3]))
			{
				return !$this->hasUserConfiguredTwoFactorAuthentication();
			}
		}

		if ($pluginsAdministratorEnable && $this->isClient('administrator'))
		{
			if (\in_array($enforce2faOptions, [2, 3]))
			{
				return !$this->hasUserConfiguredTwoFactorAuthentication();
			}
		}

		return false;
	}

	/**
	 * Redirects user to his Two Factor Authentication setup page
	 *
	 * Based on: https://github.com/joomla/joomla-cms/blob/4.0.0-beta3/libraries/src/Application/CMSApplication.php#L1246-L1253
	 *
	 * @return void
	 *
	 * @since  1.0.0
	 */
	protected function redirectIfTwoFactorAuthenticationRequired(): void
	{
		$option = $this->input->get('option');
		$task   = $this->input->get('task');
		$view   = $this->input->get('view', null, 'STRING');
		$layout = $this->input->get('layout', null, 'STRING');

		if ($this->isClient('site'))
		{
			// If user is already on edit profile screen or press update/apply button, do nothing to avoid infinite redirect
			if (($option === 'com_users' && \in_array($task, ['profile.edit', 'profile.save', 'profile.apply', 'user.logout', 'user.menulogout'], true))
				|| $option === 'com_users' && $view === 'profile' && $layout === 'edit')
			{
				return;
			}

			// Redirect to com_users profile edit
			$this->enqueueMessage(Text::_('JENFORCE_2FA_REDIRECT_MESSAGE'), 'notice');
			$this->redirect('index.php?option=com_users&view=profile&layout=edit');
		}

		if ($option === 'com_admin' && \in_array($task, ['profile.edit', 'profile.save', 'profile.apply'], true)
			|| ($option === 'com_admin' && $view === 'profile' && $layout === 'edit')
			|| ($option === 'com_users' && \in_array($task, ['user.save', 'user.edit', 'user.apply', 'user.logout', 'user.menulogout'], true))
			|| ($option === 'com_users' && $view === 'user' && $layout === 'edit')
			|| ($option === 'com_login' && \in_array($task, ['save', 'edit', 'apply', 'logout', 'menulogout'], true)))
		{
			return;
		}

		// Redirect to com_admin profile edit
		$this->enqueueMessage(Text::_('JENFORCE_2FA_REDIRECT_MESSAGE'), 'notice');
		$this->redirect('index.php?option=com_admin&task=profile.edit&id=' . $this->getIdentity()->id);
	}

	/**
	 * Checks if otpKey and otep for the user are not empty
	 * if any one is empty returns false, else returns true
	 *
	 * Based on: https://github.com/joomla/joomla-cms/blob/4.0.0-beta3/libraries/src/Application/CMSApplication.php#L1288-L1298
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 *
	 * @throws \Exception
	 */
	private function hasUserConfiguredTwoFactorAuthentication(): bool
	{
		$user = $this->getIdentity();

		if (empty($user->otpKey) || empty($user->otep))
		{
			return false;
		}

		// Set session to user has configured 2fa
		$this->getSession()->set('has2fa', 1);

		return true;
	}
}
