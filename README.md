# TYPO3 Extension clearcache_recursive

## 1 Features

* Shows a button in the side view. This can be used to delete the cache of the current page and the subpages.

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your [Composer][1] based TYPO3 project:

```
composer require ayacoo/clearcache-recursive
```

## 3 Configuration

### 3.1 UserTsConfig

The visibility of the "Recursive Clear Cache" button in the button bar and the entry in the context menu can be controlled via UserTsConfig.
By default, the function is only available to **administrators**.

To enable the function for non-admin users, add the following line to the UserTsConfig of the user or group:

```typoscript
options.clearCache.subpages = 1
```

## 4 Administration corner

### 4.1 Versions and support

| clearcache_recursive | TYPO3 | PHP       | Support / Development                |
|----------------------|-------|-----------|--------------------------------------|
| 4.x                  | 14.x  | 8.2 - 8.5 | features, bugfixes, security updates |
| 3.x                  | 13.x  | 8.2 - 8.5 | features, bugfixes, security updates |
| 2.x                  | 12.x  | 8.1 - 8.4 | bugfixes, security updates           |
| 1.x                  | 11.x  | 7.4 - 8.0 | security updates                     |

### 4.2 Release Management

clearcache_recursive uses [**semantic versioning**][2], which means, that

* **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or
  security relevant stuff without breaking changes,
* **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller
  tasks without breaking changes,
* and **major updates** (e.g. 1.0.0 => 2.0.0) breaking changes which can be
  refactorings, features or bugfixes.

### 4.3 Contribution

**Pull Requests** are gladly welcome! Nevertheless please don't forget to add an
issue and connect it to your pull requests. This
is very helpful to understand what kind of issue the **PR** is going to solve.

**Bugfixes**: Please describe what kind of bug your fix solve and give us
feedback how to reproduce the issue. We're going
to accept only bugfixes if we can reproduce the issue.

## 5 Thanks / Notices

Special thanks to Georg Ringer and his [news][3] extension. A good template to
build a TYPO3 extension. Here, for example, the structure of README.md is used.

[1]: https://getcomposer.org/

[2]: https://semver.org/

[3]: https://github.com/georgringer/news

## 6 Support

If you are happy with the extension and would like to support it in any way, I
would appreciate the support of social institutions.
