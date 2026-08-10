<div align="center">

![Extension icon](Resources/Public/Icons/Extension.png)

# TYPO3 extension `typo3_environment_indicator`

[![Latest Stable Version](https://typo3-badges.dev/badge/typo3_environment_indicator/version/shields.svg)](https://extensions.typo3.org/extension/typo3_environment_indicator)
![TYPO3](https://img.shields.io/badge/TYPO3-13.4%20%7C%2014.3-orange.svg)
[![Coverage](https://img.shields.io/coverallsCoverage/github/konradmichalik/typo3-environment-indicator?logo=coveralls)](https://coveralls.io/github/konradmichalik/typo3-environment-indicator)
[![CGL](https://img.shields.io/github/actions/workflow/status/konradmichalik/typo3-environment-indicator/cgl.yml?label=cgl&logo=github)](https://github.com/konradmichalik/typo3-environment-indicator/actions/workflows/cgl.yml)
[![Tests](https://img.shields.io/github/actions/workflow/status/konradmichalik/typo3-environment-indicator/tests.yml?label=tests&logo=github)](https://github.com/konradmichalik/typo3-environment-indicator/actions/workflows/tests.yml)
[![License](https://poser.pugx.org/konradmichalik/typo3-environment-indicator/license)](LICENSE.md)

</div>

This extension provides several features to show an environment indicator in the TYPO3 frontend and backend, and beyond (CLI, mails).

> [!NOTE]
> Has it ever happened to you that you changed data on a test system and then realized: oh no, that's the live system. 
> Well, to prevent that from happening (again), I created this extension.

![Environment Indicator Preview](Documentation/Images/intro.jpg)

## ✨ Features

<table>
  <thead>
    <tr>
      <th>Icon</th>
      <th>Preview</th>
      <th>Feature</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th colspan="3" align="left">Frontend + Backend</th>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-Favicon.png" width="80" alt="Favicon Icon"></td>
      <td><img src="Documentation/Images/preview-favicon.png" width="320" alt="Favicon Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/Favicon.html">Modified favicon</a></strong><br/><br/>Modify the favicon for frontend and backend based on the original favicon, the current application context and your configuration.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-PageTitlePrefix.png" width="80" alt="Page Title Icon"></td>
      <td><img src="Documentation/Images/preview-page-title-prefix.jpg" width="320" alt="Page Title Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/PageTitle.html">Page title</a></strong><br/><br/>Prefix or suffix the page title in frontend and backend with the current application context.</td>
    </tr>
    <tr>
      <th colspan="3" align="left">Frontend</th>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-FrontendHint.png" width="80" alt="Frontend Hint Icon"></td>
      <td><img src="Documentation/Images/preview-frontend-hint.png" width="320" alt="Frontend Hint Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/FrontendHint.html">Frontend hint</a></strong><br/><br/>Adds an informative hint to the frontend showing the website title and the current application context.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-FrontendImage.png" width="80" alt="Frontend Image Icon"></td>
      <td><img src="Documentation/Images/preview-frontend-image.jpg" width="320" alt="Frontend Image Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/FrontendImage.html">Modified frontend image</a></strong><br/><br/>Modify frontend image based on the original image, the current application context and your configuration.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-BrowserConsole.png" width="80" alt="Frontend Console Icon"></td>
      <td><img src="Documentation/Images/preview-console.png" width="320" alt="Frontend Console Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/Console.html">Browser console badge</a></strong><br/><br/>Print a styled environment badge to the browser console on page load.</td>
    </tr>
    <tr>
      <th colspan="3" align="left">Backend</th>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-BackendToolbarItem.png" width="80" alt="Backend Toolbar Item Icon"></td>
      <td><img src="Documentation/Images/preview-backend-toolbar-item.png" width="320" alt="Backend Toolbar Item Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendToolbar.html">Backend toolbar item</a></strong><br/><br/>Adds an informative item with the current application context to the backend toolbar.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-BackendTopbar.png" width="80" alt="Backend Topbar Icon"></td>
      <td><img src="Documentation/Images/preview-backend-topbar.jpg" width="320" alt="Backend Topbar Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendTopbar.html">Backend topbar</a></strong><br/><br/>Colorize the backend header topbar regarding the application context.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-BackendLogo.png" width="80" alt="Backend Logo Icon"></td>
      <td><img src="Documentation/Images/preview-backend-logo.jpg" width="320" alt="Backend Logo Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendLogo.html">Modified backend logo</a></strong><br/><br/>Modify the backend logo based on the original logo, the current application context and your configuration.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-DashboardWidget.png" width="80" alt="Dashboard Widget Icon"></td>
      <td><img src="Documentation/Images/preview-dashboard-widget.jpg" width="320" alt="Dashboard Widget Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/DashboardWidget.html">Dashboard widget</a></strong><br/><br/>Render a dashboard widget according to the environment.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-BackendTheme.png" width="80" alt="Backend Theme Icon"></td>
      <td><img src="Documentation/Images/preview-theme.jpg" width="320" alt="Backend Theme Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendTheme.html">Backend theme</a></strong> <em>(experimental)</em><br/><br/>Colorize the entire TYPO3 v14+ backend (primary color, header, sidebar) based on the environment.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-BackendLogin.png" width="80" alt="Backend Login Icon"></td>
      <td><img src="Documentation/Images/preview-backend-login.jpg" width="320" alt="Backend Login Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/BackendLogin.html">Backend login</a></strong><br/><br/>Show a colored environment badge directly on the backend login screen.</td>
    </tr>
    <tr>
      <th colspan="3" align="left">Misc</th>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-CLI.png" width="80" alt="CLI Banner Icon"></td>
      <td><img src="Documentation/Images/preview-cli-banner.jpg" width="320" alt="CLI Banner Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/CliBanner.html">CLI banner</a></strong><br/><br/>Print a colored environment banner to stderr before an interactive console command runs.</td>
    </tr>
    <tr>
      <td><img src="Documentation/Images/Extension-EI-MailSubjectPrefix.png" width="80" alt="Mail Subject Prefix Icon"></td>
      <td><img src="Documentation/Images/preview-mail-subject-prefix.jpg" width="320" alt="Mail Subject Prefix Preview"></td>
      <td><strong><a href="https://docs.typo3.org/p/konradmichalik/typo3-environment-indicator/main/en-us/Indicators/MailSubjectPrefix.html">Mail subject prefix</a></strong><br/><br/>Prepends the environment to the subject of every mail sent through the TYPO3 Mailer API.</td>
    </tr>
  </tbody>
</table>

> [!NOTE]
> These environment indicators are mainly for development purposes (e.g. distinguishing between different test systems)
> and will not show on the live production site. They do show in `Production/Staging` and `Production/Standby`
> application contexts, where they help distinguish those systems from the live site.

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

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## 💎 Credits

This project is partly inspired by the [laravel-favicon](https://github.com/beyondcode/laravel-favicon) package.

## ⭐ License

This project is licensed
under [GNU General Public License 2.0 (or later)](LICENSE.md).
