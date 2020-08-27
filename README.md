# Force2faUsergroup Plugin

This plugin allows to force users to set up 2FA in a specific user group.

## Sponsoring and Donation

You use this extension in an commercial context and / or want to support me and give something back?

There are two ways to support me right now:
- This extension is part of [Github Sponsors](https://github.com/sponsors/zero-24/) by sponsoring me, you help me continue my oss work for the [Joomla! Project](https://volunteers.joomla.org/joomlers/248-tobias-zulauf), write bug fixes, improving features and maintain my extensions.
- You just want to send me an one-time donation? Great you can do this via [PayPal.me/zero24](https://www.paypal.me/zero24).

Thanks for your support!

## Features

This plugin allows to force users to set up 2FA in a specific user group.

- Setup the groups to force a 2FA setup on the next login.

## Configuration

### Initial setup the plugin

- Download the latest version of the plugin: https://github.com/zero-24/plg_system_force2fausergroup/releases/latest
- Install the plugin using Upload & Install
- Enable the plugin form the plugin manager
- Setup the groups that you want 2FA to be enforced one.

Now the inital setup is completed.

### Option descriptions

#### 2FA Usergroups

Select form a multiselect the groups on where the 2FA should be enforced on the next login.

## Translation

This plugin is translated into the following languages:
- de-DE by @zero-24
- en-GB by @zero-24

You want to contribute a translation for an additional language? Feel free to create an Pull Request against the master branch.

## Update Server

Please note that my update server only supports the latest version running the latest version of Joomla and atleast PHP 7.2.5.
Any other plugin version I may have added to the download section don't get updates using the update server.

## Issues / Pull Requests

You have found an Issue or have an question / suggestion regarding the plugin, or do you want to propose code changes?
[Open an issue in this repo](https://github.com/zero-24/plg_system_force2fausergroup/issues/new) or submit a pull request with the proposed changes against the master branch.

## Joomla! Extensions Directory (JED)

This plugin can also been found in the Joomla! Extensions Directory: [Force2faUsergroup by zero24](https://extensions.joomla.org/extension/force2fausergroup/)

## Release steps

- `build/build.sh`
- `git commit -am 'prepare release Force2faUsergroup 1.0.x'`
- `git tag -s '1.0.x' -m 'Force2faUsergroup 1.0.x'`
- `git push origin --tags`
- create the release on GitHub
- `git push origin master`
