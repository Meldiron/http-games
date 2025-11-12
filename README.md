<div align="center">

# 🎮 HTTP Games

<picture><source media="(prefers-color-scheme: dark)" srcset="docs/public/logo-on-dark.svg"><img src="docs/public/logo-on-light.svg" alt="HTTP Games Logo" width="400"></picture>

*A modern, HTTP-based multiplayer gaming platform built with PHP*

[![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-777bb4?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/github/license/meldiron/http-games?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-Hurl-success?style=flat-square)](tests/)
[![Code Style](https://img.shields.io/badge/Code%20Style-Pint-success?style=flat-square)](https://github.com/laravel/pint)

[🚀 Quick Start](#-quick-start) • [📚 Documentation](#-documentation) • [🎯 Games](#-available-games) • [🤝 Contributing](#-contributing)

</div>

---

## ✨ Features

- 🎮 **Multiple Games** - Currently featuring GridTrap with more games coming soon
- 🔄 **Real-time Gameplay** - HTTP-based real-time multiplayer experience
- 🏗️ **Modern Architecture** - Built with Utopia Framework and PHP 8.4+
- 🔐 **User Management** - Complete authentication and user system
- 📊 **Game Analytics** - Track player progress and game statistics
- 🧪 **Well Tested** - Comprehensive API testing with Hurl
- 🐳 **Container Ready** - Easy deployment with modern PHP runtime

## 🛠️ Tech Stack

- **Backend**: PHP 8.4+ with Utopia Framework
- **Database**: Appwrite Database
- **Runtime**: FrankenPHP
- **Testing**: Hurl for API testing
- **Code Quality**: PHPStan + Laravel Pint
- **HTTP Server**: Caddy

## 🚀 Quick Start

### Prerequisites

- PHP 8.4+
- Composer
- FrankenPHP (or any PHP server)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/meldiron/http-games.git
   cd http-games
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   # Edit .env with your Appwrite configuration
   ```

4. **Start development server**
   ```bash
   composer run dev
   ```

The server will start at `http://localhost:8000` 🎉

## 🎯 Available Games

### 🕳️ GridTrap
A strategic puzzle game where players navigate through grid-based challenges.

*More games coming soon...*

## 📚 API Documentation

### Authentication Endpoints
- `POST /auth/login` - User login
- `POST /auth/register` - User registration
- `POST /auth/logout` - User logout

### Game Endpoints
- `GET /games` - List available games
- `POST /games/{gameId}/join` - Join a game
- `GET /games/{gameId}/status` - Get game status
- `POST /games/{gameId}/move` - Make a move

### User Endpoints
- `GET /users/profile` - Get user profile
- `PUT /users/profile` - Update user profile

## 🧪 Development

### Available Commands

```bash
# Development
composer run dev          # Start development server
composer run dev:reset    # Kill development server

# Code Quality
composer run lint         # Check code style
composer run format       # Fix code style
composer run check        # Run static analysis

# Testing
composer run test         # Run API tests with Hurl
```

### Project Structure

```
http-games/
├── app/                  # Application bootstrap
│   ├── http.php         # HTTP entry point
│   ├── init.php         # Initialization
│   └── resources.php    # Resource definitions
├── src/                 # Source code
│   ├── Games/           # Game implementations
│   ├── Users/           # User management
│   ├── Tokens/          # Authentication tokens
│   └── ...
├── tests/               # API tests (Hurl)
├── docs/                # Documentation
└── vendor/              # Dependencies
```

### Adding a New Game

1. Create a new directory in `src/Games/`
2. Implement the game logic following the existing pattern
3. Add game registration in the module
4. Create API tests in `tests/`

## 🔧 Configuration

The application uses environment variables for configuration:

- `_APP_DATABASE_OVERRIDE` - Database override for development
- Additional Appwrite configuration variables

## 🐳 Deployment

### Using FrankenPHP (Recommended)

```bash
frankenphp run --listen :8000
```

### Using Docker

```dockerfile
FROM dunglas/frankenphp

COPY . /app
WORKDIR /app

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000
```

## 🤝 Contributing

We welcome contributions! Please read our contributing guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests and code quality checks:
   ```bash
   composer run lint
   composer run check
   composer run test
   ```
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## 📝 License

This project is licensed under the terms specified in the [LICENSE](LICENSE) file.

## 🙏 Acknowledgments

- Built with [Utopia Framework](https://github.com/utopia-php/framework)
- Database powered by [Appwrite](https://appwrite.io)
- Runtime provided by [FrankenPHP](https://frankenphp.dev)

---

<div align="center">

**[⭐ Star this repo](https://github.com/meldiron/http-games)** if you find it useful!

Made with ❤️ by [Meldiron](https://github.com/meldiron)

</div>