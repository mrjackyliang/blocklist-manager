Block List Manager for Pi-hole
===============================

[![GitHub Releases](https://img.shields.io/github/v/release/mrjackyliang/blocklist-manager?style=flat-square&color=blue&sort=semver)](https://github.com/mrjackyliang/blocklist-manager/releases)
[![GitHub Top Languages](https://img.shields.io/github/languages/top/mrjackyliang/blocklist-manager?style=flat-square&color=success)](https://github.com/mrjackyliang/blocklist-manager)
[![GitHub License](https://img.shields.io/github/license/mrjackyliang/blocklist-manager?style=flat-square&color=yellow)](https://github.com/mrjackyliang/blocklist-manager/blob/master/LICENSE)
[![Become a GitHub Sponsor](https://img.shields.io/badge/sponsor-github-black?style=flat-square&color=orange)](https://github.com/sponsors/mrjackyliang)
[![Become a Patreon](https://img.shields.io/badge/donate-patreon-orange?style=flat-square&color=red)](https://www.patreon.com/mrjackyliang)

This is a network management tool to help you build better block lists. Custom block lists created with Block List Manager have been designed for use with [Pi-hole](https://github.com/pi-hole/pi-hole). Works on any LAMP server with support for `.htaccess` configuration.

To use Block List Manager (BLM), here are the steps you need to follow:
1. [Download](https://github.com/mrjackyliang/blocklist-manager/releases) and install to LAMP server
2. Configure BLM using the [these instructions](#server-configuration)
3. Begin creating custom block lists!

## Server Configuration
These settings that may be changed depending on your configuration. Examples located below. Before you configure BLM, remember to rename `config-sample.php` to `config.php`. __If you need help, feel free to [open an issue](https://github.com/mrjackyliang/blocklist-manager/issues/new/choose) on GitHub!__

##### Pi-hole Configuration
1. __PI_HOLE_URL__ - The URL to access Pi-hole admin (e.g. `http://192.168.1.2:80`).
2. __PI_HOLE_TOKEN__ - The token to access Pi-hole admin API ([instructions here](#retrieving-pi-hole-token))

##### Script Configuration
1. __BASE_URL__ - If BLM is hosted in a sub-folder, you will need to configure this ([instructions here](#sub-folder-configuration))
2. __WEB_PASSWORD__ - Set a strong password to protect intruders from accessing BLM

##### Block Lists File Location
1. __BLOCK_DIRECTORY__ - Place block lists in this directory
2. __BLOCK_LIST_FILES__ - Block list files ([instructions on creating list files](#creating-list-files))

##### Accept Lists File Location
1. __ACCEPT_DIRECTORY__ - Place accept lists in this directory
2. __ACCEPT_LIST_FILES__ - Accept list files ([instructions on creating list files](#creating-list-files))

##### Watch Lists File Location
1. __WATCH_DIRECTORY__ - Place watch lists in this directory
2. __WATCH_LIST_FILES__ - Watch list files ([instructions on creating list files](#creating-list-files))

## Retrieving Pi-hole Token
In order for BLM to work properly, an API token will be required to access the __Top Permitted Domains__ and __Top Blocked Domains__. Follow the instructions here to retrieve these lists:

1. Login to the Pi-hole Admin Console
2. Click Settings > API / Web interface
4. Click __Show API token__
5. Read the confirmation, then click __Yes, show API token__
6. Copy the text after `Raw API Token:`
7. Paste the copied text to the PI_HOLE_TOKEN above

## Sub-Folder Configuration
If you are setting up BLM on an existing server, chances are you may want to store it in a sub-folder. When you do, several changes need to be made. Instructions below assume you will place it in the `blocklist` folder:

1. Edit BASE_URL in `config.php` file:
   - Change `/` to `/blocklist/`
2. Edit `.htaccess` file:
   - Change `RewriteBase /` to `RewriteBase /blocklist/`

## Creating List Files
BLM does not come with block lists by default. For starters, add [StevenBlack's Hosts](https://github.com/StevenBlack/hosts) into Pi-hole. Then apply the experience towards your own lists with the instructions below:

| List Type   | Directory        | Description                                   | Supported File Extensions |
|-------------|------------------|-----------------------------------------------|---------------------------|
| Block list  | `/lists/block/`  | Block list files are stored in this location  | `.list`, `.txt`           |
| Accept list | `/lists/accept/` | Accept list files are stored in this location | `.list`, `.txt`           |
| Watch list  | `/lists/watch/`  | Watch list files are stored in this location  | `.list`, `.txt`           |

#### File Naming Conventions
By default, BLM converts file names to nice names for display in the interface. If you would like more naming conventions, feel free to [open an issue](https://github.com/mrjackyliang/blocklist-manager/issues/new/choose) on GitHub! Examples are:

| Naming Type          | File Name                 | Nice Name          |
|----------------------|---------------------------|--------------------|
| Convert to words     | `cool-block-list.list`    | Cool Block List    |
| Convert to ampersand | `abc--def.list`           | Abc & Def          |
| Lowercase "of"       | `internet-of-things.list` | Internet of Things |
| Lowercase "and"      | `this-and-this.list`      | This and This      |
| Lowercase "or"       | `this-or-this.list`       | This or This       |

#### Add Lists to Block List Manager
When you create a new list, these lists must be configured for use in BLM. To add the file for use, open the `config.php` file, and then configure the appropriate settings:

| List         | Setting             | Example                      | Type       |
|--------------|---------------------|------------------------------|------------|
| Block lists  | `BLOCK_LIST_FILES`  | `[ 'abc.list', 'def.list' ]` | `string[]` |
| Accept lists | `ACCEPT_LIST_FILES` | `[ 'abc.list', 'def.list' ]` | `string[]` |
| Watch lists  | `WATCH_LIST_FILES`  | `[ 'abc.list', 'def.list' ]` | `string[]` |

#### Cannot Read or Write Files
If BLM is complaining that the file cannot be read or written, make sure the permissions and user ownership has been set correctly. Under an identical server configuration, you may use the commands below to correct these permission errors:

```sh
chmod -R 644 lists
chown -R www-data:www-data lists
```

#### Add Block Lists to Pi-hole
Once the block lists have been created, add them to Pi-hole:

1. In BLM, click __My Pi-hole__ to reveal a menu
2. Click __Configure Ad Lists__
3. Login to the Pi-hole Admin Console (if you haven't)
4. Under the __Add a new adlist__ section:
   - Type the URL to the block list in the `Address:` section (e.g. `http://192.168.1.2:80/lists/block/abc.list`)
   - Optionally add a description in the `Comment:` section (e.g. Example Block List)
5. Click __Add__ to add the block list into Pi-hole

__NOTE:__ Accept and watch lists DO NOT need to be added into Pi-hole. They are meant for use with BLM to help you build better block lists.

## Table Usages
While Pi-hole is meant to cover all DNS requests of your network, you will often be working with very large tables (spanning up to a billion rows). This is to ensure that any network activity will be shown under the respective tables below.

| Table             | Description                                      | Display Conditions                                                                                |
|-------------------|--------------------------------------------------|---------------------------------------------------------------------------------------------------|
| Allowed Domains   | Shows domains passed through Pi-hole             | - Domain not in BLM block lists<br>- Domain not in BLM accept lists                               |
| Blocked Domains   | Shows domains blocked by Pi-hole                 | - Domain not in BLM block lists                                                                   |
| Watchlist Domains | Shows domains partially matching the watch lists | - Domain not in BLM block lists<br>- Domain not in BLM accept lists<br>- Domain in BLM watch list |
| Stagnant Domains  | Shows domains never blocked by Pi-hole           | - Domain not shown in Blocked Domains table                                                       |

__NOTE:__ If Pi-hole logs becomes too large to work, you can always reset them by going to My Pi-hole > System Settings > __Flush logs__.

## Lists Formatting
While BLM and Pi-hole are different applications, BLM is designed to work with a simple domain list format instead of using the [hosts file](https://en.wikipedia.org/wiki/Hosts_(file)) format. For example:

```text
# Company 1
company-a.com

# Company 2
company-b.com

# Company 3
company-c.com
abc.company-c.com

# Company 4
company-d.com
```

#### Wildcard domain support
When creating block lists, it is crucial to know that block lists have a strict match. This is a Pi-hole Gravity list limitation.

| List         | Partial Domain Support | Does `example.com` match `www.example.com`? | Does `www.example.com` match `example.com`? | Does `ad` match `ad.com`? |
|--------------|------------------------|---------------------------------------------|---------------------------------------------|---------------------------|
| Block lists  | No                     | No                                          | No                                          | No                        |
| Accept lists | Yes                    | Yes                                         | No                                          | Yes                       |
| Watch lists  | Yes                    | Yes                                         | No                                          | Yes                       |

__NOTE:__ With great power comes with great responsibility. By design, watch lists should have a lossy format (to detect domains), and accept lists should have a strict format (to hide domains).
