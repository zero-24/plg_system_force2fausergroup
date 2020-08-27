<?php
/**
 * Force2faUsergroup Plugin
 *
 * @copyright  Copyright (C) 2020 Tobias Zulauf All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

/**
 * Plugin class for Fetch Metadata
 *
 * @since  1.0
 */
class PlgUserForce2faUsergroup extends CMSPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

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
		if ($this->isTwoFactorAuthenticationRequired())
		{
			$this->redirectIfTwoFactorAuthenticationRequired();
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
	private function isTwoFactorAuthenticationRequired(): bool
	{
		$user = Factory::getUser();

		if ($user->guest)
		{
			return false;
		}

		// Check session if user has set up 2fa
		if ($this->app->getUserState('has2fa', false))
		{
			return false;
		}

		// If the user is not allowed to view the output then end here.
		$forced2faGroups = (array) $this->params->get('force2fausergroups', []);

		if (!empty($forced2faGroups))
		{
			$userGroups = (array) $user->get('groups', []);

			if (!array_intersect($forced2faGroups, $userGroups))
			{
				return false;
			}
		}

		if (!PluginHelper::isEnabled('twofactorauth'))
		{
			return false;
		}

		return !$this->hasUserConfiguredTwoFactorAuthentication();
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
	private function redirectIfTwoFactorAuthenticationRequired(): void
	{
		$option = (string) $this->app->input->get('option');
		$task   = (string) $this->app->input->get('task');
		$view   = (string) $this->app->input->get('view', null, 'string');
		$layout = (string) $this->app->input->get('layout', null, 'string');

		if ($this->app->isClient('site'))
		{
			// If user is already on edit profile screen or press update/apply button, do nothing to avoid infinite redirect
			if (($option === 'com_users' && \in_array($task, ['profile.edit', 'profile.save', 'profile.apply', 'user.logout', 'user.menulogout'], true))
				|| $option === 'com_users' && $view === 'profile' && $layout === 'edit')
			{
				return;
			}

			// Redirect to com_users profile edit
			$this->app->enqueueMessage(Text::_('PLG_USER_FORCE2FAUSERGROUP_2FA_REDIRECT_MESSAGE'), 'notice');
			$this->app->redirect('index.php?option=com_users&view=profile&layout=edit');
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
		$this->app->enqueueMessage(Text::_('PLG_USER_FORCE2FAUSERGROUP_2FA_REDIRECT_MESSAGE'), 'notice');
		$this->app->redirect('index.php?option=com_admin&task=profile.edit&id=' . Factory::getUser()->id);
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
		$user = Factory::getUser();

		// Check whether there is a 2FA setup for that user
		if (empty($user->otpKey) || empty($user->otep))
		{
			return false;
		}

		// Set session to user has configured 2fa
		$this->app->setUserState('has2fa', true);

		return true;
	}
}
