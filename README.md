<div align="center">

# 🎮 HTTP Games

<picture><source media="(prefers-color-scheme: dark)" srcset="docs/public/logo-on-dark.svg"><img src="docs/public/logo-on-light.svg" alt="HTTP Games Logo" height="150"></picture>

*An HTTP-based game platform. By developers, for developers.*

[![PHP Version](https://img.shields.io/badge/PHP-8.4-777bb4?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/github/license/meldiron/http-games?style=flat-square)](LICENSE)
[![Code Style](https://img.shields.io/badge/Code%20Style-Pint-e79248?style=flat-square)](https://github.com/laravel/pint)
[![Code Quality](https://img.shields.io/badge/Code%20Quality-PHPStan-576bae?style=flat-square)](https://phpstan.org/)

[![Tests](https://github.com/Meldiron/http-games/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/Meldiron/http-games/actions/workflows/tests.yml)


[📚 Documentation](https://http-games.almostapps.eu/) • [🎯 Games](#-available-games) • [🤝 Contributing](#-contributing)

</div>

---

## 🎯 Available Games

### GridTrap

A strategic puzzle game where cartographer navigate through grid-based challenges.

[Play now](https://http-games.almostapps.eu/docs/grid-trap/)

*More games coming soon...*

## 🤝 Contributing

We welcome contributions for new games!

To run HTTP Games server locally, prepare your environment:

```
composer install --ignore-platform-reqs
cp .env.example .env
```

> Ensure you have [FrankenPHP](https://frankenphp.dev) installed.

And fill in the `.env` file with your Appwrite credentials:

```
_APP_APPWRITE_ENDPOINT=https://fra.cloud.appwrite.io/v1
_APP_APPWRITE_KEY=standard_9ab3ed4525aa7546db...
_APP_APPWRITE_PROJECT_ID=6910...
_APP_DATABASE_OVERRIDE=local_version1
```

> I recommend to rotate database override each time you change database schema

Next, run the HTTP server on port 8000:

```
composer dev
```

All PRs must pass CI/CD checks. To run them locally, use following commands:

```bash
composer lint
composer check
composer test # Requires server running
```

## 📝 License

This project is licensed under the terms specified in the [LICENSE](LICENSE) file.

---

Feel free to contribute to documentation too! Setup here is very simple:

```
cd docs
npm install
npm run dev
```

## 🙏 Acknowledgments

- Built with [Utopia PHP](https://github.com/utopia-php)
- Database powered by [Appwrite](https://appwrite.io)
- Runtime provided by [FrankenPHP](https://frankenphp.dev)

---

<div align="center">

**[⭐ Star this repo](https://github.com/meldiron/http-games)** if you find it useful!

Made with ❤️ by [Meldiron](https://github.com/meldiron)

</div>