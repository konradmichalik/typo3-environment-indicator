<div align="center">

![Extension icon](Resources/Public/Icons/Extension.svg)

# TYPO3 extension `typo3_environment_indicator`

[![Latest Stable Version](https://typo3-badges.dev/badge/typo3_environment_indicator/version/shields.svg)](https://extensions.typo3.org/extension/typo3_environment_indicator)
[![Supported TYPO3 versions](https://typo3-badges.dev/badge/typo3_environment_indicator/typo3/shields.svg)](https://extensions.typo3.org/extension/typo3_environment_indicator)
[![Coverage](https://img.shields.io/coverallsCoverage/github/konradmichalik/typo3-environment-indicator?logo=coveralls)](https://coveralls.io/github/konradmichalik/typo3-environment-indicator)
[![CGL](https://img.shields.io/github/actions/workflow/status/konradmichalik/typo3-environment-indicator/cgl.yml?label=cgl&logo=github)](https://github.com/konradmichalik/typo3-environment-indicator/actions/workflows/cgl.yml)
[![Tests](https://img.shields.io/github/actions/workflow/status/konradmichalik/typo3-environment-indicator/tests.yml?label=tests&logo=github)](https://github.com/konradmichalik/typo3-environment-indicator/actions/workflows/tests.yml)
[![License](https://poser.pugx.org/konradmichalik/typo3-environment-indicator/license)](LICENSE.md)

</div>

This extension provides several features to show an environment indicator in the TYPO3 frontend and backend.

> [!NOTE]
> Has it ever happened to you that you changed data on a test system and then realized: oh no, that's the live system. 
> Well, to prevent that from happening (again), I created this extension.

![Environment Indicator Preview](Documentation/Images/intro.jpg)

## ✨ Features

| Preview                                                                                | Feature                                                                                                                                                                                                                                                                      | Frontend | Backend |
|----------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|---------|
| ![Frontend Hint Preview](Documentation/Images/preview-frontend-hint.png)               | **[Frontend hint](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/FrontendHint.html)** <br/><br/> Adds an informative hint to the frontend showing the website title and the current application context.                          | ✔️       |         |
| ![Backend Toolbar Item Preview](Documentation/Images/preview-backend-toolbar-item.png) | **[Backend toolbar item](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendToolbar.html)** <br/><br/> Adds an informative item with the current application context to the backend toolbar.                                   |          | ✔️      |
| ![Backend Topbar Preview](Documentation/Images/preview-backend-topbar.jpg)             | **[Backend topbar](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendTopbar.html)** <br/><br/> Colorize the backend header topbar regarding the application context.                                                          |          | ✔️      |
| ![Favicon Preview](Documentation/Images/preview-favicon.png)                           | **[Modified favicon](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/Favicon.html)** <br/><br/> Modify the favicon for frontend and backend based on the original favicon, the current application context and your configuration. | ✔️       | ✔️      |
| ![Backend Logo Preview](Documentation/Images/preview-backend-logo.jpg)                 | **[Modified backend logo](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendLogo.html)** <br/><br/> Modify the backend logo based on the original logo, the current application context and your configuration.               |          | ✔️      |
| ![Dashboard Widget Preview](Documentation/Images/preview-dashboard-widget.jpg)         | **[Dashboard widget](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/DashboardWidget.html)** <br/><br/> Render a dashboard widget according to the environment.                                                                    |          | ✔️      |
| ![Frontend Image Preview](Documentation/Images/preview-frontend-image.jpg)             | **[Modified frontend image](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/FrontendImage.html)** <br/><br/> Modify frontend image based on the original image, the current application context and your configuration.            | ✔️       |         |
| ![preview-theme.jpg](Documentation/Images/preview-theme.jpg)                                                                                       | **[Backend theme](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendTheme.html)** *(experimental)* <br/><br/> Colorize the entire TYPO3 v14+ backend (primary color, header, sidebar) based on the environment.              |          | ✔️      |

> [!NOTE]
> These environment indicators are mainly for development purposes (e.g. distinguishing between different test systems)
> and will not show in production environments.

## 🔥 Installation

### Requirements

| Version | TYPO3       | PHP          |
|---------|-------------|--------------|
| 3.x     | 13.4 - 14.x | 8.2 - 8.5    |
| 2.x     | 11.5 - 13.4 | 8.1 - 8.4    |

### Composer

[![Packagist](https://img.shields.io/packagist/v/konradmichalik/typo3-environment-indicator?label=version&logo=packagist)](https://packagist.org/packages/konradmichalik/typo3-environment-indicator)
[![Packagist Downloads](https://img.shields.io/packagist/dt/konradmichalik/typo3-environment-indicator?color=brightgreen)](https://packagist.org/packages/konradmichalik/typo3-environment-indicator)

Use the following composer command to install the extension:

```bash
composer require konradmichalik/typo3-environment-indicator
```

### TER

[![TER version](https://typo3-badges.dev/badge/typo3_environment_indicator/version/shields.svg)](https://extensions.typo3.org/extension/typo3_environment_indicator)
[![TER downloads](https://typo3-badges.dev/badge/typo3_environment_indicator/downloads/shields.svg)](https://extensions.typo3.org/extension/typo3_environment_indicator)

Download the zip file
from [TYPO3 extension repository (TER)](https://extensions.typo3.org/extension/typo3_environment_indicator).

## 📙 Documentation

Please have a look at the
[official extension documentation](https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Index.html).

## 🚧 Migration from version 1.x to 2.x

Since version 2.x, the extension is using the new `Handler::addIndicator` method to add the environment indicator
configuration instead of the old `ConfigurationUtility::configByContext` method.

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## 💎 Credits

This project is partly inspired by the [laravel-favicon](https://github.com/beyondcode/laravel-favicon) package.

## ⭐ License

This project is licensed
under [GNU General Public License 2.0 (or later)](LICENSE.md).
