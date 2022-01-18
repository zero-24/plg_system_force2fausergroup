# Force2faUsergroup Plugin

This plugin allows to force users to set up 2FA in a specific user group.

## Features

This plugin allows to force users to set up 2FA in a specific user group.

- Setup the groups to force a 2FA setup on the next login.

## Configuration

### Initial setup the plugin

- [Download the latest version of the plugin](https://github.com/zero-24/plg_system_force2fausergroup/releases/latest)
- Install the plugin using `Upload & Install`
- Enable the plugin `System - Force2faUsergroup` from the plugin manager
- Setup the groups that you want 2FA to be enforced one.

Now the inital setup is completed.

### Option descriptions

#### 2FA Usergroups

Select form a multiselect the groups on where the 2FA should be enforced on the next login.

## Update Server

Please note that my update server only supports the latest version running the latest version of Joomla and atleast PHP 7.2.5.
Any other plugin version I may have added to the download section don't get updates using the update server.

## Issues / Pull Requests

You have found an Issue, have a question or you would like to suggest changes regarding this extension?
[Open an issue in this repo](https://github.com/zero-24/plg_system_force2fausergroup/issues/new) or submit a pull request with the proposed changes.

## Translations

You want to translate this extension to your own language? Check out my [Crowdin Page for my Extensions](https://joomla.crowdin.com/zero-24) for more details. Feel free to [open an issue here](https://github.com/zero-24/plg_system_force2fausergroup/issues/new) on any question that comes up.

## Beyond this repo

This feature [has been merged to the Joomla! Core](https://github.com/joomla/joomla-cms/pull/30522) and will be part of the upcomming 4.1 release. This plugin here acts as a backport for Joomla 3.9+ 

## Joomla! Extensions Directory (JED)

This plugin can also been found in the Joomla! Extensions Directory: [Force2faUsergroup by zero24](https://extensions.joomla.org/extension/force2fausergroup/)

## Release steps

- `build/build.sh`
- `git commit -am 'prepare release Force2faUsergroup 1.0.x'`
- `git tag -s '1.0.x' -m 'Force2faUsergroup 1.0.x'`
- `git push origin --tags`
- create the release on GitHub
- `git push origin master`

## Crowdin

### Upload new strings

`crowdin upload sources`

### Download translations

`crowdin download --skip-untranslated-files --ignore-match`
